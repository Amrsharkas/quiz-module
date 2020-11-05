<?php

namespace mennaAbouelsaadat\quizGenerator\Http\Controllers;

use App\Helpers\Helper;
use App\Question;
use App\QuestionChoice;
use App\QuizEntry;
use App\QuizGenerator;
use Auth;
use Illuminate\Http\Request;

//uses
class QuizGeneratorController extends Controller
{
    public function index()
    {
        $data = [];
        $data['partialView'] = 'quiz_generator.list';
        $data['quizzes'] = QuizGenerator::orderBy('created_at', 'desc')->where('admin_show', 1)->get();
        return view('quiz_generator.base', $data);
    }

    public function init()
    {
        $quiz = new QuizGenerator();
        $quiz->save();
        return redirect(route('admin.quiz_generator.edit', ['id' => $quiz->id]));
    }

    public function edit($id)
    {
        $quiz = QuizGenerator::findOrFail($id);
        $data = [];
        $data['partialView'] = 'quiz_generator.form';
        $data['quiz'] = $quiz;

        $data['questions'] = Question::where('quiz_id', $quiz->id)->where('admin_show', 1)->orderBy('question_order', 'ASC')->get();
        $data['question_choices'] = QuestionChoice::orderBy('stuff_order', 'asc')->where('admin_show', 1)->get();
        return view('quiz_generator.base', $data);
    }

    public function delete($id)
    {
        QuizGenerator::destroy($id) or abort(404);
    }

    public function questions_order(Request $request)
    {
        $new_sequence = $request->order;

        $quiz_id = $request->quiz_id;
        $new_sequence = explode("item[]=", $new_sequence);
        
        $order = 0;
        foreach ($new_sequence as $item) {
            $item = explode('&', $item);
            $id = $item[0];
            Question::where('id', $id)->update(['question_order' => $order++]);
        }
    }

    public function update(Request $request, $id)
    {
        $question_ids = $request->multi_question_id;
        $question_content = $request->question_content;
        $question_weight = $request->weight;
        $question_type = $request->question_type;
        $question_choices_type = $request->question_choices_type;
        $question_answer_ids = $request->answer_id;//it's question_id in table
        $correct_answers = $request->correct_answers;
        $question_choices_ids = $request->question_choices_id;
        /*
         *Validation Start
         */
        if (!Helper::not_negative_integer($request->duration_hours)) {
            return response()->json(['status' => 'error', 'msg' => 'Please add a non-negative value for duration hours', 'url' => '']);
        }
        if (!Helper::not_negative_integer($request->duration_minutes)) {
            return response()->json(['status' => 'error', 'msg' => 'Please add a non-negative value for duration minutes', 'url' => '']);
        }
        if ($request->duration_minutes > 59) {
            return response()->json(['status' => 'error', 'msg' => 'Minutes field should not exceed 59 mintues', 'url' => '']);
        }
        if ((count($question_ids) < $request->rand_num) && $request->randomize==1) {
            return response()->json(['status' => 'error', 'msg' => 'You can not randomize questions more than '.count($question_ids), 'url' => '']);
        }
        $request->quiz_time = $request->duration_hours.":".$request->duration_minutes;
        if ($question_ids) {
            foreach ($question_ids as $key=>$question_id) {
                $question_file_id = Question::where('id', $question_id)->first()->file_id;
                if (
                    ($question_content[$key] == null && $question_file_id == null) ||
                    $question_weight[$key] == null || $question_type[$key] == null
                ) {//Question with empty fields
                    return response()->json(['status' => 'error', 'msg' => 'Please insert data into all questions fields', 'url' => '','animate' => 'animate','question'=>"item_".$question_id]);
                }
                if ($question_answer_ids == null) {
                    return response()->json(['status' => 'error', 'msg' => 'Please insert answer data for all answer options', 'url' => '','animate' => 'animate','question'=>"item_".$question_id]);
                }
                if (!in_array($question_id, $question_answer_ids)) { //Question with no answer
                    return response()->json(['status' => 'error', 'msg' => 'Please insert at least one answer option for each question', 'url' => '','animate' => 'animate','question'=>"item_".$question_id]);
                }
                $no_correct_answer = 1;
                foreach ($question_answer_ids as $i =>$question_answer_id) {
                    if ($question_answer_id == $question_id) {//the answer is for that question
                        if ($question_choices_ids[$i] != null && $correct_answers[$i] == 1) {
                            $no_correct_answer = 0;//there exist correct answer for the question
                        } elseif ($question_choices_ids[$i] == null) {
                            $no_correct_answer = 0;//the question is text all answers are possibly right
                        }
                    }
                }
                if ($no_correct_answer) { //the question is MCQ without any correct answer
                    return response()->json(['status' => 'error', 'msg' => 'Please make sure that at least one correct answer is checked', 'url' => '','animate' => 'animate','question'=>"item_".$question_id]);
                }
            }
        } else {
            return response()->json(['status' => 'error', 'msg' => 'You must add at least one question', 'url' => '']);
        }
        $answer_type = $request->type;
        $question_answers = $request->question_answers;
        $answer_fields = $request->answer_field;

        foreach ($question_choices_ids as $index => $answer_id) {
            $question_choice  = QuestionChoice::where('id', $answer_id)->first();
            if ($question_choices_ids[$index] != null) { // MCQ question answers validation
                $answer_file_id = $question_choice->file_id;
                if ($question_answers[$index] == null && $answer_file_id == null) {
                    return response()->json(['status' => 'error', 'msg' => 'Please insert answer data for all answer options', 'url' => '','animate' => 'animate','question'=>"item_".$question_choice->question_id]);
                }
            } else { // Text question answers validation
                if ($question_answers[$index] == null) {
                    return response()->json(['status' => 'error', 'msg' => 'Please insert answer data for all answer options', 'url' => '','animate' => 'animate','question'=>"item_".$question_choice->question_id]);
                }
            }
        }
        /*
         *Validation End
         */

        $question_files = $request->file('question_file');
        $service_id=$request->service_id;
        $clients_ids=$request->client_id;
        $description=$request->description;
        $language_id = $request->language_id;
        $locales_ids = $request->locales;
        $description=str_replace('<p>', '', $description);
        $description=str_replace('</p>', '', $description);

        $data = $request->input();
        if (isset($data['update'])) {
        }
        if (isset($data['new'])) { // Quiz Cloning
//            unset($data['new']);
            $quiz_generator = new QuizGenerator();
            $description = Auth::user()->name.' initialized QuizGenerator record';
            $operation= 'copy';
            $quiz_generator->save();
            $id = $quiz_generator->id;
        }
        //Update Quiz data
        $quiz = QuizGenerator::where('id', $id)->update([
            'title' => $request->quiz_title,
            'description'=>$description,
            'passing_percentage' => $request->passing_percentage,
            'attempts_number' => $request->attempts,
            'admin_show' => 1,
            'quiz_time' => $request->quiz_time,
            'show_result' => $request->show_result,
            'random_number' => $request->rand_num
        ]);


        //Copy Quiz questions to the cloned Quiz
        $questions_old_ids=[];
        $questions_new_ids=[];


        // Update question data
        foreach ($question_ids as $i => $question_id) {
            $question = Question::where('id', $question_id)
                    ->update(
                        ['content' => $question_content[$i], 'weight' => $question_weight[$i],
                            'quiz_id' => $id, 'type' => $question_type[$i], 'question_choices_type' => $question_choices_type[$i] /*,'answer_type'=> $answer_type[$i]*/, 'admin_show' => 1]
                    );
            $questions_new_ids[] = $question_id;
        }

        $i =1;
        foreach ($question_answer_ids as $index => $answer_id) {
            $old_question_index = array_search($answer_id, $questions_old_ids);
            $old_question_choice = QuestionChoice::where('id', $question_choices_ids[$index])->first();

            $old_question_choice->question_id = $questions_new_ids[$old_question_index];
            $old_question_choice->content = $question_answers[$index];
            $old_question_choice->answer_type = $answer_type[$index];
            $old_question_choice->file_id = $old_question_choice->file_id;
            $old_question_choice->correct = $correct_answers[$index];
            $old_question_choice->hasTextfield = $answer_fields[$index];
            $old_question_choice->stuff_order = $i;
            $old_question_choice->admin_show=1;
            $old_question_choice->save();
                
            $i++;
        }

        if ($request->save_progress == "Save progress") {
            return response()->json(['status' => 'success', 'msg' =>'Quiz progress has been successfully saved', 'url' => '','page'=>'none']);
        }
        return response()->json(['status' => 'success', 'msg' =>'Quiz has been successfully saved', 'url' => route('admin.quiz_generator.index')]);
    }

    public function override($id)
    { // Override Fail
        QuizEntry::where('id', $id)->update(['passed'=>3, 'qualified_by'=>Auth::user()->id]);
    }
}
