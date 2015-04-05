<?php require_once $dir_public.'requires/header.php';?>
<!-- the middle contents -->
<div class="container">
	<article class="row">
		<nav><ol class="breadcrumb">
				<li><a href="home">Home</a></li>
				<li><a href="companies">Companies</a></li>
				<li><a href="organization/<?php echo urlencode($organization->name);?>/<?php echo $organization->id;?>"><?php echo $organization->name; ?></a></li>
				<li><a href="organization/<?php echo urlencode($organization->name);?>/<?php echo $organization->id;?>#experience">Papers</a></li>
				<li class="active"><?php echo $experience->title;?></li>
		</ol></nav>
		
		<h2 class="sub-heading"><?php echo $experience->title;?></h2>
		
		<nav>
			<ul class="pager">
			<?php
				if($previous){
					echo '<li class="previous"><a href="experience/'.urlencode($experience->title).'/'.$previous.'"><span aria-hidden="true">&larr;</span> Previous</a></li>';
				}
				if($next){
					echo '<li class="next"><a href="experience/'.urlencode($experience->title).'/'.$next.'">Next <span aria-hidden="true">&rarr;</span></a></li>';
				}
			?>
			</ul>
		</nav>
		<div class="panel panel-default">
			<div class="panel-body">
				<?php echo $experience->details;?>
			</div>
		</div>
	</article>
</div>
<?php require_once $dir_public.'requires/footer.php';?>