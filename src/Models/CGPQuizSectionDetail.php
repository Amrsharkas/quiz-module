<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CGPQuizSectionDetail extends Model
{
    use SoftDeletes;
    protected $table = 'cgp_quiz_section_details';
    public function questions()
    {
        return $this->hasMany('mennaAbouelsaadat\quizGenerator\Models\CGPQuizSectionDetailQuestion');
    }

    public function section()
    {
        return $this->belongsTo('mennaAbouelsaadat\quizGenerator\Models\CGPQuizSection', 'quiz_section_id');
    }

    public function updateData($data)
    {
        $this->number = $data['number_of_questions_'.$this->id];
        $this->difficulty_id = $data['difficulty_'.$this->id];
        $this->question_type_id = $data['question_type_'.$this->id];
        $this->admin_show = 1;
        $this->save();
    }
}
