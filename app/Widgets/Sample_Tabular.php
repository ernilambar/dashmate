<?php
/**
 * Sample_Tabular
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Widgets;

use Nilambar\Dashmate\Abstract_Widget;

/**
 * Sample_Tabular class.
 *
 * @since 1.0.0
 */
class Sample_Tabular extends Abstract_Widget {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Widget instance ID.
	 */
	public function __construct( $id ) {
		parent::__construct( $id, 'tabular', esc_html__( 'Sample Tabular', 'dashmate' ) );
	}

	/**
	 * Define widget configuration.
	 *
	 * @since 1.0.0
	 */
	protected function define_widget() {
		$this->description = esc_html__( 'Display sample data in tabular format.', 'dashmate' );
		$this->icon        = 'table_chart';

		$this->settings_schema = [
			'max_items'       => [
				'type'        => 'number',
				'label'       => esc_html__( 'Items Per Page', 'dashmate' ),
				'description' => esc_html__( 'Number of items to show per page.', 'dashmate' ),
				'default'     => 2,
				'min'         => 1,
				'max'         => 10,
				'refresh'     => true,
				'choices'     => [
					[
						'value' => 3,
						'label' => '3',
					],
					[
						'value' => 5,
						'label' => '5',
					],
					[
						'value' => 10,
						'label' => '10',
					],
					[
						'value' => 15,
						'label' => '15',
					],
					[
						'value' => 20,
						'label' => '20',
					],
					[
						'value' => 50,
						'label' => '50',
					],
					[
						'value' => 100,
						'label' => '100',
					],
				],
			],
			'visible_columns' => [
				'type'        => 'sortable',
				'label'       => esc_html__( 'Visible Columns', 'dashmate' ),
				'description' => esc_html__( 'Drag to reorder and toggle to show/hide.', 'dashmate' ),
				'default'     => [ 'id', 'title', 'status', 'actions' ],
				'refresh'     => true,
				'choices'     => [
					[
						'value' => 'id',
						'label' => esc_html__( 'ID', 'dashmate' ),
					],
					[
						'value' => 'title',
						'label' => esc_html__( 'Title', 'dashmate' ),
					],
					[
						'value' => 'status',
						'label' => esc_html__( 'Status', 'dashmate' ),
					],
					[
						'value' => 'actions',
						'label' => esc_html__( 'Actions', 'dashmate' ),
					],
				],
			],
		];

		$this->output_schema = [
			'tables' => [
				'type'        => 'array',
				'required'    => true,
				'description' => esc_html__( 'Array of table objects.', 'dashmate' ),
			],
		];
	}

	/**
	 * Get widget content.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 * @return array Widget content.
	 */
	public function get_content( array $settings = [] ): array {
		$settings = $this->merge_settings_with_defaults( $settings );

		// Get visible columns setting.
		$visible_columns = $settings['visible_columns'] ?? [ 'id', 'title', 'status', 'actions' ];

		// Define all available columns.
		$all_columns = [
			'id'      => esc_html__( 'ID', 'dashmate' ),
			'title'   => esc_html__( 'Title', 'dashmate' ),
			'status'  => esc_html__( 'Status', 'dashmate' ),
			'actions' => esc_html__( 'Actions', 'dashmate' ),
		];

		// Filter headers based on visible columns.
		$filtered_headers = [];
		foreach ( $visible_columns as $column_key ) {
			if ( isset( $all_columns[ $column_key ] ) ) {
				$filtered_headers[] = [ 'text' => $all_columns[ $column_key ] ];
			}
		}

		// Sample data for products table.
		$products = [
			'title'   => esc_html__( 'Sample Products', 'dashmate' ),
			'headers' => $filtered_headers,
			'rows'    => [
				[
					'cells'   => $this->filter_cells_by_columns(
						[
							'id'      => [ 'text' => '<a href="https://wordpress.org/plugins/woocommerce/" target="_blank">#001</a>' ],
							'title'   => [ 'text' => '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ],
							'status'  => [ 'text' => esc_html__( 'Active', 'dashmate' ) ],
							'actions' => [ 'text' => '' ],
						],
						$visible_columns
					),
					'actions' => [
						'sync' => [
							'title' => esc_html__( 'Sync Product', 'dashmate' ),
						],
					],
				],
				[
					'cells'   => $this->filter_cells_by_columns(
						[
							'id'      => [ 'text' => '<a href="https://wordpress.org/plugins/contact-form-7/" target="_blank">#002</a>' ],
							'title'   => [ 'text' => '<a href="https://contactform7.com/" target="_blank">Contact Form 7</a>' ],
							'status'  => [ 'text' => esc_html__( 'Active', 'dashmate' ) ],
							'actions' => [ 'text' => '' ],
						],
						$visible_columns
					),
					'actions' => [
						'sync' => [
							'title' => esc_html__( 'Sync Product', 'dashmate' ),
						],
					],
				],
				[
					'cells'   => $this->filter_cells_by_columns(
						[
							'id'      => [ 'text' => '<a href="https://wordpress.org/plugins/yoast-seo/" target="_blank">#003</a>' ],
							'title'   => [ 'text' => '<a href="https://yoast.com/" target="_blank">Yoast SEO</a>' ],
							'status'  => [ 'text' => esc_html__( 'Active', 'dashmate' ) ],
							'actions' => [ 'text' => '' ],
						],
						$visible_columns
					),
					'actions' => [
						'sync' => [
							'title' => esc_html__( 'Sync Product', 'dashmate' ),
						],
					],
				],
			],
		];

		// Sample data for orders table.
		$orders = [
			'title'   => esc_html__( 'Sample Orders', 'dashmate' ),
			'headers' => $filtered_headers,
			'rows'    => [
				[
					'cells'   => $this->filter_cells_by_columns(
						[
							'id'      => [ 'text' => '<a href="https://example.com/order/1001" target="_blank">#1001</a>' ],
							'title'   => [ 'text' => '<a href="https://example.com/product/premium-theme" target="_blank">Premium Theme License</a>' ],
							'status'  => [ 'text' => esc_html__( 'Completed', 'dashmate' ) ],
							'actions' => [ 'text' => '' ],
						],
						$visible_columns
					),
					'actions' => [
						'sync' => [
							'title' => esc_html__( 'Sync Order', 'dashmate' ),
						],
					],
				],
				[
					'cells'   => $this->filter_cells_by_columns(
						[
							'id'      => [ 'text' => '<a href="https://example.com/order/1002" target="_blank">#1002</a>' ],
							'title'   => [ 'text' => '<a href="https://example.com/product/plugin-bundle" target="_blank">Plugin Bundle</a>' ],
							'status'  => [ 'text' => esc_html__( 'Processing', 'dashmate' ) ],
							'actions' => [ 'text' => '' ],
						],
						$visible_columns
					),
					'actions' => [
						'sync' => [
							'title' => esc_html__( 'Sync Order', 'dashmate' ),
						],
					],
				],
				[
					'cells'   => $this->filter_cells_by_columns(
						[
							'id'      => [ 'text' => '<a href="https://example.com/order/1003" target="_blank">#1003</a>' ],
							'title'   => [ 'text' => '<a href="https://example.com/product/seo-plugin" target="_blank">SEO Plugin</a>' ],
							'status'  => [ 'text' => esc_html__( 'Completed', 'dashmate' ) ],
							'actions' => [ 'text' => '' ],
						],
						$visible_columns
					),
					'actions' => [
						'sync' => [
							'title' => esc_html__( 'Sync Order', 'dashmate' ),
						],
					],
				],
			],
		];

		$max_items = $settings['max_items'] ?? 2;

		// Limit products table rows.
		$products['rows'] = array_slice( $products['rows'], 0, (int) $max_items );

		// Limit orders table rows.
		$orders['rows'] = array_slice( $orders['rows'], 0, (int) $max_items );

		return [
			'tables' => [
				$products,
				$orders,
			],
		];
	}

	/**
	 * Filter cells by visible columns.
	 *
	 * @since 1.0.0
	 *
	 * @param array $cells All cells data.
	 * @param array $visible_columns Array of visible column keys.
	 * @return array Filtered cells array.
	 */
	private function filter_cells_by_columns( array $cells, array $visible_columns ): array {
		$filtered_cells = [];

		foreach ( $visible_columns as $column_key ) {
			if ( isset( $cells[ $column_key ] ) ) {
				$filtered_cells[] = $cells[ $column_key ];
			}
		}
		return $filtered_cells;
	}
}
