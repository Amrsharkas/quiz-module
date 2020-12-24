<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class CGPQuestion extends Model
{
    protected $table='cgp_questions';
    
    public function files()
    {
        return $this->belongsToMany('App\File', 'question_files', 'question_id');
    }
    public function questionFiles()
    {
        return $this->belongsTo('mennaAbouelsaadat\quizGenerator\Models\CGPQuestionFile', 'question_id');
    }

    public function questionType()
    {
        return $this->belongsTo('mennaAbouelsaadat\quizGenerator\Models\CGPQuestionType', 'question_type_id');
    }

    public function difficulty()
    {
        return $this ->belongsTo('mennaAbouelsaadat\quizGenerator\Models\Difficulty', 'difficulty') ;
    }
    public function questionInfos()
    {
        return $this->hasMany('mennaAbouelsaadat\quizGenerator\Models\CGPQuestionInfo', 'question_id');
    }
    public function allowTextInput()
    {
        $has_text_answer = $this->questionInfos()->where('name', 'text_input')->first();
        if ($has_text_answer) {
            return true;
        }
        return false;
    }
    public function allowMultipleAnswer()
    {
        $has_multiple_answer = $this->questionInfos()->where('name', 'multiple_answers')->first();
        if ($has_multiple_answer) {
            return true;
        }
        return false;
    }
    public function answers()
    {
        return $this->hasMany('mennaAbouelsaadat\quizGenerator\Models\CGPQuestionAnswer', 'question_id');
    }

    public function textAnswers()
    {
        $text_input_answer_id = CGPQuestionAnswerType::where('type', 'text input')->first()->id;
        return $this->answers()->where('question_answer_type_id', $text_input_answer_id);
    }
    public function choiceAnswers()
    {
        $question_input_answer_id = CGPQuestionAnswerType::where('type', 'question input')->first()->id;
        return $this->answers()->where('question_answer_type_id', $question_input_answer_id);
    }
    public function topics()
    {
        return $this ->belongsToMany('mennaAbouelsaadat\quizGenerator\Models\CGPTopic', 'cgp_question_topics', 'question_id', 'topic_id') ;
    }

    public function questionTopics()
    {
        return $this->hasMany('mennaAbouelsaadat\quizGenerator\Models\CGPQuestionTopic', 'question_id');
    }

    public function generatedQuizzes()
    {
        $ids = CGPGeneratedQuizQuestion::where('question_id', $this->id)->pluck('generated_quiz_id')->toArray();
        return CGPGeneratedQuiz::whereIN('id', $ids);
    }

    public function quizSectionDetails()
    {
        return CGPQuizSectionDetailQuestion::where('question_id', $this->id);
        ;
    }

    public function quizSections()
    {
        $details_id = $this->quizSectionDetails()->pluck('quiz_section_detail_id')->toArray();
        $sections_id = CGPQuizSectionDetail::whereIn('id', $details_id)->pluck('quiz_section_id')->toArray();
        return CGPQuizSection::whereIn('id', $sections_id);
    }

    public function quizTemplates()
    {
        $quizzes_id  = $this->quizSections()->pluck('quiz_id')->toArray();
        return CGPQuiz::whereIn('id', $quizzes_id);
    }

    public function questionsIdThatShouldBeSuspended()
    {
        $quizzes_id = $this->quizTemplates()->pluck('id')->toArray();
        $sections_id = CGPQuizSection::whereIn('quiz_id', $quizzes_id)->pluck('id')->toArray();
        $details_id = CGPQuizSectionDetail::whereIn('id', $sections_id)->pluck('id')->toArray();
        return CGPQuizSectionDetailQuestion::whereIn('quiz_section_detail_id', $details_id)->pluck('question_id')->toArray();
    }

    public function validateSufficientQuizzes($token=null)
    {
        $insufficient_quizzes = [];
        $quiz_templates_id = db::select("SELECT DISTINCT quiz_id FROM cgp_generated_quizzes t WHERE t.quiz_id IN  (SELECT DISTINCT cgp_quizzes.id FROM cgp_generated_quiz_questions join cgp_generated_quizzes ON  cgp_generated_quizzes.id = cgp_generated_quiz_questions.generated_quiz_id join cgp_quizzes ON cgp_quizzes.id = cgp_generated_quizzes.quiz_id where cgp_quizzes.status = 'sufficient' AND  cgp_generated_quiz_questions.question_id  =".$this->id.") GROUP BY t.quiz_id
            HAVING  (COUNT(t.id) - (SELECT COUNT(s.id) FROM cgp_generated_quizzes s where s.id in (SELECT DISTINCT cgp_generated_quizzes.id FROM cgp_generated_quiz_questions join cgp_generated_quizzes ON  cgp_generated_quizzes.id = cgp_generated_quiz_questions.generated_quiz_id join cgp_quizzes ON cgp_quizzes.id = cgp_generated_quizzes.quiz_id where cgp_quizzes.status = 'sufficient' AND  cgp_generated_quiz_questions.question_id  =".$this->id.") AND s.quiz_id = t.quiz_id)) = 0");
        $quizzes_names = '';
        foreach ($quiz_templates_id as $key => $quiz_template_id) {
            $quiz = CGPQuiz::find($quiz_template_id->quiz_id);
            $response = $quiz->generateQuiz($validate=1, $number=50, $quiz_limit=0, $with_saving=0);
            if (isset($response[0]->false)) {
                array_push($insufficient_quizzes, $quiz);
                $quizzes_names .= $quiz->name.', ';
            }
        }
        $data['quizzes_names'] = $quizzes_names;
        $data['quizzes_objects'] = $insufficient_quizzes;
        return $data;
    }

    public function validateInsufficientQuizzes($quiz_details_id=null)
    {
        if (!$quiz_details_id) {
            $quiz_details_id = $this->quizSectionDetails()->pluck('quiz_section_detail_id')->toArray();
        }
        $quizzes_names = '';
        $validated_quizzez_id = [];
        $quizzes_converted_sufficient =[];
        foreach ($quiz_details_id as $key => $detail_id) {
            $quiz_section_detail = CGPQuizSectionDetail::find($detail_id);
            $quiz = $quiz_section_detail->section->quiz;
            if (!in_array($quiz->id, $validated_quizzez_id)) {
                $sufficient_quiz = $quiz->validate();
                array_push($validated_quizzez_id, $quiz->id);
                if ($sufficient_quiz) {
                    $quiz->status = 'sufficient';
                    $quiz->save();
                    $quiz->generateQuizJob();
                    array_push($quizzes_converted_sufficient, $quiz);
                    $quizzes_names .= $quiz->name.', ';
                }
            }
        }
        $data['quizzes_names'] = $quizzes_names;
        $data['quizzes_objects'] = $quizzes_converted_sufficient;
        return  $data;
    }

    public function continueEditting()
    {
        if ($this->clonedOne) {
            $generated_quizzes = $this->generatedQuizzes()->get();
            foreach ($generated_quizzes as $key => $generated_quiz) {
                $generated_quiz->deleteData();
            }
            $this->removeSuspendedToken();
            $quizzes = $this->clonedOne->quizTemplates()->get();
            foreach ($quizzes as $key => $quiz) {
                $quiz->generateQuizJob();
            }
        }
    }

    public function rollback()
    {
        foreach ($this->generatedQuizzes() as $key => $quiz) {
            $quiz->deleteData();
        }
        $this->archived = null;
        $this->save();
        $this->deleteClonedOne();
        $this->removeSuspendedToken();
    }

    public function originalOne()
    {
        return $this->belongsTo('mennaAbouelsaadat\quizGenerator\Models\CGPQuestion', 'original_id');
    }

    public function replaceThisWithOriginalOneInGeneratedQuizzes()
    {
        $question = $this->originalOne;
        if ($question) {
            $generated_quizzes = $question->generatedQuizzes();
            foreach ($generated_quizzes as $key => $generated_quiz) {
                $generated_quiz->questions()->where('question_id', $question->id)->update(['question_id',$this->id]);
            }
        }
    }

    public function deleteClonedOne()
    {
        $question =  $this->clonedOne;
        $question->deleteData();
    }

    public function deleteData()
    {
        foreach ($this->answers as $key => $answer) {
            $answer->deleteData();
        }

        $this->topics()->delete();
        $this->questionInfos()->delete();
        $this->questionFiles()->delete();
        $this->delete();
    }

    public function clonedOne()
    {
        return $this->hasOne('mennaAbouelsaadat\quizGenerator\Models\CGPQuestion', 'original_id');
    }

    public function removeSuspendedToken()
    {
        $token = $this->suspended_token;
        $suspended_questions_ids = CGPQuestion::where('suspended_token', $token)->pluck('id')->toArray();
        CGPQuestion::whereIn('id', $suspended_questions_ids)->update(['suspended_token'=>null]);
    }

    public function removeOldAnswers($ids, $cloned)
    {
        if ($cloned) {
            CGPQuestionAnswer::whereNotIn('original_id', $ids)
            ->where('question_id', $this->id)
            ->where('original_id', '!=', $this->textAnswers()->first()?$this->textAnswers()->first()->id:'')
            ->delete() ;
        } else {
            CGPQuestionAnswer::whereNotIn('id', $ids)
            ->where('question_id', $this->id)
            ->where('id', '!=', $this->textAnswers()->first()?$this->textAnswers()->first()->id:'')
            ->delete() ;
        }
    }

    public static function init()
    {
        $question = new self();
        $question->question_type_id = CGPQuestionType::where('type', 'MCQ')->first()->id;
        $question->save();
        $data['question_id'] = $question->id;
        $data['question_type'] = 'MCQ';
        CGPQuestionAnswer::init($data);
        CGPQuestionAnswer::init($data);
        return $question;
    }

    public function updateData($data, $cloned=0)
    {
        $validate_quizzes = 1;
        $new_question = 0;
        if ($this->suspended_token) {
            return false;
        }

        $token = md5(uniqid().$this->id);
        CGPQuestion::whereIn('id', $this->questionsIdThatShouldBeSuspended())->update(['suspended_token'=>$token]);
        if ($this->admin_show && ($this->criteria_effect_quiz == $data['criteria_effect_quiz'])) {
            $validate_quizzes = 0;
        }

        if (!$this->admin_show) {
            $new_question = 1;
        }
        $question_type_id = CGPQuestionType::where('type', $data['question_type'])->first()->id;

        $this ->updateAnswers($data, $cloned);
        $this->updateInfos($data);
        $this->question_type_id =$question_type_id;
        $this->difficulty = $data ['difficulty_id'] ;
        if (isset($data ['system_assesset'])) {
            $this->system_assesset = $data ['system_assesset'] ;
        }
        $this->question_text = $data ['question_text'] ;
        $this->topics()->sync($data ['topics']) ;
        $this->weight = $data ['weight'] ;
        $this->criteria_effect_quiz == $data['criteria_effect_quiz'];
        $this->admin_show =1 ;
        $this->save() ;
        $this->questionFiles()->update(['admin_show'=>1]);
        $output = array() ;
        if ($validate_quizzes && !$new_question) {
            $insufficient_quizzes_data = $this->validateSufficientQuizzes($token);
            $output['insufficient_quizzes_data'] = $insufficient_quizzes_data;
            if (count($insufficient_quizzes_data['quizzes_objects']) == 0) {
                if (session()->has('question_quiz_section_details')) {
                    $question_quiz_section_details = session()->pull('question_quiz_section_details', []);
                    if ($cloned) {
                        $question_id = $this->original_id;
                    } else {
                        $question_id = $this->id;
                    }
                    
                    $question_key = array_search($question_id, array_column($question_quiz_section_details, 'question_id'));
                    if ($question_key !== false) {
                        $old_details_id = $question_quiz_section_details[$question_key]['details_id'];
                        $new_details_id = $this->quizSectionDetails()->pluck('quiz_section_detail_id')->toArray();
                        $details_added =  array_diff($new_details_id, $old_details_id);
                        if (count($details_added) > 0) {
                            $quizzes_converted_sufficient_data = $this->validateInsufficientQuizzes($quiz_templates_id=$details_added);
                            $output['quizzes_converted_sufficient_data'] = $quizzes_converted_sufficient_data;
                        }
                    }
                }
            } else {
                $this->replaceThisWithOriginalOneInGeneratedQuizzes();
            }
        } else {
            $this->replaceThisWithOriginalOneInGeneratedQuizzes();
        }
        if ($new_question) {
            $quizzes_converted_sufficient_data = $this->validateInsufficientQuizzes();
            $output['quizzes_converted_sufficient_data'] = $quizzes_converted_sufficient_data;
        }

        return $output;
    }

    public function updateInfos($data)
    {
        if (isset($data['multiple_answers'])) {
            $question_info = new CGPQuestionInfo();
            $question_info->question_id = $this->id;
            $question_info->info_id = CGPInfo::where('name', 'multiple_answers')->first()->id;
            $question_info->save();
        }
    }

    public static function cloneQuestion($id)
    {
        $question = self::find($id) ;
        $clone = $question ->replicate() ;
        $clone->original_id = $id;
        $clone ->save() ;

        CGPQuestionTopic::cloneTopics($id, $clone ->id) ;
        CGPQuestionAnswer::cloneAnswers($id, $clone ->id) ;
        CGPQuestionFile::cloneQuestionFiles($id, $clone ->id) ;
        CGPQuestionInfo::cloneQuestionInfos($id, $clone ->id) ;
        return $clone;
    }

    public function updateAnswers($data, $cloned)
    {
        foreach ($this->answers as $key => $answer) {
            $answer->answerFiles()->update(['admin_show'=>1]);
        }
        CGPQuestionAnswer::where('question_id', $this ->id)->update(['is_correct' => 0]) ;

        if ($data ['question_type'] == 'Text') {
            foreach ($data ['text_inputs'] as $key => $value) {
                if ($cloned) {
                    CGPQuestionAnswer::where('original_id', $key) ->update(['answer_text' => $value, 'is_correct' => 1, 'admin_show' => 1]) ;
                } else {
                    CGPQuestionAnswer::where('id', $key) ->update(['answer_text' => $value, 'is_correct' => 1, 'admin_show' => 1]) ;
                }
            }

            $this->removeOldAnswers(array_keys($data ['text_inputs']), $cloned) ;
        } elseif ($data ['question_type'] == 'MCQ') {
            if ($cloned) {
                $this ->answers()->whereIn('original_id', $data ['correct_answers']) ->update(['is_correct' => 1, 'admin_show' => 1]) ;
            } else {
                $this ->answers()->whereIn('id', $data ['correct_answers'])->update(['is_correct' => 1, 'admin_show' => 1]) ;
            }


            if (isset($data ['text_correct_answers'])) {
                foreach ($data ['text_correct_answers'] as $key => $value) {
                    if ($cloned) {
                        CGPTextCorrectAnswer::where('original_id', $key) ->update(['text' => $value, 'admin_show' => 1]) ;
                    } else {
                        CGPTextCorrectAnswer::where('id', $key) ->update(['text' => $value, 'admin_show' => 1]) ;
                    }
                }

                $this ->removeOldTextCorrectAnswers(array_keys($data ['text_correct_answers']), $cloned);
            }

            if (isset($data ['answers'])) {
                foreach ($data ['answers'] as $key => $value) {
                    if ($cloned) {
                        CGPQuestionAnswer::where('original_id', $key)->update(['answer_text' => $value, 'admin_show' => 1]) ;
                    } else {
                        CGPQuestionAnswer::where('id', $key)->update(['answer_text' => $value, 'admin_show' => 1]) ;
                    }
                }

                $this ->removeOldAnswers(array_keys($data ['answers']), $cloned) ;
            }
        }
    }

    public function removeOldTextCorrectAnswers($ids, $cloned)
    {
        if ($cloned) {
            CGPTextCorrectAnswer::whereNotIn('original_id', $ids) ->delete() ;
        } else {
            CGPTextCorrectAnswer::whereNotIn('id', $ids) ->delete() ;
        }
    }
}
