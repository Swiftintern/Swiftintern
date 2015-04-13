<?php require_once $dir_employer.'requires/header.php';?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header text-center">Competition Participants</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <form class="form-inline text-center" role="form" id="fetch_participants">
                <div class="form-group">
                    <div class="input-group">
                        <label class="sr-only">Competition</label>
                        <select id="test_id" class="form-control">
                        <?php
                            foreach ($competitions as $competition) {
                                echo '<option value="'.$competition->type_id.'">'.$competition->title.'</option>';
                            }
                        ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">View Participants</button>
            </form>
            <hr>
            <div class="panel panel-default">
                <div class="panel-body" id="result_status">
                    <p>Please select the Test from above to see participants.</p>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Score</th>
                            <th>Time Taken</th>
                            <th>Questions Attempted</th>
                            <th>Given at</th>
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
</script>
<?php require_once $dir_employer.'requires/footer.php';?>