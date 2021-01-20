<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class CGPGeneratedQuiz extends Model
{
    protected $table = 'cgp_generated_quizzes';
    public function questions()
    {
        return $this->hasMany('mennaAbouelsaadat\quizGenerator\Models\CGPGeneratedQuizQuestion', 'generated_quiz_id');
    }

    public function sections()
    {
        $sections_id = $this->questions()->pluck('section_id');
        return CGPQuizSection::whereIn('id',$sections_id)->get();
    }

    public function quiz()
    {
        return $this->hasMany('mennaAbouelsaadat\quizGenerator\Models\CGPQuiz', 'quiz_id');
    }

    public function deleteData()
    {
        $this->questions()->delete();
        $this->delete();
    }
}
