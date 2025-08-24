<?php
/**
 * Template: app
 *
 * @package Dashmate
 */

use Nilambar\Optify\Panel_Manager;
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<div id="dashmate-wrap-settings">
		<?php
		Panel_Manager::render_panel(
			'dashmate-settings',
			[
				'show_title' => true,
				'display'    => 'modal',
			]
		);
		?>
	</div>
	<div id="dashmate-app"><?php esc_html_e( 'Loading...', 'dashmate' ); ?></div>
</div><!-- .wrap -->
