<?php

namespace mennaAbouelsaadat\quizGenerator\Tests;

use mennaAbouelsaadat\quizGenerator\QuizGeneratorServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // $this->withFactories(__DIR__.'/../database/factories');
        // additional setup
    }

    protected function getPackageProviders($app)
    {
        return [
        QuizGeneratorServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // import the CreatePostsTable class from the migration
        include_once __DIR__ .'\..\..\database\migrations\create_quizzes_table.php.stub';

        // run the up() method of that migration class
        (new \CreateQuizzesTable)->up();
    }
}
