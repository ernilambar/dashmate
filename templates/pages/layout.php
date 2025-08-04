<?php
/**
 * Template: layout
 *
 * @package Dashmate
 */

use Nilambar\Dashmate\Utils\Layout_Utils;

$yaml_output = Layout_Utils::export_to_string();

if ( false === $yaml_output ) {
	return;
}
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<div class="yaml-container">
		<div class="yaml-header">
			<button type="button" class="button button-secondary" onclick="copyYamlContent()">
				Copy YAML
			</button>
		</div>
		<div id="dashmate-layout-content">
			<pre><code><?php echo $yaml_output; ?></code></pre>
		</div>
	</div>
</div>
