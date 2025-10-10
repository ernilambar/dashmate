<?php
/**
 * Template: app
 *
 * @package Dashmate
 */

?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<!-- Multiple Dashboard Sections -->
	<div id="dashboard-main" class="dashmate-dashboard" data-slug="main">
		<h2>Main Dashboard</h2>
		<div class="dashboard-content"><?php esc_html_e( 'Loading...', 'dashmate' ); ?></div>
	</div>

	<div id="dashboard-analytics" class="dashmate-dashboard" data-slug="analytics">
		<h2>Analytics Dashboard</h2>
		<div class="dashboard-content"><?php esc_html_e( 'Loading...', 'dashmate' ); ?></div>
	</div>

	<div id="dashboard-reports" class="dashmate-dashboard" data-slug="reports">
		<h2>Reports Dashboard</h2>
		<div class="dashboard-content"><?php esc_html_e( 'Loading...', 'dashmate' ); ?></div>
	</div>

	<!-- React App with Multiple Dashboard Configs -->
	<div id="dashmate-app" data-dashboards='<?php echo json_encode( $dashboard_configs ); ?>'>
		<?php esc_html_e( 'Loading...', 'dashmate' ); ?>
	</div>
</div><!-- .wrap -->
