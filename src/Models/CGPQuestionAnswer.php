<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class CGPQuestionAnswer extends Model
{
    protected $table = "question_answers";
    protected $fillable = ['quiz_id', 'question_id','question_content','question_file_id', 'entry_id', 'degree', 'choice_ids', 'created_at', 'updated_at'];

    public function get_question()
    {
        return $this->belongsTo('App\Question', 'question_id');
    }
    public function quizEntry()
    {
        return $this->belongsTo('App\QuizEntry', 'entry_id');
    }

    public function questionCorrectAnswer()
    {
        return $this->hasMany('App\QuestionCorrectAnswer', 'question_answer_id');
    }

    public function questionFreelancerAnswer()
    {
        return $this->hasMany('App\QuestionFreelancerAnswer', 'question_answer_id');
    }
    public function files()
    {
        return $this->belongsTo('App\File', 'question_file_id');
    }
}
