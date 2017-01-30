<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Welcome to Guestbook</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<style type="text/css">
/*
A custom Bootstrap 3.2 'Google Plus style' theme
from http://bootply.com

This CSS code should follow the 'bootstrap.css'
in your HTML file.

license: MIT
author: bootply.com
*/

@import url(http://fonts.googleapis.com/css?family=Roboto:400);
body {
  background-color:#e0e0e0;
  -webkit-font-smoothing: antialiased;
  font: normal 14px Roboto,arial,sans-serif;
}
.navbar-default {background-color:#f4f4f4;margin-top:50px;border-width:0;z-index:5;}
.navbar-default .navbar-nav > .active > a,.navbar-default .navbar-nav > li:hover > a {border:0 solid #4285f4;border-bottom-width:2px;font-weight:800;background-color:transparent;}
.navbar-default .dropdown-menu {background-color:#ffffff;}
.navbar-default .dropdown-menu li > a {padding-left:30px;}

.header {background-color:#ffffff;border-width:0;}
.header .navbar-collapse {background-color:#ffffff;}
.btn,.form-control,.panel,.list-group,.well {border-radius:1px;box-shadow:0 0 0;}
.form-control {border-color:#d7d7d7;}
.btn-primary {border-color:transparent;}
.btn-primary,.label-primary,.list-group-item.active, .list-group-item.active:hover, .list-group-item.active:focus {background-color:#4285f4;}
.btn-plus {background-color:#ffffff;border-width:1px;border-color:#dddddd;box-shadow:1px 1px 0 #999999;border-radius:3px;color:#666666;text-shadow:0 0 1px #bbbbbb;}
.well,.panel {border-color:#d2d2d2;box-shadow:0 1px 0 #cfcfcf;border-radius:3px;}
.btn-success,.label-success,.progress-bar-success{background-color:#65b045;}
.btn-info,.label-info,.progress-bar-info{background-color:#a0c3ff;border-color:#a0c3ff;}
.btn-danger,.label-danger,.progress-bar-danger{background-color:#dd4b39;}
.btn-warning,.label-warning,.progress-bar-warning{background-color:#f4b400;color:#444444;}

hr {border-color:#ececec;}
button {
 outline: 0;
}
textarea {
 resize: none;
 outline: 0; 
}
.panel .btn i,.btn span{
 color:#666666;
}
.panel .panel-heading {
 background-color:#ffffff;
 font-weight:700;
 font-size:16px;
 color:#262626;
 border-color:#ffffff;
}
.panel .panel-heading a {
 font-weight:400;
 font-size:11px;
}
.panel .panel-default {
 border-color:#cccccc;
}
.panel .panel-thumbnail {
 padding:0;
}
.panel .img-circle {
 width:50px;
 height:50px;
}
.list-group-item:first-child,.list-group-item:last-child {
 border-radius:0;
}
h3,h4,h5 { 
 border:0 solid #efefef; 
 border-bottom-width:1px;
 padding-bottom:10px;
}
.modal-dialog {
 width: 450px;
}
.modal-footer {
 border-width:0;
}
.dropdown-menu {
 background-color:#f4f4f4;
 border-color:#f0f0f0;
 border-radius:0;
 margin-top:-1px;
}
/* end theme */

/* template layout*/
#subnav {
 position:fixed;
 width:100%;
}

@media (max-width: 768px) {
 #subnav {
  padding-top: 6px;
 }
}

#main {
 padding-top:120px;
}
	</style>
</head>
<body>

<div class="container">
	<h1>Welcome to Guestbook!</h1>

	<?php
	if ($errors) {
		?>
		<div class="alert alert-info alert-dismissable">
			<?= $errors ?>
		</div>
		<p>Click <a href="#" onclick="window.history.go(-1);">back</a> to try again...</p>
		<?php
	}
	?>

	<div class="col-md-12">
		<?php foreach ($entries as $entry) { 
			$date = new DateTime($entry->guestbook_created, new DateTimeZone($entry->guestbook_timezone));
			?>
			<div class="panel panel-default">
				<div class="panel-heading" id="entry_<?= $entry->guestbook_id ?>"><a href="guestbook/delete?id=<?= $entry->guestbook_id ?>" class="pull-right">delete</a> <h4><?= $entry->guestbook_name ?></h4></div>
				<div class="panel-body">
					<p class="date">Signed on <?= $date->format("M j, Y P") ?></p>
					<hr>
					<div class="comment">
						<?= $entry->guestbook_comment ?>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>

	<?php if ($content) { ?>
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading"><h4>Sign our Guestbook!</h4></div>
			<div class="panel-body">
				<div class="well">
					<?= $content ?>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
</div>

<div class="modal-footer">
	<p class="right">Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?></p>
</div>
<script type="text/JavaScript">
$(document).ready(function() {
	$('.panel-heading a').on('click', function () {
		event.preventDefault();
		if (confirm("Are you sure you want to remove this entry?")) {
			$.ajax({url: "guestbook/remove?id=" + $(this)[0].id.substring(6)});
			$(this).parent().fadeOut(1000, function() { $(this).remove() });
		}
		return false;
	});
});
</script>
</body>
</html>