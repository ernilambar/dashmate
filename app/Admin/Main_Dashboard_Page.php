<?php
/**
 * Main_Dashboard_Page
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\Admin;

/**
 * Main_Dashboard_Page class.
 *
 * @since 1.0.0
 */
class Main_Dashboard_Page extends Abstract_Dashboard_Page {

	/**
	 * Initialize dashboard page properties.
	 *
	 * @since 1.0.0
	 */
	protected function init_properties() {
		$this->page_slug     = 'dashmate';
		$this->page_title    = esc_html__( 'Dashmate', 'dashmate' );
		$this->menu_title    = esc_html__( 'Dashmate', 'dashmate' );
		$this->capability    = 'manage_options';
		$this->menu_icon     = 'dashicons-admin-home';
		$this->menu_position = 0;
		$this->template_name = 'pages/app';
		$this->dashboard_id  = 'main';
	}
}
