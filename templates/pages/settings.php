<?php
/**
 * Template: settings
 *
 * @package Dashmate
 */

use Nilambar\Dashmate\Layout_Manager;
use Nilambar\Optify\Panel_Manager;

$all_layouts = Layout_Manager::get_layouts();
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

	<!-- Add apply layout section. -->
	<div class="dashmate-apply-layout-section">
		<h2><?php echo esc_html__( 'Apply Layout', 'dashmate' ); ?></h2>
		<p><?php echo esc_html__( 'Select a layout from the dropdown below and click the button to apply it to your dashboard. This will override all current widget positions and settings.', 'dashmate' ); ?></p>
		<p>
			<select id="dashmate-layout">
				<?php foreach ( $all_layouts as $layout_key => $layout ) : ?>
					<option value="<?php echo esc_attr( $layout_key ); ?>"><?php echo esc_html( $layout['title'] ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<button type="button" id="dashmate-apply-layout-btn" class="button button-secondary">
			<?php echo esc_html__( 'Apply Layout', 'dashmate' ); ?>
		</button>
		<div id="dashmate-apply-status"></div>
	</div>
</div>
