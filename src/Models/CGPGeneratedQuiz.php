<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class CGPGeneratedQuiz extends Model
{
    protected $table = 'cgp_generated_quizzes';
    public function questions()
    {
        return $this->hasMany('mennaAbouelsaadat\quizGenerator\Models\GeneratedQuizQuestion');
    }

    public function deleteData()
    {
        $this->questions()->delete();
        $this->delete();
    }
}
