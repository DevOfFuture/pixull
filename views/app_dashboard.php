<?php

Viewer::set_param('title', 'Dashboard');
Viewer::view('app_header');

?>

<section class="content-header">
	<h1><?php echo Viewer::get_param('title'); ?></h1>
</section>

<section class="content">
	(charts etc. to go here)
</section>

<?php

Viewer::view('app_footer');