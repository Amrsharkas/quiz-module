<style type="text/css">
    ul {
    list-style: none;
}

.menu > li {
    float: left;
}
.menu button {
    border: 0;
    background: blue;
    cursor: pointer;
    float:right;
    position: absolute;
    right: 20px;
}
.menu button:hover,
.menu button:focus {
    outline: 0;
    text-decoration: underline;
}

.submenu {
    display: none;
    /*position: absolute;*/
    padding: 10px;
}
.menu button:focus + .submenu,
.submenu:hover {
    display: block;
}
</style>
<div class="m-portlet m-portlet--mobile">
                            <div class="m-portlet__head">
                                <div class="m-portlet__head-caption">
                                    <div class="m-portlet__head-title">
                                        <h3 class="m-portlet__head-text">
                                            Questions
                                            
                                        </h3>
                                    </div>
                                </div>
                                <div class="m-portlet__head-tools">
                                    <ul class="m-portlet__nav">
                                        <li class="m-portlet__nav-item">
                                            <div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
                                                <a href="javascript:void(0)" class="
    m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl m-dropdown__toggle">
                                                    <i class="la la-plus m--hide"></i>
                                                    <i class="fa fa-ellipsis-h m--font-brand"></i>
                                                </a>
                                                <div class="m-dropdown__wrapper">
                                                    <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 17.4062px;"></span>
                                                    <div class="m-dropdown__inner">
                                                        <div class="m-dropdown__body">
                                                            <div class="m-dropdown__content">
                                                                <ul class="m-nav">
                                                                    <li class="m-nav__section m-nav__section--first">
                                                                        <span class="m-nav__section-text">
                                                                            Export
                                                                        </span>
                                                                    </li>
                                                                    <li class="m-nav__item">
                                                                        <a href="" class="m-nav__link excel_export">
                                                                            <i class="m-nav__link-icon fa-file-excel-o fa"></i>
                                                                            <span class="m-nav__link-text">
                                                                                Excel
                                                                            </span>
                                                                        </a>
                                                                    </li>
                                                                    <li class="m-nav__item">
                                                                        <a href="" class="m-nav__link pdf_export">
                                                                            <i class="m-nav__link-icon fa fa-file-pdf-o"></i>
                                                                            <span class="m-nav__link-text">
                                                                                PDF
                                                                            </span>
                                                                        </a>
                                                                    </li>
                                                                    
                                                                    
                                                                    
                                                                    
                                                                    <li class="m-nav__separator m-nav__separator--fit m--hide"></li>
                                                                    <li class="m-nav__item m--hide">
                                                                        <a href="#" class="btn btn-outline-danger m-btn m-btn--pill m-btn--wide btn-sm">
                                                                            Submit
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="m-portlet__body">
                                <!--begin: Search Form -->
                                <ul class="menu">
                                    <li>
                                        {{-- <button class=" btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill">
                                            <span>
                                                <i class="fa fa-plus"></i>
                                                <span>
                                                    Add Question
                                                </span>
                                            </span>

                                        </button> --}}
                                        <a class="btn btn-primary" href="/admin/questions/init">Add Question</a>
                                        <ul class="submenu">
                                            @foreach($questoin_types as $question_type)
                                            <li><a href="{{route('admin.questions.init',['type_id'=>$question_type->id])}}">{{$question_type->type}}</a></li>
                                            @endforeach
                                        </ul>
                                    </li>
                                </ul>
                                <!--end: Search Form -->
                                <!--begin: Datatable -->
                              <table class="table table-striped table-bordered table-hover  quiz_generator " id="quiz_generator" width="100%">
        <thead>
        <tr class="tr-head">

            <th valign="middle">
                Name
            </th>
            
            <th valign="middle">
                Weight
            </th>
            <th valign="middle">
                Type
            </th>
            <th valign="middle">
                Difficulty
            </th>
            
          <th valign="middle">
                Actions
          </th>

        </tr>
        </thead>
        <tbody>
            @foreach ($questions as $question)
            <tr>
                <td>{!! $question ->question_text !!}</td>
                <td>{!! $question ->weight !!}</td>
                <td>{!! $question ->type ->type !!}</td>
                <td>{!! $question ->difficulty() ->first() ->type   !!}</td>
                <td><a href="/admin/questions/edit/{!! $question ->id !!}" class="btn btn-primary btn-sm">Edit</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    
                                <!--end: Datatable -->
                            </div>
                        </div>
