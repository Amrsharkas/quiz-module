@if($system_assesset)
<div id="text_inputs_div">
	<div class="qu-text-inputs">
		<label class=" h-100 mb-0 pt-2">
			Possible Correct Answers
		</label>

		<div class="d-flex align-items-center">
			<input type="text" name="" id="next_text_input">

			<button class="my-3 reset-btn-style" type="button" onclick="addAnswer('Text Input')" id="add_text_input">
				<i class="fa fa-plus plus-btn-style"></i>
			</button>
		</div>

		@foreach($question->textAnswers()?$question->textAnswers()->get():[]   as $answer)
			@include('CGP_questions.question_contents.answer')
		@endforeach
	</div>
</div>


@else
<div id="Essay" class="unit form-group tabcontent mt-3">
	<label class="label green-color">Model Answer <span class="required" aria-required="true"> * </span></label>
	<textarea placeholder="Model Answer" class="form-control exam_modal_answer_style" name="text_inputs[{!! !$question->system_assesset?  $question->essayAnswer() ->id : ''  !!}]" id="model_answer" data-name="Answer" data-validation=",required,,">{!! !$question->system_assesset ? $question->answers()->first()->answer_text : ''  !!}</textarea>
</div>
@endif