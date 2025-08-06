<?php
/**
 * Layout_Command
 *
 * @package Dashmate
 */

namespace Nilambar\Dashmate\CLI;

use Nilambar\Dashmate\Models\Dashboard_Model;
use Nilambar\Dashmate\Utils\JSON_Utils;
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
	 * ## EXAMPLES
	 *
	 *     # Export layout to current directory.
	 *     $ wp dashmate layout export
	 *
	 *     # Export layout to specific directory.
	 *     $ wp dashmate layout export --dir=/path/to/exports
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

		if ( ! Dashboard_Model::data_exists() ) {
			WP_CLI::error( 'No dashboard data found.' );
			return;
		}

		$dashboard_data = Dashboard_Model::get_data();

		if ( empty( $dashboard_data ) ) {
			WP_CLI::error( 'No dashboard data found.' );
			return;
		}

		$output = JSON_Utils::encode_to_json( $dashboard_data );

		if ( is_wp_error( $output ) ) {
			WP_CLI::error( $output->get_error_message() );
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
		$file_path = trailingslashit( $export_dir ) . 'layout.json';

		// Write data to file.
		$result = file_put_contents( $file_path, $output );

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
	 * : Path to the JSON file to import.
	 *
	 * ## EXAMPLES
	 *
	 *     # Import layout from JSON file.
	 *     $ wp dashmate layout import layout.json
	 *
	 *     # Import layout from specific path.
	 *     $ wp dashmate layout import /path/to/layout.json
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

		// Parse JSON file.
		$dashboard_data = JSON_Utils::parse_file( $file_path );

		if ( is_wp_error( $dashboard_data ) ) {
			WP_CLI::error( sprintf( 'Failed to parse file "%s": %s', $file_path, $dashboard_data->get_error_message() ) );
			return;
		}

		// Set dashboard data.
		$result = Dashboard_Model::set_data( $dashboard_data );

		if ( is_wp_error( $result ) ) {
			WP_CLI::error( sprintf( 'Failed to import layout from file "%s": %s', $file_path, $result->get_error_message() ) );
			return;
		}

		WP_CLI::success( sprintf( 'Layout imported successfully from "%s".', $file_path ) );
	}

	/**
	 * Test command.
	 *
	 * ## EXAMPLES
	 *
	 *     # Run test command.
	 *     $ wp dashmate layout test
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
