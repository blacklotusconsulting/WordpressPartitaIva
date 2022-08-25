<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h1><?php esc_html_e( wp_partita_iva_NAME ); ?> Impostazioni</h1>

	<form method="post" action="options.php">
		<?php settings_fields( 'wp_partita_iva_settings' ); ?>
		<?php do_settings_sections( 'wp_partita_iva_settings' ); ?>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button-primary" value="<?php esc_html_e( 'Save Changes' ); ?>" />
		</p>
	</form>
</div> <!-- .wrap -->
