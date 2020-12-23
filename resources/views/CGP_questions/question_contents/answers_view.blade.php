
@if($question_type == 'MCQ')
	@include('questions.question_contents.multiple_choice',['system_assesset'=>isset($system_assesset)?$system_assesset:0,'multiple_answers'=>isset($multiple_answers)?$multiple_answers:0,'text_answer'=>isset($text_answer)?$text_answer:0])	

@elseif($question_type == 'Text')
	@include('questions.question_contents.text_input',['system_assesset'=>isset($system_assesset)?$system_assesset:0,'multiple_answers'=>isset($multiple_answers)?$multiple_answers:0,'text_answer'=>isset($text_answer)?$text_answer:0])
@endif