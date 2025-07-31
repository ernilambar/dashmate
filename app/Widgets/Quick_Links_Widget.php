<?php
/**
 * Quick_Links_Widget
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Widgets;

use Nilambar\Dashmate\Abstract_Widget;

/**
 * Quick_Links_Widget class.
 *
 * @since 1.0.0
 */
class Quick_Links_Widget extends Abstract_Widget {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Widget instance ID.
	 */
	public function __construct( $id ) {
		parent::__construct( $id, 'links', 'Quick Links Widget' );

		$this->description = 'Display quick access links to WordPress admin pages';
		$this->icon        = 'admin-links';
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
		// Merge settings with defaults.
		$settings = $this->merge_settings_with_defaults( $settings );

		// Get links based on settings.
		$links = $this->get_links( $settings );

		return [
			'links' => $links,
		];
	}

	/**
	 * Get links based on settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return array
	 */
	private function get_links( $settings ) {
		$all_links = [
			[
				'title'    => 'Dashboard',
				'url'      => admin_url(),
				'icon'     => 'dashicons-admin-home',
				'category' => 'content',
			],
			[
				'title'    => 'Posts',
				'url'      => admin_url( 'edit.php' ),
				'icon'     => 'dashicons-admin-post',
				'category' => 'content',
			],
			[
				'title'    => 'Pages',
				'url'      => admin_url( 'edit.php?post_type=page' ),
				'icon'     => 'dashicons-admin-page',
				'category' => 'content',
			],
			[
				'title'    => 'Media',
				'url'      => admin_url( 'upload.php' ),
				'icon'     => 'dashicons-admin-media',
				'category' => 'media',
			],
			[
				'title'    => 'Comments',
				'url'      => admin_url( 'edit-comments.php' ),
				'icon'     => 'dashicons-admin-comments',
				'category' => 'content',
			],
		];

		// Filter links based on settings.
		$filter = $settings['filterLinks'] ?? 'content';
		if ( 'all' !== $filter ) {
			$all_links = array_filter(
				$all_links,
				function ( $link ) use ( $filter ) {
					return $link['category'] === $filter;
				}
			);
		}

		// Hide icons if setting is enabled.
		if ( ! empty( $settings['hideIcon'] ) ) {
			foreach ( $all_links as &$link ) {
				unset( $link['icon'] );
			}
		}

		return array_values( $all_links );
	}
}
