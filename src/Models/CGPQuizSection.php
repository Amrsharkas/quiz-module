<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class CGPQuizSection extends Model
{
    public function sectionDetails()
    {
        return $this->hasMany('App\QuizSectionDetail');
    }

    public function sectionTopics()
    {
        return $this->hasMany('App\QuizSectionTopic');
    }

    public function quiz()
    {
        return $this->belongsTo('App\Quiz');
    }

    public function updateData($input)
    {
        // save topics
        foreach ($input['topics_'.$this->id] as $key => $topic_id) {
            QuizSectionTopic::create($this->id, $topic_id);
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
