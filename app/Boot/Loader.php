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
use Nilambar\Optify\Panel_Manager;

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
		add_action( 'dashmate_action_after_page_title', [ $this, 'add_settings_app' ] );

		add_filter(
			'linkit_menu_bar_pages',
			function ( $pages ) {
				return array_merge( $pages, [ 'toplevel_page_dashmate', 'dashmate_page_dashmate-layouts' ] );
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
	 * Add settings app.
	 *
	 * @since 1.0.0
	 *
	 * @param string $menu_slug Menu slug.
	 */
	public function add_settings_app( $menu_slug ) {
		if ( 'dashmate' === $menu_slug ) {
			echo '<div id="dashmate-wrap-settings">';

			Panel_Manager::render_panel(
				'dashmate-settings',
				[
					'show_title' => true,
					'display'    => 'modal',
				]
			);

			echo '</div>';
		}
	}
}
