<?php
if (AppUser::is_signed_in() && Viewer::get_param('title') != '404') :
	// app user is not signed in and page is not 404, display full footer
	?>
		</div>

		<footer class="main-footer">
			<div class="pull-right hidden-xs">
				v<?php echo APP_VERSION; ?>
			</div>
			&copy;<?php echo date('Y', time()).' '.APP_TITLE; ?>
		</footer>
	</div>
	<?php
elseif (!AppUser::is_signed_in() && Viewer::get_param('title') != '404') :
	// app user is not signed in and page is not 404, display login footer
	?>
		</div>
	</div>
	<?php
endif;
?>

<!-- JS includes -->
<script src="<?php echo APP_URL; ?>/views/js/jquery.min.js"></script>
<script src="<?php echo APP_URL; ?>/views/js/bootstrap.min.js"></script>
<script src="<?php echo APP_URL; ?>/views/js/datatables.min.js"></script>
<script src="<?php echo APP_URL; ?>/views/js/app.min.js"></script>
<script src="<?php echo APP_URL; ?>/views/js/pixull.js"></script>
<script src="<?php echo APP_URL; ?>/views/js/custom.js"></script>
</body>
</html>