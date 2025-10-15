<?php
/**
 * Dashmate
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Core;

use Nilambar\Dashmate\API\API_Main;

/**
 * Main Dashmate class.
 *
 * @since 1.0.0
 */
class Dashmate {

	/**
	 * Whether the package has been initialized.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	private static $initialized = false;

	/**
	 * Asset base path.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private static $asset_path = '';

	/**
	 * Asset base URL.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private static $asset_url = '';

	/**
	 * Initialize the Dashmate package.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		if ( self::$initialized ) {
			return;
		}

		// Register REST API.
		new API_Main();

		// Initialize widgets.
		add_action( 'init', [ __CLASS__, 'init_widgets' ] );

		self::$initialized = true;
	}

	/**
	 * Load assets with custom paths.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path Asset base path.
	 * @param string $url  Asset base URL.
	 */
	public static function load_assets( string $path, string $url ) {
		self::$asset_path = $path;
		self::$asset_url  = $url;

		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
	}

	/**
	 * Initialize widgets.
	 *
	 * @since 1.0.0
	 */
	public static function init_widgets() {
		Widget_Initializer::init();
	}

	/**
	 * Enqueue assets.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook Current admin page hook.
	 */
	public static function enqueue_assets( string $hook ) {
		if ( empty( self::$asset_url ) || empty( self::$asset_path ) ) {
			return;
		}

		$asset_file_path = self::$asset_path . '/assets/index.asset.php';

		if ( ! file_exists( $asset_file_path ) ) {
			return;
		}

		$asset_file = include $asset_file_path;

		// Enqueue WordPress components styles as dependency.
		wp_enqueue_style( 'wp-components' );

		// Enqueue dashboard styles.
		wp_enqueue_style(
			'dashmate',
			self::$asset_url . '/assets/index.css',
			[ 'wp-components' ],
			$asset_file['version']
		);

		// Enqueue dashboard scripts.
		wp_enqueue_script(
			'dashmate',
			self::$asset_url . '/assets/index.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);

		// Localize script with API settings.
		$api_settings = [
			'nonce'   => wp_create_nonce( 'wp_rest' ),
			'restUrl' => rest_url( 'dashmate/v1/' ),
		];

		wp_localize_script( 'dashmate', 'dashmateApiSettings', $api_settings );
	}

	/**
	 * Check if package is initialized.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if initialized, false otherwise.
	 */
	public static function is_initialized(): bool {
		return self::$initialized;
	}

	/**
	 * Get asset base path.
	 *
	 * @since 1.0.0
	 *
	 * @return string Asset base path.
	 */
	public static function get_asset_path(): string {
		return self::$asset_path;
	}

	/**
	 * Get asset base URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string Asset base URL.
	 */
	public static function get_asset_url(): string {
		return self::$asset_url;
	}

	/**
	 * Get package directory path.
	 *
	 * @since 1.0.0
	 *
	 * @return string Package directory path.
	 */
	public static function get_package_dir(): string {
		return dirname( __DIR__ );
	}
}
