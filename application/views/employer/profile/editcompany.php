<?php require_once $dir_employer.'requires/header.php';?>
<div id="page-wrapper">
    <div class="row col-lg-12">
        <br>
        <form class="row" id="MyUploadForm">
            <legend>Company Logo</legend>
            <div class="col-xs-12 col-md-6">
                <div class="form-group">
                    <div id="output">
                        <img src="<?php echo $photo->image_path_thumb();?>" alt="<?php echo $company->name;?>" width=100>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-6">
                <div class="form-group">
                <label for="">Change Logo</label>
                <input type="file" id="imageInput" name="photo" class="form-control">
                </div>
            </div>
        </form>
    	<form id="company_edit">
            <legend>Company/Organization Details</legend>
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                    <label for="">Company Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo $company->name;?>" required="">
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                    <label for="">Website</label>
                    <input type="text" name="website" class="form-control" value="<?php echo $company->website;?>">
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                    <label for="">Company Facebook Page</label>
                    <input type="text" name="fbpage" class="form-control" value="<?php echo $company->fbpage;?>" placeholder="https://www.facebook.con/yourcompany">
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                    <label for="">Country</label>
                    <input type="text" name="country" class="form-control" value="<?php echo $company->country;?>">
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                    <label>Number of Employee</label>
                    <select name="number_employee" class="form-control" value="<?php echo $company->number_employee;?>">
                        <option value="" selected="selected">-- No of employees --</option>
                        <option value="0 to 5">0-5</option>
                        <option value="5 to 25">5-25</option>
                        <option value="25 to 100">25-100</option>
                        <option value="100 to 500">100-500</option>
                        <option value="500 to 1000">500-1000</option>
                        <option value="grater than 1000">>1000</option>
                    </select>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                    <label for="">Address</label>
                    <textarea name="address" class="form-control"><?php echo $company->address;?></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                    <label>Sector</label>
                    <select name="sector" class="form-control" value="<?php echo $company->sector;?>">
                        <?php require_once $dir_employer.'requires/company_sector.php';?>
                    </select>
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                    <label for="">Enquiry Phone</label>
                    <input type="tel" name="phone" class="form-control" value="<?php echo $company->phone;?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <div class="form-group">
                    <label for="">About</label>
                    <textarea name="about" class="form-control"><?php echo strip_tags($company->about);?></textarea>
                    </div>
                </div>
            </div>
            <br>
            <input type="hidden" name="organization_id" value="<?php echo $company->id;?>">
            <button type="submit" class="btn btn-primary pull-right">Save</button>
        </form>
    </div>
</div>
<script type="text/javascript" src="<?php echo $cdn;?>plugins/jquery/jquery.form.min.js"></script>
<script type="text/javascript">
$(document).ready(function() { 
    var options = { 
        target: '#output',   // target element(s) to be updated with server response 
        beforeSubmit: beforeSubmit,  // pre-submit callback
        url: 'application/controllers/public/action.php',
        type: 'POST',
        resetForm: true        // reset the form after successful submit 
    };

    $("#MyUploadForm").submit(function(){
        $(this).ajaxSubmit(options);
        return false;
    });

    $('#company_edit').submit(function() { 
        var dataString = $("#company_edit").serialize();
        updateOrganization(dataString).success(function (data) {
            bootbox.alert(data);
        })
        return false; 
    });
});
</script>
<?php require_once $dir_employer.'requires/footer.php';?>