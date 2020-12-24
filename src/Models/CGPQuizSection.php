<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class CGPQuizSection extends Model
{
    protected $table = 'cgp_quiz_sections';
    public function sectionDetails()
    {
        return $this->hasMany('mennaAbouelsaadat\quizGenerator\Models\CGPQuizSectionDetail', 'quiz_section_id');
    }

    public function sectionTopics()
    {
        return $this->hasMany('mennaAbouelsaadat\quizGenerator\Models\CGPQuizSectionTopic', 'quiz_section_id');
    }

    public function quiz()
    {
        return $this->belongsTo('mennaAbouelsaadat\quizGenerator\Models\CGPQuiz', 'quiz_id');
    }

    public function updateData($input)
    {
        // save topics
        foreach ($input['topics_'.$this->id] as $key => $topic_id) {
            CGPQuizSectionTopic::create($this->id, $topic_id);
        }
    }

    public function validateDBHasEnoughQuestions()
    {
        $section_details = $this->sectionDetails;
        foreach ($section_details as $key => $section_detail) {
            $db_questions_count = $section_detail->questions()->count();
            if ($db_questions_count < $section_detail->number) {
                return false;
            }
        }
        return true;
    }

    public function randomQuestion()
    {
        $section_details = $this->sectionDetails;
        foreach ($section_details as $key => $section_detail) {
        }
    }
}
