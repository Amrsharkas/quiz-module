<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class CGPTopic extends Model
{
    protected $table = 'cgp_topics';
    public static function init($data)
	{
		$topic = new self ; 
		$topic ->name = $data ['text'] ; 
		$topic ->admin_show = 0 ; 
		$topic ->save() ; 

		return $topic ; 
	}
}
