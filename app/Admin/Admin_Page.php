<?php
/**
 * Admin_Page
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\Admin;

use Nilambar\Dashmate\Panels\SettingsPanel;
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
	}

	/**
	 * Register settings
	 *
	 * @since 1.0.0
	 */
	public function register_pages() {
		new Dashmate_Page();
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
}
