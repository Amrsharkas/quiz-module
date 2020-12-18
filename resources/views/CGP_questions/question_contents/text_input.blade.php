@if(in_array(EVALUATED_AUTOMATICALLY, $infos))
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

		@foreach($question ->textAnswers as $answer)
			@include('questions.question_contents.answer')
		@endforeach
	</div>
</div>


@elseif(in_array(EVALUATED_BY_REVIEWER, $infos))
<div id="Essay" class="unit form-group tabcontent mt-3">
	<label class="label green-color">Model Answer <span class="required" aria-required="true"> * </span></label>
	<textarea placeholder="Model Answer" class="form-control exam_modal_answer_style" name="text_inputs[{!! $question ->essayAnswer() ?  $question ->essayAnswer() ->id : ''  !!}]" id="model_answer" data-name="Answer" data-validation=",required,,">{!! $question ->essayAnswer() ?  $question ->essayAnswer() ->answer_text : ''  !!}</textarea>
</div>
@endif