<?php
	/**
	 * Dynamic Block Template.
	 *
	 * @package    StorePress/Base
	 * @since      1.0.0
	 * @version    1.0.0
	 */

	declare( strict_types=1 );

	namespace StorePress\Base;

	use WP_Block;

	defined( 'ABSPATH' ) || die( 'Keep Silent' );
	/**
	 * The following variables are exposed to the file:
	 *
	 * @var array{x:int, y:int, id: int, parent: 'popup'|'slider'}    $attributes -  A clean associative array of block attributes.
	 * @var WP_Block $block      - The block instance. All the block settings and attributes.
	 * @var string   $content    - The block inner HTML (usually empty unless using inner blocks).
	 *
	 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
	 */

	/**
	 * $unique_id = uniqid( 'p-' );
	 */

	$classes = array(
		'class-01' => true,
	);

	$styles = array(
		'--css-variable' => $attributes['x'] . '%',
	);

	$context = $block->context;

	$wrapper_attributes = get_block_wrapper_attributes(
		array(
			'class' => esc_attr( get_blocks()->get_css_classes( $classes ) ),
			'style' => esc_attr( get_blocks()->get_inline_styles( $styles ) ),
		)
	);

	$allowed_html = get_blocks()->get_kses_allowed_html();
	?>
<div
	<?php
		echo wp_kses_data( $wrapper_attributes );
	?>
>
	<h1>StorePress Base Block - render.php</h1>
	<?php
		echo wp_kses( $content, $allowed_html );
	?>
</div>
