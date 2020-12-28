<div class="input-40px-height question_details">
    <input type="hidden" name="quiz_section_details[]" value="{{$quiz_section_detail->id}}">
    <div class="d-flex align-items-center mt-3">
        <div class="row col">
            <div class="col-3 pl-0 d-flex align-items-center">
                <label class="mr-2 mb-0 min-w-100px"># of Questions</label>
                <input type="text" name="number_of_questions_{{$quiz_section_detail->id}}" value="{{$quiz_section_detail->number}}" class="validate_quiz">
            </div>

            <div class="col-9 d-flex align-items-center">
                <label class="mr-2 mb-0 min-w-100px">Difficulty</label>
                <select name="difficulty_{{$quiz_section_detail->id}}" class="select2 validate_quiz">
                    @foreach($difficulties as $difficulty)
                    <option value="{{$difficulty->id}}" @if($difficulty->id == $quiz_section_detail->difficulty_id ) selected @endif>{{$difficulty->type}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <a id="{{$quiz_section_detail->id}}" type="button" class="btn delete_unit_btn_style btn-40px px-0 pb-0 pt-1 remove-from-view" data-action="{{route('admin.quiz.delete_quiz_section_detail',['quiz_section_detail_id'=>$quiz_section_detail->id])}}" data-confirm="1" data-role="AssessmentContainerForm">
                <span class="material-icons text-danger">close</span>
            </a>

            <a id="" type="button" class="btn bg-white btn-40px" data-action="" data-confirm="" data-role="">
                <span class="material-icons green-color font-weight-bold">add</span>
            </a>
        </div>

    </div>

    <div class="d-flex align-items-center mt-2">
        <label class="mr-2 mb-0 min-w-100px">Question Type</label>
        <select name="question_type_{{$quiz_section_detail->id}}" class="select2 validate_quiz">
            @foreach($question_types as $type)
            <option value="{{$type->id}}" @if($type->id == $quiz_section_detail->question_type_id) selected @endif>{{$type->type}}</option>
            @endforeach
        </select>
    </div>
</div>