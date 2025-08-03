<?php
/**
 * SettingsPanel
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\Panels;

use Nilambar\Dashmate\Core\Option;
use Nilambar\Dashmate\Widget_Dispatcher;
use Nilambar\Optify\Abstract_Panel;

/**
 * SettingsPanel class.
 *
 * @since 1.0.0
 */
class SettingsPanel extends Abstract_Panel {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct(
			'dashmate-settings',
			esc_html__( 'Settings', 'dashmate' ),
			'dashmate_options'
		);
	}

	/**
	 * Get field configuration.
	 *
	 * @since 1.0.0
	 *
	 * @return array Field configuration.
	 */
	public function get_field_configuration() {
		$all_widgets = Widget_Dispatcher::get_widgets();

		$widget_choices = [];

		if ( ! empty( $all_widgets ) ) {
			$widget_keys = array_keys( $all_widgets );
			foreach ( $widget_keys as $key ) {
				$widget_choices[] = [
					'label' => $key,
					'value' => $key,
				];
			}
		}

		return [
			[
				'name'    => 'max_columns',
				'label'   => esc_html__( 'Columns Number', 'dashmate' ),
				'type'    => 'buttonset',
				'default' => Option::defaults( 'max_columns' ),
				'choices' => [
					[
						'label' => 'One',
						'value' => '1',
					],
					[
						'label' => 'Two',
						'value' => '2',
					],
					[
						'label' => 'Three',
						'value' => '3',
					],
					[
						'label' => 'Four',
						'value' => '4',
					],

				],
			],
			[
				'name'    => 'inactive_widgets',
				'label'   => esc_html__( 'Inactive Widgets', 'dashmate' ),
				'type'    => 'multi-check',
				'default' => Option::defaults( 'inactive_widgets' ),
				'choices' => $widget_choices ?? [],
			],
		];
	}
}
