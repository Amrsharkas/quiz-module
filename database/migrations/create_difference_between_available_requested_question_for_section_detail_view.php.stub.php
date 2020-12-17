<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DifferenceBetweenAvailableRequestedQuestionForSectionDetialView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP table IF EXISTS available_requested_question_difference');
        DB::unprepared('CREATE view available_requested_question_difference as SELECT count(quiz_section_detail_questions.question_id) - quiz_section_details.number as difference, quiz_section_detail_questions.quiz_section_detail_id from quiz_section_details join quiz_section_detail_questions on quiz_section_details.id = quiz_section_detail_questions.quiz_section_detail_id group by quiz_section_detail_questions.quiz_section_detail_id');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP view available_requested_question_difference');
    }
}
