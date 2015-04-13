<?php require_once $dir_public.'requires/header.php';?>
<section class="container">
	<div class="row">
		<h1 class="page-header text-center">Member Invitation</h1>
		<div class="col-md-6">

			<div class="media">
				<div class="media-left">
					<i class="fa fa-graduation-cap fa-4x"></i>
				</div>
				<div class="media-body">
					<h4 class="media-heading">Post Internship</h4>
					<p>Now you will be able to post opportunity for all students and even create online test for competition or practice</p>
				</div>
			</div>						
			<br>
			<br>
			<div class="media">
				<div class="media-left">
					<i class="fa fa-line-chart fa-4x"></i>
				</div>
				<div class="media-body">
					<h4 class="media-heading">Advanced Anlytics</h4>
					<p>See reports of engagement of students towards any opportunity through a institute. weekly, monthly or yearly and also get reports to email.</p>
				</div>
			</div>
			<br>
			<div class="media">
				<div class="media-left">
					<i class="fa fa-support fa-4x"></i>
				</div>
				<div class="media-body">
					<h4 class="media-heading">Support</h4>
					<p>Get direct support from our Team we are always ready to help you as we can, for any further support you can email at info@swiftintern.com or call at +91-9891048495</p>
				</div>
			</div>
		</div>
		<div id="col-md-6">
		<span id="user_id" class="hide"><?php echo $member->user_id;?></span>
		<?php if ($member->user_id == '0') {?>
			<form id="user_register">
				<legend>User Details</legend>
				<div class="row">
		  			<div class="col-xs-12 col-md-6">
				    	<div class="form-group">
					    <label for="">Name</label>
					    <input type="text" name="name" class="form-control" required="">
					  	</div>
			    	</div>
			    	<div class="col-xs-12 col-md-6">
				    	<div class="form-group">
					    <label for="">Create Password</label>
					    <input type="password" name="password" class="form-control" required="">
					  	</div>
			    	</div>
			    	<input type="hidden" name="email" class="form-control">
		    	</div>
		  		<div class="row">
		  			<div class="col-xs-12 col-md-6">
				    	<div class="form-group">
					    <label for="">Phone (Optional)</label>
					    <input type="tel" name="phone" class="form-control">
					  	</div>
			    	</div>
			    	<div class="col-xs-12 col-md-6">
				    	<div class="form-group">
					    <label for="">Email</label>
					    <input type="email" name="email" class="form-control" value="">
					  	</div>
			    	</div>
		    	</div>
		    </form>
		<?php } ?>
		    <form id="accept_invite">
				<legend>Member Details</legend>
		    	<div class="row">
		  			<div class="col-xs-12 col-md-6">
				    	<div class="form-group">
					    <label for="">Designation</label>
					    <input type="text" name="designation" class="form-control" required="">
					  	</div>
			    	</div>
			    	<div class="col-xs-12 col-md-6">
				    	<div class="form-group">
					    <label for="">Authority</label>
					    <input type="text" name="authority" class="form-control" value="<?php echo $member->authority;?>" readonly>
					  	</div>
			    	</div>
		    	</div>
		    	<br>
		    	<input type="hidden" name="member_id" value="<?php echo $member->id;?>">
		    	<button type="submit" class="btn btn-primary">Save</button>
			</form>
		</div>
	</div>
</section>
<script type="text/javascript">
$(document).ready(function() {
	$('#accept_invite').submit(function(e) {
		var dataString2 = $(this).serialize();
		e.preventDefault();
		var user_id = $('#user_id').html();
		if (user_id != '') {
			var dataString = $('#user_register').serialize();
	        updateUserProfile(dataString).success(function (user) {
	            window.user_id = user.id;
	        });
		}
		inviteMember(dataString2).success(function (member) {
			bootbox.alert('Thanks for registering. Click Ok to login', function () {
				window.location.href = 'login';
			});
		});
	});
});
</script>
<?php require_once $dir_public.'requires/footer.php';?>