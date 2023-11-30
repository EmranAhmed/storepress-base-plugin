<?php
	/**
	 * Blocks API: Blocks class
	 *
	 * @package    StorePress
	 * @subpackage Base
	 * @since      1.0.0
	 */

	namespace StorePress\Base;

	defined( 'ABSPATH' ) || die( 'Keep Silent' );

	/**
	 *  Blocks Class.
	 *
	 * @since 1.0.0
	 */
class Blocks {

	use Common;

	/**
	 * Initialise class.
	 *
	 * @since      1.0.0
	 */
	protected function __construct() {
		$this->includes();
		$this->hooks();
		$this->init();

		/**
		 * Action to signal that Plugin has finished loading.
		 *
		 * @param Blocks $this Plugin Object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'storepress_blocks_loaded', $this );
	}


	/**
	 * Include Blocks Dependencies
	 *
	 * @since      1.0.0
	 */
	public function includes() {
	}

	/**
	 * Blocks Hooks
	 *
	 * @since      1.0.0
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'register_blocks' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'block_editor_scripts' ) );
		add_filter( 'block_categories_all', array( $this, 'add_block_category' ) );
	}

	/**
	 * Initialize Blocks Included Classes
	 *
	 * @since      1.0.0
	 */
	public function init() {
	}

	/**
	 *  Add custom block category
	 *
	 * @param array $block_categories Available block category.
	 *
	 * @return array New category.
	 * @since      1.0.0
	 */
	public function add_block_category( array $block_categories ): array {

		$category = array(
			'slug'  => 'storepress',
			'title' => esc_html__( 'StorePress', 'storepress-base-plugin' ),
			'icon'  => null,
		);

		array_unshift( $block_categories, $category );

		return $block_categories;
	}

	/**
	 * Block Editor Script
	 *
	 * @since      1.0.0
	 */
	public function block_editor_scripts() {

		// Editor Scripts.
		$editor_script_src_url    = storepress_base_plugin()->build_url() . '/editor-scripts.js';
		$editor_script_asset_file = storepress_base_plugin()->build_path() . '/editor-scripts.asset.php';
		$editor_script_asset      = require $editor_script_asset_file;

		wp_enqueue_script( 'storepress-base-plugin-editor-scripts', $editor_script_src_url, $editor_script_asset['dependencies'], $editor_script_asset['version'], array( 'strategy' => 'defer' ) );

		// Docs: https://developer.wordpress.org/reference/functions/wp_set_script_translations/.
		wp_set_script_translations( 'storepress-base-plugin-editor-scripts', 'storepress-base-plugin', storepress_base_plugin()->plugin_path() . '/languages' );
	}

	/**
	 * Block Frontend Script
	 *
	 * @since      1.0.0
	 */
	public function frontend_scripts() {
		$js_file_url  = storepress_base_plugin()->build_url() . '/frontend.js';
		$css_file_url = storepress_base_plugin()->build_url() . '/frontend.css';
		$asset_file   = storepress_base_plugin()->build_path() . '/frontend.asset.php';
		if ( ! file_exists( $asset_file ) ) {
			return;
		}
		$asset = require $asset_file;

		wp_register_style( 'storepress-base-plugin-style', $css_file_url, array(), $asset['version'] );
		wp_register_script( 'storepress-base-plugin-script', $js_file_url, $asset['dependencies'], $asset['version'], array( 'strategy' => 'defer' ) );
	}

	/**
	 * Block Register
	 *
	 * @since      1.0.0
	 */
	public function register_blocks() {

		if ( ! file_exists( storepress_base_plugin()->build_path() ) ) {
			return;
		}

		// Scanning block.json directory.
		$block_json_files = glob( storepress_base_plugin()->build_path() . '/**/block.json' );

		// Auto register all blocks that were found.
		foreach ( $block_json_files as $filename ) {
			$block_type = dirname( $filename );
			register_block_type( $block_type );
		}
	}

	/**
	 * Returns an array of allowed HTML tags and attributes for a given context.
	 *
	 * @param array $args extra argument.
	 *
	 * @return array
	 */
	public function get_kses_allowed_html( array $args = array() ): array {

		$defaults = wp_kses_allowed_html( 'post' );

		$svg_args = array(
			'svg'   => array(
				'class'           => true,
				'aria-hidden'     => true,
				'aria-labelledby' => true,
				'role'            => true,
				'xmlns'           => true,
				'width'           => true,
				'height'          => true,
				'viewbox'         => true, // <= Must be Lowercase!
			),
			'g'     => array( 'fill' => true ),
			'title' => array( 'title' => true ),
			'path'  => array(
				'd'    => true,
				'fill' => true,
			),
		);

		return array_merge( $defaults, $svg_args, $args );
	}
}
