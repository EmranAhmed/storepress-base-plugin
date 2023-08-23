<?php
	/**
	 * Plugin Class.
	 *
	 * @package    StorePress
	 * @subpackage Base
	 */

	namespace StorePress\Base;

	defined( 'ABSPATH' ) || die( 'Keep Silent' );


	/**
	 * Class Plugin.
	 */
class Plugin {

	/**
	 * Class Instance.
	 *
	 * @var Plugin
	 */
	protected static $instance = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->includes();
		$this->hooks();
		$this->init();
		do_action( 'storepress_base_plugin_loaded', $this );
	}

	/**
	 * Plugin Version.
	 */
	public function version() {
		return esc_attr( STOREPRESS_BASE_PLUGIN_VERSION );
	}

	/**
	 * Set constant if not defined and prevent reassign
	 *
	 * @param string $name  Constant name.
	 * @param array  $value Constant value.
	 *
	 * @return void No Return.
	 */
	protected function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Includes.
	 */
	public function includes() {
	}

	/**
	 * Initialize
	 */
	public function init() {
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		// Register with hook.
		add_action( 'init', array( $this, 'language' ), 1 );
		add_action( 'init', array( $this, 'register_blocks' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'block_editor_scripts' ) );
		add_filter( 'block_categories_all', array( $this, 'add_block_category' ), 10, 2 );
	}

	/**
	 *  Add Custom block category
	 *
	 * @param array  $block_categories     Available block category.
	 * @param object $block_editor_context Editor contaxt.
	 *
	 * @return array With New category.
	 */
	public function add_block_category( $block_categories, $block_editor_context ) {
		if ( empty( $block_editor_context->post ) ) {
			return $block_categories;
		}

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
	 */
	public function block_editor_scripts() {

		// Editor Scripts.
		$editor_script_src_url    = $this->build_url() . '/editor-scripts.js';
		$editor_script_asset_file = $this->build_path() . '/editor-scripts.asset.php';
		$editor_script_asset      = include_once $editor_script_asset_file;

		wp_enqueue_script( 'storepress-base-plugin-editor-scripts', $editor_script_src_url, $editor_script_asset['dependencies'], $editor_script_asset['version'], array( 'strategy' => 'defer' ) );
	}

	/**
	 * Block Frontend Script
	 */
	public function frontend_scripts() {
		$js_file_url  = $this->build_url() . '/frontend.js';
		$css_file_url = $this->build_url() . '/frontend.css';
		$asset_file   = $this->build_path() . '/frontend.asset.php';
		$asset        = include_once $asset_file;

		wp_register_style( 'storepress-base-plugin-style', $css_file_url, array(), $asset['version'] );
		wp_register_script( 'storepress-base-plugin-script', $js_file_url, $asset['dependencies'], $asset['version'], array( 'strategy' => 'defer' ) );
	}

	/**
	 * Block Register
	 */
	public function register_blocks() {
		register_block_type( $this->build_path() . '/pointer' );
	}

	/**
	 * Language
	 */
	public function language() {
		load_plugin_textdomain( 'storepress-base-plugin', false, $this->plugin_path() . '/languages' );
	}

	/**
	 * Get Plugin basename directory name
	 */
	public function basename() {
		return basename( dirname( STOREPRESS_BASE_PLUGIN_FILE ) );
	}

	/**
	 * Get Plugin basename
	 */
	public function plugin_basename() {
		return plugin_basename( STOREPRESS_BASE_PLUGIN_FILE );
	}

	/**
	 * Get Plugin directory name
	 */
	public function plugin_dirname() {
		return dirname( plugin_basename( STOREPRESS_BASE_PLUGIN_FILE ) );
	}

	/**
	 * Get Plugin directory path
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( STOREPRESS_BASE_PLUGIN_FILE ) );
	}

	/**
	 * Get Plugin directory url
	 */
	public function plugin_url() {
		return untrailingslashit( plugin_dir_url( STOREPRESS_BASE_PLUGIN_FILE ) );
	}

	/**
	 * Get Plugin image url
	 */
	public function images_url() {
		return untrailingslashit( plugin_dir_url( STOREPRESS_BASE_PLUGIN_FILE ) . 'images' );
	}


	/**
	 * Get WordPress.org asset url
	 *
	 * @param string $file Asset file name.
	 *
	 * @return string WordPress.org file url
	 */
	public function org_assets_url( $file = '' ) {
		return 'https://ps.w.org/storepress-base-plugin/assets' . $file . '?ver=' . $this->version();
	}

	/**
	 * Get Asset URL
	 */
	public function assets_url() {
		return untrailingslashit( plugin_dir_url( STOREPRESS_BASE_PLUGIN_FILE ) . 'assets' );
	}

	/**
	 * Get Asset path
	 */
	public function assets_path() {
		return $this->plugin_path() . '/assets';
	}

	/**
	 * Get Build URL
	 */
	public function build_url() {
		return untrailingslashit( plugin_dir_url( STOREPRESS_BASE_PLUGIN_FILE ) . 'build' );
	}

	/**
	 * Get Build path
	 */
	public function build_path() {
		return $this->plugin_path() . '/build';
	}

	/**
	 * Get Asset version
	 *
	 * @param string $file Asset file name.
	 *
	 * @return numeric asset file make time.
	 */
	public function assets_version( $file ) {
		return filemtime( $this->assets_path() . $file );
	}

	/**
	 * Get Include path
	 */
	public function include_path() {
		return untrailingslashit( plugin_dir_path( STOREPRESS_BASE_PLUGIN_FILE ) . 'includes' );
	}
}
