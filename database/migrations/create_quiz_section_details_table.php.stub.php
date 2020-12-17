<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizSectionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_section_details', function (Blueprint $table) {
            $table->id();
            $table->integer('quiz_section_id')->nullable();
            $table->integer('number')->nullable();
            $table->integer('question_type_id')->nullable();
            $table->integer('difficulty_id')->nullable();
            $table->integer('admin_show')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quiz_section_details');
    }
}
