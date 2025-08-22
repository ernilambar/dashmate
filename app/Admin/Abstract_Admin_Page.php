<?php
/**
 * Abstract_Admin_Page
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\Admin;

/**
 * Abstract admin page class.
 *
 * @since 1.0.0
 */
abstract class Abstract_Admin_Page {

	/**
	 * Page title.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $page_title;

	/**
	 * Menu title.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $menu_title;

	/**
	 * Menu slug.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $menu_slug;

	/**
	 * Required capability.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $capability = 'manage_options';

	/**
	 * Icon.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $icon = '';

	/**
	 * Menu position.
	 *
	 * @since 1.0.0
	 * @var int|null
	 */
	protected $position = null;

	/**
	 * Parent slug for submenu.
	 *
	 * @since 1.0.0
	 * @var string|null
	 */
	protected $parent_slug = null;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->init();
		add_action( 'admin_menu', [ $this, 'register' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'maybe_enqueue_assets' ] );
	}

	/**
	 * Initialize the page.
	 *
	 * @since 1.0.0
	 */
	abstract protected function init();

	/**
	 * Render the page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		$this->render_header();
		$this->render_content();
		$this->render_footer();
	}

	/**
	 * Render page header.
	 *
	 * @since 1.0.0
	 */
	public function render_header() {
		echo '<div class="wrap">';

		// Get the default admin page title.
		$default_title = get_admin_page_title();

		/**
		 * Filters the admin page title displayed in the page.
		 *
		 * @since 1.0.0
		 *
		 * @param string $title     The page title to display.
		 * @param string $menu_slug The menu slug of the current page.
		 * @param self   $page      The current admin page instance.
		 */
		$page_title = apply_filters( 'dashmate_admin_page_title', $default_title, $this->menu_slug, $this );

		do_action( 'dashmate_action_before_page_title', $this->menu_slug, $this );

		echo '<h1>' . esc_html( $page_title ) . '</h1>';

		do_action( 'dashmate_action_after_page_title', $this->menu_slug, $this );
	}

	/**
	 * Render page footer.
	 *
	 * @since 1.0.0
	 */
	public function render_footer() {
		echo '</div>';
	}

	/**
	 * Register menu/submenu page.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		if ( $this->parent_slug ) {
			add_submenu_page(
				$this->parent_slug,
				$this->page_title,
				$this->menu_title,
				$this->capability,
				$this->menu_slug,
				[ $this, 'render' ]
			);
		} else {
			add_menu_page(
				$this->page_title,
				$this->menu_title,
				$this->capability,
				$this->menu_slug,
				[ $this, 'render' ],
				$this->icon,
				$this->position
			);
		}
	}

	/**
	 * Render page content.
	 *
	 * @since 1.0.0
	 */
	abstract public function render_content();

	/**
	 * Maybe enqueue assets.
	 *
	 * @since 1.0.0
	 * @param string $hook Current admin page hook.
	 */
	public function maybe_enqueue_assets( $hook ) {
		if ( $this->is_current_page( $hook ) ) {
			$this->enqueue_assets();
		}
	}

	/**
	 * Enqueue assets.
	 *
	 * @since 1.0.0
	 */
	protected function enqueue_assets() {}

	/**
	 * Check if current page.
	 *
	 * @since 1.0.0
	 * @param string $hook Current admin page hook.
	 * @return bool True if current page.
	 */
	protected function is_current_page( $hook ) {
		return false !== strpos( $hook, $this->menu_slug );
	}
}
