<?php require_once $dir_employer.'requires/header.php';?>
<div id="page-wrapper">
    <div class="row col-lg-12"><br>
        <div class="panel panel-default">
            <div class="panel-body">
                <h3 class="page-header">Online Competition Details</h3>
                <form class="row" id="internship_step1">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" placeholder="eg. Best PHP Developer" required="">
                        </div>
                        <div class="form-group">
                            <label>Eligibility</label>
                            <input type="text" class="form-control" name="eligibility" placeholder="eg. BE, B.Tech, MCA" required="">
                        </div>
                        <input type="hidden" name="location" value="online">
                        <input type="hidden" name="duration" value="">
                        <input type="hidden" name="category" value="competition">
                        <div class="form-group">
                            <label>LastDate to Participate</label>
                            <input type="date" class="form-control" name="last_date" placeholder="eg. 2014-12-26">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Available Test</label>
                            <select name="type_id" class="form-control" required="">
                            <?php
                                echo '<option value="">Select Test</option>';
                                foreach ($tests as $test) {
                                    $test = Test::find_by_id('id', $test->id);
                                    echo '<option value="'.$test->id.'">'.$test->title.'</option>';
                                }
                                echo "</select>";
                                if (count($test)=='0') {
                                    echo '<small class="help-block">You have not created any test create <a href="employer/create/test" target="_blank">here.</a></small>';
                                }else {
                                    echo '<small class="help-block">The Test will appear when students participate in competition</a></small>';
                                }
                            ?>
                            </select>
                            <input type="hidden" name="application_type" value="test">
                            <input type="hidden" name="type" value="competition">
                        </div>
                        <div class="form-group">
                            <label>Competition Type</label>
                            <div class="form-control">
                                <label class="radio-inline">
                                    <input type="radio" name="payment_mode" value="offline" checked=""> Free
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="payment_mode" value="online" disabled=""> Paid
                                </label>
                            </div>
                            <small class="help-block">The amount received will be transferred to your account, see FAQs</small>
                        </div>
                        <div class="form-group hide" id="payment">
                            <label>Amount(in Rs)</label>
                            <input type="text" class="form-control" name="payment" placeholder="eg. 300">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Details about the Online Competition</label>
                            <textarea class="form-control editor" name="details" rows="3" placeholder="Necessary Details, prize, rank"></textarea>
                        </div>
                        <input type="hidden" name="action" value="create_opportunity">
                        <input type="hidden" name="organization_id" value="<?php echo $company->id;?>">
                        <button type="submit" class="btn btn-primary pull-right" <?php if(count($tests) == '0'){echo 'disabled';}?>><i class="fa fa-check-circle fa-fw"></i> Create Competition</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="panel panel-default hide" id="after_results">
            <div class="panel-body">
                <h3 class="page-header text-center">Created Successfully</h3>
                <p>The Competition you Created is Pending Approval. It will be activated within 12 hours, you may be contacted if any information is missing</p>
                <p>To view participant who have attempted to your Competition Please visit Participants under Opportunity from Left Menu</p>
                <p>To see the Reach of the Opportunity please visit the analytics> opportunity from left menu</p>
                <p>If you find any problem while posting or you dont understand, you can always call at +91-9891048495 we would be happy to hear from you.</p>
                <p>You can always suggest us any improvement or bug, we will fix it for you. <button name="message" value="<?php echo $admin_id;?>" class="btn btn-danger btn-xs"><i class="fa fa-exclamation-circle"></i> Report</button></p>
                <p></p>
                <p class="text-center">
                    <a href="opportunity/id" id="internship_link" class="btn btn-success" target="_blank"><i class="fa fa-eye"></i> View Internship</a>
                    <a href="employer/opportunities" class="btn btn-primary" target="_blank"><i class="fa fa-edit"></i> Edit</a>
                </p>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('input[type=radio]').change(function(e) {
        if (this.value == 'online') {
            $('#payment').removeClass('hide');
        } else{
            $('#payment').addClass('hide');
        };
    });
    $('#internship_step1').submit(function(e) {
        e.preventDefault();
		tinyMCE.triggerSave();
        var organization_id     = $('#organization_id').html();
        var dataString          = $(this).serialize();
        createOpportunity(dataString).success(function (opportunity_id) {
            window.opportunity_id = opportunity_id;
            $('#internship_link').attr('href', 'competition/'+opportunity_id);
            $('#internship_step1').parent('div').addClass('hide');
            $('#after_results').removeClass('hide');
        });
    });
});
</script>
<?php require_once $dir_employer.'requires/footer.php';?>