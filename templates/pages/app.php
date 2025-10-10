<?php
/**
 * Template: app
 *
 * @package Dashmate
 */

$dashboard_id = $data['dashboard_id'] ?? '';
?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<div id="dashmate-app" data-dashboard-id="<?php echo esc_attr( $dashboard_id ); ?>"><?php esc_html_e( 'Loading...', 'dashmate' ); ?></div>
</div><!-- .wrap -->
