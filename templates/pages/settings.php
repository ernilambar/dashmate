<?php
/**
 * Template: settings
 *
 * @package Dashmate
 */

use Nilambar\Optify\Panel_Manager;

?>
<div class="wrap">
	<h1><?php echo esc_html__( 'Dashmate Settings', 'dashmate' ); ?></h1>
	<?php
	Panel_Manager::render_panel(
		'dashmate-settings',
		[
			'show_title' => false,
			'display'    => 'inline',
		]
	);
	?>

	<!-- Add reset layout section. -->
	<div class="dashmate-reset-layout-section">
		<h2><?php echo esc_html__( 'Reset Layout', 'dashmate' ); ?></h2>
		<p><?php echo esc_html__( 'Click the button below to reset the dashboard layout to the default configuration. This will override all current widget positions and settings.', 'dashmate' ); ?></p>
		<button type="button" id="dashmate-reset-layout-btn" class="button button-secondary">
			<?php echo esc_html__( 'Reset Layout', 'dashmate' ); ?>
		</button>
		<div id="dashmate-reset-status"></div>
	</div>
</div>
