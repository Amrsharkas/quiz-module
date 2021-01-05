<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class CGPQuizSectionTopic extends Model
{
    protected $table = 'cgp_quiz_section_topics';

    public function topic()
    {
        return $this->belongsTo('mennaAbouelsaadat\quizGenerator\Models\CGPTopic', 'topic_id');
    }
    public static function create($section_id, $topic_id)
    {
        $quiz_section_topic = new self();
        $quiz_section_topic->topic_id = $topic_id;
        $quiz_section_topic->quiz_section_id = $section_id;
        $quiz_section_topic->save();
    }
}
