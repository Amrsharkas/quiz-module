<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class CGPTextCorrectAnswer extends Model
{
    //

    protected $fillable = [
        'question_answer_id',
        'text'
    ] ;

    protected $table = 'cgp_text_correct_answers';
    public function answer()
    {
        return $this ->belongsTo('mennaAbouelsaadat\quizGenerator\Models\CGPQuestionAnswer', 'question_answer_id') ;
    }
    public static function cloneTextCorrectAnswers($answer_id, $clone_answer_id)
    {
        $text_correct_answers = self::where('question_answer_id')->where('admin_show', 1) ->get() ;
        foreach ($text_correct_answers as $text_correct_answer) {
            $clone = $text_correct_answer ->replicate() ;
            $clone->original_id = $text_correct_answer->id;
            $clone ->save() ;
        }
    }
}
