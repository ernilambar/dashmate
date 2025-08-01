<?php
/**
 * Widget_Blueprint_Manager
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate;

/**
 * Widget_Blueprint_Manager class.
 *
 * @since 1.0.0
 */
class Widget_Blueprint_Manager {

	/**
	 * Widget blueprint types.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private static $widget_blueprints = [
		'html'             => [
			'name'            => 'HTML Widget',
			'description'     => 'Display HTML content',
			'icon'            => 'editor-code',
			'settings_schema' => [],
			'output_schema'   => [
				'html_content' => [
					'type'        => 'string',
					'required'    => true,
					'description' => 'HTML content to render',
				],
			],
		],
		'links'            => [
			'name'            => 'Links Widget',
			'description'     => 'Display quick access links',
			'icon'            => 'admin-links',
			'settings_schema' => [
				'hideIcon'  => [
					'type'        => 'checkbox',
					'label'       => 'Hide Icons',
					'description' => 'Hide link icons',
					'default'     => false,
				],
				'linkStyle' => [
					'type'    => 'select',
					'label'   => 'Link Style',
					'options' => [
						[
							'value' => 'list',
							'label' => 'List',
						],
						[
							'value' => 'grid',
							'label' => 'Grid',
						],
					],
					'default' => 'list',
				],
			],
			'output_schema'   => [
				'links' => [
					'type'        => 'array',
					'required'    => true,
					'description' => 'Array of link objects',
				],
			],
		],
		'progress-circles' => [
			'name'            => 'Progress Circles Widget',
			'description'     => 'Display progress circles',
			'icon'            => 'feedback',
			'settings_schema' => [
				'hideCaption' => [
					'type'        => 'checkbox',
					'label'       => 'Hide Caption',
					'description' => 'Hide circle caption',
					'default'     => false,
				],
			],
			'output_schema'   => [
				'items' => [
					'type'        => 'array',
					'required'    => true,
					'description' => 'Array of circle objects',
				],
			],
		],
		'tabular'          => [
			'name'            => 'Tabular Widget',
			'description'     => 'Display multiple tables with data',
			'icon'            => 'editor-table',
			'settings_schema' => [
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
			],
			'output_schema'   => [
				'tables' => [
					'type'        => 'array',
					'required'    => true,
					'description' => 'Array of table objects',
				],
			],
		],
	];

	/**
	 * Get all registered widget blueprints.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_widget_blueprints() {
		/**
		 * Filter the registered widget blueprints.
		 *
		 * This filter allows other plugins and addons to add their own widget blueprints
		 * to the Dashmate dashboard.
		 *
		 * @since 1.0.0
		 *
		 * @param array $blueprints Array of registered widget blueprints.
		 */
		return apply_filters( 'dashmate_widget_blueprints', self::$widget_blueprints );
	}

	/**
	 * Get a specific widget blueprint.
	 *
	 * @since 1.0.0
	 *
	 * @param string $blueprint_type Blueprint type.
	 *
	 * @return array|null
	 */
	public static function get_widget_blueprint( $blueprint_type ) {
		$blueprints = self::get_widget_blueprints();
		return $blueprints[ $blueprint_type ] ?? null;
	}

	/**
	 * Check if a widget blueprint is registered.
	 *
	 * @since 1.0.0
	 *
	 * @param string $blueprint_type Blueprint type.
	 *
	 * @return bool
	 */
	public static function is_widget_blueprint_registered( $blueprint_type ) {
		$blueprints = self::get_widget_blueprints();
		return isset( $blueprints[ $blueprint_type ] );
	}

	/**
	 * Get widget blueprints for frontend.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_widget_blueprints_for_frontend() {
		$blueprints          = self::get_widget_blueprints();
		$frontend_blueprints = [];

		foreach ( $blueprints as $type => $blueprint ) {
			$frontend_blueprints[ $type ] = [
				'name'            => $blueprint['name'],
				'description'     => $blueprint['description'],
				'icon'            => $blueprint['icon'],
				'settings_schema' => $blueprint['settings_schema'],
				'output_schema'   => $blueprint['output_schema'],
			];
		}

		return $frontend_blueprints;
	}
}
