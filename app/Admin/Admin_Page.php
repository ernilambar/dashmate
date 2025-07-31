<?php
/**
 * Admin_Page
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\Admin;

/**
 * Admin_Page class.
 *
 * @since 1.0.0
 */
class Admin_Page {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_page' ] );
	}

	/**
	 * Add page.
	 *
	 * @since 1.0.0
	 */
	public function add_page() {
		add_dashboard_page(
			esc_html__( 'Dashmate', 'dashmate' ),
			esc_html__( 'Dashmate', 'dashmate' ),
			'manage_options',
			'dashmate',
			function () {
				echo 'Hello';}
		);
	}
}
