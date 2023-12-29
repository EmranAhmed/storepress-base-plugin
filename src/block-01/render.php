<?php
	namespace StorePress\Base;

	use WP_Block;

	defined( 'ABSPATH' ) || die( 'Keep Silent' );

	/**
	 * Dynamic Block Template.
	 * @global   array    $attributes -  A clean associative array of block attributes.
	 * @global   WP_Block $block      - The block instance. All the block settings and attributes.
	 * @global   string   $content    - The block inner HTML (usually empty unless using inner blocks).
	 */


	/**
	 * $unique_id = uniqid( 'p-' );
	 */

	$classes = array(
		'class-01' => true,
	);

	$styles = array(
		'--css-variable' => $attributes[ 'x' ] . '%',
	);

	$context = $block->context;

	$wrapper_attributes = get_block_wrapper_attributes( array(
															'class' => esc_attr( storepress_base_plugin()->get_blocks()->get_css_classes( $classes ) ),
															'style' => esc_attr( storepress_base_plugin()->get_blocks()->get_inline_styles( $styles ) ),
														) );

	$allowed_html = storepress_base_plugin()->get_blocks()->get_kses_allowed_html();
?>
<div <?php echo wp_kses_data( $wrapper_attributes ); ?>>
	<h1>StorePress Base Block - render.php</h1>
	<?php echo wp_kses( $content, $allowed_html ); ?>
</div>
