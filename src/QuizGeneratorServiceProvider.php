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
        if ($this->app->runningInConsole()) {
            $this->commands([
            InstallQuizGeneratorPackage::class,
            ]);

            $this->publishes([
            __DIR__.'\..\config\config.php' => config_path('quizGeneratorPackage.php'),
            ], 'config');

            if (! class_exists('CreateUsersTable')) {
                // dd(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);
                $this->publishes([
                __DIR__ . DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR.'create_users_table.php.stub' => database_path('migrations'. DIRECTORY_SEPARATOR . date('Y_m_d_His', time()) . '_create_users_table.php'),
                // you can add any number of migrations here
                ], 'migrations');
            }

            if (! class_exists('CreateQuizzesTable')) {
                $this->publishes([
                __DIR__ . '/../database/migrations/create_quizzes_table.php.stub' => database_path('migrations'. DIRECTORY_SEPARATOR . date('Y_m_d_His', time()) . '_create_quizzes_table.php'),
                // you can add any number of migrations here
                ], 'migrations');
            }

            if (! class_exists('CreateQuestionsTable')) {
                $this->publishes([
                __DIR__ . '/../database/migrations/create_questions_table.php.stub' => database_path('migrations'. DIRECTORY_SEPARATOR. date('Y_m_d_His', time()) . '_create_questions_table.php'),
                // you can add any number of migrations here
                ], 'migrations');
            }
        }
    }
}
