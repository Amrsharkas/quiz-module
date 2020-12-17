<div id="s_possible_answer" class="s_answer my-3">
	<div class="d-flex align-items-center">
		<input type="text" readonly name="text_correct_answers[{!! $answer ->id !!}]" value="{!! $answer ->text !!}" />

		<button data-action="/admin/questions/{!! $answer->answer ->question_id !!}/possible_answers/{!! $answer->id !!}/remove" class="remove_possible_answer possible-answer-delete-btn-style bg-transparent" data-id="{!! $answer ->id !!}" onclick="removeTextCorrectAnswer(this)" type="button">
			<i class="fa fa-times"></i>
		</button>
	</div>
</div>