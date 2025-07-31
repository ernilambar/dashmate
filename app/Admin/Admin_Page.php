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
		add_action( 'admin_enqueue_scripts', [ $this, 'load_assets' ] );
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
				echo '<div class="wrap"><div id="dashmate-app">Loading...</div></wrap>';
			}
		);
	}

	/**
	 * Load assets.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function load_assets( $hook ) {
		if ( 'dashboard_page_dashmate' !== $hook ) {
			return;
		}

		$asset_file_name = DASHMATE_DIR . '/assets/index.asset.php';
		// nsdump( $asset_file_name );

		if ( ! file_exists( $asset_file_name ) ) {
			return;
		}

		$asset_file = include $asset_file_name;

		wp_enqueue_style(
			'dashmate-main',
			DASHMATE_URL . '/assets/index.css',
			$asset_file['dependencies'],
			$asset_file['version']
		);
		wp_enqueue_script(
			'dashmate-main',
			DASHMATE_URL . '/assets/index.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);
	}
}
