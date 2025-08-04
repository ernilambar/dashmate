<?php
/**
 * Layout_Page
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\Admin;

use Nilambar\Dashmate\View\View;

/**
 * Layout_Page class.
 *
 * @since 1.0.0
 */
class Layout_Page extends Abstract_Admin_Page {

	/**
	 * Initialize the page.
	 *
	 * @since 1.0.0
	 */
	protected function init() {
		$this->page_title  = esc_html__( 'Layout', 'dashmate' );
		$this->menu_title  = esc_html__( 'Layout', 'dashmate' );
		$this->menu_slug   = 'dashmate-layout';
		$this->parent_slug = 'dashmate';
	}

	/**
	 * Render page content.
	 *
	 * @since 1.0.0
	 */
	public function render_content() {
		View::render( 'pages/layout' );
	}

	/**
	 * Enqueue assets.
	 *
	 * @since 1.0.0
	 */
	protected function enqueue_assets() {
		$asset_file_name = DASHMATE_DIR . '/assets/layout.asset.php';

		if ( file_exists( $asset_file_name ) ) {
			$asset_file = include $asset_file_name;

			wp_enqueue_style( 'dashmate-layout', DASHMATE_URL . '/assets/layout.css', [], $asset_file['version'] );
			wp_enqueue_script( 'dashmate-layout', DASHMATE_URL . '/assets/layout.js', $asset_file['dependencies'], $asset_file['version'], true );
		}
	}
}
