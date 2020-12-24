<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;
use mennaAbouelsaadat\quizGenerator\Models\QuizSection;
use DB;
use mennaAbouelsaadat\quizGenerator\Jobs\GenerateQuiz;

class CGPQuiz extends Model
{
    protected $table = 'cgp_quizzes';
    public function quizSections()
    {
        return $this->hasMany('mennaAbouelsaadat\quizGenerator\Models\CGPQuizSection', 'quiz_id');
    }
    public function updateData($data)
    {
        $this->name = $data['name'];
        $this->passing_percentage = $data['success_percentage'];
        $this->duration = $data['duration'];
        $this->attempts_number = $data['number_of_attempts'];
        $this->admin_show = 1;
        $this->save();

        foreach ($data['quiz_section_id'] as $key => $quiz_section_id) {
            $quiz_section = CGPQuizSection::find($quiz_section_id);
            $quiz_section->order = $key +1;
            $quiz_section->save();
            $quiz_section->updateData($data);
        }
        foreach ($data['quiz_section_details'] as $key => $quiz_section_detail_id) {
            $quiz_section_detail = CGPQuizSectionDetail::find($quiz_section_detail_id);
            $quiz_section_detail->updateData($data);
        }
        if ($this->validate()) {
            $this->status = 'sufficient';
            $this->generateQuizJob();
        } else {
            $this->status = 'insufficient';
        }

        $this->save();
    }
    public function validateDBHasEnoughQuestions()
    {
        $count = $this->quizSections()->join('cgp_quiz_section_details', 'cgp_quiz_section_details.quiz_section_id', 'cgp_quiz_sections.id')->join('cgp_view_2nd_available_requested_question_difference', 'cgp_view_2nd_available_requested_question_difference.quiz_section_detail_id', 'cgp_quiz_section_details.id')->where('cgp_view_2nd_available_requested_question_difference.difference', '<', 0)->count();

        $unique_available_questions_number =  $this->quizSections()->join('cgp_quiz_section_details', 'cgp_quiz_section_details.quiz_section_id', 'cgp_quiz_sections.id')
        ->join('cgp_view_1st_quiz_section_detail_questions', 'cgp_view_1st_quiz_section_detail_questions.quiz_section_detail_id', 'cgp_quiz_section_details.id')
        ->select(DB::raw('count(DISTINCT cgp_view_1st_quiz_section_detail_questions.question_id) questions_number'))->first();

        $questions_number_requested =  $this->quizSections()->join('cgp_quiz_section_details', 'cgp_quiz_section_details.quiz_section_id', 'cgp_quiz_sections.id')

        ->select(DB::raw('sum(cgp_quiz_section_details.number) number'))->first();

        if ($count || ($unique_available_questions_number->questions_number < $questions_number_requested->number)) {
            return false;
        }
        return true;
    }
    public function validate()
    {
        $has_enough_questions_in_db = $this->validateDBHasEnoughQuestions();
        
        if ($has_enough_questions_in_db) {
            $result = $this->generateQuiz(1, 50);

            if (isset($result[0]->l_generated_quiz_id) || isset($result[0]->exsits)) {
                return true;
            }
        }
        return false;
    }

    public function generateQuiz($validate=0, $number=100, $quiz_limit=20, $with_saving=1, $token=0)
    {
        return DB::select(DB::raw('call cgp_zstored_procedure_generate_quiz("'.$this->id.'", "'.$validate.'","'.$number.'","'.$quiz_limit.'","'.$with_saving.'","'.$token.'")'));
    }

    public function generateQuizJob()
    {
        dispatch(new GenerateQuiz($this));
    }

    public function getRandomGeneratedQuizzes($number)
    {
        return CGPGeneratedQuiz::where('quiz_id', $this->id)
            ->inRandomOrder()
            ->limit($number);
    }
    public function randomQuestions()
    {
        $quiz_sections = $this->quizSections;
        foreach ($quiz_sections as $key => $quiz_section) {
            $section_random_question = $quiz_section->randomQuestion();
        }
    }
}
