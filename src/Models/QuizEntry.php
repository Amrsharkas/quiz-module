<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class QuizEntry extends Model
{
    protected $table='quiz_enteries';
    protected $fillable=['quiz_id','user_id','status','grade','quiz_status','passed','qualified_by', 'canceled_by',
        'start_time','end_time','max_end_time','created_at','updated_at','project_id'];
    public function quiz()
    {
        return $this->belongsTo('App\QuizGenerator', 'quiz_id');
    }

    public function canceledBy()
    {
        return $this->belongsTo('App\User', 'canceled_by');
    }


    public function questionAnswer()
    {
        return $this->hasMany('App\QuestionAnswer', 'entry_id');
    }

    public function quizTotal()
    {
        $quiz_total_grade = Question::
        where('question_answers.quiz_id', $this->quiz_id)
        ->join('question_answers', 'question_answers.question_id', '=', 'questions.id')
        ->where('question_answers.entry_id', $this->id)
        ->sum('weight')
        
        ;
        return $quiz_total_grade;
    }
}
