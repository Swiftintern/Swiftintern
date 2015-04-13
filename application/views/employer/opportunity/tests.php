<?php require_once $dir_employer.'requires/header.php';?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header text-center">All Test Created</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <form class="form-inline" role="form" id="fetch_test">
                <div class="form-group">
                    <div class="input-group">
                        <label class="sr-only">Test</label>
                        <select id="test_id" class="form-control">
                        <?php
                            foreach ($tests as $test) {
                                echo '<option value="'.$test->id.'">'.$test->title.'</option>';
                            }
                        ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">View/Edit</button>
            </form>
            <hr>
            <div class="panel panel-default">
                <div class="panel-heading">
                    Details
                    <div class="pull-right">
                        <div class="btn-group">
                            
                        </div>
                    </div>
                </div>
                <form id="update_test">
                <table class="table text-center">
                    <tbody id="results"><p class="text-center">Please Select an Test and click view details in the above form to view details of opportunity.</p></tbody>
                </table>
                <div id="after_results" class="text-center"></div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once $dir_employer.'requires/footer.php';?>