@if($question_type == 'Multiple Choice')
	@include('questions.question_contents.multiple_choice')	

@elseif($question_type == 'Text Input')
	@include('questions.question_contents.text_input')
@endif