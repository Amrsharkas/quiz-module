<?php

namespace mennaAbouelsaadat\quizGenerator\Http\Controllers;

use Illuminate\Http\Request;
use mennaAbouelsaadat\quizGenerator\Models\CGPQuiz;
use mennaAbouelsaadat\quizGenerator\Models\CGPQuizSection;
use mennaAbouelsaadat\quizGenerator\Models\CGPTopic;
use mennaAbouelsaadat\quizGenerator\Models\CGPQuestionType;
use mennaAbouelsaadat\quizGenerator\Models\CGPDifficulty;
use mennaAbouelsaadat\quizGenerator\Models\CGPQuizSectionDetail;

class CGPQuizController extends Controller
{
    public function index()
    {
        return view('quiz/index');
    }

    public static function addQuizTemplate()
    {
        $quiz = new CGPQuiz();
        $quiz->save();
        $data['quiz'] = $quiz;
        return $quiz;
    }

    public static function editQuizTemplate($quiz_id)
    {
        $quiz = CGPQuiz::find($quiz_id);
        $data['quiz'] = $quiz;
        $data['question_types'] = CGPQuestionType::get();
        $data['difficulties'] = CGPDifficulty::get();
        $data['topics'] = CGPTopic::get();
        return $data;
    }

    public function addQuizSection($quiz_id)
    {
        $quiz_section  = new CGPQuizSection();
        $quiz_section->quiz_id = $quiz_id;
        $quiz_section->save();

        $data['quiz_section'] = $quiz_section;
        $data['topics'] = CGPTopic::get();
        return view('CGP_quiz.quiz_section', $data);
    }

    public function deleteQuizSection($quiz_section_id)
    {
        $quiz_section = CGPQuizSection::find($quiz_section_id);
        $quiz_section->delete();
    }

    public static function update(Request $request, $url='reload')
    {
        $data = $request->input();
        $quiz = CGPQuiz::find($data['quiz_id']);
        $msg = $quiz->updateData($data);
        if ($msg) {
            $action_chain['Run function'] = ['insufficient_quiz'];
            $parameters['msg'] = $msg;
            $parameters['quiz_id'] = $quiz->id;
            $parameters['url'] = $url;
            $action_chain['parameters'] = $parameters;
        } else {
            $action_chain['swal']['title'] = '';
            $action_chain['swal']['msg'] = 'successfully updated';
            $action_chain['page'] = $url;
        }
        $response['action_chain'] = $action_chain;
        return response()->json($response);
    }

    public function updateAfterUserResponse(Request $request)
    {
        $data = $request->input();
        $quiz = CGPQuiz::find($data['quiz_id']);
        if ($data['response'] == 'yes') {
            $quiz->removeGeneratedQuizzes();
            $quiz->valid_request = $quiz->testing_request;
            $quiz->testing_request = null;
            $quiz->save();
        } else {
            $quiz->rollback();
        }
        $action_chain['swal']['title'] = '';
        $action_chain['swal']['msg'] = 'successfully updated';
        $action_chain['page'] = 'reload';
        $response['action_chain'] = $action_chain;
        return response()->json($response);
    }

    public function addQuizSectionQuestionDetail($quiz_section_id)
    {
        $quiz_section_detail  = new CGPQuizSectionDetail();
        $quiz_section_detail->quiz_section_id = $quiz_section_id;
        $quiz_section_detail->save();
        $data['quiz_section_detail'] = $quiz_section_detail;
        $data['question_types'] = CGPQuestionType::get();
        $data['difficulties'] = CGPDifficulty::get();
        return view('CGP_quiz.quiz_question_details', $data);
    }

    public function deleteQuizSectionDetail($quiz_section_detail_id)
    {
        $quiz_section_detail = CGPQuizSectionDetail::find($quiz_section_detail_id);
        $quiz_section_detail->delete();
    }

    public function generateQuiz($quiz_id)
    {
        $quiz = CGPQuiz::find($quiz_id);
        dd($quiz->randomQuestions());
    }
}
