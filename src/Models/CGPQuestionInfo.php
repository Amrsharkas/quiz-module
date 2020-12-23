<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class CGPQuestionInfo extends Model
{
    //
    public $timestamps = false ;
    protected $fillable = ['question_id', 'info_id']  ;
    protected $table = 'cqp_question_infos';
    public static function cloneQuestionInfos($question_id, $clone_id)
    {
        $infos = self::where('question_id', $question_id) ->get() ;
        foreach ($infos as $key => $info) {
            $clone = $info->replicate() ;
            $clone ->question_id = $clone_id ;
            $clone ->save() ;
        }
    }
}
