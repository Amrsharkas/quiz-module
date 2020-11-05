<?php

namespace mennaAbouelsaadat\quizGenerator\Tests\Unit;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use mennaAbouelsaadat\quizGenerator\Tests\TestCase;

class installQuizGeneratorTest extends TestCase
{
    /** @test */
    public function the_install_command_copies_the_configuration()
    {
        // make sure we're starting from a clean state
        if (File::exists(config_path('quizGeneratorPackage.php'))) {
            unlink(config_path('quizGeneratorPackage.php'));
        }

        $this->assertFalse(File::exists(config_path('quizGeneratorPackage.php')));

        Artisan::call('quizGeneratorPackage:install');
        // dd(File::exists(config_path('quizGeneratorPackage.php')));
        $this->assertTrue(File::exists(config_path('quizGeneratorPackage.php')));
    }
}
