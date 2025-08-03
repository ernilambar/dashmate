<?php
/**
 * Sample_HTML
 *
 * @package Dashmate
 */

declare(strict_types=1);

namespace Nilambar\Dashmate\Widgets;

use Nilambar\Dashmate\Abstract_Widget;

/**
 * Sample_HTML class.
 *
 * @since 1.0.0
 */
class Sample_HTML extends Abstract_Widget {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Widget instance ID.
	 */
	public function __construct( $id ) {
		parent::__construct( $id, 'html', esc_html__( 'Sample HTML', 'dashmate' ) );
	}

	/**
	 * Define widget configuration.
	 *
	 * @since 1.0.0
	 */
	protected function define_widget() {
		$this->description = esc_html__( 'Display sample HTML content', 'dashmate' );
		$this->icon        = 'editor-code';

		$this->settings_schema = [
			'allow_scripts' => [
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Allow Scripts', 'dashmate' ),
				'description' => esc_html__( 'Allow JavaScript execution in HTML content', 'dashmate' ),
				'default'     => false,
				'refresh'     => false,
			],
		];

		$this->output_schema = [
			'html_content' => [
				'type'        => 'string',
				'required'    => true,
				'description' => esc_html__( 'HTML content to render', 'dashmate' ),
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

		$html_content = $this->get_html_content( $settings );

		return [
			'html_content' => $html_content,
		];
	}

	/**
	 * Get HTML content.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return string
	 */
	private function get_html_content( array $settings ): string {
		ob_start();
		?>
		<h3><?php echo esc_html__( 'Welcome to Dashmate!', 'dashmate' ); ?></h3>
		<p><?php echo esc_html__( 'This is a custom HTML widget that demonstrates various HTML elements and styling.', 'dashmate' ); ?></p>

		<h4><?php echo esc_html__( 'Features:', 'dashmate' ); ?></h4>
		<ul>
			<li><?php echo esc_html__( 'Custom HTML content', 'dashmate' ); ?></li>
			<li><?php echo esc_html__( 'Flexible styling options', 'dashmate' ); ?></li>
			<li><?php echo esc_html__( 'Rich text formatting', 'dashmate' ); ?></li>
			<li><?php echo esc_html__( 'Embedded media support', 'dashmate' ); ?></li>
		</ul>
		<?php
		return ob_get_clean();
	}
}
