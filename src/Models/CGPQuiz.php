<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;
use mennaAbouelsaadat\quizGenerator\Models\QuizSection;
use DB;
use mennaAbouelsaadat\quizGenerator\Jobs\GenerateQuiz;
use Auth;

class CGPQuiz extends Model
{
    protected $table = 'cgp_quizzes';
    public function quizSections()
    {
        return $this->hasMany('mennaAbouelsaadat\quizGenerator\Models\CGPQuizSection', 'quiz_id');
    }
    public function quizSectionsDetails()
    {
        $section_ids = $this->quizSections()->pluck('id')->toArray();
        return CGPQuizSectionDetail::whereIn('id', $section_ids);
    }

    public function generatedQuizzes()
    {
        return $this->hasMany('mennaAbouelsaadat\quizGenerator\Models\CGPGeneratedQuiz', 'quiz_id');
    }
    public function updateData($data, $rollback = 0)
    {
        if ($rollback) {
            $data = json_decode($data);
            $data = (array) $data;
        }
        $sufficient = 0;
        if ($this->status == 'sufficient') {
            $sufficient = 1;
        }

        $criteria_changed = 0;
        if ($this->criteria_effect_quiz != $data['criteria_effect_quiz']) {
            $criteria_changed = 1;
        }

        $this->testing_request = $data;
        $this->save();
        
        $this->criteria_effect_quiz = $data['criteria_effect_quiz'];
        if (isset($data['name'])) {
            $this->name = $data['name'];
        }
        if (isset($data['success_percentage'])) {
            $this->passing_percentage = $data['success_percentage'];
        }
        if (isset($data['number_of_attempts'])) {
            $this->attempts_number = $data['number_of_attempts'];
        }
        if (isset($data['duration'])) {
            $this->duration = $data['duration'];
        }
        
        $this->admin_show = 1;
        $this->save();
        $sections_db_ids = $this->quizSections()->pluck('id')->toArray();
        $sections_ids_should_be_deleted = array_diff($sections_db_ids, $data['quiz_section_id']);
        $sections_should_be_deleted = CGPQuizSection::whereIn('id', $sections_ids_should_be_deleted)->get();
        foreach ($sections_should_be_deleted as $key => $section_should_be_deleted) {
            $section_should_be_deleted->deleteData();
        }
        foreach ($data['quiz_section_id'] as $key => $quiz_section_id) {
            if (!in_array($quiz_section_id, $sections_db_ids)) {
                $quiz_section = CGPQuizSection::withTrashed()->find($quiz_section_id)->restore();
            } else {
                $quiz_section = CGPQuizSection::find($quiz_section_id);
            }
            $quiz_section->order = $key +1;
            $quiz_section->save();
            $quiz_section->updateData($data);
        }
        $sections_db_ids = $this->quizSections()->pluck('id')->toArray();
        $sections_details_db_ids = CGPQuizSectionDetail::whereIn('quiz_section_id', $sections_db_ids)->pluck('id')->toArray();
        $sections_details_ids_should_be_deleted = array_diff($sections_details_db_ids, $data['quiz_section_details']);
        CGPQuizSectionDetail::whereIn('id', $sections_details_ids_should_be_deleted)->delete();
        foreach ($data['quiz_section_details'] as $key => $quiz_section_detail_id) {
            if (!in_array($quiz_section_detail_id, $sections_details_db_ids)) {
                $quiz_section_detail = CGPQuizSectionDetail::withTrashed()->find($quiz_section_detail_id);
            } else {
                $quiz_section_detail = CGPQuizSectionDetail::find($quiz_section_detail_id);
            }
           
            $quiz_section_detail->updateData($data);
        }
        $token = md5(uniqid().$this->id);
        if ($criteria_changed || $rollback) {
            if ($this->validate($token)) {
                $this->status = 'sufficient';
                $this->removeGeneratedQuizzes('old');
                $this->generateQuizJob();
                $this->testing_request = null;
                $this->valid_request = $data;
                $this->save();
            } else {
                $this->status = 'insufficient';
                $this->save();
                if ($sufficient && !$rollback) {
                    return 'This quiz will be insufficient';
                }
            }
        } else {
            $this->testing_request = null;
            $this->valid_request = $data;
            $this->save();
        }

        $this->save();
    }
    public function rollback()
    {
        if ($this->testing_request) {
            if ($this->valid_request) {
                $this->updateData($this->valid_request, $rollback=1);
            }
            $this->testing_request = null;
            $this->save();
        }
    }
    public function removeGeneratedQuizzes($status=null)
    {
        if ($status == 'old') {
            $generated_quizzes = $this->generatedQuizzes()->whereNull('token')->get();
        } else {
            $generated_quizzes = $this->generatedQuizzes;
        }
        
        foreach ($generated_quizzes as $key => $generated_quiz) {
            $generated_quiz->deleteData();
        }

        if ($status == 'old') {
            $this->generatedQuizzes()->whereNotNull('token')->update(['token'=>null]);
        }
    }
    public function validateDBHasEnoughQuestions()
    {
        $count = $this->quizSections()->join('cgp_quiz_section_details', 'cgp_quiz_section_details.quiz_section_id', 'cgp_quiz_sections.id')->join('cgp_view_2nd_available_requested_question_difference', 'cgp_view_2nd_available_requested_question_difference.quiz_section_detail_id', 'cgp_quiz_section_details.id')->where('cgp_view_2nd_available_requested_question_difference.difference', '<', 0)->count();

        $unique_available_questions_number =  $this->quizSections()->join('cgp_quiz_section_details', 'cgp_quiz_section_details.quiz_section_id', 'cgp_quiz_sections.id')
        ->join('cgp_view_1st_quiz_section_detail_questions', 'cgp_view_1st_quiz_section_detail_questions.quiz_section_detail_id', 'cgp_quiz_section_details.id')
        ->select(DB::raw('count(DISTINCT cgp_view_1st_quiz_section_detail_questions.question_id) questions_number'))->first();
        $questions_number_requested =  $this->quizSections()->join('cgp_quiz_section_details', 'cgp_quiz_section_details.quiz_section_id', 'cgp_quiz_sections.id')->whereNull('cgp_quiz_section_details.deleted_at')
        ->select(DB::raw('sum(cgp_quiz_section_details.number) number'))->first();

        if ($count || ($unique_available_questions_number->questions_number < $questions_number_requested->number)) {
            return false;
        }
        return true;
    }
    public function validate($token=null)
    {
        $has_enough_questions_in_db = $this->validateDBHasEnoughQuestions();
        if ($has_enough_questions_in_db) {
            if ($token) {
                $result = $this->generateQuiz($validate=1, $number=50, $quiz_limit=20, $with_saving=0, $token);
            } else {
                $result = $this->generateQuiz(1, 50);
            }
            if (isset($result[0]->l_generated_quiz_id) || isset($result[0]->exsits)) {
                return true;
            }
        }
        return false;
    }

    public function generateQuiz($validate=0, $number=100, $quiz_limit=20, $with_saving=1, $token=0)
    {
        if (Auth::user()) {
            $id = Auth::user()->id;
        }
        else
        {
            $id = 0;
        }
        $db_limit = $this->max_generated_quizzes_number;
        return DB::select(DB::raw('call cgp_zstored_procedure_generate_quiz("'.$this->id.'", "'.$validate.'","'.$number.'","'.$quiz_limit.'","'.$with_saving.'","'.$token.'","'.$db_limit.'","'.$id.'")'));
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

    public function deleteData()
    {
        $sections = $this->quizSections;
        foreach ($sections as $key => $section) {
            $section->deleteData();
        }
        $generated_quizzes = $this->generatedQuizzes;
        foreach ($generated_quizzes as $key => $generated_quiz) {
            $generated_quiz->deleteData();
        }
        $this->delete();
    }
}
