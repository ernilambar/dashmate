<?php
/**
 * Sales_Widget
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Widgets;

use Nilambar\Dashmate\Abstract_Widget;

/**
 * Sales_Widget class.
 *
 * @since 1.0.0
 */
class Sales_Widget extends Abstract_Widget {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Widget instance ID.
	 */
	public function __construct( $id ) {
		parent::__construct( $id, 'tabular', esc_html__( 'Sales Overview', 'dashmate' ) );
	}

	/**
	 * Define widget configuration.
	 *
	 * @since 1.0.0
	 */
	protected function define_widget() {
		$this->description = 'Display sales data in tables';
		$this->icon        = 'editor-table';

		$this->settings_schema = [
			'showHeaders' => [
				'type'        => 'checkbox',
				'label'       => 'Show Headers',
				'description' => 'Show table headers',
				'default'     => true,
			],
			'stripedRows' => [
				'type'        => 'checkbox',
				'label'       => 'Striped Rows',
				'description' => 'Alternate row colors',
				'default'     => true,
			],
			'showPagination' => [
				'type'        => 'checkbox',
				'label'       => 'Show Pagination',
				'description' => 'Show pagination controls',
				'default'     => true,
			],
			'itemsPerPage' => [
				'type'        => 'number',
				'label'       => 'Items Per Page',
				'description' => 'Number of items to show per page',
				'default'     => 10,
				'min'         => 5,
				'max'         => 50,
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
	 * @since 1.0.0
	 *
	 * @param string $widget_id Widget ID.
	 * @param array  $settings  Widget settings.
	 *
	 * @return array
	 */
	public function get_content( $widget_id = null, $settings = [] ) {
		$settings = $this->merge_settings_with_defaults( $settings );

		// Sample data for recent orders table.
		$recent_orders = [
			'title'   => 'Recent Orders',
			'headers' => [
				[ 'text' => 'ID' ],
				[ 'text' => 'Title' ],
				[ 'text' => 'Status' ],
				[ 'text' => 'Actions' ],
			],
			'rows'    => [
				[
					'cells' => [
						[ 'text' => '#1001' ],
						[ 'text' => 'Premium Theme License' ],
						[ 'text' => 'Completed' ],
						[ 'text' => 'View | Edit' ],
					],
				],
				[
					'cells' => [
						[ 'text' => '#1002' ],
						[ 'text' => 'Plugin Bundle' ],
						[ 'text' => 'Processing' ],
						[ 'text' => 'View | Edit' ],
					],
				],
				[
					'cells' => [
						[ 'text' => '#1003' ],
						[ 'text' => 'Support Package' ],
						[ 'text' => 'Pending' ],
						[ 'text' => 'View | Edit' ],
					],
				],
				[
					'cells' => [
						[ 'text' => '#1004' ],
						[ 'text' => 'Custom Development' ],
						[ 'text' => 'Completed' ],
						[ 'text' => 'View | Edit' ],
					],
				],
				[
					'cells' => [
						[ 'text' => '#1005' ],
						[ 'text' => 'Hosting Service' ],
						[ 'text' => 'Cancelled' ],
						[ 'text' => 'View | Edit' ],
					],
				],
			],
		];

		// Sample data for top products table.
		$top_products = [
			'title'   => 'Top Products',
			'headers' => [
				[ 'text' => 'ID' ],
				[ 'text' => 'Title' ],
				[ 'text' => 'Status' ],
				[ 'text' => 'Actions' ],
			],
			'rows'    => [
				[
					'cells' => [
						[ 'text' => '#P001' ],
						[ 'text' => 'WordPress Theme Pro' ],
						[ 'text' => 'Active' ],
						[ 'text' => 'View | Edit' ],
					],
				],
				[
					'cells' => [
						[ 'text' => '#P002' ],
						[ 'text' => 'SEO Plugin Bundle' ],
						[ 'text' => 'Active' ],
						[ 'text' => 'View | Edit' ],
					],
				],
				[
					'cells' => [
						[ 'text' => '#P003' ],
						[ 'text' => 'Security Plugin' ],
						[ 'text' => 'Active' ],
						[ 'text' => 'View | Edit' ],
					],
				],
				[
					'cells' => [
						[ 'text' => '#P004' ],
						[ 'text' => 'Backup Solution' ],
						[ 'text' => 'Inactive' ],
						[ 'text' => 'View | Edit' ],
					],
				],
				[
					'cells' => [
						[ 'text' => '#P005' ],
						[ 'text' => 'Performance Plugin' ],
						[ 'text' => 'Active' ],
						[ 'text' => 'View | Edit' ],
					],
				],
			],
		];

		return [
			'tables' => [
				$recent_orders,
				$top_products,
			],
		];
	}
}
