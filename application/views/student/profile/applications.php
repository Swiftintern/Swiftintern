<?php require_once $dir_student.'requires/header.php';?>
<section class="container">
	<div class="row">

			<ol class="breadcrumb">
				<li><a href="home">Home</a></li>
				<li><a href="student">Profile</a></li>
				<li class="active">Messages</li>
			</ol>
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-check-square fa-fw"></i> Applications
					<div class="pull-right">
						<div class="btn-group">
							<button name="message" value="1" class="btn btn-danger btn-xs"><i class="fa fa-exclamation-circle fa-fw"></i> Report</button>
						</div>
					</div>
				</div>
				<table class="table">
					<thead>
						<tr>
							<th>Opportunity</th>
							<th>Status</th>
							<th>Applied</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
					<?php
						if ($applications) {
							foreach ($applications as $application) {
								$application 	= Application::find_by_id('id', $application->id);
								$opportunity 	= Opportunity::find_by_id('id', $application->opportunity_id);
								$organization 	= Organization::find_by_id('id', $opportunity->organization_id);
								echo '<tr>
										<td><a href="'.urlencode($opportunity->title).'/'.$opportunity->id.'" target="_blank">'.$opportunity->title.'</a></td>
										<td>'.$application->status.'</td>
										<td>'.only_date($application->created).'</td>
										<td>
											<button name="application" value="'.$application->id.'" class="btn btn-info btn-xs">View</button>
											<button name="message" value="'.$opportunity->user_id.'" class="btn btn-warning btn-xs"><i class="fa fa-envelope fa-fw"></i> Message Company</button> 
										</td>
									</tr>';
							}
						} else {
							echo '<p class="text-center">You have not applied to any Opportunity/internship available. Do apply to Internship/Opportunity mathching your Profile.</p>';
						}
					?>
					</tbody>
				</table>
			</div>
	</div>
</section>
<?php require_once $dir_public.'requires/footer.php';?>