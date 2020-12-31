<?php

namespace mennaAbouelsaadat\quizGenerator\Http\Controllers;

use Illuminate\Http\Request;
use mennaAbouelsaadat\quizGenerator\Models\CGPQuizGenerator;
use mennaAbouelsaadat\quizGenerator\Models\CGPQuestion;
use mennaAbouelsaadat\quizGenerator\Models\CGPTopic;
use mennaAbouelsaadat\quizGenerator\Models\CGPQuestionTopic;
use mennaAbouelsaadat\quizGenerator\Models\CGPDifficulty;
use mennaAbouelsaadat\quizGenerator\Models\CGPQuestionType;
use mennaAbouelsaadat\quizGenerator\Models\CGPQuestionAnswer ;
use mennaAbouelsaadat\quizGenerator\Models\CGPTextCorrectAnswer ;
use Illuminate\Http\Response;
use mennaAbouelsaadat\quizGenerator\Models\File;
use mennaAbouelsaadat\quizGenerator\Models\CGPQuestionInfo;
use mennaAbouelsaadat\quizGenerator\Models\CGPQuizSectionDetail;
use Illuminate\Filesystem\Filesystem;
use Storage;

class CGPQuestionController extends Controller
{
    public function index()
    {
        $data = [];
        $data['partialView'] = 'questions.index';
        $data['questions'] = CGPQuestion::orderBy('created_at', 'desc')->where('admin_show', 1)->get();
        $data['questoin_types'] = CGPQuestionType::get();
        return view('quiz_generator.base', $data);
    }


    public static function init()
    {
        $question = CGPQuestion::init();
        return $question;
    }

    public static function edit($id)
    {
        $question = CGPQuestion::findOrFail($id);
        $data ['topics'] = CGPTopic::all() ;
        $data ['difficulties'] = CGPDifficulty::all() ;
        $data ['question_topics'] = CGPQuestionTopic::where('question_id', $id) ->get() ->pluck('topic_id') ->toArray() ;
        $data['question'] = $question;
        if (!session()->has('question_quiz_section_details')) {
            session()->put('question_quiz_section_details', []);
        }
        $question_quiz_section_details = session()->pull('question_quiz_section_details', []);
        if ($question_key = array_search($question->id, array_column($question_quiz_section_details, 'question_id')) !== false) {
            unset($question_quiz_section_details[$question_key]);
        }
        $question_quiz_section_detail['question_id'] = $question->id;
        $question_quiz_section_detail['details_id'] = $question->quizSectionDetails()->pluck('quiz_section_detail_id')->toArray();
        session()->push('question_quiz_section_details', $question_quiz_section_detail);
        return $data;
    }

    public function getQuestionContent(Request $request, $id)
    {
        $data = $request ->input() ;
        $question = CGPQuestion::find($id) ;
        $response ['question'] = $question ;
        $data ['infos'] = isset($data ['infos']) ? $data ['infos'] : array() ;

        foreach ($data ['infos'] as $key => $info) {
            $response[$info] = 1;
        }
        $response['question_type'] = $data['question_type'];
        if (isset($data['system_assesset'])) {
            $response['system_assesset'] = $data['system_assesset'];
        }

        $data ['question_id'] = $id ;
        if ($data ['question_type'] == 'MCQ') {
            
            // dd($question ->textCorrectAnswersQuestionAnswer) ;
            if (in_array('text_input', $data ['infos'])) {
                if (!$question ->hasTextCorrectAnswers()) {
                    CGPQuestionAnswer::initTextCorrectAnswerQuestionAnswer($id) ;
                }
            }
            
            if (count($question->choiceAnswers() ->get()) == 0) {
                CGPQuestionAnswer::init($data);
                CGPQuestionAnswer::init($data);
            }
        } elseif ($data ['question_type'] == 'Text') {
            if (!$request ->system_assesset) {
                if (!$question->essayAnswer()) {
                    $question_answer_data ['question_id']  = $question ->id ;
                    $question_answer_data ['question_answer_type_id']  = 2 ;

                    $data ['system_assesst']  = 0 ;
                    CGPQuestionAnswer::init($data);
                }
            } else {
                if (count($question->textAnswers()->get()) == 0) {
                    $question_answer_data ['question_id']  = $question ->id ;
                    $question_answer_data ['question_answer_type_id']  = 2 ;

                    $data ['system_assesst']  = 1 ;
                    CGPQuestionAnswer::init($data);
                }
            }
        }

        $data['question_id'] = $id;

        return response([
            'status' => 'success',
            'content' => view('CGP_questions.question_contents.answers_view', $response) ->__toString()
        ]) ;
    }


    public static function update(Request $request, $url = "reload")
    {
        $data = $request->input();
        $question = CGPQuestion::find($data ['question_id']) ;
        $data['infos'] = $request->infos ? $request->infos : array() ;

        if ($data ['question_type'] == 'Multiple Choice' && !isset($data ['correct_answers'])) {
            $action_chain['swal']['title'] = '';
            $action_chain['swal']['msg'] = 'Please select at least one answer';
            $response['action_chain'] = $action_chain;
            return response() ->json($response) ;
        }

        if (!isset($data ['topics'])) {
            $action_chain['swal']['title'] = '';
            $action_chain['swal']['msg'] = 'Select at Least 1 Topic' ;
            $response['action_chain'] = $action_chain;
            return response() ->json($response) ;
        }

        if ($question->admin_show) {
            if ($question->suspended_token) {
                $action_chain['swal']['title'] = '';
                $action_chain['swal']['msg'] = 'Try again later';
                $action_chain['page'] = $url;
                $response['action_chain'] = $action_chain;
                return response()->json($response);
            }
            
            $token = md5(uniqid().$question->id);

            CGPQuestion::whereIn('id', $question->questionsIdThatShouldBeSuspended())->update(['suspended_token'=>$token]);
            $question->archived = 1;
            $question->save();
            $cloned_question =  CGPQuestion::cloneQuestion($question->id);
            $cloned_question->archived = null;
            $cloned_question->save();
            $output = $cloned_question->updateData($data, $cloned=1);
        } else {
            $output = $question->updateData($data);
        }

        if (isset($output['insufficient_quizzes_data']['quizzes_objects']) &&count($output['insufficient_quizzes_data']['quizzes_objects']) > 0) {
            $action_chain['Run function'] = ['insufficient_quizzes'];
            $parameters['question_id'] = $question->id;
            $parameters['msg'] = $output['insufficient_quizzes_data']['quizzes_names'];
            $parameters['url'] = $url;
            $action_chain['parameters'] = $parameters;
        } elseif (isset($output['quizzes_converted_sufficient_data']['quizzes_objects']) && count($output['quizzes_converted_sufficient_data']['quizzes_objects']) > 0) {
            $parameters=array();
            $question->removeSuspendedToken();
            $action_chain['swal']['title'] = '';
            $action_chain['swal']['msg'] = $output['quizzes_converted_sufficient_data']['quizzes_names'];
            $action_chain['page'] = $url;
            $action_chain['parameters'] = $parameters;
        } else {
            $question->removeSuspendedToken();
            $action_chain['swal']['title'] = '';
            $action_chain['swal']['msg'] = 'successfully updated';
            $action_chain['page'] = $url;
        }
        $response['action_chain'] = $action_chain;
        return response()->json($response);
        return redirect('/admin/questions/edit/' . $data ['question_id']) ;
        // dd($data);
    }

    public function delete($id)
    {
        $question = Question::find($id);
        if ($question) {
            Question::destroy($id);
        }
    }
    public function deleteFile($id, $model)
    {
        $model='App\\'.$model;
        $file_id = $model::find($id);
        $model::where('id', $id)->update(['file_id'=>null]);
        $file = File::where('id', $file_id->file_id)->first();
        $dir = new Filesystem();

        if ($model =="App\Question") {
            $other_questions_using_the_same_file = $model::where('file_id', $file->id)->count();
            if ($other_questions_using_the_same_file) {
                return "file not deleted";
            }
        }


        Storage::disk('s3')->delete($file->hash);
        return "file  deleted";
    }

    public function initAnswer(Request $request)
    {
        $data = $request ->input() ;
        $answer = CGPQuestionAnswer::init($data) ;
        $question_id = $data ['question_id'] ;
        $question_type = $data ['question_type'] ;

        $infos = $request ->infos ? $request ->infos : array()  ;
        $view = view('CGP_questions.question_contents.answer', compact('question_id', 'answer', 'infos', 'question_type')) ->__toString() ;
        return response(['status' => 'success', 'content' => $view, 'id' => $answer ->id]);
    }

    public function removeAnswer($question_id, $answer_id)
    {
        CGPQuestionAnswer::where('id', $answer_id)->delete() ;
        return response(['status' => 'success']) ;
    }

    public function initTextCorrectAnswer(Request $request)
    {
        $question = CGPQuestion::find($request ->question_id);


        $text_correct_answer = new CGPTextCorrectAnswer ;
        $text_correct_answer ->question_answer_id = $question->hasTextCorrectAnswers()->id ;
        $text_correct_answer ->text = $request ->answer_text ;
        $text_correct_answer ->save() ;

        $answer  = $text_correct_answer  ;

        $view = view('CGP_questions.question_contents.possible_answer', compact('answer')) ->__toString() ;
        return response(['status' => 'success', 'content' => $view, 'id' => $answer ->id])  ;
    }

    public function updateAfterUserResponse(Request $request)
    {
        $data = $request->input();
        $question = CGPQuestion::find($data['question_id']);
        if ($data['response'] == 'yes') {
            $question->continueEditting();
            $quizzes_converted_sufficient_data = $question->validateInsufficientQuizzes();
            if (count($quizzes_converted_sufficient_data['quizzes_objects']) > 0) {
                return $quizzes_converted_sufficient_data['quizzes_names'];
            } else {
                return 'successfully updated';
            }
        } else {
            $question->rollback();
            return 'successfully updated';
        }

        $action_chain['page'] = 'reload';
        $response['action_chain'] = $action_chain;
        return response()->json($response);
    }

    public function rollbackQuestions()
    {
        $questions = CGPQuestion::whereNotNull('testing_request')->get();
        foreach ($questions as $key => $question) {
            $question->rollback();
        }
    }
    public function removeTextCorrectAnswer($question_id, $text_correct_answer_id)
    {
        CGPTextCorrectAnswer::find($text_correct_answer_id) ->delete() ;
        return response(['status' => 'success']) ;
    }
    public function initTopic(Request $request)
    {
        $topic = CGPTopic::init($request ->input()) ;

        return response(['status' => 'success', 'id' => $topic ->id]) ;
    }
}
