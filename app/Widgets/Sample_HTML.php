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
		$this->description = esc_html__( 'Display sample HTML content.', 'dashmate' );
		$this->icon        = 'code';

		$this->settings_schema = [
			'text_field'        => [
				'type'        => 'text',
				'label'       => esc_html__( 'Text Field', 'dashmate' ),
				'description' => esc_html__( 'A simple text input field for testing.', 'dashmate' ),
				'default'     => '',
				'placeholder' => esc_html__( 'Enter text...', 'dashmate' ),
			],
			'url_field'         => [
				'type'        => 'url',
				'label'       => esc_html__( 'URL Field', 'dashmate' ),
				'description' => esc_html__( 'A URL input field for testing.', 'dashmate' ),
				'placeholder' => 'https://example.com',
			],
			'checkbox_field'    => [
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Checkbox Field', 'dashmate' ),
				'description' => esc_html__( 'A checkbox field for testing.', 'dashmate' ),
				'message'     => esc_html__( 'Enable this checkbox?', 'dashmate' ),
				'default'     => true,
			],
			'toggle_field'      => [
				'type'        => 'toggle',
				'label'       => esc_html__( 'Toggle Field', 'dashmate' ),
				'description' => esc_html__( 'A toggle switch field for testing.', 'dashmate' ),
				'default'     => false,
			],
			'select_field'      => [
				'type'        => 'select',
				'label'       => esc_html__( 'Select Field', 'dashmate' ),
				'description' => esc_html__( 'A dropdown select field for testing.', 'dashmate' ),
				'default'     => 'option2',
				'choices'     => [
					[
						'value' => 'option1',
						'label' => esc_html__( 'Option 1', 'dashmate' ),
					],
					[
						'value' => 'option2',
						'label' => esc_html__( 'Option 2', 'dashmate' ),
					],
					[
						'value' => 'option3',
						'label' => esc_html__( 'Option 3', 'dashmate' ),
					],
				],
			],
			'number_field'      => [
				'type'        => 'number',
				'label'       => esc_html__( 'Number Field', 'dashmate' ),
				'description' => esc_html__( 'A number input field for testing.', 'dashmate' ),
				'default'     => 42,
				'min'         => 1,
				'max'         => 100,
			],
			'textarea_field'    => [
				'type'        => 'textarea',
				'label'       => esc_html__( 'Textarea Field', 'dashmate' ),
				'description' => esc_html__( 'A simple textarea input field for testing.', 'dashmate' ),
				'placeholder' => esc_html__( 'Enter text content...', 'dashmate' ),
			],

			'radio_field'       => [
				'type'        => 'radio',
				'label'       => esc_html__( 'Radio Field', 'dashmate' ),
				'description' => esc_html__( 'A radio button field for testing.', 'dashmate' ),
				'default'     => 'radio2',
				'choices'     => [
					[
						'value' => 'radio1',
						'label' => esc_html__( 'Radio Option 1', 'dashmate' ),
					],
					[
						'value' => 'radio2',
						'label' => esc_html__( 'Radio Option 2', 'dashmate' ),
					],
					[
						'value' => 'radio3',
						'label' => esc_html__( 'Radio Option 3', 'dashmate' ),
					],
				],
			],
			'buttonset_field'   => [
				'type'        => 'buttonset',
				'label'       => esc_html__( 'Buttonset Field', 'dashmate' ),
				'description' => esc_html__( 'A buttonset field for testing.', 'dashmate' ),
				'default'     => 'grid',
				'choices'     => [
					[
						'value' => 'list',
						'label' => esc_html__( 'List', 'dashmate' ),
					],
					[
						'value' => 'grid',
						'label' => esc_html__( 'Grid', 'dashmate' ),
					],
					[
						'value' => 'table',
						'label' => esc_html__( 'Table', 'dashmate' ),
					],
				],
			],
			'multi_check_field' => [
				'type'        => 'multi-check',
				'label'       => esc_html__( 'Multi-Check Field', 'dashmate' ),
				'description' => esc_html__( 'A multi-check field for testing.', 'dashmate' ),
				'default'     => [ 'item1', 'item3' ],
				'choices'     => [
					[
						'value' => 'item1',
						'label' => esc_html__( 'Item 1', 'dashmate' ),
					],
					[
						'value' => 'item2',
						'label' => esc_html__( 'Item 2', 'dashmate' ),
					],
					[
						'value' => 'item3',
						'label' => esc_html__( 'Item 3', 'dashmate' ),
					],
					[
						'value' => 'item4',
						'label' => esc_html__( 'Item 4', 'dashmate' ),
					],
				],
			],
			'sortable_field'    => [
				'type'        => 'sortable',
				'label'       => esc_html__( 'Sortable Field', 'dashmate' ),
				'description' => esc_html__( 'A sortable field for testing drag and drop functionality.', 'dashmate' ),
				'default'     => [ 'sort1', 'sort3', 'sort2' ],
				'choices'     => [
					[
						'value' => 'sort1',
						'label' => esc_html__( 'Sortable Item 1', 'dashmate' ),
					],
					[
						'value' => 'sort2',
						'label' => esc_html__( 'Sortable Item 2', 'dashmate' ),
					],
					[
						'value' => 'sort3',
						'label' => esc_html__( 'Sortable Item 3', 'dashmate' ),
					],
					[
						'value' => 'sort4',
						'label' => esc_html__( 'Sortable Item 4', 'dashmate' ),
					],
					[
						'value' => 'sort5',
						'label' => esc_html__( 'Sortable Item 5', 'dashmate' ),
					],
				],
			],
			'hidden_field'      => [
				'type'  => 'hidden',
				'value' => 'hidden_value_for_testing',
			],
		];

		$this->output_schema = [
			'html_content' => [
				'type'        => 'string',
				'required'    => true,
				'description' => esc_html__( 'HTML content to render.', 'dashmate' ),
			],
		];
	}

	/**
	 * Get widget content.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
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
	 * @return string HTML content.
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
