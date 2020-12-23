<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class CGPQuestionFile extends Model
{
    protected $table = 'cgp_question_files';
    public static function cloneQuestionFiles($question_id, $clone_id)
    {
        $files = self::where('question_id', $question_id)->get();
        foreach ($files as $file) {
            $clone = $file ->replicate() ;
            $clone ->question_id = $clone_id ;
            $clone ->file_id = File::cloneFile($file ->file_id, \Str::uuid()) ;
            $clone->original_id = $file->id;
            $clone ->save() ;
        }
    }
}
