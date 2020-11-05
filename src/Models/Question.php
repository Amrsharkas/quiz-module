<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table='questions';
    protected $fillable=[ 'content', 'weight', 'file_id','question_order','question_choices_type','admin_show'];

    public function quizGenerator()
    {
        return $this->belongsTo('App\QuizGenerator', 'quiz_id');
    }

    public function question_answers()
    {
        return $this->hasMany('App\QuestionAnswer', 'question_id', 'id');
    }
    public function getAnswers()
    {
        return $this->hasMany('App\QuestionChoice', 'question_id')->where('admin_show', 1);
    }

    public function getRightAnswers()
    {
        return $this->hasMany('App\QuestionChoice', 'question_id')->where('correct', 1);
    }
    public function files()
    {
        return $this->belongsTo('App\File', 'file_id');
    }
}
