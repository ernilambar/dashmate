<?php
/**
 * Layouts_Page
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\Admin;

use Nilambar\Dashmate\View\View;

/**
 * Layouts_Page class.
 *
 * @since 1.0.0
 */
class Layouts_Page extends Abstract_Admin_Page {

	/**
	 * Initialize the page.
	 *
	 * @since 1.0.0
	 */
	protected function init() {
		$this->page_title  = esc_html__( 'Layouts', 'dashmate' );
		$this->menu_title  = esc_html__( 'Layouts', 'dashmate' );
		$this->menu_slug   = 'dashmate-layouts';
		$this->parent_slug = 'dashmate';
	}

	/**
	 * Render page content.
	 *
	 * @since 1.0.0
	 */
	public function render_content() {
		View::render( 'pages/layouts' );
	}

	/**
	 * Enqueue assets.
	 *
	 * @since 1.0.0
	 */
	protected function enqueue_assets() {
		$asset_file_name = DASHMATE_DIR . '/assets/layouts.asset.php';

		if ( file_exists( $asset_file_name ) ) {
			$asset_file = include $asset_file_name;

			wp_enqueue_style( 'dashmate-layouts', DASHMATE_URL . '/assets/layouts.css', [], $asset_file['version'] );
			wp_enqueue_script( 'dashmate-layouts', DASHMATE_URL . '/assets/layouts.js', $asset_file['dependencies'], $asset_file['version'], true );

			wp_localize_script(
				'dashmate-layouts',
				'dashmateLayouts',
				[
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'restUrl' => rest_url( 'dashmate/v1/' ),
					'nonce'   => wp_create_nonce( 'dashmate_layouts' ),
					'isDebug' => defined( 'WP_DEBUG' ) && WP_DEBUG,
					'strings' => [
						'loading'           => esc_html__( 'Loading layouts...', 'dashmate' ),
						'loadingData'       => esc_html__( 'Loading layout data...', 'dashmate' ),
						'applying'          => esc_html__( 'Applying...', 'dashmate' ),
						'applyLayout'       => esc_html__( 'Apply Layout', 'dashmate' ),
						'copyJson'          => esc_html__( 'Copy JSON', 'dashmate' ),
						'layoutJsonContent' => esc_html__( 'Layout Content', 'dashmate' ),
						'selectLayout'      => esc_html__( 'Select Layout:', 'dashmate' ),
						'noDataAvailable'   => esc_html__( 'No layout data available', 'dashmate' ),
						'copiedToClipboard' => esc_html__( 'Layout JSON copied to clipboard!', 'dashmate' ),
						'failedToCopy'      => esc_html__( 'Failed to copy to clipboard', 'dashmate' ),
						'currentReadOnly'   => esc_html__( 'Cannot apply current layout as it is read-only.', 'dashmate' ),
						'failedToFetch'     => esc_html__( 'Failed to fetch layouts', 'dashmate' ),
						'failedToFetchData' => esc_html__( 'Failed to fetch layout data', 'dashmate' ),
						'failedToApply'     => esc_html__( 'Failed to apply layout', 'dashmate' ),
						'unknownError'      => esc_html__( 'Unknown error occurred.', 'dashmate' ),
					],
				]
			);
		}
	}
}
