<?php require_once $dir_employer.'requires/header.php';?>
<?php require_once $dir_employer.'modal/add_options.php';?>
<div id="page-wrapper">
    <div class="row col-lg-12"><br>
    <ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
        <li class="active"><a href="#basic" role="tab" data-toggle="tab">Test</a></li>
        <li><a href="#add_questions" role="tab" data-toggle="tab">Questions</a></li>
    </ul>

    <div class="tab-content">

        <div class="tab-pane active" id="basic">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h3 class="page-header">Test Details</h3>
                    <form class="row" id="save_test">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" class="form-control" name="title" placeholder="eg. Beginner Aptitude Test" required="">
                            </div>
                            <div class="form-group">
                                <label>Syllabus</label>
                                <textarea class="form-control" name="syllabus" placeholder="eg. basic maths, Triangles" required=""></textarea>
                            </div>
                            <div class="form-group">
                                <label>Subject</label>
                                <input type="text" class="form-control" name="subject" placeholder="eg. Maths">
                            </div>
                        </div>
                        <!-- /.col-lg-6 (nested) -->
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Time Limit</label>
                                <input type="text" class="form-control" name="time_limit" placeholder="eg. 2:00:00">
                            </div>
                            
                            <div class="form-group">
                                <label>Test Created for</label>
                                <div class="form-control">
                                    <label class="radio-inline">
                                        <input type="radio" name="type" value="internship" checked=""> Internship Application
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="type" value="competition"> Competition
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="type" value="practice"> Student's Practice
                                    </label>
                                </div>
                                <small class="help-block">Test type to be used in opportunity. eg for practice test all students would be able to give test.</small>
                            </div>
                            <div class="form-group">
                                <label for="">Photo(size less than 1 MB)</label>
                                <input type="file" id="imageInput" name="photo[]" class="form-control" required="">
                                <div id="output"></div>
                            </div>
                        </div>
                        <input type="hidden" name="action" value="save_test">
                        <input type="hidden" name="organization_id" value="<?php echo $company->id;?>">
                        <button type="submit" class="btn btn-primary pull-right" id="nextbtn">Create Test and Add Questions <i class="fa fa-arrow-circle-o-right"></i></button>
                    </form>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="add_questions">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h3 class="page-header">Add Questions</h3>
                    <div id="results">
                        <p>Please complete step 1 to proceed.</p>
                    </div>
                    <hr>
                    <div id="after_results" class="hide">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Question</th>
                                    <th>Answer Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="allquestions">
                                <tr>
                                    <form class="save_question">
                                    <td>0</td>
                                    <td><input type="text" name="question" class="form-control" placeholder="New Question..."></td>
                                    <td>
                                        <select name="type" class="form-control">
                                            <option value="">Select Answer Type</option>
                                            <option value="options">Options</option>
                                            <option value="text">Text Answer</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Add Question</button>
                                    </td>
                                    <input type="hidden" name="test_id" value="">
                                    <input type="hidden" name="action" value="save_question">
                                    </form>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo $cdn;?>plugins/jquery/jquery.form.min.js"></script>
<script type="text/javascript">
var count = 0;
$(document).ready(function() {
    $('#save_test').submit(function(e) {
        e.preventDefault();
        var dataString = $(this).serialize();
        $(this).ajaxSubmit({
            target: '#output',
            beforeSubmit: beforeSubmit,
            url: 'application/controllers/employer/action.php',
            type: 'POST',
            data: dataString,
            resetForm: true,
            success: function (test_id) {
                window.test_id = test_id;
                $('#results').html('<p>Your Test has been Successfully created now add questions.</p>')
                $('#nextbtn').addClass('disabled');
                $('#after_results').removeClass('hide');
                $('.nav-tabs a[href="#add_questions"]').tab('show');
            }
        });
    });

    $('.save_question').submit(function(e) {
        e.preventDefault();
        var dataString = $(this).serialize();
        createQuestion(dataString.concat('&test_id='+test_id)).success(function (ques) {
            $('#results').html('Question Created Successfully');
            var type = ques.type;
            switch(ques.type){
                case 'text':
                    break;
                case 'options':
                    var type = type.concat('<button name="addoption" value="'+ques.id+'" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Add Option</button>');
                    break;
            }
            $('#allquestions').append('<tr><form class="save_question"><td>'+ ++count +'</td><td><input type="text" name="question" id="'+ques.id+'" class="form-control" value="'+ques.question+'"></td><td>'+type+'</td><td><button name="delete" value="question" id="'+ques.id+'" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Delete</button><button type="submit" class="btn btn-success btn-xs"><i class="fa fa-check-circle"></i> Save</button></td><input type="hidden" name="ques_id" value="'+ques.id+'"><input type="hidden" name="action" value="save_question"></form></tr>');
        });
    });
    
    $(document).on("click",'button[name="addoption"]',function(e){
        var ques_id     = this.value;
        var element     = this;
        window.element  = element;
        $('input[name="ques_id"]').attr('value', ques_id);
        $('#add_options').modal('show');
    });

    $(document).on("click",'button[name="deleteQues"]',function(e){
        var ques_id     = this.value;
        var element     = this;
        window.element  = element;
        $('input[name="ques_id"]').attr('value', ques_id);
        $('#add_options').modal('show');
    });

});
</script>
<?php require_once $dir_employer.'requires/footer.php';?>