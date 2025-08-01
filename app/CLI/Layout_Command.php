<?php
/**
 * Layout_Command
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\CLI;

use Nilambar\Dashmate\Utils\Layout_Utils;
use WP_CLI;

/**
 * Layout_Command class.
 *
 * @since 1.0.0
 */
final class Layout_Command {

	/**
	 * Export layout.
	 *
	 * ## OPTIONS
	 *
	 * [--dir=<dirname>]
	 * : Full path to directory where export files should be stored. Defaults to current working directory.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args       List of the positional arguments.
	 * @param array $assoc_args List of the associative arguments.
	 *
	 * @when after_wp_load
	 * @subcommand export
	 */
	public function export_( $args, $assoc_args = [] ) {
		$defaults = [
			'dir' => null,
		];

		$assoc_args = wp_parse_args( $assoc_args, $defaults );

		if ( ! Layout_Utils::has_layout_data() ) {
			WP_CLI::error( 'No dashboard data found.' );
			return;
		}

		$yaml_output = Layout_Utils::export_to_string();

		if ( false === $yaml_output ) {
			WP_CLI::error( 'Failed to encode dashboard data as YAML.' );
			return;
		}

		// Determine export directory.
		$export_dir = $assoc_args['dir'];

		if ( empty( $export_dir ) ) {
			$export_dir = getcwd();
		}

		// Ensure directory exists and is writable.
		if ( ! is_dir( $export_dir ) ) {
			WP_CLI::error( sprintf( 'Directory "%s" does not exist.', $export_dir ) );
			return;
		}

		if ( ! is_writable( $export_dir ) ) {
			WP_CLI::error( sprintf( 'Directory "%s" is not writable.', $export_dir ) );
			return;
		}

		// Create file path.
		$file_path = trailingslashit( $export_dir ) . 'layout.yml';

		// Write YAML to file.
		$result = file_put_contents( $file_path, $yaml_output );

		if ( false === $result ) {
			WP_CLI::error( sprintf( 'Failed to write file "%s".', $file_path ) );
			return;
		}

		WP_CLI::success( sprintf( 'Layout exported successfully to "%s".', $file_path ) );
	}

	/**
	 * Import layout.
	 *
	 * ## OPTIONS
	 *
	 * <file>
	 * : Path to the YAML file to import.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args       List of the positional arguments.
	 * @param array $assoc_args List of the associative arguments.
	 *
	 * @when after_wp_load
	 * @subcommand import
	 */
	public function import_( $args, $assoc_args = [] ) {
		if ( empty( $args[0] ) ) {
			WP_CLI::error( 'File path is required.' );
			return;
		}

		$file_path = $args[0];

		// Check if file exists.
		if ( ! file_exists( $file_path ) ) {
			WP_CLI::error( sprintf( 'File "%s" does not exist.', $file_path ) );
			return;
		}

		// Check if file is readable.
		if ( ! is_readable( $file_path ) ) {
			WP_CLI::error( sprintf( 'File "%s" is not readable.', $file_path ) );
			return;
		}

		// Import layout from file.
		$result = Layout_Utils::import_from_file( $file_path );

		if ( false === $result ) {
			WP_CLI::error( sprintf( 'Failed to import layout from file "%s".', $file_path ) );
			return;
		}

		WP_CLI::success( sprintf( 'Layout imported successfully from "%s".', $file_path ) );
	}

	/**
	 * Test command.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args       List of the positional arguments.
	 * @param array $assoc_args List of the associative arguments.
	 *
	 * @when after_wp_load
	 * @subcommand test
	 */
	public function test_( $args, $assoc_args = [] ) {
		WP_CLI::success( 'Test.' );
	}
}
