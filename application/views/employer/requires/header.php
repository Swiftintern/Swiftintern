<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $title;?></title>

    <base href="<?php echo $baseUrl;?>" />
    <!-- Core CSS - Include with every page -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <script type="text/javascript" src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
	<link href="<?php echo $cdn;?>plugins/bootstrap-tour-0.10.1/build/css/bootstrap-tour.min.css" rel="stylesheet">

    <!-- Plugin CSS  -->
    <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">

    <!-- SB Admin CSS - Include with every page -->
    <link href="<?php echo $cdn;?>css/sb-admin.css" rel="stylesheet">
	
	<script src="http://tinymce.cachefly.net/4.1/tinymce.min.js"></script>
	<script type="text/javascript">
	tinymce.init({
		selector: ".editor",
		plugins: [
			"advlist autolink lists link image charmap print preview anchor",
			"searchreplace visualblocks code fullscreen",
			"insertdatetime media table contextmenu"
		],
		toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
		autosave_ask_before_unload: false,
		max_height: 200,
		min_height: 160,
		height : 180
	});
	</script>

</head>

<body>
    <div id="wrapper">
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="employer">
                    <span id="organization_id" class="hidden"><?php echo $_SESSION['organization']['id'];?></span>
                    <i class="fa fa-briefcase fa-fw"></i><?php echo $_SESSION['organization']['name'];?>
                </a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> <?php echo $session->name;?>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="employer/faq"><i class="fa fa-thumbs-up fa-fw"></i> Help</a></li>
                        <li><a href="employer"><i class="fa fa-gear fa-fw"></i> Dashboard</a></li>
                        <li class="divider"></li>
                        <li><a href="logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>

        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="side-menu">
                    <li><a href="employer/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                    <li>
                        <a href="#"><i class="fa fa-user fa-fw"></i> Profile<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a href="employer/edit/profile">Edit</a></li>
                            <li><a href="employer/members">Members</a></li>
							<li><a href="organization/<?php echo urlencode($_SESSION['organization']['name']);?>/<?php echo $_SESSION['organization']['id'];?>" target="_blank">View Company</a></li>
                            <li><a href="employer/edit/company">Edit Company</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-graduation-cap fa-fw"></i> Internship<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a href="employer/create/internship">Create</a></li>
							<li><a href="employer/opportunities">View/Edit</a></li>
							<li><a href="employer/applicants">Applicants</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-trophy fa-fw"></i> Competition<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a href="employer/create/competition">Create</a></li>
                            <li><a href="employer/opportunities">View/Edit</a></li>
							<li><a href="employer/participants">Participants</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-clock-o fa-fw"></i> Test<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a href="employer/create/test">Create Test</a></li>
							<li><a href="employer/tests">View/Edit</a></li>
							<li><a href="employer/participants">Submissions</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> Analytics<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a href="employer/analytics/opportunity">Opportunity</a></li>
                            <li><a href="employer/analytics/profile">Profile</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-desktop fa-fw"></i> Integration<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a href="employer/integration/website">Website</a></li>
                        </ul>
                    </li>
                    <li><a href="employer/messages"><i class="fa fa-envelope fa-fw"></i> Messages</a></li>
                    <li><a href="employer/faq"><i class="fa fa-thumbs-up fa-fw"></i> Help</a></li>
                </ul>
            </div>
        </nav>
<?php require_once $dir_employer.'modal/message.php';?>