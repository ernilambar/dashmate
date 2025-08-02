<?php
/**
 * Plugin Name: Example Dashmate Widget Plugin
 * Description: Demonstrates how to add custom widgets to Dashmate
 * Version: 1.0.0
 * Author: Example Author
 * Text Domain: Example Text Domain
 */
use Nilambar\Dashmate\Abstract_Widget;
use Nilambar\Dashmate\Widget_Template_Registry;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'EXAMPLE_PLUGIN_VERSION', '1.0.0' );
define( 'EXAMPLE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Include autoloader if using Composer.
if ( file_exists( EXAMPLE_PLUGIN_DIR . '/vendor/autoload.php' ) ) {
	require_once EXAMPLE_PLUGIN_DIR . '/vendor/autoload.php';
}

/**
 * Example Income Widget Class.
 */
class Example_Income_Widget extends Abstract_Widget {

	/**
	 * Constructor.
	 *
	 * @param string $id Widget instance ID.
	 */
	public function __construct( $id ) {
		parent::__construct( $id, 'tabular', 'Income Overview' );
	}

	/**
	 * Define widget configuration.
	 */
	protected function define_widget() {
		$this->description = 'Display income data with advanced filtering';
		$this->icon        = 'money-alt';

		$this->settings_schema = [
			'currency'       => [
				'type'        => 'select',
				'label'       => 'Currency',
				'description' => 'Select currency for display',
				'options'     => [
					[
						'value' => 'USD',
						'label' => 'USD',
					],
					[
						'value' => 'EUR',
						'label' => 'EUR',
					],
					[
						'value' => 'GBP',
						'label' => 'GBP',
					],
				],
				'default'     => 'USD',
			],
			'timeframe'      => [
				'type'        => 'select',
				'label'       => 'Timeframe',
				'description' => 'Select time period for data',
				'options'     => [
					[
						'value' => 'daily',
						'label' => 'Daily',
					],
					[
						'value' => 'weekly',
						'label' => 'Weekly',
					],
					[
						'value' => 'monthly',
						'label' => 'Monthly',
					],
					[
						'value' => 'yearly',
						'label' => 'Yearly',
					],
				],
				'default'     => 'monthly',
			],
			'showPagination' => [
				'type'        => 'checkbox',
				'label'       => 'Show Pagination',
				'description' => 'Enable pagination controls',
				'default'     => true,
			],
			'itemsPerPage'   => [
				'type'        => 'number',
				'label'       => 'Items Per Page',
				'description' => 'Number of items to show per page',
				'default'     => 15,
				'min'         => 5,
				'max'         => 100,
			],
			'sortable'       => [
				'type'        => 'checkbox',
				'label'       => 'Sortable Columns',
				'description' => 'Allow column sorting',
				'default'     => true,
			],
		];

		$this->output_schema = [
			'tables' => [
				'type'        => 'array',
				'required'    => true,
				'description' => 'Array of table objects',
			],
		];
	}

	/**
	 * Get widget content.
	 *
	 * @param string $widget_id Widget ID.
	 * @param array  $settings  Widget settings.
	 *
	 * @return array
	 */
	public function get_content( $widget_id = null, $settings = [] ) {
		$settings = $this->merge_settings_with_defaults( $settings );

		// Your custom logic here to fetch income data
		$income_data = $this->get_income_data( $settings );

		return [
			'tables' => $income_data,
		];
	}

	/**
	 * Get income data based on settings.
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return array
	 */
	private function get_income_data( $settings ) {
		// This is where you would implement your actual data fetching logic
		// For this example, we'll return sample data

		$currency  = $settings['currency'] ?? 'USD';
		$timeframe = $settings['timeframe'] ?? 'monthly';

		// Sample income table
		$income_table = [
			'title'   => "Income Data ({$currency})",
			'headers' => [
				[ 'text' => 'Date' ],
				[ 'text' => 'Source' ],
				[ 'text' => 'Amount' ],
				[ 'text' => 'Status' ],
			],
			'rows'    => [
				[
					'cells' => [
						[ 'text' => '2024-01-15' ],
						[ 'text' => 'Product Sales' ],
						[ 'text' => "{$currency} 1,250.00" ],
						[ 'text' => 'Completed' ],
					],
				],
				[
					'cells' => [
						[ 'text' => '2024-01-14' ],
						[ 'text' => 'Service Fee' ],
						[ 'text' => "{$currency} 850.00" ],
						[ 'text' => 'Completed' ],
					],
				],
				[
					'cells' => [
						[ 'text' => '2024-01-13' ],
						[ 'text' => 'Consultation' ],
						[ 'text' => "{$currency} 500.00" ],
						[ 'text' => 'Pending' ],
					],
				],
			],
		];

		// Sample summary table
		$summary_table = [
			'title'   => "Summary ({$timeframe})",
			'headers' => [
				[ 'text' => 'Metric' ],
				[ 'text' => 'Value' ],
			],
			'rows'    => [
				[
					'cells' => [
						[ 'text' => 'Total Income' ],
						[ 'text' => "{$currency} 2,600.00" ],
					],
				],
				[
					'cells' => [
						[ 'text' => 'Average per Day' ],
						[ 'text' => "{$currency} 866.67" ],
					],
				],
				[
					'cells' => [
						[ 'text' => 'Transactions' ],
						[ 'text' => '3' ],
					],
				],
			],
		];

		return [
			$income_table,
			$summary_table,
		];
	}
}

/**
 * Register custom widget with Dashmate.
 *
 * @param array $widgets Array of registered widgets.
 *
 * @return array
 */
function example_plugin_register_dashmate_widget( $widgets ) {
	// No need to require_once - autoloader handles it!
	$widgets['income-overview'] = new Example_Income_Widget( 'income-overview' );

	return $widgets;
}

// Use a later priority to ensure your autoloader is loaded.
add_filter( 'dashmate_widgets', 'example_plugin_register_dashmate_widget', 20 );

/**
 * Example of how to register a custom template.
 */
function example_plugin_register_custom_template() {
	// Only register if Dashmate is active
	if ( class_exists( '\Nilambar\Dashmate\Widget_Template_Registry' ) ) {
		Widget_Template_Registry::register_template(
			'custom-chart',
			[
				'component'        => 'CustomChartWidget',
				'capabilities'     => [ 'interactive', 'animations', 'export' ],
				'default_settings' => [
					'showLegend' => true,
					'animate'    => true,
				],
			]
		);
	}
}

add_action( 'init', 'example_plugin_register_custom_template' );

/**
 * Example of how to customize the default layout file path.
 */
function example_plugin_customize_default_layout_file( $default_path ) {
	// Use a custom layout file from your plugin
	$custom_path = EXAMPLE_PLUGIN_DIR . '/layouts/custom-default.yml';

	// Check if the custom file exists, otherwise fall back to default
	if ( file_exists( $custom_path ) ) {
		return $custom_path;
	}

	return $default_path;
}
// Commented out to prevent interference with the main plugin
// add_filter( 'dashmate_default_layout_file', 'example_plugin_customize_default_layout_file' );

/**
 * Alternative example: Use different layouts based on user role or site configuration.
 */
function example_plugin_advanced_layout_file_customization( $default_path ) {
	// Example 1: Different layouts for different user roles
	if ( current_user_can( 'administrator' ) ) {
		$admin_layout = EXAMPLE_PLUGIN_DIR . '/layouts/admin-default.yml';
		if ( file_exists( $admin_layout ) ) {
			return $admin_layout;
		}
	}

	// Example 2: Different layouts based on site configuration
	$site_layout = get_option( 'example_plugin_custom_layout' );
	if ( ! empty( $site_layout ) && file_exists( $site_layout ) ) {
		return $site_layout;
	}

	// Example 3: Different layouts based on environment
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		$debug_layout = EXAMPLE_PLUGIN_DIR . '/layouts/debug-default.yml';
		if ( file_exists( $debug_layout ) ) {
			return $debug_layout;
		}
	}

	return $default_path;
}
// Uncomment to use advanced customization:
// add_filter( 'dashmate_default_layout_file', 'example_plugin_advanced_layout_file_customization', 20 );
