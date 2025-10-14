<?php
/**
 * Abstract_Dashboard_Page
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\Admin;

use Nilambar\Dashmate\Models\Dashboard_Model;

/**
 * Abstract Dashboard Page class.
 *
 * This abstract class provides a foundation for creating dashboard pages
 * that can be extended to support multiple dashboard pages in the future.
 * It handles common dashboard functionality like page registration (both parent
 * and child pages), asset enqueuing, and provides hooks for customization.
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
	 * Parent page slug for child pages.
	 *
	 * @since 1.0.0
	 *
	 * @var string|null
	 */
	protected $parent_page;

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
	}

	/**
	 * Registers the dashboard page.
	 *
	 * @since 1.0.0
	 */
	public function register_dashboard_page() {
		$page_title = apply_filters( 'dashmate_dashboard_page_title', $this->page_title, $this->page_slug, $this->dashboard_id );
		$menu_title = apply_filters( 'dashmate_dashboard_menu_title', $this->menu_title, $this->page_slug, $this->dashboard_id );

		// Check if this is a child page.
		if ( ! empty( $this->parent_page ) ) {
			add_submenu_page(
				$this->parent_page,
				$page_title,
				$menu_title,
				$this->capability,
				$this->page_slug,
				[ $this, 'render_dashboard_content' ]
			);
		} else {
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
	}

	/**
	 * Renders dashboard content.
	 *
	 * @since 1.0.0
	 */
	public function render_dashboard_content() {
		$template_data = $this->get_template_data();
		$this->render_template( $template_data );
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
	 * @param array $data Template data.
	 */
	protected function render_template( $data = [] ) {
		$dashboard_id = $data['dashboard_id'] ?? '';
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<div id="dashmate-app" data-dashboard-id="<?php echo esc_attr( $dashboard_id ); ?>"><?php esc_html_e( 'Loading...', 'dashmate' ); ?></div>
		</div><!-- .wrap -->
		<?php
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
	 * Get parent page slug.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null Parent page slug.
	 */
	public function get_parent_page() {
		return $this->parent_page;
	}

	/**
	 * Register starter layout with the model.
	 *
	 * @since 1.0.0
	 */
	private function register_starter_layout() {
		if ( ! empty( $this->starter_layout ) ) {
			Dashboard_Model::set_starter_layout( $this->dashboard_id, $this->starter_layout );
		}
	}
}
