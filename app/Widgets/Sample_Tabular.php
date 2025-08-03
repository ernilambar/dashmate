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
		$this->description = esc_html__( 'Display sample data in tabular format', 'dashmate' );
		$this->icon        = 'editor-table';

		$this->settings_schema = [
			'max_items' => [
				'type'        => 'number',
				'label'       => esc_html__( 'Items Per Page', 'dashmate' ),
				'description' => esc_html__( 'Number of items to show per page', 'dashmate' ),
				'default'     => 2,
				'min'         => 1,
				'max'         => 10,
				'refresh'     => true,
			],
		];

		$this->output_schema = [
			'tables' => [
				'type'        => 'array',
				'required'    => true,
				'description' => esc_html__( 'Array of table objects', 'dashmate' ),
			],
		];
	}

	/**
	 * Get widget content.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return array
	 */
	public function get_content( array $settings = [] ): array {
		$settings = $this->merge_settings_with_defaults( $settings );

		// Sample data for products table.
		$products = [
			'title'   => esc_html__( 'Sample Products', 'dashmate' ),
			'headers' => [
				[ 'text' => esc_html__( 'ID', 'dashmate' ) ],
				[ 'text' => esc_html__( 'Title', 'dashmate' ) ],
				[ 'text' => esc_html__( 'Status', 'dashmate' ) ],
				[ 'text' => esc_html__( 'Actions', 'dashmate' ) ],
			],
			'rows'    => [
				[
					'cells'   => [
						[ 'text' => '<a href="https://wordpress.org/plugins/woocommerce/" target="_blank">#001</a>' ],
						[ 'text' => '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ],
						[ 'text' => esc_html__( 'Active', 'dashmate' ) ],
						[ 'text' => '' ],
					],
					'actions' => [
						'sync' => [
							'title' => esc_html__( 'Sync Product', 'dashmate' ),
						],
					],
				],
				[
					'cells'   => [
						[ 'text' => '<a href="https://wordpress.org/plugins/contact-form-7/" target="_blank">#002</a>' ],
						[ 'text' => '<a href="https://contactform7.com/" target="_blank">Contact Form 7</a>' ],
						[ 'text' => esc_html__( 'Active', 'dashmate' ) ],
						[ 'text' => '' ],
					],
					'actions' => [
						'sync' => [
							'title' => esc_html__( 'Sync Product', 'dashmate' ),
						],
					],
				],
				[
					'cells'   => [
						[ 'text' => '<a href="https://wordpress.org/plugins/yoast-seo/" target="_blank">#003</a>' ],
						[ 'text' => '<a href="https://yoast.com/" target="_blank">Yoast SEO</a>' ],
						[ 'text' => esc_html__( 'Active', 'dashmate' ) ],
						[ 'text' => '' ],
					],
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
			'headers' => [
				[ 'text' => esc_html__( 'ID', 'dashmate' ) ],
				[ 'text' => esc_html__( 'Title', 'dashmate' ) ],
				[ 'text' => esc_html__( 'Status', 'dashmate' ) ],
				[ 'text' => esc_html__( 'Actions', 'dashmate' ) ],
			],
			'rows'    => [
				[
					'cells'   => [
						[ 'text' => '<a href="https://example.com/order/1001" target="_blank">#1001</a>' ],
						[ 'text' => '<a href="https://example.com/product/premium-theme" target="_blank">Premium Theme License</a>' ],
						[ 'text' => esc_html__( 'Completed', 'dashmate' ) ],
						[ 'text' => '' ],
					],
					'actions' => [
						'sync' => [
							'title' => esc_html__( 'Sync Order', 'dashmate' ),
						],
					],
				],
				[
					'cells'   => [
						[ 'text' => '<a href="https://example.com/order/1002" target="_blank">#1002</a>' ],
						[ 'text' => '<a href="https://example.com/product/plugin-bundle" target="_blank">Plugin Bundle</a>' ],
						[ 'text' => esc_html__( 'Processing', 'dashmate' ) ],
						[ 'text' => '' ],
					],
					'actions' => [
						'sync' => [
							'title' => esc_html__( 'Sync Order', 'dashmate' ),
						],
					],
				],
				[
					'cells'   => [
						[ 'text' => '<a href="https://example.com/order/1003" target="_blank">#1003</a>' ],
						[ 'text' => '<a href="https://example.com/product/seo-plugin" target="_blank">SEO Plugin</a>' ],
						[ 'text' => esc_html__( 'Completed', 'dashmate' ) ],
						[ 'text' => '' ],
					],
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
		$products['rows'] = array_slice( $products['rows'], 0, $max_items );

		// Limit orders table rows.
		$orders['rows'] = array_slice( $orders['rows'], 0, $max_items );

		return [
			'tables' => [
				$products,
				$orders,
			],
		];
	}
}
