<div id="Mcq" class="unit form-group tabcontent mt-3">
	<div class="d-flex justify-content-between align-items-center mb-1">
		<label class="label mb-0">Answers <span class="required" aria-required="true"> * </span></label>
		<i class="fa fa-plus plus-btn-style" id="add_new_option" data-question="1"></i>
	</div>

	<div class="unit form-group options">
		<div id="div_options" class="row mx-0">
			@if(isset($text_input) && $text_input)
			<div id="possible_answers_div" class="col-12 my-3">

				@if($multiple_answers)
				{{-- Checkbox Buttons --}}
				<label class="m-checkbox m-checkbox--solid m-checkbox--brand qu_add_highlighted_class">
					<input {!! $question->withTextCorrectAnswers() ? 'checked' : '' !!} class="tablinks mr-3 answer" name="correct_answers[]" type="checkbox"  value="{!! $question ->hasTextCorrectAnswers()->id !!}">
					Possible Correct Answers
					<span class="toggle_btn_checkbox_style"></span>
				</label>
				@else
				{{-- Radion Buttons --}}
				<label class="radio">
					<input {!! $question ->withTextCorrectAnswers() ? 'checked' : '' !!} class="option_radio mr-3 answer" name="correct_answers[]" type="radio"  value="{!! $question ->hasTextCorrectAnswers()->id !!}">
					Possible Correct Answers
					<span class=""></span>
				</label>
				@endif

				<div class="w-100 possible-answers-style qu-possible-answers">
					<div id="s_possible_answer" class="s_answer" hidden>
						<input type="text" disabled name="possible_correct_answers[]" value="default_answer" /><button class="remove_possible_answer" onclick="removeTextCorrectAnswer(this)" type="button">Delete</button>
					</div>
					
					<div class="d-flex align-items-center">
						<input type="text" name="" id="next_possible_answer">

						<button class="my-3 reset-btn-style" type="button" onclick="addTextCorrectAnswer(this)" id="add_possible_answer">
							<i class="fa fa-plus plus-btn-style"></i>
						</button>
					</div>
					@foreach($question->hasTextCorrectAnswers()?$question->hasTextCorrectAnswers()->textCorrectAnswers:[]   as $answer)
					@include('CGP_questions.question_contents.possible_answer')
					@endforeach
				</div>					

			</div>
			@endif

			@foreach($question->choiceAnswers()->get() as $answer)
			@include('CGP_questions.question_contents.answer')
			@endforeach


		</div>


	</div>
</div>