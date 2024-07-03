<?php

declare( strict_types=1 );

use StorePress\Base\Common;

class CommonTest extends WP_UnitTestCase {
	use Common;

	public function test_instance() {
		$this->assertInstanceOf('CommonTest', self::instance());
	}

	public function test_always_return_same_instance() {
		$a = self::instance();
		$b = self::instance();
		$c = self::instance();

		$this->assertSame( $a, $b );
		$this->assertSame( $a, $c );
		$this->assertSame( $c, $b );
	}

	/**
	 * @dataProvider get_html_attributes_data_provider
	 */
	public function test_get_html_attributes( array $attributes, array $exclude, string $expected ) {
		$this->assertSame( $expected, $this->get_html_attributes( $attributes, $exclude ) );
	}

	public function get_html_attributes_data_provider(): array {
		return [
			'simple attributes'                                           =>[ [
				'class'          => 'hello ok',
				'data-name'      => 'name',
				'data-only-attr' => true,
				'data-only-null' => null,
			],
			[],
			'class="hello ok" data-name="name" data-only-attr'],

			'with class array but not data-name-2'                        => [[
				'class'          => [ 'x', 'y', 'z' ],
				'data-name'      => 'name',
				'data-name-2'    => 'name',
				'data-only-attr' => true,
				'data-only-null' => null,
			],
			[ 'data-name-2' ],
			'class="x y z" data-name="name" data-only-attr'],

			'with class array and custom attribute array not data-name-2' =>[ [
				'data-global'      => [ 'x', 'y', 'z' ],
				'class'            => [ 'x', 'y' => false, 'z' ],
				'data-name'        => 'name',
				'data-name-2'      => 'skip',
				'data-only-attr-2' => 1000,
				'data-only-attr-3' => '',
				'data-only-attr'   => true,
				'data-only-false'  => false,
			],
			[ 'data-name-2' ],
			'data-global="[&quot;x&quot;,&quot;y&quot;,&quot;z&quot;]" class="x z" data-name="name" data-only-attr-2="1000" data-only-attr'],
		];
	}

	/**
	 * @dataProvider get_inline_styles_data_provider
	 */
	public function test_get_inline_styles(array $style_properties, string $expected) {
		$this->assertSame( $expected, $this->get_inline_styles( $style_properties ) );
	}

	public function get_inline_styles_data_provider(): array {
		return [
			'simple attribute'=>[[
				                  'display'             => 'block',
				                  'color'               => '#ffccff',
				                  '--custom-variable'   => '0px',
				                  '--custom-variable-2' => 'var(--custom-variable)'
			                  ], 'display: block; color: #ffccff; --custom-variable: 0px; --custom-variable-2: var(--custom-variable);'],
			'only attribute value type  should string'=>[[
				                  'display'             => 'block',
				                  'color'               => '#ffccff',
				                  '--custom-variable'   => '0px',
				                  '--custom-variable-2' => 'var(--custom-variable)',
				                  'background'          => null,
				                  'background-color'    => true,
				                  'background-image'    => false,
			                  ], 'display: block; color: #ffccff; --custom-variable: 0px; --custom-variable-2: var(--custom-variable);'],
			'attribute value type  should not empty string'=>[[
				                                             'display'             => 'block',
				                                             'color'               => '#ffccff',
				                                             '--custom-variable'   => '0px',
				                                             '--custom-variable-2' => 'var(--custom-variable)',
				                                             'background'          => '',
				                                             'background-color'    => '',
				                                             'background-image'    => '',
			                                             ], 'display: block; color: #ffccff; --custom-variable: 0px; --custom-variable-2: var(--custom-variable);'],
		];
	}

	public function test_get_css_classes() {
		$numeric_classes = [
			'class-a',
			'class-b',
			'',
			123,
			false,
			null,
			true,
			[],
		];

		$expect = 'class-a class-b';

		$this->assertEquals( $expect, $this->get_css_classes( $numeric_classes ) );


		$conditional_classes = [
			'class-raw',
			'class-a' => true,
			'class-b' => null,
			'',
			2233,
			false,
			null,
			true,
			[],
			[ 'YYY', 'TTTT' ],
			'class-x' => '',
			'class-y' => 'hello',
			'class-p' => [],
			'class-k' => 123,
			'class-d' => [ 'okook' ],
		];

		$expect = 'class-raw class-a class-y class-k class-d';

		$this->assertEquals( $expect,
			$this->get_css_classes( $conditional_classes ) );
	}

	public function test_is_empty_string() {
		$test_1 = "";
		$test_2 = " \t ";
		$test_3 = "   \n ";
		$test_4 = ".";

		$this->assertTrue( $this->is_empty_string( $test_1 ) );
		$this->assertTrue( $this->is_empty_string( $test_2 ) );
		$this->assertTrue( $this->is_empty_string( $test_3 ) );
		$this->assertFalse( $this->is_empty_string( $test_4 ) );
	}

	public function test_is_empty_array() {
		$test_1 = []; // true

		$test_2 = [ [] ]; // false
		$test_3 = [ '' ]; // false
		$test_4 = [ '', '' ]; // false
		$test_5 = [ '', '_' ]; // false
		$test_6 = [ '', 0, null, false, [] ]; // false
		$test_7 = [ 'x' => true, 'y' => false, 'z' => false ]; // false
		$test_8 = [ 'x' => 0, 'y' => false, 'z' => false ]; // false
		$test_9 = [ 0, 0, 0 ]; // false

		$this->assertTrue( $this->is_empty_array( $test_1 ) );
		$this->assertFalse( $this->is_empty_array( $test_2 ) );
		$this->assertFalse( $this->is_empty_array( $test_3 ) );
		$this->assertFalse( $this->is_empty_array( $test_4 ) );
		$this->assertFalse( $this->is_empty_array( $test_5 ) );
		$this->assertFalse( $this->is_empty_array( $test_6 ) );
		$this->assertFalse( $this->is_empty_array( $test_7 ) );
		$this->assertFalse( $this->is_empty_array( $test_8 ) );
		$this->assertFalse( $this->is_empty_array( $test_9 ) );
	}

	public function test_is_array_each_empty_value() {
		$test_1 = []; // true
		$test_2 = [ [] ]; // true
		$test_3 = [ '' ]; // true
		$test_4 = [ '', '' ]; // true
		$test_5 = [ '', '_' ]; // false
		$test_6 = [ '', 0, null, false, [] ]; // true
		$test_7 = [ 'x' => true, 'y' => false, 'z' => false ]; // false
		$test_8 = [ 'x' => 0, 'y' => false, 'z' => false ]; // true
		$test_9 = [ 0, 0, 0 ]; // true

		$this->assertTrue( $this->is_array_each_empty_value( $test_1 ) );
		$this->assertTrue( $this->is_array_each_empty_value( $test_2 ) );
		$this->assertTrue( $this->is_array_each_empty_value( $test_3 ) );
		$this->assertTrue( $this->is_array_each_empty_value( $test_4 ) );
		$this->assertFalse( $this->is_array_each_empty_value( $test_5 ) );
		$this->assertTrue( $this->is_array_each_empty_value( $test_6 ) );
		$this->assertFalse( $this->is_array_each_empty_value( $test_7 ) );
		$this->assertTrue( $this->is_array_each_empty_value( $test_8 ) );
		$this->assertTrue( $this->is_array_each_empty_value( $test_9 ) );
	}

	/**
	 * @dataProvider array_is_list_data_provider
	 */
	public function test_array_is_list( array $check, bool $expected ) {
		$this->assertSame( $expected, $this->array_is_list( $check ) );
	}

	public function array_is_list_data_provider(): array {
		return [
			'blank array'                             => [ [], true ],
			'blank deep array'                        => [ [ [] ], true ],
			'numeric array with mixed value'          => [
				[ 'apple', 2, 3 ],
				true,
			],
			'array does not start at 0'               => [
				[ 1 => 'apple', 'orange' ],
				false,
			],
			'array keys are not in the correct order' => [
				[
					1 => 'apple',
					0 => 'orange',
				],
				false,
			],
			'array has non-integer keys'              => [
				[
					0     => 'apple',
					'foo' => 'bar',
				],
				false,
			],
			'Non-consecutive keys'                    => [
				[
					0 => 'apple',
					2 => 'bar',
				],
				false,
			],
		];
	}
}
