 <div class="content assessment-form-style qu-topic-select quiz-section">
        <input type="hidden" name="quiz_section_id[]" value="{{$quiz_section->id}}">
        <div class="unit form-group d-flex align-items-center mb-4">
            <label class="label mb-0 mr-5">Choose Topics</label>

            <label class="mb-0 pt-1 toggle m-checkbox m-checkbox--solid m-checkbox--brand qu_add_highlighted_class">
                <input class="chk qu_target_checkbox" @if(isset($all_topics)&&$all_topics==1)checked="checked" value=1 @else value=0 @endif type="checkbox" name="all_topics" id="all_topics" data-name="All Topics" data-validation="">
                All Topics
                <span class="toggle_btn_checkbox_style"></span>
            </label>

            <a id="{{$quiz_section->id}}" type="button" class="btn delete_unit_btn_style py-0 ml-auto remove_quiz_section"
                data-action="{{route('admin.quiz.delete_quiz_section',['quiz_section_id'=>$quiz_section->id])}}"
                data-confirm="1"
                data-role="AssessmentContainerForm">
                    <i class="text-danger fa fa-close"></i>
            </a>
        </div>

        <div class="unit form-group mb-4">
            <label class="input select">
                <select data-id="{{$quiz_section->id}}" id="quiz_section_{{$quiz_section->id}}"  @if(isset($all_topics)&&$all_topics==1) disabled @endif class="form-control topic_select select2" multiple="multiple" type="text" name="topics_{{$quiz_section->id}}[]" id="select_all_topics" data-name="Topics">
                    data-name="Topic" data-validation=",required"
                    data-editable="0" data-url="">
                    @foreach($topics as $topic)
                            <option data-depending-value="{{$topic->id}}"
                                    @foreach($quiz_section->sectionTopics as $section_topic)
                                    @if($topic->id == $section_topic->topic_id)
                                    selected
                                    @endif
                                    @endforeach
                                    value="{{$topic->id}}">{{$topic->name}}</option>
                      
                    @endforeach
                </select>
                <i></i>
            </label>
        </div>

       <button  type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill portlet-btn float-none add_quiz_section_question_detail" data-target=".questions_details_container_{{$quiz_section->id}}" data-form="{{route('admin.quiz.add_quiz_section_question_detail',['quiz_section_id'=>$quiz_section->id])}}">
                        <span>
                            <i class="la la-plus"></i>
                            <span>
                                Add Question Detail
                            </span>
                        </span>
                    </button>
        <ol class="questions_details_container_{{$quiz_section->id}}">
           @foreach($quiz_section->sectionDetails as $detail)
           @include('quiz.quiz_question_details',['quiz_section_detail'=>$detail])
           @endforeach
        </ol>
        </div>
