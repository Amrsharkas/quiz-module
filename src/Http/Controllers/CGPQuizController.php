<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use mennaAbouelsaadat\quizGenerator\Models\CGPQuiz;
use mennaAbouelsaadat\quizGenerator\Models\CGPQuizSection;
use mennaAbouelsaadat\quizGenerator\Models\CGPTopic;
use mennaAbouelsaadat\quizGenerator\Models\CGPQuestionType;
use mennaAbouelsaadat\quizGenerator\Models\CGPDifficulty;
use mennaAbouelsaadat\quizGenerator\Models\CGPQuizSectionDetail;

class QuizController extends Controller
{
    public function index()
    {
        return view('quiz/index');
    }

    public function addQuizTemplate()
    {
        $quiz = new CGPQuiz();
        $quiz->save();
        $data['partialView'] = 'quiz.quiz_template';
        $data['quiz'] = $quiz;
        return view('quiz.base', $data);
    }

    public function editQuizTemplate($quiz_id)
    {
        $quiz = CGPQuiz::find($quiz_id);
        $data['partialView'] = 'quiz.quiz_template';
        $data['quiz'] = $quiz;
        $data['question_types'] = CGPQuestionType::get();
        $data['difficulties'] = CGPDifficulty::get();
        $data['topics'] = CGPTopic::get();
        return view('quiz.base', $data);
    }

    public function addQuizSection($quiz_id)
    {
        $quiz_section  = new CGPQuizSection();
        $quiz_section->quiz_id = $quiz_id;
        $quiz_section->save();

        $data['quiz_section'] = $quiz_section;
        $data['topics'] = CGPTopic::get();
        return view('quiz.quiz_section', $data);
    }

    public function deleteQuizSection($quiz_section_id)
    {
        $quiz_section = CGPQuizSection::find($quiz_section_id);
        $quiz_section->delete();
    }

    public function update(Request $request)
    {
        $data = $request->input();
        $quiz = CGPQuiz::find($data['quiz_id']);
        $quiz->updateData($data);
    }

    public function addQuizSectionQuestionDetail($quiz_section_id)
    {
        $quiz_section_detail  = new CGPQuizSectionDetail();
        $quiz_section_detail->quiz_section_id = $quiz_section_id;
        $quiz_section_detail->save();
        $data['quiz_section_detail'] = $quiz_section_detail;
        $data['question_types'] = CGPQuestionType::get();
        $data['difficulties'] = CGPDifficulty::get();
        return view('quiz.quiz_question_details', $data);
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
