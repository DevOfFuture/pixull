<?php

Viewer::set_param('title', '404');
Viewer::view('app_header');

?>

<section class="content">
	<div class="error-page">
		<h2 class="headline text-yellow" style="margin-top: 0;">404</h2>
		<div class="error-content">
			<h3 style="padding-top: 10px;"><i class="fa fa-warning text-yellow"></i> Oops! Page not found.</h3>
			<p>We could not find the page you were looking for.</p>
			<a href="#" class="btn btn-warning" onclick="window.history.go(-1); return false;"><i class="fa fa-arrow-left"></i> Go Back</a>
		</div>
	</div>
</section>

<?php

Viewer::view('app_footer');