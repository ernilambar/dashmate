<?php
/**
 * Dashmate_Page
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\Admin;

use Nilambar\Dashmate\Core\Option;
use Nilambar\Dashmate\View\View;

/**
 * Dashmate_Page class.
 *
 * @since 1.0.0
 */
class Dashmate_Page extends Abstract_Admin_Page {

	/**
	 * Initialize the page.
	 *
	 * @since 1.0.0
	 */
	protected function init() {
		$this->page_title = esc_html__( 'Dashmate', 'dashmate' );
		$this->menu_title = apply_filters( 'dashmate_menu_title', esc_html__( 'Dashmate', 'dashmate' ) );
		$this->menu_slug  = 'dashmate';
		$this->icon       = 'dashicons-admin-home';
		$this->position   = 0;
	}

	/**
	 * Render page content.
	 *
	 * @since 1.0.0
	 */
	public function render_content() {
		View::render( 'pages/app' );
	}

	/**
	 * Enqueue assets.
	 *
	 * @since 1.0.0
	 */
	protected function enqueue_assets() {
		$asset_file_name = DASHMATE_DIR . '/assets/index.asset.php';

		if ( file_exists( $asset_file_name ) ) {
			$asset_file = include $asset_file_name;

			// Enqueue WordPress components styles as dependency.
			wp_enqueue_style( 'wp-components' );

			wp_enqueue_style( 'dashmate-main', DASHMATE_URL . '/assets/index.css', [ 'wp-components' ], $asset_file['version'] );
			wp_enqueue_script( 'dashmate-main', DASHMATE_URL . '/assets/index.js', $asset_file['dependencies'], $asset_file['version'], true );
			wp_localize_script(
				'dashmate-main',
				'dashmateApiSettings',
				[
					'nonce'   => wp_create_nonce( 'wp_rest' ),
					'restUrl' => rest_url( 'dashmate/v1/' ),
					'config'  => [
						'maxColumns' => absint( Option::get( 'max_columns' ) ),
					],
				]
			);
		}
	}
}
