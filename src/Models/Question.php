<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table='questions';
    
    public function files()
    {
        return $this->belongsToMany('App\File', 'question_files');
    }

    public function type()
    {
        return $this->belongsTo('App\QuestionType', 'question_type_id');
    }

    public function difficulty()
    {
        return $this ->belongsTo('App\Difficulty', 'difficulty') ;
    }
    public function getQuestionType()
    {
        if ($this ->question_type_id == MULTIPLE_CHOICE_WITH_SINGLE_CORRECT_ANSWER || $this ->question_type_id == MULTIPLE_CHOICE_WITH_SINGLE_CORRECT_ANSWER_ALLOW_TEXT_INPUT || $this ->question_type_id == MULTIPLE_CHOICE_WITH_MULTIPLE_CORRECT_ANSWERS || $this ->question_type_id == MULTIPLE_CHOICE_WITH_MULTIPLE_CORRECT_ANSWERS_ALLOW_TEXT_INPUT) {
            return 'Multiple Choice';
        } else {
            return 'Text Input' ;
        }
    }
    public function multipleChoiceQuestion()
    {
        return ($this ->question_type_id == MULTIPLE_CHOICE_WITH_SINGLE_CORRECT_ANSWER || $this ->question_type_id == MULTIPLE_CHOICE_WITH_SINGLE_CORRECT_ANSWER_ALLOW_TEXT_INPUT || $this ->question_type_id == MULTIPLE_CHOICE_WITH_MULTIPLE_CORRECT_ANSWERS || $this ->question_type_id == MULTIPLE_CHOICE_WITH_MULTIPLE_CORRECT_ANSWERS_ALLOW_TEXT_INPUT) ;
    }
    public function textInputQuestion()
    {
        return ($this ->question_type_id == TEXT_INPUT_EVALUATED_BY_REVIEWER || $this ->question_type_id == TEXT_INPUT_EVALUATED_BY_REVIEWER) ;
    }
    public function answers()
    {
        return $this->hasMany('App\QuestionAnswer') ->where('question_answer_type_id', '!=', TEXT_CORRECT_ANSWERS);
    }
    public function choiceAnswers()
    {
        return $this->hasMany('App\QuestionAnswer') ->where('question_answer_type_id', QUESTION_INPUT);
    }
    public function textAnswers()
    {
        return $this->hasMany('App\QuestionAnswer') ->where('question_answer_type_id', TEXT_INPUT) ->where('system_assesst', 1);
    }
    public function essayAnswer()
    {
        return QuestionAnswer::where('question_id', $this ->id) ->where('question_answer_type_id', TEXT_INPUT) ->where('system_assesst', '!=', 1) ->first();
    }
    public function textCorrectAnswersQuestionAnswer()
    {
        return $this->hasMany('App\QuestionAnswer') ->where('question_answer_type_id', TEXT_CORRECT_ANSWERS) ->first();
    }
    public function hasTextCorrectAnswers()
    {
        if (count($this ->textCorrectAnswers())) {
            return true ;
        }
        return false ;
    }
    public function textCorrectAnswers()
    {
        return  $this ->textCorrectAnswersQuestionAnswer() ->textCorrectAnswers()->get();
    }
    public function topics()
    {
        return $this ->belongsToMany('App\Topic', 'question_topics') ;
    }

    public function questionTopics()
    {
        return $this->hasMany('App\QuestionTopic');
    }

    public function generatedQuizzes()
    {
        $ids = GeneratedQuizQuestion::where('question_id', $this->id)->pluck('generated_quiz_id')->toArray();
        return GeneratedQuiz::whereIN('id', $ids);
    }

    public function quizSectionDetails()
    {
        return QuizSectionDetailQuestion::where('question_id', $this->id);
        ;
    }

    public function quizSections()
    {
        $details_id = $this->quizSectionDetails()->pluck('quiz_section_detail_id')->toArray();
        $sections_id = QuizSectionDetail::whereIn('id', $details_id)->pluck('quiz_section_id')->toArray();
        return QuizSection::whereIn('id', $sections_id);
    }

    public function quizTemplates()
    {
        $quizzes_id  = $this->quizSections()->pluck('quiz_id')->toArray();
        return Quiz::whereIn('id', $quizzes_id);
    }

    public function questionsIdThatShouldBeSuspended()
    {
        $quizzes_id = $this->quizTemplates()->pluck('id')->toArray();
        $sections_id = QuizSection::whereIn('quiz_id', $quizzes_id)->pluck('id')->toArray();
        $details_id = QuizSectionDetail::whereIn('id', $sections_id)->pluck('id')->toArray();
        return QuizSectionDetailQuestion::whereIn('quiz_section_detail_id', $details_id)->pluck('question_id')->toArray();
    }

    public function validateSufficientQuizzes($token=null)
    {
        $insufficient_quizzes = [];
        $quiz_templates_id = db::select("SELECT DISTINCT quiz_id FROM generated_quizzes t WHERE t.quiz_id IN  (SELECT DISTINCT quizzes.id FROM generated_quiz_questions join generated_quizzes ON  generated_quizzes.id = generated_quiz_questions.generated_quiz_id join quizzes ON quizzes.id = generated_quizzes.quiz_id where quizzes.status = 'sufficient' AND  generated_quiz_questions.question_id  =".$this->id.") GROUP BY t.quiz_id
            HAVING  (COUNT(t.id) - (SELECT COUNT(s.id) FROM generated_quizzes s where s.id in (SELECT DISTINCT generated_quizzes.id FROM generated_quiz_questions join generated_quizzes ON  generated_quizzes.id = generated_quiz_questions.generated_quiz_id join quizzes ON quizzes.id = generated_quizzes.quiz_id where quizzes.status = 'sufficient' AND  generated_quiz_questions.question_id  =".$this->id.") AND s.quiz_id = t.quiz_id)) = 0");
        $quizzes_names = '';
        foreach ($quiz_templates_id as $key => $quiz_template_id) {
            $quiz = Quiz::find($quiz_template_id->quiz_id);
            $response = $quiz->generateQuiz($validate=1, $number=50, $quiz_limit=0, $with_saving=0, $token=$token);
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
            $quiz_section_detail = QuizSectionDetail::find($detail_id);
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
        if ($this->testing_request) {
            $this->valid_request = $this->testing_request;
            $this->testing_request = null;
            $this->save();

            $generated_quizzes = $this->generatedQuizzes()->whereNull('token')->get();
            foreach ($generated_quizzes as $key => $generated_quiz) {
                $generated_quiz->questions()->delete();
                $generated_quiz->delete();
            }
            $this->generatedQuizzes()->update(['token'=>null]);
            $this->removeSuspendedToken();
            $quizzes = $this->quizTemplates()->get();
            foreach ($quizzes as $key => $quiz) {
                $quiz->generateQuizJob();
            }
        }
    }

    public function rollback()
    {
        $this->generatedQuizzes()->where('token', $this->suspended_token)->delete();
        $this->updateData($this->valid_request, $without_validation=1);
        $this->testing_request = null;
        $this->save();
        $this->removeSuspendedToken();
    }

    public function removeSuspendedToken()
    {
        $token = $this->suspended_token;
        $suspended_questions_ids = Question::where('suspended_token', $token)->pluck('id')->toArray();
        Question::whereIn('id', $suspended_questions_ids)->update(['suspended_token'=>null]);
    }
    public function getInfos()
    {
        if ($this ->question_type_id == MULTIPLE_CHOICE_WITH_SINGLE_CORRECT_ANSWER) {
            return array() ;
        } elseif ($this ->question_type_id == MULTIPLE_CHOICE_WITH_SINGLE_CORRECT_ANSWER_ALLOW_TEXT_INPUT) {
            return array(ALLOW_MULTIPLE_CORRECT_ANSWERS) ;
        } elseif ($this ->question_type_id == MULTIPLE_CHOICE_WITH_MULTIPLE_CORRECT_ANSWERS) {
            return array(ALLOW_TEXT_INPUT) ;
        } elseif ($this ->question_type_id == MULTIPLE_CHOICE_WITH_MULTIPLE_CORRECT_ANSWERS_ALLOW_TEXT_INPUT) {
            return array(ALLOW_MULTIPLE_CORRECT_ANSWERS,ALLOW_TEXT_INPUT) ;
        } elseif ($this ->question_type_id == TEXT_INPUT_EVALUATED_BY_REVIEWER) {
            return array(EVALUATED_BY_REVIEWER) ;
        } elseif ($this ->question_type_id == TEXT_INPUT_EVALUATED_AUTOMATICALLY) {
            return array(EVALUATED_AUTOMATICALLY) ;
        }
    }
    public function updateQuestionType($question_type, $infos)
    {
        if ($question_type == 'Multiple Choice') {
            if (!in_array(ALLOW_MULTIPLE_CORRECT_ANSWERS, $infos) && !in_array(ALLOW_TEXT_INPUT, $infos)) {
                $this ->question_type_id = MULTIPLE_CHOICE_WITH_SINGLE_CORRECT_ANSWER ;
            } elseif (in_array(ALLOW_MULTIPLE_CORRECT_ANSWERS, $infos) && !in_array(ALLOW_TEXT_INPUT, $infos)) {
                $this ->question_type_id = MULTIPLE_CHOICE_WITH_SINGLE_CORRECT_ANSWER_ALLOW_TEXT_INPUT ;
            } elseif (!in_array(ALLOW_MULTIPLE_CORRECT_ANSWERS, $infos) && in_array(ALLOW_TEXT_INPUT, $infos)) {
                $this ->question_type_id = MULTIPLE_CHOICE_WITH_MULTIPLE_CORRECT_ANSWERS ;
            } elseif (in_array(ALLOW_MULTIPLE_CORRECT_ANSWERS, $infos) && in_array(ALLOW_TEXT_INPUT, $infos)) {
                $this ->question_type_id = MULTIPLE_CHOICE_WITH_MULTIPLE_CORRECT_ANSWERS_ALLOW_TEXT_INPUT ;
            }
        } elseif ($question_type == 'Text Input') {
            if (in_array(EVALUATED_BY_REVIEWER, $infos)) {
                $this ->question_type_id = TEXT_INPUT_EVALUATED_BY_REVIEWER ;
            } elseif (in_array(EVALUATED_AUTOMATICALLY, $infos)) {
                $this ->question_type_id = TEXT_INPUT_EVALUATED_AUTOMATICALLY ;
            }
        }

        $this ->save() ;
    }

    public function removeOldAnswers($ids)
    {
        QuestionAnswer::whereNotIn('id', $ids)
        ->where('question_id', $this ->id)
        ->where('id', '!=', $this ->textCorrectAnswersQuestionAnswer() ->id)
        ->delete() ;
    }

    public function updateData($data, $validate=1)
    {
        if ($validate) {
            $validate_quizzes = 1;
            $new_question = 0;
            $request_changed = 0;
            unset($data['_token']);
            if ($this->suspended_token) {
                return false;
            }
            
            $token = md5(uniqid().$this->id);
            Question::whereIn('id', $this->questionsIdThatShouldBeSuspended())->update(['suspended_token'=>$token]);
            if ($this->admin_show && ($this->criteria_effect_quiz == $data['criteria_effect_quiz'])) {
                $validate_quizzes = 0;
            }

            if (!$this->admin_show) {
                $new_question = 1;
            }
        }
        
        $this->updateQuestionType($data ['question_type'], $data ['infos']) ;
        QuestionAnswer::where('question_id', $this ->id) ->update(['is_correct' => 0]) ;

        if ($data ['question_type'] == 'Text Input') {
            foreach ($data ['text_inputs'] as $key => $value) {
                QuestionAnswer::where('id', $key) ->update(['answer_text' => $value, 'is_correct' => 1, 'admin_show' => 1]) ;
            }

            $this ->removeOldAnswers(array_keys($data ['text_inputs'])) ;
        } elseif ($data ['question_type'] == 'Multiple Choice') {
            $this ->answers() ->whereIn('id', $data ['correct_answers']) ->update(['is_correct' => 1]) ;

            if (isset($data ['text_correct_answers'])) {
                foreach ($data ['text_correct_answers'] as $key => $value) {
                    TextCorrectAnswer::find($key) ->update(['text' => $value]) ;
                }
            }


            if (isset($data ['answers'])) {
                foreach ($data ['answers'] as $key => $value) {
                    QuestionAnswer::where('id', $key) ->update(['answer_text' => $value, 'admin_show' => 1]) ;
                }

                $this ->removeOldAnswers(array_keys($data ['answers'])) ;
            }
        }

        $this->save() ;
        $this->difficulty = $data ['difficulty_id'] ;
        $this->question_text = $data ['question_text'] ;
        $this->topics()->sync($data ['topics']) ;
        $this->weight = $data ['weight'] ;
        $this->criteria_effect_quiz == $data['criteria_effect_quiz'];
        $this->admin_show =1 ;
        $this->save() ;

        if ($validate) {
            if ($validate_quizzes && !$new_question) {
                $insufficient_quizzes_data = $this->validateSufficientQuizzes($token);
                $output['insufficient_quizzes_data'] = $insufficient_quizzes_data;
                if (count($insufficient_quizzes_data['quizzes_objects']) == 0) {
                    if (session()->has('question_quiz_section_details')) {
                        $question_quiz_section_details = session()->pull('question_quiz_section_details', []);
                        if ($question_key = array_search($this->id, array_column($question_quiz_section_details, 'question_id')) !== false) {
                            $question_key = array_search($this->id, array_column($question_quiz_section_details, 'question_id'));
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
                    $this->testing_request = $data;
                    $this->save();
                }
            }
            if ($new_question) {
                $quizzes_converted_sufficient_data = $this->validateInsufficientQuizzes();
                $output['quizzes_converted_sufficient_data'] = $quizzes_converted_sufficient_data;
            }
        }

        if (!$this->testing_request) {
            $this->valid_request = $data;
            $this->save();
        }

        return $output;
    }
}
