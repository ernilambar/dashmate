<?php
/**
 * Loader
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\Boot;

use Nilambar\Dashmate\Admin\Admin_Page;
use Nilambar\Dashmate\API\API_Main;
use Nilambar\Dashmate\Widget_Initializer;

/**
 * Loader class.
 *
 * @since 1.0.0
 */
class Loader {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'plugin_action_links_' . DASHMATE_BASE_FILENAME, [ $this, 'customize_plugin_links' ] );

		add_filter(
			'linkit_menu_pages',
			function ( $pages ) {
				$pages [] = 'dashboard_page_dashmate';
				return $pages;
			}
		);

		// Initialize widgets after init hook to ensure text domain is loaded.
		add_action( 'init', [ $this, 'init_widgets' ] );

		new API_Main();
		new Admin_Page();
	}

	/**
	 * Initialize widgets.
	 *
	 * @since 1.0.0
	 */
	public function init_widgets() {
		Widget_Initializer::init();
	}

	/**
	 * Customize plugin action links.
	 *
	 * @since 1.0.0
	 *
	 * @param array $actions Action links.
	 * @return array Modified action links.
	 */
	public function customize_plugin_links( $actions ) {
		return array_merge(
			[
				'settings' => '<a href="' . esc_url( add_query_arg( [ 'page' => 'dashmate-settings' ], admin_url( 'options-general.php' ) ) ) . '">' . esc_html__( 'Settings', 'dashmate' ) . '</a>',
			],
			$actions
		);
	}
}
