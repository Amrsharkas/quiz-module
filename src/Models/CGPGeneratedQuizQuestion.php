<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class CGPGeneratedQuizQuestion extends Model
{
    protected $table = 'cgp_generated_quiz_questions';
    
    public function question()
    {
        return $this ->belongsTo('mennaAbouelsaadat\quizGenerator\Models\CGPQuestion', 'question_id') ;
    }
}
