<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo Viewer::get_param('title').' | '.APP_TITLE; ?></title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

	<!-- CSS includes -->
	<link rel="stylesheet" href="<?php echo APP_URL; ?>/views/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo APP_URL; ?>/views/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?php echo APP_URL; ?>/views/css/datatables.min.css">
	<link rel="stylesheet" href="<?php echo APP_URL; ?>/views/css/datatables.font-awesome.min.css">
	<link rel="stylesheet" href="<?php echo APP_URL; ?>/views/css/adminlte.min.css">
	<link rel="stylesheet" href="<?php echo APP_URL; ?>/views/css/adminlte.skins.css">
	<link rel="stylesheet" href="<?php echo APP_URL; ?>/views/css/pixull.css">
	<link rel="stylesheet" href="<?php echo APP_URL; ?>/views/css/custom.css">

	<!--[if lt IE 9]>
	<script src="<?php echo APP_URL; ?>/assets/js/html5shiv.min.js"></script>
	<script src="<?php echo APP_URL; ?>/assets/js/respond.min.js"></script>
	<![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-mini<?php if (!AppUser::is_signed_in() && Viewer::get_param('title') != '404') echo ' login-page'; ?>">
<?php
if (AppUser::is_signed_in() && Viewer::get_param('title') != '404') :
	// app user is not signed in and page is not 404, display full header
	?>
	<div class="wrapper">
		<header class="main-header">
			<a href="<?php echo APP_URL; ?>" class="logo">
				<span class="logo-mini"><?php echo APP_TITLE_SHORT; ?></span>
				<span class="logo-lg"><?php echo APP_TITLE; ?></span>
			</a>
			<nav class="navbar navbar-static-top" role="navigation">
				<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
					<span class="sr-only">Toggle navigation</span>
				</a>
				<div class="navbar-custom-menu">
					<ul class="nav navbar-nav">
						<li<?php if (Viewer::get_param('title') == 'Update Profile') echo ' class="active"'; ?>><a href="<?php echo APP_URL; ?>/update_profile"><i class="fa fa-user"></i> <?php echo AppUser::get_username(); ?></a></li>
						<li><a href="<?php echo APP_URL; ?>/sign_out"><i class="fa fa-sign-out"></i> Sign Out</a></li>
					</ul>
				</div>
			</nav>
		</header>

		<aside class="main-sidebar">
			<section class="sidebar">
				<ul class="sidebar-menu">
					<li<?php if (Viewer::get_param('title') == 'Dashboard') echo ' class="active"'; ?>><a href="<?php echo APP_URL; ?>/dashboard"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
					<!-- generator hook (do not remove or modify this comment) -->
					<?php if (AppUser::has_permission('Read Users')): ?><li<?php if (Viewer::get_param('title') == 'Users') echo ' class="active"'; ?>><a href="<?php echo APP_URL; ?>/user"><i class="fa fa-users"></i> <span>Users</span></a></li><?php endif; ?>
				</ul>
			</section>
		</aside>

		<div class="content-wrapper">
	<?php
elseif (!AppUser::is_signed_in() && Viewer::get_param('title') != '404') :
	// app user is not signed in and page is not 404, display login header
	?>
	<div class="login-box">
		<div class="login-logo">
			<a href="<?php echo APP_URL; ?>"><?php echo APP_TITLE; ?></a>
		</div>
		<div class="login-box-body">
	<?php
endif;