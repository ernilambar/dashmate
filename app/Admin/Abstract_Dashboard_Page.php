<?php
/**
 * Abstract_Dashboard_Page
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\Admin;

use Nilambar\Dashmate\Models\Dashboard_Model;
use Nilambar\Dashmate\View\View;

/**
 * Abstract Dashboard Page class.
 *
 * This abstract class provides a foundation for creating dashboard pages
 * that can be extended to support multiple dashboard pages in the future.
 * It handles common dashboard functionality like page registration,
 * asset enqueuing, and provides hooks for customization.
 *
 * @since 1.0.0
 */
abstract class Abstract_Dashboard_Page {

	/**
	 * Dashboard page slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $page_slug;

	/**
	 * Dashboard page title.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $page_title;

	/**
	 * Dashboard menu title.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $menu_title;

	/**
	 * Required capability.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $capability;

	/**
	 * Menu icon.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $menu_icon;

	/**
	 * Menu position.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	protected $menu_position;

	/**
	 * Dashboard template name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $template_name;

	/**
	 * Dashboard ID for API calls.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $dashboard_id;

	/**
	 * Starter layout to use when no dashboard data exists.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $starter_layout;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->init_properties();
		$this->init_hooks();
		$this->register_starter_layout();
	}

	/**
	 * Initialize dashboard page properties.
	 *
	 * @since 1.0.0
	 */
	abstract protected function init_properties();

	/**
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 */
	protected function init_hooks() {
		add_action( 'admin_menu', [ $this, 'register_dashboard_page' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_dashboard_assets' ] );
	}

	/**
	 * Registers the dashboard page.
	 *
	 * @since 1.0.0
	 */
	public function register_dashboard_page() {
		$page_title = apply_filters( 'dashmate_dashboard_page_title', $this->page_title, $this->page_slug, $this->dashboard_id );
		$menu_title = apply_filters( 'dashmate_dashboard_menu_title', $this->menu_title, $this->page_slug, $this->dashboard_id );

		add_menu_page(
			$page_title,
			$menu_title,
			$this->capability,
			$this->page_slug,
			[ $this, 'render_dashboard_content' ],
			$this->menu_icon,
			$this->menu_position
		);
	}

	/**
	 * Renders dashboard content.
	 *
	 * @since 1.0.0
	 */
	public function render_dashboard_content() {
		$template_data = $this->get_template_data();
		$this->render_template( $this->template_name, $template_data );
	}

	/**
	 * Get template data for rendering.
	 *
	 * @since 1.0.0
	 *
	 * @return array Template data.
	 */
	protected function get_template_data() {
		return apply_filters(
			'dashmate_dashboard_template_data',
			[
				'dashboard_id' => $this->dashboard_id,
				'page_slug'    => $this->page_slug,
			],
			$this->page_slug,
			$this->dashboard_id
		);
	}

	/**
	 * Render template.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_name Template name.
	 * @param array  $data         Template data.
	 */
	protected function render_template( $template_name, $data = [] ) {
		View::render( $template_name, $data );
	}

	/**
	 * Enqueue assets for dashboard pages.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook Hook name.
	 */
	public function enqueue_dashboard_assets( $hook ) {
		$asset_file_path = DASHMATE_DIR . '/assets/index.asset.php';

		if ( ! file_exists( $asset_file_path ) ) {
			return;
		}

		$asset_file = include $asset_file_path;

		// Enqueue WordPress components styles as dependency.
		wp_enqueue_style( 'wp-components' );

		// Enqueue dashboard styles.
		wp_enqueue_style(
			'dashmate-dashboard',
			DASHMATE_URL . '/assets/index.css',
			[ 'wp-components' ],
			$asset_file['version']
		);

		// Enqueue dashboard scripts.
		wp_enqueue_script(
			'dashmate-dashboard',
			DASHMATE_URL . '/assets/index.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);

		// Localize script with API settings.
		$api_settings = apply_filters(
			'dashmate_dashboard_api_settings',
			[
				'nonce'       => wp_create_nonce( 'wp_rest' ),
				'restUrl'     => rest_url( 'dashmate/v1/' ),
				'dashboardId' => $this->dashboard_id,
				'pageSlug'    => $this->page_slug,
			],
			$this->page_slug,
			$this->dashboard_id
		);

		wp_localize_script( 'dashmate-dashboard', 'dashmateApiSettings', $api_settings );
	}


	/**
	 * Get dashboard page slug.
	 *
	 * @since 1.0.0
	 *
	 * @return string Dashboard page slug.
	 */
	public function get_page_slug() {
		return $this->page_slug;
	}

	/**
	 * Get dashboard page title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Dashboard page title.
	 */
	public function get_page_title() {
		return $this->page_title;
	}

	/**
	 * Get dashboard menu title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Dashboard menu title.
	 */
	public function get_menu_title() {
		return $this->menu_title;
	}

	/**
	 * Get required capability.
	 *
	 * @since 1.0.0
	 *
	 * @return string Required capability.
	 */
	public function get_capability() {
		return $this->capability;
	}

	/**
	 * Get menu icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Menu icon.
	 */
	public function get_menu_icon() {
		return $this->menu_icon;
	}

	/**
	 * Get menu position.
	 *
	 * @since 1.0.0
	 *
	 * @return int Menu position.
	 */
	public function get_menu_position() {
		return $this->menu_position;
	}

	/**
	 * Get dashboard template name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Dashboard template name.
	 */
	public function get_template_name() {
		return $this->template_name;
	}

	/**
	 * Get dashboard ID.
	 *
	 * @since 1.0.0
	 *
	 * @return string Dashboard ID.
	 */
	public function get_dashboard_id() {
		return $this->dashboard_id;
	}

	/**
	 * Get starter layout.
	 *
	 * @since 1.0.0
	 *
	 * @return string Starter layout.
	 */
	public function get_starter_layout() {
		return $this->starter_layout;
	}

	/**
	 * Register starter layout with the model.
	 *
	 * @since 1.0.0
	 */
	private function register_starter_layout() {
		if ( ! empty( $this->starter_layout ) && 'main' === $this->dashboard_id ) {
			Dashboard_Model::set_starter_layout( $this->starter_layout );
		}
	}
}
