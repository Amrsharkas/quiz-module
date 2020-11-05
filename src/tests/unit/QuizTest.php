<?php

namespace mennaAbouelsaadat\quizGenerator\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use mennaAbouelsaadat\quizGenerator\Tests\TestCase;
use mennaAbouelsaadat\quizGenerator\Models\QuizEntry;

class QuizTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function quizEntryTitle()
    {
        $quiz = factory(QuizEntry::class)->create(['title' => 'Fake Title']);
        $this->assertEquals('Fake Title', $quiz->title);
    }
}
