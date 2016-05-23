<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit; ?>
<div class="wrap">
	<h2>
		<?php _e( 'LB Discount Options', 'lb-discount' ); ?>
	</h2>

	<?php if( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == 'true' ){ ?>
		<div id="setting-error-settings_updated" class="updated settings-error">
		<p><strong><?php _e( 'Saved settings', 'lb-discount' ); ?></strong></p></div>
	<?php } ?>

	<form method='post' action='options.php'>
		<?php
			settings_fields( 'lb_discount_settings' );
			do_settings_sections( 'lb_discount_options' );
			submit_button();
		?>
	</form>
</div>
