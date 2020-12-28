<form id="main-form" class="ajax_form" method="post" action="{{route('admin.quiz.update')}}" data-beforeSerialize="" data-beforeSubmit="" data-request="" data-on-start="false">
    {{csrf_field()}}
    <div class="content">
        <input type="hidden" name="criteria_effect_quiz" value="">
        <input type="hidden" name="quiz_id" value="{{$quiz->id}}">
        <div class="unit form-group mb-4">
            <input placeholder="Name *" class="form-control" type="text" name="name" id="name" data-name="Name" data-validation=",required" value="{{$quiz->name}}" />
        </div>

        <div id="Quiz_Exam" class="tabcontent input-25px-height" >
            <div class="d-flex flex-wrap justify-content-between label-width-style">
                <div class="unit form-group d-flex align-items-center">
                    <label class="mb-0">% Of Success <span class="required" aria-required="true"> * </span></label>
                    <div class="d-flex">
                        <input class="form-control" type="text" name="success_percentage" id="success_percentage_quiz" data-name="Success Percentage" data-validation=",required" value="{{$quiz->passing_percentage}}" />
                        <div class="group-icon-style">%</div>
                    </div>
                </div>

                <div class="unit form-group d-flex align-items-center">
                    <label class="mb-0">Duration <span class="required" aria-required="true"> * </span></label>
                    <div class="d-flex">
                        <input value="{{$quiz->duration}}" class="form-control" type="text" name="duration" id="duration_quiz" data-name="Duration" data-validation=",required" />
                        <div class="group-icon-style"><span class="material-icons">alarm</span></div>
                    </div>
                </div>

                <div  id="number_of_attempts_div" class="unit form-group align-items-center">
                    <label class="mb-0">Number Of Attempts <span class="required" aria-required="true"> * </span></label>
                    <div class="d-flex">
                        <input value="{{$quiz->attempts_number}}" class="form-control" type="text" name="number_of_attempts" id="number_of_attempts" data-name="Number Of Attempts" data-validation=",required" />
                        <div class="group-icon-style">%</div>
                    </div>
                </div>

               <!--  <div  id="randomize_all_questions" class="unit form-group align-items-center">
                    <label class="mb-0">Randomize all questions <span class="required" aria-required="true"> * </span></label>
                    <div class="d-flex">
                        <input value="" class="form-control" type="checkbox" name="randomize_all_questions" id="randomize_all_questions" data-name="Randomize all questions" data-validation=",required" />
                    </div>
                </div> -->
            </div>
        </div>

        <hr class="mb-4" />
        <div class="unit form-group add_form_parent" data-form="">
            <div class="d-flex align-items-center mb-4" style="height:32px;">
                <label class="mr-4 mb-0">Topics</label>
                <div class="d-flex w-100 justify-content-end">
                    <button  type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill portlet-btn float-none add_quiz_section" data-target=".quiz_containers_{{$quiz->id}}" data-form="{{route('admin.quiz.add_quiz_section',['quiz_id'=>$quiz->id])}}">
                        <span>
                            <i class="la la-plus"></i>
                            <span>
                                Section
                            </span>
                        </span>
                    </button>
                </div>
            </div>
            <div class="" data-retrieve-route="" data-target="">
                <ol class="quiz_containers_{{$quiz->id}} px-0" data-sort="0" id="assessment_containers" data-serialize-input-name="">
                    <input type="hidden" name="">
                    @foreach($quiz->quizSections as $quiz_section)
                        @include('CGP_quiz.quiz_section',["quiz_section"=>$quiz_section])
                    @endforeach

                </ol>
            </div>
        </div>

        <div class="form-actions text-center">
            <button class="btn custom_rouneded_btn_style px-5 mt-4 mb-0" type="submit">Save</button>
        </div>

    </div>
</form>