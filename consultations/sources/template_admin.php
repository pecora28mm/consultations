<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Template_Admin extends Template {
	function header() {
		return '
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>'.$GLOBALS['config']['name'].'</title>
		<link href="medias/css/bootstrap.min.css" rel="stylesheet">
		<link href="medias/css/styles.css" rel="stylesheet">
		<link href="medias/css/styles_admin.css" rel="stylesheet">
		</head>
 	<body>';
	}

	function navigation() {
		return '
<nav class="navbar navbar-default" role="navigation">
	<div class="container-fluid">
	<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="admin.php"><strong>'.$GLOBALS['config']['name'].'</strong></a>
		</div>

		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
<!-- 			<a class="navbar-brand" href="index.php?page=passage.php">Nouveau passage</a> -->			
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>';
	}
}
