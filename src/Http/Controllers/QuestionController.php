<?php

namespace mennaAbouelsaadat\quizGenerator\Http\Controllers;

use Illuminate\Http\Request;
use mennaAbouelsaadat\quizGenerator\Models\QuizGenerator;
use mennaAbouelsaadat\quizGenerator\Models\Question;
use mennaAbouelsaadat\quizGenerator\Models\Topic;
use mennaAbouelsaadat\quizGenerator\Models\QuestionTopic;
use mennaAbouelsaadat\quizGenerator\Models\Difficulty;
use mennaAbouelsaadat\quizGenerator\Models\QuestionType;
use mennaAbouelsaadat\quizGenerator\Models\QuestionAnswer ;
use mennaAbouelsaadat\quizGenerator\Models\TextCorrectAnswer ;
use Illuminate\Http\Response;
use mennaAbouelsaadat\quizGenerator\Models\File;
use mennaAbouelsaadat\quizGenerator\Models\QuestionInfo;
use mennaAbouelsaadat\quizGenerator\Models\QuizSectionDetail;
use Illuminate\Filesystem\Filesystem;
use Storage;

class QuestionController extends Controller
{
    public function index()
    {
        $data = [];
        $data['partialView'] = 'questions.index';
        $data['questions'] = Question::orderBy('created_at', 'desc')->where('admin_show', 1)->get();
        $data['questoin_types'] = QuestionType::get();
        return view('quiz_generator.base', $data);
    }


    public function init()
    {
        $question = new Question();
        $question->question_type_id = MULTIPLE_CHOICE ;
        $question->save();
        QuestionAnswer::create([
            'question_id' => $question ->id,
            'question_answer_type_id' => 3,

        ]);
        return redirect('/admin/questions/edit/'.$question->id);
    }

    public function edit($id)
    {
        $question = Question::findOrFail($id);
        $data ['types'] = array() ;
        $data ['topics'] = Topic::all() ;
        $data ['difficulties'] = Difficulty::all() ;
        $data ['question_topics'] = QuestionTopic::where('question_id', $id) ->get() ->pluck('topic_id') ->toArray() ;
        // $data = [];
        $data ['infos'] = $question ->getInfos()  ;
        $data['question'] = $question;
        $data ['question_type'] = $question ->getQuestionType() ;
        $data ['partialView'] = 'questions.form.edit' ;
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
        return view('questions.base', $data);
    }

    public function getQuestionContent(Request $request, $id)
    {
        $data = $request ->input() ;
        $question = Question::find($id) ;
        $data ['question'] = $question ;
        if (!isset($data ['infos'])) {
            $data ['infos'] = array() ;
        }

        if (in_array(EVALUATED_AUTOMATICALLY, $data ['infos']) && !$question ->essayAnswer()) {
            QuestionAnswer::init([
                'question_id' => $id,
                'answer_text' => '',
                'question_type' => $data ['question_type'],
                'system_assesst' => 0
            ]) ;
        }

        return response([
            'status' => 'success',
            'content' => view('questions.question_contents.answers_view', $data) ->__toString()
        ]) ;
    }


    public function update(Request $request)
    {
        $data = $request->input();
        $question = Question::find($data ['question_id']) ;
        $data['infos'] = $request->infos ? $request->infos : array() ;

        if ($data ['question_type'] == 'Multiple Choice' && !isset($data ['correct_answers'])) {
            return redirect() ->back() ->with(['status' => 'error', 'message' => 'Please select at least one answer']) ;
        }

        if (!isset($data ['topics'])) {
            return redirect() ->back() ->with(['status' => 'error', 'message' => 'Select at Least 1 Topic']) ;
        }

        $output = $question->updateData($data);
        if (isset($output['insufficient_quizzes_data']['quizzes_objects']) &&count($output['insufficient_quizzes_data']['quizzes_objects']) > 0) {
            $action_chain['Run function'] = ['insufficient_quizzes'];
            $parameters['question_id'] = $question->id;
            $parameters['msg'] = $output['insufficient_quizzes_data']['quizzes_names'];
            $action_chain['parameters'] = $parameters;
        } elseif (isset($output['quizzes_converted_sufficient_data']['quizzes_objects']) && count($output['quizzes_converted_sufficient_data']['quizzes_objects']) > 0) {
            $question->removeSuspendedToken();
            $action_chain['swal']['title'] = '';
            $action_chain['swal']['msg'] = $output['quizzes_converted_sufficient_data']['quizzes_names'];
            $action_chain['page'] = 'reload';
            $action_chain['parameters'] = $parameters;
        } else {
            $question->removeSuspendedToken();
            $action_chain['swal']['title'] = '';
            $action_chain['swal']['msg'] = 'successfully updated';
            $action_chain['page'] = 'reload';
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
        $answer = QuestionAnswer::init($data) ;
        $question_id = $data ['question_id'] ;
        $question_type = $data ['question_type'] ;

        $infos = $request ->infos ? $request ->infos : array()  ;
        $view = view('questions.question_contents.answer', compact('question_id', 'answer', 'infos', 'question_type')) ->__toString() ;
        return response(['status' => 'success', 'content' => $view, 'id' => $answer ->id]);
    }

    public function removeAnswer($question_id, $answer_id)
    {
        QuestionAnswer::where('id', $answer_id)->delete() ;
        return response(['status' => 'success']) ;
    }

    public function initTextCorrectAnswer(Request $request)
    {
        $question = Question::find($request ->question_id);


        $text_correct_answer = new TextCorrectAnswer ;
        $text_correct_answer ->question_answer_id = $question ->textCorrectAnswersQuestionAnswer() ->id ;
        $text_correct_answer ->text = $request ->answer_text ;
        $text_correct_answer ->save() ;

        $answer  = $text_correct_answer  ;

        $view = view('questions.question_contents.possible_answer', compact('answer')) ->__toString() ;
        return response(['status' => 'success', 'content' => $view, 'id' => $answer ->id])  ;
    }

    public function updateAfterUserResponse(Request $request)
    {
        $data = $request->input();
        $question = Question::find($data['question_id']);
        if ($data['response'] == 'yes') {
            $question->continueEditting();
            $quizzes_converted_sufficient_data = $question->validateInsufficientQuizzes();
            if (count($quizzes_converted_sufficient_data['quizzes_objects']) > 0) {
                $action_chain['swal']['title'] = '';
                $action_chain['swal']['msg'] = 'successfully updated';
            } else {
                $action_chain['swal']['title'] = '';
                $action_chain['swal']['msg'] = 'successfully updated';
            }
        } else {
            $question->rollback();
            $action_chain['swal']['title'] = '';
            $action_chain['swal']['msg'] = 'successfully updated';
        }

        $action_chain['page'] = 'reload';
        $response['action_chain'] = $action_chain;
        return response()->json($response);
    }

    public function rollbackQuestions()
    {
        $questions = Question::whereNotNull('testing_request')->get();
        foreach ($questions as $key => $question) {
            $question->rollback();
        }
    }
    public function removeTextCorrectAnswer($question_id, $text_correct_answer_id)
    {
        TextCorrectAnswer::find($text_correct_answer_id) ->delete() ;
        return response(['status' => 'success']) ;
    }
}
