<?php
	/**
	 * Plugin Settings Service.
	 *
	 * @package    StorePress/Base
	 * @since      1.0.0
	 * @version    1.0.0
	 */

	namespace StorePress\Base\Services;

	defined( 'ABSPATH' ) || die( 'Keep Silent' );

	use StorePress\AdminUtils\Traits\SingletonTrait;
	use StorePress\Base\Integrations\AdminPage;

	/**
	 * Defines all plugin settings tabs and their field configurations.
	 *
	 * @name Settings
	 */
class Settings extends AdminPage {

	use SingletonTrait;

	/**
	 * Returns the settings tab definitions.
	 *
	 * @return array<string, string|array<string, mixed>>
	 * @since 1.0.0
	 */
	public function add_settings(): array {
		return array(
			'general' => 'General',

			'basic'   => array(
				'name'    => 'Basic',
				'sidebar' => 25,
			),

			'advance' => array(
				'name'     => 'Advanced',
				'icon'     => 'dashicons dashicons-analytics',
				'sidebar'  => false,
				'hidden'   => false,
				'external' => false,
			),

			'rest'    => 'Rest',
		);
	}

	/**
	 * Returns field definitions for the General settings tab.
	 *
	 * @return array<int, array<string, mixed>>
	 * @since 1.0.0
	 */
	public function add_general_settings_fields() {
		return array(
			array(
				'type'        => 'section',
				'title'       => 'Section title',
				'description' => 'Section description',
			),

			array(
				'id'            => 'field-text-mn',
				'type'          => 'text',
				'title'         => 'Input Type text',
				'description'   => 'Input Description',
				'placeholder'   => 'Placeholder',
				'default'       => 'text field default',
				'suffix'        => 'px',
				'required'      => true,
				'add_tag'       => array( 'PRO' ),
				'condition'     => array( 'selector' => $this->get_field_selector( 'inputxxxxx' ) ),
				'html_datalist' => array( 'yes', 'no' ),
				'class'         => array( 'large-text', 'code', 'custom-class' ),
			),


			array(
				'id'       => 'grps',
				'type'     => 'toggle',
				'title'    => 'Show Grpups',
				'default'  => 'no',
				'tooltip'  => 'Textarea Help tooltip',
				'required' => true,
				'add_tag'  => array( 'NEW', '#d63639' ),
				'options'  => array(
					'key'  => 'value',
					'key2' => 'value2',
				),
			),


			array(
				'condition'    => array( 'selector' => $this->get_field_selector( 'grps' ) ),
				'id'           => 'input_group',
				'type'         => 'group',
				'show_in_rest' => false,
				'title'        => 'Input text 01 general',
				'description'  => 'Input desc of 01',
				'tooltip'      => 'Input Group Help Tooltip',
				'fields'       => array(

					array(
						'id'            => 'inputaxxx',
						'type'          => 'text',
						'title'         => 'Single Toggle',
						'placeholder'   => 'Abcd',
						'default'       => 'yes',
						'html_datalist' => array( 'yes', 'no' ),
						'class'         => array( 'large-text' ),
						'tooltip'       => 'Textarea Help tooltip',
						'required'      => true,
					),

					array(
						'id'            => 'field-color',
						'type'          => 'color',
						'title'         => 'Input Type Color',
						'description'   => 'Input Type Color',
						'placeholder'   => 'Placeholder',
						'default'       => '#ffccff',
						'html_datalist' => array( '#dddddd', '#eeeeee' ),
						'tooltip'       => 'Textarea Help tooltip',
						'required'      => true,
						'show_in_rest'  => array( 'x' ),
						'class'         => array( 'large-text', 'code', 'custom-class' ),
					),

					array(
						'id'          => 'inputakk',
						'type'        => 'unit',
						'title'       => 'Single Unit',
						'placeholder' => '',
						'default'     => '20px',
						'units'       => array( '%', 'px' ),
						'tooltip'     => 'Textarea Help tooltip',
						'required'    => true,
					),

					array(
						'id'          => 'inputas',
						'type'        => 'toggle',
						'title'       => 'Single Toggle',
						'placeholder' => 'Abcd',
						'default'     => 'no',
						'tooltip'     => 'Textarea Help tooltip',
						'required'    => true,
						'options'     => array( 'X', 'Y', 'Z' ),
					),

					array(
						'id'          => 'input2',
						'type'        => 'checkbox',
						'title'       => 'Single Checkbox',
						'placeholder' => 'Abcd',
						'default'     => 'yes',
						'tooltip'     => 'Textarea Help tooltip',
						'required'    => true,
					),

					array(
						'show_in_rest'      => false,
						'condition'         => array( 'selector' => $this->get_group_field_selector( 'input_group', 'inputas' ) ),
						'id'                => 'input5',
						'type'              => 'number',
						'title'             => 'Width WW',
						'description'       => 'Input desc of 01',
						'placeholder'       => 'Abcd',
						'default'           => '100',
						'suffix'            => 'x',
						'sanitize_callback' => 'absint',
						'html_attributes'   => array( 'min' => 10 ),
						'tooltip'           => 'Textarea Help tooltip',
						'required'          => true,
					),

					array(
						'id'              => 'input45',
						'type'            => 'textarea',
						'title'           => 'Width',
						'description'     => 'Input desc of 01',
						'placeholder'     => 'Abcd',
						'default'         => '100',
						'suffix'          => 'x',
						'html_attributes' => array( 'min' => 10 ),
						'tooltip'         => 'Textarea Help tooltip',
						'required'        => true,
					),

					array(
						'id'              => 'inputse2xx',
						'type'            => 'select',
						'title'           => 'Int value',
						'description'     => 'Input desc of 01<code>rxxx</code>',
						'default'         => '2',
						'class'           => array( 'x', 'y' ),
						'tooltip'         => 'Textarea Help tooltip',
						'required'        => true,
						'html_attributes' => array( 'data-demo' => true ),
						'options'         => array(
							'1' => 'Home One',
							'2' => 'Home Two',
							'3' => 'Home three',
						),
					),

					array(
						'id'          => 'input_wi',
						'type'        => 'radio',
						'title'       => 'Width X',
						'placeholder' => 'Abcd',
						'default'     => 'y',
						'options'     => array(
							'x' => 'Home X',
							'y' => 'Home Y',
							'z' => 'Home Z',
						),
						'tooltip'     => 'Textarea Help tooltip',
						'required'    => true,
					),

					array(
						'id'       => 'input_wx',
						'type'     => 'checkbox',
						'title'    => 'Multi Checkbox',
						'default'  => array( 'y', 'z' ),
						'options'  => array(
							'x' => 'Home X',
							'y' => 'Home Y',
							'z' => 'Home Z',
						),
						'tooltip'  => 'Textarea Help tooltip',
						'required' => true,
					),

					array(
						'id'       => 'input_wewdsf',
						'type'     => 'toggle',
						'title'    => 'Multi Toggle Checkbox',
						'default'  => array( 'y', 'z' ),
						'options'  => array(
							'x' => 'Home X',
							'y' => 'Home Y',
							'z' => 'Home Z',
						),
						'tooltip'  => 'Textarea Help tooltip',
						'required' => true,
					),
				),
			),

			array(
				'id'           => 'field-password',
				'type'         => 'password',
				'title'        => 'Input Type Password',
				'description'  => 'Input Description',
				'placeholder'  => 'Placeholder',
				'default'      => 'text field default',
				'required'     => true,
				'tooltip'      => 'Password Help tooltip',
				'condition'    => array( 'selector' => $this->get_field_selector( 'field-textarea' ) ),
				'show_in_rest' => array( 'name' => 'custom_rest_id' ),
			),

			array(
				'id'           => 'field-textarea',
				'type'         => 'textarea',
				'title'        => 'Input Type text',
				'description'  => 'Input Description',
				'placeholder'  => 'Placeholder',
				'default'      => '',
				'suffix'       => 'px',
				'required'     => true,
				'tooltip'      => 'Textarea Help tooltip',
				'show_in_rest' => array( 'name' => 'custom_rest_id' ),
			),

			array(
				'id'            => 'field-text',
				'type'          => 'text',
				'title'         => 'Input Type text',
				'description'   => 'Input Description',
				'placeholder'   => 'Placeholder',
				'default'       => 'text field default',
				'suffix'        => 'px',
				'required'      => true,
				'html_datalist' => array( 'yes', 'no' ),
			),

			array(
				'id'          => 'field-number',
				'type'        => 'number',
				'title'       => 'Input Type Number',
				'description' => 'Input Type Number',
				'placeholder' => '',
				'default'     => '1',
				'suffix'      => 'px',
			),


			array(
				'id'            => 'field-color',
				'type'          => 'color',
				'title'         => 'Input Type Color',
				'description'   => 'Input Type Color',
				'placeholder'   => 'Placeholder',
				'default'       => '#ffccff',
				'html_datalist' => array( '#dddddd', '#eeeeee' ),
			),

			array(
				'id'          => 'field-radio',
				'type'        => 'radio',
				'title'       => 'Input Type Radio',
				'description' => 'Input Type Radio',
				'placeholder' => 'Placeholder',
				'default'     => 'y',
				'options'     => array(
					'x' => 'Home X',
					'y' => 'Home Y',
					'z' => 'Home Z',
				),
			),

			array(
				'id'           => 'license',
				'type'         => 'code',
				'title'        => 'License',
				'private'      => true,
				'show_in_rest' => false,
				'description'  => 'Input for license',
				'placeholder'  => 'xxxx-xxxx-xxxx',
			),

			array(
				'id'          => 'input-single-check',
				'type'        => 'checkbox',
				'title'       => 'Single Checkbox Full',
				'description' => 'Single Checkbox Full desc',
				'default'     => 'yes',
				'full_width'  => true,
				'add_tag'     => array( 'NEW', '#d63639' ),
			),

			array(
				'id'          => 'input-single-toggle',
				'type'        => 'toggle',
				'title'       => 'Single Toggle Full',
				'description' => 'Single Checkbox Full desc',
				'default'     => 'yes',
				'full_width'  => true,
			),

			array(
				'id'          => 'input-multi-check',
				'type'        => 'checkbox',
				'title'       => 'Multiple Checkbox Full',
				'description' => 'Multiple Checkbox  Full desc',
				'default'     => 'yes',
				'options'     => array(
					'x'     => 'Home X',
					'y'     => 'Home Y',
					'new'   => array(
						'label'       => 'New',
						'description' => 'New Item',
					),
					'z'     => 'Home Z',
					'alpha' => 'Alpha',
					'yes'   => 'YES',

				),
			),

			array(
				'id'      => 'input-multi-check-toggle',
				'type'    => 'toggle',
				'title'   => 'Multiple Toggle Full',
				'default' => 'yes',
				'options' => array(
					'x'     => 'Home X',
					'new'   => array(
						'label'       => 'New',
						'description' => 'New Item',
					),
					'y'     => 'Home Y',
					'z'     => 'Home Z',
					'alpha' => 'Alpha',
				),
			),

			array(
				'id'          => 'inputr',
				'type'        => 'radio',
				'title'       => 'Input text 01 general',
				'description' => 'Input desc of 01',
				'default'     => 'home2',
				'options'     => array(
					'home'  => 'Home One',
					'home2' => 'Home Twos',
				),
			),

			array(
				'id'          => 'inputc',
				'type'        => 'checkbox',
				'title'       => 'Input text 01 general single checkbox',
				'description' => 'Input desc of 01',
				'default'     => 'home3',
				'options'     => array(
					'home1' => 'Home One',
					'home3' => 'Home 3',
					'home2' => 'Home 2',
				),
			),

			array(
				'id'              => 'inputse',
				'type'            => 'wc-enhanced-select',
				'title'           => '2 Input text 01 general single selectbox',
				'description'     => 'Input desc of 01<code>xxx</code>',
				'default'         => 'home3',
				'class'           => array( 'x', 'y' ),
				'html_attributes' => array( 'data-demo' => true ),
				'options'         => array(
					'home1' => 'Home One',
					'home3' => 'Home 3',
					'home2' => 'Home 2',
				),
			),

			array(
				'id'              => 'inputse2xx',
				'type'            => 'wc-enhanced-select',
				'title'           => 'Int value',
				'description'     => 'Input desc of 01<code>rxxx</code>',
				'default'         => '2',
				'class'           => array( 'x', 'y' ),
				'html_attributes' => array( 'data-demo' => true ),
				'options'         => array(
					'1' => 'Home One',
					'2' => 'Home Two',
					'3' => 'Home three',
				),
			),

			array(
				'id'          => 'input2',
				'type'        => 'text',
				'title'       => 'Input text 02',
				'description' => 'Input desc of 02',
				'default'     => '',
				'placeholder' => 'Abcd 02',
			),

			array(
				'id'              => 'inputunit',
				'type'            => 'unit',
				'title'           => 'Input text unit',
				'description'     => 'Input desc of unit',
				'default'         => '10px',
				'html_attributes' => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 5,
				),
				'units'           => array( 'px', '%', 'em', 'rem' ),
				'condition'       => array( 'selector' => $this->get_field_selector( 'input2' ) ),
			),

			array(
				'type'        => 'section',
				'title'       => 'Section 02',
				'description' => 'Section of 02',
			),
		);
	}

	/**
	 * Returns field definitions for the Rest settings tab.
	 *
	 * @return array<int, array<string, mixed>>
	 * @since 1.0.0
	 */
	public function add_rest_settings_fields() {
		return array(
			array(
				'type'        => 'section',
				'title'       => 'Section rest',
				'description' => 'Section description',
			),

			array(
				'id'           => 'field-textarea-x',
				'type'         => 'textarea',
				'title'        => 'Input Type text',
				'description'  => 'Input Description',
				'placeholder'  => 'Placeholder',
				'default'      => 'text field default',
				'suffix'       => 'px',
				'required'     => true,
				'add_tag'      => array( 'NEW' ),
				'show_in_rest' => array( 'name' => 'custom_rest_id' ),
			),

			array(
				'id'          => 'field-text-x',
				'type'        => 'text',
				'title'       => 'Input Type text',
				'description' => 'Input Description',
				'placeholder' => 'Placeholder',
				'suffix'      => 'px',
				'add_tag'     => array( 'PRO', '#d63639' ),
			),

			array(
				'id'              => 'field-text-x-dep',
				'type'            => 'unit',
				'title'           => 'Input Type Unit extra comp',
				'description'     => 'Input Type Number',
				'placeholder'     => '',
				'default'         => '10px',
				'html_attributes' => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 5,
				),
				'units'           => array( 'px', '%', 'em', 'rem' ),
				'condition'       => array( 'selector' => $this->get_field_selector( 'field-text-x' ) ),
			),

			array(
				'id'          => 'field-text-x-dep-text',
				'type'        => 'text',
				'title'       => 'Input Type Unit extra comp 2',
				'description' => 'Input Type Number',
				'placeholder' => '',
				'condition'   => array( 'selector' => $this->get_field_selector( 'field-text-x' ) ),
			),

			array(
				'id'           => 'field-number-x',
				'type'         => 'number',
				'title'        => 'Input Type Number',
				'description'  => 'Input Type Number',
				'placeholder'  => '',
				'default'      => '1',
				'suffix'       => 'px',
				'add_tag'      => array( 'BETA', '#000' ),

				'show_in_rest' => array( 'name' => 'fieldNumberX' ),
			),

			array(
				'id'           => 'field-extra2-x',
				'type'         => 'number',
				'title'        => 'Input Type Number extra',
				'description'  => 'Input Type Number',
				'placeholder'  => '',
				'default'      => '1',
				'suffix'       => 'px',
				'show_in_rest' => 'fieldExtraX',
			),

			array(
				'id'              => 'field-extra2-unit',
				'type'            => 'unit',
				'title'           => 'Input Type Unit extra',
				'description'     => 'Input Type Number',
				'placeholder'     => '',
				'default'         => '10px',
				'html_attributes' => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 5,
				),
				'units'           => array( 'px', '%', 'em', 'rem' ),

				'show_in_rest'    => array(
					'name'   => 'fieldExtra2Unit',
					'schema' => array(
						'type'        => 'string',
						'description' => 'string',
						'required'    => 'boolean',
					),
				),

			),
		);
	}
}
