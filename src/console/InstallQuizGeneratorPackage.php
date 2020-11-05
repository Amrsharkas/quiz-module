<?php

namespace mennaAbouelsaadat\quizGenerator\Console;

use Illuminate\Console\Command;

class InstallQuizGeneratorPackage extends Command
{
    protected $signature = 'quizGeneratorPackage:install';

    protected $description = 'Install the quizGeneratorPackage';

    public function handle()
    {
        $this->info('Installing quizGenerator...');

        $this->info('Publishing configuration...');

        $this->call('vendor:publish', [
            '--provider' => "mennaAbouelsaadat\quizGenerator\QuizGeneratorServiceProvider",
            '--tag' => "config"
        ]);

        $this->info('Installed quizGenerator');
    }
}
