<?php

Viewer::set_param('title', '404');
Viewer::view('app_header');

?>

<section class="content">
	<div class="error-page">
		<h2 class="headline text-yellow" style="margin-top: 0;">404</h2>
		<div class="error-content">
			<h3 style="padding-top: 10px;"><img src="http://zupimages.net/up/16/47/enlb.jpg" alt="ghost" title="ghost"></h3><br/> 
			 <h3>Oops! Page not found.</h3>
			<p>We could not find the page you were looking for.</p>
			<a href="#" class="btn btn-warning" onclick="window.history.go(-1); return false;"><i class="fa fa-arrow-left"></i> Go Back</a>
		</div>
	</div>
</section>

<?php

Viewer::view('app_footer');
