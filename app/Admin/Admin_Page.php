<?php
/**
 * Admin_Page
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\Admin;

use Nilambar\Dashmate\View\View;

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
		add_action( 'admin_menu', [ $this, 'register_page' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	/**
	 * Registers the page.
	 *
	 * @since 1.0.0
	 */
	public function register_page() {
		add_menu_page(
			apply_filters( 'dashmate_page_title', esc_html__( 'Dashmate', 'dashmate' ) ),
			apply_filters( 'dashmate_menu_title', esc_html__( 'Dashmate', 'dashmate' ) ),
			'manage_options',
			'dashmate',
			[ $this, 'render_page_content' ],
			'dashicons-admin-home',
			0
		);
	}

	/**
	 * Renders page content.
	 *
	 * @since 1.0.0
	 */
	public function render_page_content() {
		View::render( 'pages/app' );
	}

	/**
	 * Enqueue assets.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook Hook name.
	 */
	public function enqueue_assets( $hook ) {
		if ( 'toplevel_page_dashmate' !== $hook ) {
			return;
		}

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
				]
			);
		}
	}
}
