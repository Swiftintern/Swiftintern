<?php require_once $dir_employer.'requires/header.php';?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">All Opportunities Created <small>(Internship, Competition, etc)</small></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <form class="form-inline" role="form" id="fetch_opportunity">
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
                <button type="submit" class="btn btn-primary">View Details</button>
            </form>
            <hr>
            <div class="panel panel-default">
                <div class="panel-heading">
                    Details
                    <div class="pull-right">
                        <div class="btn-group">
                            <button name="message" value="1" class="btn btn-danger btn-xs"><i class="fa fa-exclamation-circle"></i> Report</button>
                            <a href="employer/analytics/opportunity" target="_blank" class="btn btn-info btn-xs"><i class="fa fa-bar-chart-o fa-fw"></i> Analytics</a> 
                            <a href="employer/applicants" target="_blank" class="btn btn-success btn-xs"><i class="fa fa-users fa-fw"></i> Applicants</a>
                        </div>
                    </div>
                </div>
                <form id="update_opportunity">
                <table class="table text-center">
                    <tbody id="results"><p class="text-center">Please Select an Opportunity and click view details in the above form to view details of opportunity.</p></tbody>
                </table>
                <div id="after_results" class="text-center"></div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once $dir_employer.'requires/footer.php';?>