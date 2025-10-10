<?php
/**
 * Secondary_Dashboard_Page
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\Admin;

/**
 * Secondary_Dashboard_Page class.
 *
 * @since 1.0.0
 */
class Secondary_Dashboard_Page extends Abstract_Dashboard_Page {

	/**
	 * Initialize dashboard page properties.
	 *
	 * @since 1.0.0
	 */
	protected function init_properties() {
		$this->page_slug      = 'secondary-board';
		$this->page_title     = esc_html__( 'Secondary Board', 'dashmate' );
		$this->menu_title     = esc_html__( 'Secondary Board', 'dashmate' );
		$this->capability     = 'manage_options';
		$this->menu_icon      = 'dashicons-feedback';
		$this->menu_position  = 0;
		$this->template_name  = 'pages/app';
		$this->dashboard_id   = 'secondary';
		$this->starter_layout = 'homely-regular';
	}
}
