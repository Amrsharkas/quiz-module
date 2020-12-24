<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class CGPQuestionAnswer extends Model
{
    protected $table = "cgp_question_answers";
    protected $fillable = ['quiz_id', 'question_id','question_content','question_file_id', 'entry_id', 'degree', 'choice_ids', 'created_at', 'updated_at', 'question_answer_type_id'];

    public function get_question()
    {
        return $this->belongsTo(' mennaAbouelsaadat\quizGenerator\Models\CGPQuestion', 'question_id');
    }

    public function files()
    {
        return $this->belongsToMany('App\File', 'cgp_question_answer_files', 'question_answer_id')->where('cgp_question_answer_files.admin_show', 1);
    }

    public function answerFiles()
    {
        return $this->belongsTo('mennaAbouelsaadat\quizGenerator\Models\CGPQuestionAnswerFile', 'question_answer_id', 'question_answer_id');
    }

    public static function init($data)
    {
        $answer = new CGPQuestionAnswer ;
        $answer ->question_id = $data ['question_id'] ;
        $answer ->answer_text = isset($data ['answer_text']) ? $data ['answer_text'] : null ;
        $answer ->system_assesst = isset($data ['system_assesst']) ? $data ['system_assesst'] : 1 ;

        if ($data ['question_type'] == 'MCQ') {
            $answer->question_answer_type_id = 1 ;
        } elseif ($data ['question_type'] == 'Text') {
            $answer->question_answer_type_id = 2 ;
        }

        $answer ->save() ;

        return $answer ;
    }

    public static function cloneAnswers($question_id, $clone_id)
    {
        $answers = self::where('question_id', $question_id)->get() ;
        foreach ($answers as $answer) {
            $clone = $answer ->replicate() ;
            $clone ->question_id = $clone_id ;
            $clone->original_id = $answer->id;
            $clone ->save() ;
            CGPTextCorrectAnswer::cloneTextCorrectAnswers($answer ->id, $clone ->id) ;
            CGPQuestionAnswerFile::cloneQuestionAnswerFiles($answer ->id, $clone ->id) ;
        }
    }

    public static function initTextCorrectAnswerQuestionAnswer($question_id)
    {
        $text_input_answer_type_id = CGPQuestionAnswerType::where('type', 'text input') ->first() ->id ;
        
        self::create([
            'question_id' => $question_id,
            'question_answer_type_id' => $text_input_answer_type_id ,
        ]);
    }

    public function deleteData()
    {
        $this->answerFiles()->delete();
        $this->textCorrectAnswers()->delete();
        $this->delete();
    }
}
