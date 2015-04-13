<?php require_once $dir_employer.'requires/header.php';?>
<?php require_once $dir_employer.'modal/message.php';?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">All Applicants for Internships</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <form class="form-inline" role="form" id="fetch_applications">
                <div class="form-group">
                    <div class="input-group">
                        <label class="sr-only">Opportunity</label>
                        <select id="opportunity_id" class="form-control">
                        <?php
                            foreach ($opportunities as $opportunity) {
                                $opportunity = Opportunity::find_by_id('id', $opportunity->id);
                                echo '<option value="'.$opportunity->id.'">'.$opportunity->title.'</option>';
                            }
                        ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <label class="sr-only">Status</label>
                        <select id="status" class="form-control">
                            <option value="applied">Applied</option>
                            <option value="shortlist">Shortlisted</option>
                            <option value="selected">Selected</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">View Applications</button>
            </form>
            <hr>
            <div class="panel panel-default">
                <div class="panel-body" id="result_status">
                    <p>Please select the opportunity from above to see applications.</p>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Application</th>
                            <th>Applied at</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="results">
                        
                    </tbody>
                </table>
                <div id="after_results" class="text-center"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
var page = '1';
var per_page = '20';
$(document).ready(function() {
	$('.modal-dialog').css('width', '800px');
});
</script>
<?php require_once $dir_employer.'requires/footer.php';?>