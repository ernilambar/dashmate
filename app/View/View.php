<?php
/**
 * View
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\View;

use Exception;

/**
 * View class.
 *
 * @since 1.0.0
 */
class View {

	/**
	 * Renders template.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Template name.
	 * @param array  $data Template data.
	 *
	 * @throws Exception Throws exception if template file do not exists.
	 */
	public static function render( string $name, array $data = [] ) {
		$file = DASHMATE_DIR . "/templates/{$name}.php";

		if ( ! file_exists( $file ) ) {
			printf( esc_html__( 'View "%1$s" not found. Expected: %2$s', 'dashmate' ), '<b>' . esc_html( $name ) . '</b>', esc_html( $file ) );
			return;
		}

		include $file;
	}
}
