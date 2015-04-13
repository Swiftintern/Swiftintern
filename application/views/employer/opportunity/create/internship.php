<?php require_once $dir_employer.'requires/header.php';?>
<?php require_once $dir_student.'requires/datalist.php';?>
<div id="page-wrapper">
    <div class="row col-lg-12"><br>
    <ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
        <li class="active"><a href="#basic" role="tab" data-toggle="tab">Step 1</a></li>
        <li><a href="#application_type" role="tab" data-toggle="tab">Step 2</a></li>
    </ul>

    <div class="tab-content">

        <div class="tab-pane active" id="basic">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h3 class="page-header">Basic Details</h3>
                    <form class="row" id="internship_step1">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" class="form-control" name="title" placeholder="eg. Software Development">
                            </div>
                            <div class="form-group">
                                <label>Eligibility</label>
                                <input type="text" list="eligibility_skills" class="form-control" name="eligibility" placeholder="eg. BE, B.Tech, MCA">
								<datalist id="eligibility_skills">
								<?php
									foreach ($allskills as $skill) {
										echo '<option value="'.$skill.'">';
									}
								?>
								</datalist>
                            </div>
                            <div class="form-group">
                                <label>Location</label>
                                <input type="text" list="intern_location" class="form-control" name="location" placeholder="eg. Delhi, Work from home">
								<datalist id="intern_location">
								<?php
									foreach ($alllocations as $loc) {
										echo '<option value="'.ucfirst($loc).'">';
									}
								?>
								</datalist>
                            </div>
                            <div class="form-group">
                                <label>Duration</label>
                                <input type="text" class="form-control" name="duration" placeholder="eg. 2nd December, 2014 to 10th January, 2014">
                            </div>
                        </div>
                        <!-- /.col-lg-6 (nested) -->
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Stipend</label>
                                <input type="text" class="form-control" name="payment" placeholder="eg. 10,000">
                            </div>
                            <div class="form-group">
                                <label>Last Date</label>
                                <input type="date" class="form-control" name="last_date" placeholder="eg. 2014-12-26">
                            </div>
                            <div class="form-group">
                                <label>Category</label>
                                <select class="form-control" name="category">
									<option>--Select Category---</option>
                                <?php
									foreach($allcategories as $category){
										echo '<option value="'.$category.'">'.$category.'</option>';
									}
								?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Student's Response</label>
                                <div class="form-control">
                                    <label class="radio-inline">
                                        <input type="radio" name="application_type" value="resume" checked=""> Resume
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="application_type" value="question"> Question
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="application_type" value="test"> Test
                                    </label>
                                </div>
                                <small class="help-block">By selecting resume  means students will provide resume while applying for internship</small>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Details about the Internship</label>
                                <textarea class="form-control editor" name="details" rows="3" placeholder="Skills Requred, work, role"></textarea>
                            </div>
                            <input type="hidden" name="type" value="internship">
                            <input type="hidden" name="action" value="create_opportunity">
                            <input type="hidden" name="organization_id" value="<?php echo $company->id;?>">
                            <button type="submit" class="btn btn-primary pull-right" id="nextbtn">Next <i class="fa fa-arrow-circle-o-right fa-fw"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="application_type">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h3 class="page-header">Application Type Details</h3>
                    <div id="results">
                        <p>Please complete step 1 to proceed.</p>
                    </div>

                    <div id="resume" class="hide">
                        <p>You will see students resume when they apply for internship.</p>
                    </div>

                    <div id="question" class="hide">
                        <p>Ask any question you want students to answer</p>
                        <form class="row" id="create_question">
                            <div class="form-group col-md-8">
                                <input type="text" name="question" class="form-control" required="">
                            </div>
                            <input type="hidden" name="action" value="save_question">
                            <input type="hidden" name="type" value="text">
                            <button type="submit" class="btn btn-primary col-md-4">Finalize and Submit</button>
                        </form>
                    </div>

                    <div id="test" class="hide">
                        <p>Select the test you want students to appear while applying to the internship.</p>
                        <p>You will be able to see the marks, questions attempted, score, rank, time taken by them.</p><hr>
                        <form id="select_type_id" class="form-inline" role="form">
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="sr-only">Select Test</label>
                                    <select name="type_id" class="form-control">
                                    <?php
                                        echo '<option value="">Select Test</option>';
                                        foreach ($tests as $test) {
                                            $test = Test::find_by_id('id', $test->id);
                                            echo '<option value="'.$test->id.'">'.$test->title.'</option>';
                                        }
                                        echo "</select>";
                                        if (count($test)=='0') {
                                    ?>
                                    <p>You have not created any test till now to create a test visit create >> test from the left menu.</p>
                                    <p>With Students Application type as test you will be able to see the marks, questions attempted, score, rank, time taken by them.</p>
                                    <p>You can complete creating this internship anytime for any problem you can contact us at +91-9891048495 we would be happy to hear from you or <button name="message" value="1" class="btn btn-danger btn-xs">Report</button></p>
                                    <p class="text-center">
                                        <a href="employer/create/test" class="btn btn-info" target="_blank">Create Test</a>
                                        <a href="employer/opportunities" class="btn btn-primary" target="_blank">Edit Internship</a>
                                    </p>
                                    <?php }?>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" <?php if(count($tests) == '0'){echo 'disabled';}?>>Finalize and Submit</button>
                        </form>
                    </div>

                    <div id="after_results" class="hide">
                        <p>The Internhip Opportunity you Created is Pending Approval. It will be activated within 12 hours, you may be contacted if any information is missing</p>
                        <p>To view any applicants who have applied to your Internship Please visit Applicants under Opportunity from Left Menu</p>
                        <p>To see the Reach of the Internship Opportunity please visit the analytics> opportunity from left menu</p>
                        <p>If you find any problem while posting internship or you dont understand, you can always call at +91-9891048495 we would be happy to hear from you.</p>
                        <p>You can always suggest us any improvement or bug, we will fix it for you. <button name="message" value="1" class="btn btn-danger btn-xs">Report</button></p>
                        <p class="text-center">
                            <a href="opportunity/'+opportunity_id+'" id="internship_link" class="btn btn-success" target="_blank">View Internship</a>
                            <a href="employer/opportunities" class="btn btn-primary" target="_blank">Edit</a>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('#internship_step1').submit(function(e) {
		tinyMCE.triggerSave();
        $('#internship_step1').addClass('disabled');
        e.preventDefault();
        var organization_id     = $('#organization_id').html();
        var application_type    = $('input[name=application_type]').val();
        var dataString          = $(this).serialize();
        createOpportunity(dataString).success(function (opportunity_id) {
            window.opportunity_id = opportunity_id;
            $('#results').html('');
            $('#'+application_type).removeClass('hide');
            $('.nav-tabs a[href="#application_type"]').tab('show');
            switch(application_type){
                case 'resume':
                    $('#internship_link').attr('href', 'opportunity/'+opportunity_id);
                    $('#after_results').removeClass('hide');
                    break;
                default:
                    $('#results').html('Please complete the following.');
                    break;
            }
        })
    });

    $('#create_question').submit(function(e) {
        e.preventDefault();
        var dataString = $('#create_question').serialize();
        createQuestion(dataString).success(function (question) {
            updateOpportunity('type_id='+question.id+'&opportunity_id='+opportunity_id).success(function (data) {
                $('#question').addClass('hide');
                $('#results').html('');
                $('#internship_link').attr('href', 'opportunity/'+opportunity_id);
                $('#after_results').removeClass('hide');
            });
        });
    });

    $('#select_type_id').submit(function(e) {
        e.preventDefault();
        var dataString = $('#select_type_id').serialize();
        updateOpportunity(dataString+'&opportunity_id='+opportunity_id).success(function (data) {
            $('#results').html('');
            $('#test').addClass('hide');
            $('#internship_link').attr('href', 'opportunity/'+opportunity_id);
            $('#after_results').removeClass('hide');
        });
    });
});
</script>
<?php require_once $dir_employer.'requires/footer.php';?>