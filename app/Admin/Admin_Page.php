<?php
/**
 * Admin_Page
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\Admin;

use Nilambar\Dashmate\Panels\SettingsPanel;
use Nilambar\Optify\Optify;
use Nilambar\Optify\Panel_Manager;

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
		add_action( 'init', [ $this, 'register_settings' ] );
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
				echo '<div class="wrap">';
				echo '<h1>' . esc_html__( 'Dashmate', 'dashmate' ) . '</h1>';
				echo '<div id="dashmate-app">Loading...</div>';
				echo '</wrap>';
			}
		);

		add_options_page(
			esc_html__( 'Dashmate', 'dashmate' ),
			esc_html__( 'Dashmate', 'dashmate' ),
			'manage_options',
			'dashmate-settings',
			function () {
				echo '<div class="wrap">';
				echo '<h1>' . esc_html__( 'Dashmate Settings', 'dashmate' ) . '</h1>';
				Panel_Manager::render_panel(
					'dashmate-settings',
					[
						'show_title' => false,
						'display'    => 'inline',
					]
				);
				echo '</wrap>';
			}
		);
	}

	/**
	 * Register settings
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {
		Optify::register_panel( 'dashmate-settings', SettingsPanel::class );

		Optify::init( 'dashmate', 'v1', DASHMATE_BASE_FILEPATH );
		Optify::load_assets(
			DASHMATE_DIR . '/vendor/ernilambar/optify/',
			DASHMATE_URL . '/vendor/ernilambar/optify/'
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

		if ( ! file_exists( $asset_file_name ) ) {
			return;
		}

		$asset_file = include $asset_file_name;

		wp_enqueue_style(
			'dashmate-main',
			DASHMATE_URL . '/assets/index.css',
			[],
			$asset_file['version']
		);

		wp_enqueue_script(
			'dashmate-main',
			DASHMATE_URL . '/assets/index.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);

		// Localize script with API settings.
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
