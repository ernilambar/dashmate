<?php
/**
 * Settings_Page
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\Admin;

use Nilambar\Dashmate\View\View;

/**
 * Settings_Page class.
 *
 * @since 1.0.0
 */
class Settings_Page extends Abstract_Admin_Page {

	/**
	 * Initialize the page.
	 *
	 * @since 1.0.0
	 */
	protected function init() {
		$this->page_title  = esc_html__( 'Settings', 'dashmate' );
		$this->menu_title  = esc_html__( 'Settings', 'dashmate' );
		$this->menu_slug   = 'dashmate-settings';
		$this->parent_slug = 'dashmate';
	}

	/**
	 * Render page content.
	 *
	 * @since 1.0.0
	 */
	public function render_content() {
		View::render( 'pages/settings' );
	}
}
