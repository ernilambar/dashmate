<?php
/**
 * Admin_Page
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\Admin;

use Nilambar\Dashmate\Core\Option;
use Nilambar\Dashmate\Panels\SettingsPanel;
use Nilambar\Dashmate\Utils\YML_Utils;
use Nilambar\Dashmate\View\View;
use Nilambar\Dashmate\Widget_Initializer;
use Nilambar\Optify\Optify;

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
		add_action( 'wp_ajax_dashmate_reset_layout', [ $this, 'handle_reset_layout' ] );
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
				View::render( 'pages/app' );
			},
			0
		);

		add_options_page(
			esc_html__( 'Dashmate', 'dashmate' ),
			esc_html__( 'Dashmate', 'dashmate' ),
			'manage_options',
			'dashmate-settings',
			function () {
				View::render( 'pages/settings' );
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
		// Load assets for dashboard page.
		if ( 'dashboard_page_dashmate' === $hook ) {
			$this->load_dashboard_assets();
		}

		// Load assets for settings page.
		if ( 'settings_page_dashmate-settings' === $hook ) {
			$this->load_settings_assets();
		}
	}

	/**
	 * Load dashboard assets.
	 *
	 * @since 1.0.0
	 */
	private function load_dashboard_assets() {
		$asset_file_name = DASHMATE_DIR . '/assets/index.asset.php';

		if ( file_exists( $asset_file_name ) ) {
			$asset_file = include $asset_file_name;

			wp_enqueue_style( 'dashmate-main', DASHMATE_URL . '/assets/index.css', [], $asset_file['version'] );
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

	/**
	 * Load settings page assets.
	 *
	 * @since 1.0.0
	 */
	private function load_settings_assets() {
		$asset_file_name = DASHMATE_DIR . '/assets/settings.asset.php';

		if ( file_exists( $asset_file_name ) ) {
			$asset_file = include $asset_file_name;

			wp_enqueue_style( 'dashmate-settings', DASHMATE_URL . '/assets/settings.css', [], $asset_file['version'] );
			wp_enqueue_script( 'dashmate-settings', DASHMATE_URL . '/assets/settings.js', $asset_file['dependencies'], $asset_file['version'], true );
			wp_localize_script(
				'dashmate-settings',
				'dashmateSettings',
				[
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'dashmate_reset_layout' ),
					'strings' => [
						'confirmReset' => esc_html__( 'Are you sure you want to reset the layout? This will override all current widget positions and settings.', 'dashmate' ),
						'resetting'    => esc_html__( 'Resetting layout...', 'dashmate' ),
						'success'      => esc_html__( 'Layout reset successfully!', 'dashmate' ),
						'error'        => esc_html__( 'An error occurred while resetting the layout.', 'dashmate' ),
					],
				]
			);
		}
	}

	/**
	 * Handle reset layout AJAX request.
	 *
	 * @since 1.0.0
	 */
	public function handle_reset_layout() {
		// Check nonce for security.
		if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'dashmate_reset_layout' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'dashmate' ) );
		}

		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'dashmate' ) );
		}

		try {
			// Import layout from default file.
			$default_file = Widget_Initializer::get_default_layout_file_path();

			if ( ! file_exists( $default_file ) ) {
				wp_send_json_error( esc_html__( 'Default layout file not found at: ' . $default_file, 'dashmate' ) );
			}

			// Load layout data from default file.
			$layout_data = YML_Utils::load_from_file( $default_file );

			if ( null === $layout_data ) {
				wp_send_json_error( esc_html__( 'Failed to load default layout data.', 'dashmate' ) );
			}

			// Delete the existing option and add the new layout data.
			delete_option( 'dashmate_dashboard_data' );
			$result = add_option( 'dashmate_dashboard_data', $layout_data, '', 'no' );

			if ( false === $result ) {
				wp_send_json_error( esc_html__( 'Failed to update dashboard data.', 'dashmate' ) );
			}

			wp_send_json_success( esc_html__( 'Layout reset successfully! The dashboard has been restored to its default configuration.', 'dashmate' ) );
		} catch ( \Exception $e ) {
			wp_send_json_error( esc_html__( 'An error occurred while resetting the layout: ', 'dashmate' ) . $e->getMessage() );
		}
	}
}
