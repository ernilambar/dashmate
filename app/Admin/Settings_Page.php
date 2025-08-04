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

	/**
	 * Enqueue assets.
	 *
	 * @since 1.0.0
	 */
	protected function enqueue_assets() {
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
					'nonce'   => wp_create_nonce( 'dashmate_apply_layout' ),
					'strings' => [
						'confirmApply' => esc_html__( 'Are you sure you want to apply this layout? This will override all current widget positions and settings.', 'dashmate' ),
						'applying'     => esc_html__( 'Applying layout...', 'dashmate' ),
						'success'      => esc_html__( 'Layout applied successfully!', 'dashmate' ),
						'error'        => esc_html__( 'An error occurred while applying the layout.', 'dashmate' ),
						'applyLayout'  => esc_html__( 'Apply Layout', 'dashmate' ),
						'selectLayout' => esc_html__( 'Please select a layout before applying.', 'dashmate' ),
						'unknownError' => esc_html__( 'Unknown error occurred.', 'dashmate' ),
					],
				]
			);
		}
	}
}
