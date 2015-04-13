<?php require_once $dir_employer.'requires/header.php';?>
<div id="page-wrapper">
    <div class="row col-lg-12">
        <br>
        <form class="row update_user_profile">
            <legend>Basic Details</legend>
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                    <label for="">Full Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo $user->name;?>" required="">
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                    <label for="">Phone</label>
                    <input type="tel" name="phone" class="form-control" value="<?php echo $user->phone;?>" required="">
                    </div>
                </div>
            </div>
            <input type="hidden" name="user_id" value="<?php echo $session->user_id;?>">
            <button type="submit" class="btn btn-primary pull-right">Save</button>
        </form>

        <legend>Change Password</legend>
        <form class="form-inline update_user_profile" role="form">
            <div class="form-group">
                <div class="input-group">
                    <label class="sr-only">Opportunity</label>
                    <input type="text" name="password" class="form-control" placeholder="New Password">
                </div>
            </div>
            <input type="hidden" name="user_id" value="<?php echo $session->user_id;?>">
            <button type="submit" class="btn btn-primary">Update Password</button>
        </form>
    
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() { 
    $('#profile_edit').submit(function() { 
        var dataString = $("#company_edit").serialize();
        updateOrganization(dataString).success(function (data) {
            bootbox.alert(data);
        })
        return false; 
    });

    $('.update_user_profile').submit(function(e) {
        e.preventDefault();
        var dataString = $(this).serialize();
        updateUserProfile(dataString).success(function (data) {
            bootbox.alert(data);
        });
    });
}); 
</script>
<?php require_once $dir_employer.'requires/footer.php';?>