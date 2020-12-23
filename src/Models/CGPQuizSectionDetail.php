<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class CGPQuizSectionDetail extends Model
{
    protected $table = 'cgp_quiz_section_details'
    public function questions()
    {
        return $this->hasMany('App\CGPQuizSectionDetailQuestion');
    }

    public function section()
    {
        return $this->belongsTo('App\CGPQuizSection', 'quiz_section_id');
    }

    public function updateData($data)
    {
        $this->number = $data['number_of_questions_'.$this->id];
        $this->difficulty_id = $data['difficulty_'.$this->id];
        $this->question_type_id = $data['question_type_'.$this->id];
        $this->save();
    }
}
