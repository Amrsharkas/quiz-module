@if($question_type == 'Multiple Choice')
<div class="single_option col-12 my-3 Answer media-card-style">
     <div class="d-flex align-items-center position-relative">

          @if(in_array(ALLOW_MULTIPLE_CORRECT_ANSWERS, $infos))

          <label class="m-checkbox m-checkbox--solid m-checkbox--brand qu_add_highlighted_class">
               <input class="tablinks mr-3 answer" name="correct_answers[]" type="checkbox" value="{!! $answer ->id !!}" {!! $answer ->is_correct ? 'checked' : '' !!}>
               <span class="toggle_btn_checkbox_style"></span>
          </label>

          @else
          <label class="radio">
               <input class="option_radio mr-3 answer" name="correct_answers[]" type="radio" value="{!! $answer ->id !!}" {!! $answer ->is_correct ? 'checked' : '' !!}>
               <span class="toggle_btn_checkbox_style"></span>
          </label>
          @endif
          <div class="media align-items-md-center flex-md-row flex-column mt-0 pt-2">
               <div class="media-img-style">
                    @if(count($answer ->files))
                    {{-- @dd(count($answer ->files)) ;  --}}
                    <div class="position-relative">
                         <img class="img-fluid" src="/uploads/{{$answer->files[0]->hash}}/{{$answer->files[0]->file}}">
                         <i class="fa fa-times uploader-x-btn-style delete-btn-style" data-class="file_id_uploader" data-action="" value=""></i>
                    </div>
                    @else
                    <div id="file_id_{!! $answer ->id !!}" class="uploader" data-entity-id="{!! $answer ->id  !!}" data-selector="file_id_{!! $answer ->id !!}" data-allowed-extensions="jpg,jpeg,png,gif" data-maximum-file-size=19000000 data-youtube-videos="false" data-encrypt-files="0" data-multiple="true" data-model="QuestionAnswerFile" data-field="question_answer_id">
                    </div>
                    @endif
               </div>

               <div class="media-body unit form-group mb-0 mw-100 mt-md-0 mt-3">
                    <h5 class="mb-0">
                         <textarea  class="form-control option_input" data-height="100" data-disable-tinyMce-tools="true" placeholder="Write an answer" value="{!! $answer ->answer_text !!}" name="answers[{!! $answer ->id !!}]">{!! $answer ->answer_text !!}</textarea>
                    </h5>
               </div>

          </div>
          <a type="button"  class="remove_single_answer pl-2" data-action="/admin/questions/{!! $answer->question_id !!}/answers/{!! $answer->id !!}/remove" onclick="removeAnswer(this, 'Question Input')" data-confirm="1" data-min="false" data-role="Answer">
               <i class="fa fa-times uploader-x-btn-style pt-2"></i>
          </a>
     </div>
</div>
@elseif($question_type == 'Text Input')
<div id="s_possible_answer" class="s_answer my-3">
     <div class="d-flex align-items-center">
          <input type="text" readonly name="text_inputs[{!! $answer ->id !!}]" value="{!! $answer ->answer_text !!}" />

          <button data-action="/admin/questions/{!! $answer->question_id !!}/answers/{!! $answer->id !!}/remove" class="remove_text_input possible-answer-delete-btn-style bg-transparent" data-id="{!! $answer ->id !!}" onclick="removeAnswer(this, 'Text Input')" type="button">
               <i class="fa fa-times"></i>
          </button>
     </div>
</div>
@endif