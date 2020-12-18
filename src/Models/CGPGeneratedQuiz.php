<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class CGPGeneratedQuiz extends Model
{
    public function questions()
    {
        return $this->hasMany('App\GeneratedQuizQuestion');
    }
}
