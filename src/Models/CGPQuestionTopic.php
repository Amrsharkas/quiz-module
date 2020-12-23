<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class CGPQuestionTopic extends Model
{
    protected $table = 'cgp_question_topics';
    public static function cloneTopics($question_id, $clone_id)
    {
        $topics = self::where('question_id', $question_id) ->get() ;

        foreach ($topics as $topic) {
            $clone = $topic ->replicate() ;
            $clone ->question_id = $clone_id ;
            $clone->original_id = $topic->id;
            $clone ->save() ;
        }
    }
}
