@if(session('status') == 'error')
<div class="alert alert-danger">{!! session('message') !!}</div>
@endif
<form id="question-form" class="ajax_form" method="post" action="/admin/questions/update" data-beforeSerialize="" data-beforeSubmit="" data-request="App/Http/Requests/QuestionRequest" data-on-start="false" data-request="App/Http/Requests/QuestionRequest">

    <input type="hidden" name="question_id" id="question_id" value="{!! $question->id !!}">
    {{csrf_field()}}
    <div class="content container">
        <input type="hidden" name="criteria_effect_quiz" value="">
        <div class="row mb-4">
            <div class="col-lg-2 col-sm-3 col-5 unit form-group">
                <label class="label mb-0">Question Type <span class="required" aria-required="true"> * </span></label>
            </div>
            <div class="col-lg-10 col-sm-9 col-7 unit form-group px-0">
                <div>
                    <div class="row mb-2 mx-0" id="multiple_choice_div">
                        <div class="col-2">
                            <label class="radio">
                                <input class="type_change tablinks validate_quiz" {!! $question->questionType->type == 'MCQ' ? 'checked' : '' !!} value="MCQ" type="radio" name="question_type" id="multiple_choice" data-validation=",required,,">
                                Multiple Choice
                                <span></span>
                            </label>
                        </div>

                        <div class="col-3 text-nowrap">
                            <label class="m-checkbox m-checkbox--solid m-checkbox--brand qu_add_highlighted_class">
                                <input class="type_change tablinks type_info" value="1"  type="checkbox" name="multiple_answers" id="allow_multiple_answers" data-validation=",required,,">
                                Allow Multiple Correct Answers
                                <span class="toggle_btn_checkbox_style"></span>
                            </label>
                        </div>

                        <div class="col-3">
                            <label class="m-checkbox m-checkbox--solid m-checkbox--brand qu_add_highlighted_class">
                                <input class="type_change tablinks type_info" value="1"  type="checkbox" name="text_input" id="allow_text_input" data-validation=",required,,">
                                Allow Text Input Option
                                <span class="toggle_btn_checkbox_style"></span>
                            </label>
                        </div>

                    </div>

                    <div class="row mx-0" id="text_input_div">
                        <div class="col-2">
                            <label class="radio">
                                <input class="validate_quiz type_change " value="Text" {!! $question->questionType->type == 'Text' ? 'checked' : '' !!} type="radio" name="question_type" id="text_input" data-validation=",required,,">
                                Text Input
                                <span></span>
                            </label>
                        </div>
                        <div class="col-3 text-nowrap">
                            <label class="radio ">
                                <input class="type_change type_info" name="system_assesset" id="evaluated_by" data-validation=",required,," value="0" type="radio" >
                                Evaluated By Reviewer
                                <span></span>
                            </label>

                        </div>

                        <div class="col-3">
                            <label class="radio ">
                                <input class="type_change tablinks type_info info"  type="radio" name="system_assesset" id="evaluated_automatically" data-validation=",required,," value="1">
                                Evaluated Automatically
                                <span></span>
                            </label>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <div class="row align-items-center mb-4">
            <div class="col-lg-2 col-sm-3 col-5 unit form-group">
                <label class="label mb-0">Topics <span class="required" aria-required="true"> * </span></label>
            </div>
            <div class="col-lg-10 col-sm-9 col-7 unit form-group">
                <select id="topics_select" class="select2 validate_quiz" multiple="multiple" class="form-control" type="text" name="topics[]" id="topics" data-name="Topics" data-validation=",required">
                    @if(count($topics)>0)
                    @foreach($topics as $topic)
                    <option name="{{$topic->name?$topic->name:''}}" @if(in_array($topic->id, $question_topics)) selected @endif
                        value="{{$topic->id}}">{{$topic->name?$topic->name:''}}</option>
                    @endforeach
                    @endif
                </select>
            </div>
        </div>

        <div class="row align-items-center mb-4">
            <div class="col-lg-2 col-sm-3 col-5 unit form-group">
                <label class="label mb-0">Weight <span class="required" aria-required="true"> * </span></label>
            </div>
            <div class="col-lg-10 col-sm-9 col-7 unit form-group">
                <input type="text" name="weight">
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-lg-2 col-sm-3 col-5 unit form-group">
                <label class="label mb-0">Difficulty <span class="required" aria-required="true"> * </span></label>
            </div>
            <div class="col-lg-10 col-sm-9 col-7 unit form-group">

                <select name="difficulty_id" id="" class="validate_quiz">
                    @foreach ($difficulties as $difficulty)
                    <option {!! $difficulty ->id == $question ->difficulty ? 'selected' : '' !!} value="{!! $difficulty ->id !!}">{!! $difficulty ->type !!}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="media-card-style mb-4">
            <label class="label mb-0">Question Content <span class="required" aria-required="true"> * </span></label>
            <div class="media align-items-md-center flex-md-row flex-column">
                <div class="media-img-style">
                    @if(count($question ->files))
                    <div class="position-relative">
                        <img class="img-fluid" src="/uploads/{{$question->files[0]->hash}}/{{$question->files[0]->file}}">
                        <i class="fa fa-times uploader-x-btn-style delete-btn-style"></i>
                    </div>
                    @else
                    <div id="file_id_{{$question->id}}" class="uploader" data-entity-id="{{$question->id}}" data-selector="file_id_{{$question->id}}" data-allowed-extensions="jpg,jpeg,png,gif" data-maximum-file-size=19000000 data-youtube-videos="false" data-encrypt-files="0" data-multiple="true" data-model="QuestionFile" data-field="question_id">
                    </div>
                    @endif
                </div>
                <div class="media-body unit form-group mw-100 mt-md-0 mt-3 mb-0">
                    <h5 class="mb-0">
                        <textarea placeholder="Content *" class="form-control" name="question_text" id="question_text" data-name="Content" data-validation=",required">{{$question ->question_text}}</textarea>
                    </h5>
                </div>
                <div class="media-body unit form-group mw-100 mt-md-0 mt-3 mb-0">
                    <h5 class="mb-0">
                        <input type="checkbox" name="youtube_link" value="1"> YouTube Link
                    </h5>
                </div>
            </div>
        </div>



        <div id="question_content">

            @include('questions.question_contents.answers_view', ['question_type' => $question->questionType->type,'system_assesset'=>$question->system_assesset,'multiple_answers'=>$question->allowMultipleAnswers,'text_answer'=>$question->allowTextAnswer])
        </div>
        <div class="text-center">
            <button class="btn custom_rouneded_btn_style px-5 mt-4 mb-0" type="submit">Save</button>
        </div>
    </div>
</form>

</html>
