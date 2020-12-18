<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCGPQuizSectionDetailQuestionsView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP table IF EXISTS CGP_quiz_section_detail_questions');
        DB::unprepared('CREATE view quiz_section_detail_questions as SELECT distinct questions.id as question_id , quiz_section_details.id as quiz_section_detail_id FROM questions JOIN quiz_section_details ON questions.question_type_id = quiz_section_details.question_type_id AND questions.difficulty = quiz_section_details.difficulty_id JOIN question_topics ON question_topics.question_id = questions.id JOIN quiz_section_topics ON quiz_section_topics.quiz_section_id = quiz_section_details.quiz_section_id WHERE question_topics.topic_id = quiz_section_topics.topic_id and questions.admin_show = 1');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP view CGP_quiz_section_detail_questions');
    }
}
