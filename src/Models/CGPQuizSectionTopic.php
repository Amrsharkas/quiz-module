<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class CGPQuizSectionTopic extends Model
{
    public static function create($section_id, $topic_id)
    {
        $quiz_section_topic = new self();
        $quiz_section_topic->topic_id = $topic_id;
        $quiz_section_topic->quiz_section_id = $section_id;
        $quiz_section_topic->save();
    }
}
