<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class CGPQuestionAnswerFile extends Model
{
    protected $table = 'cgp_question_answer_files';
    public static function cloneQuestionAnswerFiles($question_answer_id, $clone_id)
    {
        $files = self::where('question_answer_id', $question_answer_id)->get() ;

        foreach ($files as $file) {
            $clone = $file ->replicate() ;
            $clone ->question_answer_id = $clone_id ;
            $clone ->file_id = File::cloneFile($file ->file_id, \Str::uuid()) ;
            $clone->original_id = $file->id;
            $clone ->save() ;
        }
    }
}
