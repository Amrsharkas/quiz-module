<?php

namespace mennaAbouelsaadat\quizGenerator;

use Illuminate\Support\ServiceProvider;

use mennaAbouelsaadat\quizGenerator\Console\InstallQuizGeneratorPackage;

class QuizGeneratorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'\..\config\config.php', 'quizGeneratorPackage');
    }

    public function boot()
    {
        // $this->app->make('Illuminate\Database\Eloquent\Factory')
        // ->load(__DIR__.'/../database/factories');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'quizGeneratorPackage');
        if ($this->app->runningInConsole()) {
            $this->commands([
            InstallQuizGeneratorPackage::class,
            ]);


            $this->publishes([
                 __DIR__.'/../resources/views' => resource_path('views/vendor/quizGeneratorPackage'),
                ], 'views');
            
            // $this->publishes([
            // __DIR__.'\..\config\config.php' => config_path('quizGeneratorPackage.php'),
            // ], 'config');

            if (! class_exists('CreateCGPView2ndAvailableRequestedQuestionDifference')) {
                $this->publishes([
                __DIR__ . '/../database/migrations/create_CGP_view_2nd_available_requested_question_difference.php.stub' => database_path('migrations'. DIRECTORY_SEPARATOR . date('Y_m_d_His', time()) . '_create_CGP_view_2nd_available_requested_question_difference.php'),
                // you can add any number of migrations here
                ], 'migrations');
            }

            if (! class_exists('CGPZStoredProcedureGenerateQuiz')) {
                $this->publishes([
                __DIR__ . '/../database/migrations/create_CGP_zstored_procedure_generate_quiz.php.stub' => database_path('migrations'. DIRECTORY_SEPARATOR. date('Y_m_d_His', time()) . '_create_CGP_zstored_procedure_generate_quiz.php'),
                // you can add any number of migrations here
                ], 'migrations');
            }

            if (! class_exists('CreateCGPGeneratedQuizQuestionsTable')) {
                $this->publishes([
                __DIR__ . '/../database/migrations/create_CGP_generated_quiz_questions_table.php.stub' => database_path('migrations'. DIRECTORY_SEPARATOR. date('Y_m_d_His', time()) . '_create_CGP_generated_quiz_questions_table.php'),
                // you can add any number of migrations here
                ], 'migrations');
            }

            if (! class_exists('CreateCGPGeneratedQuizzesTable')) {
                $this->publishes([
                __DIR__ . '/../database/migrations/create_CGP_generated_quizzes_table.php.stub' => database_path('migrations'. DIRECTORY_SEPARATOR. date('Y_m_d_His', time()) . '_create_CGP_generated_quizzes_table.php'),
                // you can add any number of migrations here
                ], 'migrations');
            }

            if (! class_exists('CreateCGPQuestionAnswerFilesTable')) {
                $this->publishes([
                __DIR__ . '/../database/migrations/create_CGP_question_answer_files_table.php.stub' => database_path('migrations'. DIRECTORY_SEPARATOR. date('Y_m_d_His', time()) . '_create_CGP_question_answer_files_table.php'),
                // you can add any number of migrations here
                ], 'migrations');
            }

            if (! class_exists('CreateCGPQuestionAnswerTypesTable')) {
                $this->publishes([
                __DIR__ . '/../database/migrations/create_CGP_question_answer_types_table.php.stub' => database_path('migrations'. DIRECTORY_SEPARATOR. date('Y_m_d_His', time()) . '_create_CGP_question_answer_types_table.php'),
                // you can add any number of migrations here
                ], 'migrations');
            }

            if (! class_exists('CreateCGPQuestionAnswersTable')) {
                $this->publishes([
                __DIR__ . '/../database/migrations/create_CGP_question_answers_table.php.stub' => database_path('migrations'. DIRECTORY_SEPARATOR. date('Y_m_d_His', time()) . '_create_CGP_question_answers_table.php'),
                // you can add any number of migrations here
                ], 'migrations');
            }

            if (! class_exists('CreateCGPQuestionFilesTable')) {
                $this->publishes([
                __DIR__ . '/../database/migrations/create_CGP_question_files_table.php.stub' => database_path('migrations'. DIRECTORY_SEPARATOR. date('Y_m_d_His', time()) . '_create_CGP_question_files_table.php'),
                // you can add any number of migrations here
                ], 'migrations');
            }

            if (! class_exists('CreateCGPQuestionTopicsTable')) {
                $this->publishes([
                __DIR__ . '/../database/migrations/create_CGP_question_topics_table.php.stub' => database_path('migrations'. DIRECTORY_SEPARATOR. date('Y_m_d_His', time()) . '_create_CGP_question_topics_table.php'),
                // you can add any number of migrations here
                ], 'migrations');
            }

            if (! class_exists('CreateCGPQuestionTypesTable')) {
                $this->publishes([
                __DIR__ . '/../database/migrations/create_CGP_question_types_table.php.stub' => database_path('migrations'. DIRECTORY_SEPARATOR. date('Y_m_d_His', time()) . '_create_CGP_question_types_table.php'),
                // you can add any number of migrations here
                ], 'migrations');
            }

            if (! class_exists('CreateCGPQuestionsTable')) {
                $this->publishes([
                __DIR__ . '/../database/migrations/create_CGP_questions_table.php.stub' => database_path('migrations'. DIRECTORY_SEPARATOR. date('Y_m_d_His', time()) . '_create_CGP_questions_table.php'),
                // you can add any number of migrations here
                ], 'migrations');
            }

            if (! class_exists('CreateCGPView1stQuizSectionDetailQuestions')) {
                $this->publishes([
                __DIR__ . '/../database/migrations/create_CGP_view_1st_quiz_section_detail_questions.php.stub' => database_path('migrations'. DIRECTORY_SEPARATOR. date('Y_m_d_His', time()) . '_create_CGP_view_1st_quiz_section_detail_questions.php'),
                // you can add any number of migrations here
                ], 'migrations');
            }

            if (! class_exists('CreateCGPQuizSectionDetailsTable')) {
                $this->publishes([
                __DIR__ . '/../database/migrations/create_CGP_quiz_section_details_table.php.stub' => database_path('migrations'. DIRECTORY_SEPARATOR. date('Y_m_d_His', time()) . '_create_CGP_quiz_section_details_table.php'),
                // you can add any number of migrations here
                ], 'migrations');
            }

            if (! class_exists('CreateCGPQuizSectionTopicsTable')) {
                $this->publishes([
                __DIR__ . '/../database/migrations/create_CGP_quiz_section_topics_table.php.stub' => database_path('migrations'. DIRECTORY_SEPARATOR. date('Y_m_d_His', time()) . '_create_CGP_quiz_section_topics_table.php'),
                // you can add any number of migrations here
                ], 'migrations');
            }

            if (! class_exists('CreateCGPQuizSectionsTable')) {
                $this->publishes([
                __DIR__ . '/../database/migrations/create_CGP_quiz_sections_table.php.stub' => database_path('migrations'. DIRECTORY_SEPARATOR. date('Y_m_d_His', time()) . '_create_CGP_quiz_sections_table.php'),
                // you can add any number of migrations here
                ], 'migrations');
            }

            if (! class_exists('CreateCGPQuizzesTable')) {
                $this->publishes([
                __DIR__ . '/../database/migrations/create_CGP_quizzes_table.php.stub' => database_path('migrations'. DIRECTORY_SEPARATOR. date('Y_m_d_His', time()) . '_create_CGP_quizzes_table.php'),
                // you can add any number of migrations here
                ], 'migrations');
            }

            if (! class_exists('CreateCGPTextCorrectAnswersTable')) {
                $this->publishes([
                __DIR__ . '/../database/migrations/create_CGP_text_correct_answers_table.php.stub' => database_path('migrations'. DIRECTORY_SEPARATOR. date('Y_m_d_His', time()) . '_create_CGP_text_correct_answers_table.php'),
                // you can add any number of migrations here
                ], 'migrations');
            }

            if (! class_exists('CreateCGPTopicsTable')) {
                $this->publishes([
                __DIR__ . '/../database/migrations/create_CGP_topics_table.php.stub' => database_path('migrations'. DIRECTORY_SEPARATOR. date('Y_m_d_His', time()) . '_create_CGP_topics_table.php'),
                // you can add any number of migrations here
                ], 'migrations');
            }
        }
    }
}
