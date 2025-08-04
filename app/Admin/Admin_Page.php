<?php
/**
 * Admin_Page
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\Admin;

use Nilambar\Dashmate\Layout_Manager;
use Nilambar\Dashmate\Panels\SettingsPanel;
use Nilambar\Dashmate\Utils\YML_Utils;
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
		add_action( 'init', [ $this, 'register_pages' ] );
		add_action( 'init', [ $this, 'register_settings' ] );
		add_action( 'wp_ajax_dashmate_apply_layout', [ $this, 'handle_apply_layout' ] );
	}

	/**
	 * Register settings
	 *
	 * @since 1.0.0
	 */
	public function register_pages() {
			new Dashmate_Page();
			new Layout_Page();
			new Settings_Page();
	}

	/**
	 * Register settings
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {
		$optify = Optify::get_instance( 'dashmate', 'v1', DASHMATE_BASE_FILEPATH );
		$optify->register_panel( 'dashmate-settings', SettingsPanel::class );
		$optify->load_assets(
			DASHMATE_DIR . '/vendor/ernilambar/optify/',
			DASHMATE_URL . '/vendor/ernilambar/optify/'
		);
	}

	/**
	 * Handle apply layout AJAX request.
	 *
	 * @since 1.0.0
	 */
	public function handle_apply_layout() {
		// Check nonce for security.
		if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'dashmate_apply_layout' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'dashmate' ) );
		}

		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'dashmate' ) );
		}

		// Get the selected layout.
		$layout_slug = sanitize_text_field( $_POST['layout'] ?? 'default' );

		try {
			// Get layout details from Layout_Manager.
			$layout = Layout_Manager::get_layout( $layout_slug );

			if ( is_wp_error( $layout ) ) {
				wp_send_json_error( $layout->get_error_message() );
			}

			// Check if layout file exists.
			if ( ! file_exists( $layout['path'] ) ) {
				wp_send_json_error( esc_html__( 'Layout file not found at: ' . $layout['path'], 'dashmate' ) );
			}

			// Load layout data from file.
			$layout_data = YML_Utils::load_from_file( $layout['path'] );

			if ( null === $layout_data ) {
				wp_send_json_error( esc_html__( 'Failed to load layout data.', 'dashmate' ) );
			}

			// Delete the existing option and add the new layout data.
			delete_option( 'dashmate_dashboard_data' );
			$result = add_option( 'dashmate_dashboard_data', $layout_data, '', 'no' );

			if ( false === $result ) {
				wp_send_json_error( esc_html__( 'Failed to update dashboard data.', 'dashmate' ) );
			}

			wp_send_json_success( esc_html__( 'Layout applied successfully! The dashboard has been updated with the selected layout.', 'dashmate' ) );
		} catch ( \Exception $e ) {
			wp_send_json_error( esc_html__( 'An error occurred while applying the layout: ', 'dashmate' ) . $e->getMessage() );
		}
	}
}
