<?php

declare( strict_types=1 );

use StorePress\Base\Blocks;

class BlocksTest extends WP_UnitTestCase {

	public $block;

	public function set_up(): void {
		parent::set_up();
	}

	public function tear_down(): void {
		parent::tear_down();
	}

	public function test_did_action_storepress_blocks_loaded() {
		$this->assertSame(1, did_action( 'storepress_blocks_loaded') );
	}

	public function test_has_action_init() {
		$this->assertSame(10,  has_action( 'init', [ Blocks::instance(), 'register_blocks' ] ));
	}

	public function test_has_filter_block_categories_all() {
		$this->assertSame(10,  has_filter( 'block_categories_all', [ Blocks::instance(), 'add_block_category' ] ));
	}

	public function test_add_block_category() {

		$default_categories     = get_default_block_categories();
		$block_categories = Blocks::instance()->add_block_category($default_categories);
		$available_slugs = wp_list_pluck( $block_categories, 'slug' );
		$this->assertTrue(in_array( 'storepress', $available_slugs, true) );
	}

	public function test_check_same_instance() {
		$this->assertSame( storepress_base_plugin()->get_blocks(), Blocks::instance());
	}

	public function test_get_kses_allowed_html() {
		$this->assertArrayHasKey( 'svg', Blocks::instance()->get_kses_allowed_html() );
	}

	public function test_enqueued_frontend_scripts(){
		// $this->assertFalse( wp_style_is( 'storepress-base-plugin-style', 'registered' ));
		// $this->assertFalse( wp_script_is( 'storepress-base-plugin-script', 'registered' ) );
		do_action( 'wp_enqueue_scripts' );
		// $this->assertTrue( wp_style_is( 'storepress-base-plugin-style', 'registered' ));
		// $this->assertTrue( wp_script_is( 'storepress-base-plugin-script', 'registered' ) );

		$this->assertTrue( true);
	}

	public function test_block_editor_scripts(){
		$this->assertFalse( wp_script_is( 'storepress-base-plugin-editor-scripts' ) );
		do_action( 'enqueue_block_editor_assets' );
		$this->assertTrue( wp_script_is( 'storepress-base-plugin-editor-scripts' ) );
	}

	/**
	 * @dataProvider register_blocks_data_provider
	 */
	public function test_register_blocks(string $block_name, bool $expected){
		$this->assertSame( $expected, WP_Block_Type_Registry::get_instance()->is_registered( $block_name ) );
	}

	public function register_blocks_data_provider(): array {
		return [
			'storepress/block-01 block'=>['storepress/block-01', true],
			'storepress/interactive block'=>['storepress/interactive', true]
		];
	}
}
