<?php
/**
 * Template: layout
 *
 * @package Dashmate
 */

use Nilambar\Dashmate\Utils\Layout_Utils;

$output = Layout_Utils::get_layout_json();

if ( is_wp_error( $output ) ) {
	return;
}
?>

<div class="layout-container">
	<div class="layout-header">
		<button type="button" class="button button-secondary" onclick="copyLayoutContent()">
			Copy Layout
		</button>
	</div>
	<div id="dashmate-layout-content">
		<pre><code><?php echo $output; ?></code></pre>
	</div>
</div>
