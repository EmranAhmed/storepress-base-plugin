<?php

declare(strict_types=1);

namespace Unit\Services;

use StorePress\Base\Services\BlockSupport;
use WP_UnitTestCase;

class BlockSupportTest extends WP_UnitTestCase
{

	public function set_up(): void
	{
		parent::set_up();
	}

	public function tear_down(): void
	{
		parent::tear_down();
	}

	// Background.

	public function test_get_background_style_returns_nulls_without_attributes()
	{
		$support = new BlockSupport([]);

		$this->assertSame(
			[
				'backgroundImage'      => null,
				'backgroundSize'       => null,
				'backgroundPosition'   => null,
				'backgroundRepeat'     => null,
				'backgroundAttachment' => null,
			],
			$support->get_background_style()
		);
	}

	public function test_get_background_style_defaults_size_to_cover_when_image_set()
	{
		$support = new BlockSupport([
			'style' => [
				'background' => [
					'backgroundImage' => ['url' => 'https://example.com/image.jpg'],
				],
			],
		]);

		$styles = $support->get_background_style();

		$this->assertSame(['url' => 'https://example.com/image.jpg'], $styles['backgroundImage']);
		$this->assertSame('cover', $styles['backgroundSize']);
		$this->assertNull($styles['backgroundPosition']);
	}

	public function test_get_background_style_centers_position_for_contain_size()
	{
		$support = new BlockSupport([
			'style' => [
				'background' => [
					'backgroundImage' => ['url' => 'https://example.com/image.jpg'],
					'backgroundSize'  => 'contain',
				],
			],
		]);

		$styles = $support->get_background_style();

		$this->assertSame('contain', $styles['backgroundSize']);
		$this->assertSame('50% 50%', $styles['backgroundPosition']);
	}

	public function test_get_background_style_keeps_explicit_position_for_contain_size()
	{
		$support = new BlockSupport([
			'style' => [
				'background' => [
					'backgroundImage'    => ['url' => 'https://example.com/image.jpg'],
					'backgroundSize'     => 'contain',
					'backgroundPosition' => '0% 100%',
				],
			],
		]);

		$this->assertSame('0% 100%', $support->get_background_style()['backgroundPosition']);
	}

	public function test_get_background_style_does_not_default_size_without_image()
	{
		$support = new BlockSupport([
			'style' => [
				'background' => [
					'backgroundRepeat'     => 'no-repeat',
					'backgroundAttachment' => 'fixed',
				],
			],
		]);

		$styles = $support->get_background_style();

		$this->assertNull($styles['backgroundSize']);
		$this->assertSame('no-repeat', $styles['backgroundRepeat']);
		$this->assertSame('fixed', $styles['backgroundAttachment']);
	}

	// Color.

	public function test_get_color_style_with_preset_attributes()
	{
		$support = new BlockSupport([
			'backgroundColor' => 'primary',
			'textColor'       => 'white',
			'gradient'        => 'vivid-cyan-blue-to-vivid-purple',
		]);

		$this->assertSame(
			[
				'background' => 'var:preset|color|primary',
				'gradient'   => 'var:preset|gradient|vivid-cyan-blue-to-vivid-purple',
				'text'       => 'var:preset|color|white',
			],
			$support->get_color_style()
		);
	}

	public function test_get_color_style_with_custom_values()
	{
		$support = new BlockSupport([
			'style' => [
				'color' => [
					'background' => '#ff0000',
					'gradient'   => 'linear-gradient(135deg,#000 0%,#fff 100%)',
					'text'       => '#ffffff',
				],
			],
		]);

		$this->assertSame(
			[
				'background' => '#ff0000',
				'gradient'   => 'linear-gradient(135deg,#000 0%,#fff 100%)',
				'text'       => '#ffffff',
			],
			$support->get_color_style()
		);
	}

	public function test_get_color_style_prefers_preset_over_custom()
	{
		$support = new BlockSupport([
			'backgroundColor' => 'primary',
			'style'           => [
				'color' => [
					'background' => '#ff0000',
				],
			],
		]);

		$this->assertSame('var:preset|color|primary', $support->get_color_style()['background']);
	}

	public function test_get_color_style_ignores_non_string_preset_slug()
	{
		$support = new BlockSupport([
			'backgroundColor' => ['primary'],
			'style'           => [
				'color' => [
					'background' => '#ff0000',
				],
			],
		]);

		$this->assertSame('#ff0000', $support->get_color_style()['background']);
	}

	// Spacing.

	public function test_get_spacing_style()
	{
		$support = new BlockSupport([
			'style' => [
				'spacing' => [
					'padding'  => ['top' => '10px', 'bottom' => '20px'],
					'margin'   => '2rem',
					'blockGap' => 'var:preset|spacing|40',
				],
			],
		]);

		$this->assertSame(
			[
				'padding'  => ['top' => '10px', 'bottom' => '20px'],
				'margin'   => '2rem',
				'blockGap' => 'var:preset|spacing|40',
			],
			$support->get_spacing_style()
		);
	}

	public function test_get_spacing_style_returns_nulls_without_attributes()
	{
		$support = new BlockSupport([]);

		$this->assertSame(
			[
				'padding'  => null,
				'margin'   => null,
				'blockGap' => null,
			],
			$support->get_spacing_style()
		);
	}

	// Border.

	public function test_get_border_style_with_uniform_border_and_preset_color()
	{
		$support = new BlockSupport([
			'borderColor' => 'primary',
			'style'       => [
				'border' => [
					'width'  => '2px',
					'style'  => 'solid',
					'radius' => '8px',
					'color'  => '#000000',
				],
			],
		]);

		$styles = $support->get_border_style();

		$this->assertSame('8px', $styles['radius']);
		$this->assertSame('solid', $styles['style']);
		$this->assertSame('2px', $styles['width']);
		$this->assertSame('var:preset|color|primary', $styles['color']);
	}

	public function test_get_border_style_with_custom_color()
	{
		$support = new BlockSupport([
			'style' => [
				'border' => [
					'color' => '#000000',
				],
			],
		]);

		$this->assertSame('#000000', $support->get_border_style()['color']);
	}

	public function test_get_border_style_with_individual_sides()
	{
		$support = new BlockSupport([
			'style' => [
				'border' => [
					'top'    => ['width' => '1px', 'color' => '#000', 'style' => 'solid'],
					'bottom' => ['width' => '2px', 'color' => '#333', 'style' => 'dashed'],
				],
			],
		]);

		$styles = $support->get_border_style();

		$this->assertSame(['width' => '1px', 'color' => '#000', 'style' => 'solid'], $styles['top']);
		$this->assertSame(['width' => '2px', 'color' => '#333', 'style' => 'dashed'], $styles['bottom']);
		$this->assertSame(['width' => null, 'color' => null, 'style' => null], $styles['left']);
		$this->assertSame(['width' => null, 'color' => null, 'style' => null], $styles['right']);
	}

	public function test_get_border_style_with_per_corner_radius()
	{
		$radius  = ['topLeft' => '10px', 'bottomRight' => '10px'];
		$support = new BlockSupport([
			'style' => [
				'border' => [
					'radius' => $radius,
				],
			],
		]);

		$this->assertSame($radius, $support->get_border_style()['radius']);
	}

	// Shadow.

	/**
	 * @dataProvider shadow_data_provider
	 */
	public function test_get_shadow_style(array $attributes, ?string $expected)
	{
		$support = new BlockSupport($attributes);

		$this->assertSame($expected, $support->get_shadow_style());
	}

	public function shadow_data_provider(): array
	{
		return [
			'preset shadow'    => [['style' => ['shadow' => 'var:preset|shadow|natural']], 'var:preset|shadow|natural'],
			'custom shadow'    => [['style' => ['shadow' => '0 4px 6px rgba(0, 0, 0, 0.1)']], '0 4px 6px rgba(0, 0, 0, 0.1)'],
			'no shadow'        => [[], null],
			'non string value' => [['style' => ['shadow' => ['value' => 'foo']]], null],
		];
	}

	// Dimensions.

	/**
	 * @dataProvider dimensions_data_provider
	 */
	public function test_get_dimensions_style(array $attributes, array $expected)
	{
		$support = new BlockSupport($attributes);

		$this->assertSame($expected, $support->get_dimensions_style());
	}

	public function dimensions_data_provider(): array
	{
		return [
			'no dimensions'               => [
				[],
				['minHeight' => null, 'aspectRatio' => null],
			],
			'top level min height'        => [
				['minHeight' => '400px'],
				['minHeight' => '400px', 'aspectRatio' => 'unset'],
			],
			'nested min height'           => [
				['style' => ['dimensions' => ['minHeight' => '500px']]],
				['minHeight' => '500px', 'aspectRatio' => 'unset'],
			],
			'top level wins over nested'  => [
				['minHeight' => '400px', 'style' => ['dimensions' => ['minHeight' => '500px']]],
				['minHeight' => '400px', 'aspectRatio' => 'unset'],
			],
			'aspect ratio unsets height'  => [
				['style' => ['dimensions' => ['aspectRatio' => '16/9', 'minHeight' => '500px']]],
				['minHeight' => 'unset', 'aspectRatio' => '16/9'],
			],
		];
	}

	// Malformed attributes.

	/**
	 * @dataProvider malformed_style_data_provider
	 */
	public function test_malformed_style_attribute_returns_nulls(array $attributes)
	{
		$support = new BlockSupport($attributes);

		$this->assertSame(['padding' => null, 'margin' => null, 'blockGap' => null], $support->get_spacing_style());
		$this->assertNull($support->get_shadow_style());
		$this->assertNull($support->get_background_style()['backgroundImage']);
		$this->assertNull($support->get_border_style()['width']);
	}

	public function malformed_style_data_provider(): array
	{
		return [
			'style is a string'          => [['style' => 'color: red']],
			'style is null'              => [['style' => null]],
			'nested group is a string'   => [['style' => ['spacing' => '10px', 'background' => 'red', 'border' => 'none']]],
		];
	}

	// Get styles.

	public function test_get_styles_with_empty_attributes()
	{
		$support = new BlockSupport([]);

		$this->assertSame(
			[
				'classes'      => [],
				'classnames'   => '',
				'css'          => '',
				'declarations' => [],
			],
			$support->get_all_styles()
		);
	}

	public function test_get_styles_with_unknown_support_is_ignored()
	{
		$support = new BlockSupport([
			'style' => [
				'spacing' => ['padding' => '20px'],
			],
		]);

		$styles = $support->get_styles(['unknown', 'styles', 'all']);

		$this->assertSame('', $styles['css']);
		$this->assertSame([], $styles['declarations']);
	}

	public function test_get_styles_with_preset_color()
	{
		$support = new BlockSupport([
			'backgroundColor' => 'primary',
			'textColor'       => 'white',
		]);

		$styles = $support->get_styles(['color']);

		$this->assertArrayHasKey('has-primary-background-color', $styles['classes']);
		$this->assertArrayHasKey('has-background', $styles['classes']);
		$this->assertArrayHasKey('has-white-color', $styles['classes']);
		$this->assertArrayHasKey('has-text-color', $styles['classes']);
		$this->assertSame(implode(' ', array_keys($styles['classes'])), $styles['classnames']);
		$this->assertSame('var(--wp--preset--color--primary)', $styles['declarations']['background-color']);
		$this->assertSame('var(--wp--preset--color--white)', $styles['declarations']['color']);
	}

	public function test_get_styles_with_custom_color()
	{
		$support = new BlockSupport([
			'style' => [
				'color' => [
					'background' => '#ff0000',
					'text'       => '#ffffff',
				],
			],
		]);

		$styles = $support->get_styles(['color']);

		$this->assertSame('#ff0000', $styles['declarations']['background-color']);
		$this->assertSame('#ffffff', $styles['declarations']['color']);
		$this->assertStringContainsString('background-color:#ff0000;', $styles['css']);
		$this->assertArrayHasKey('has-background', $styles['classes']);
		$this->assertArrayHasKey('has-text-color', $styles['classes']);
	}

	public function test_get_styles_with_spacing()
	{
		$support = new BlockSupport([
			'style' => [
				'spacing' => [
					'padding' => '20px',
					'margin'  => ['top' => '0', 'bottom' => '2rem'],
				],
			],
		]);

		$styles = $support->get_styles(['spacing']);

		$this->assertSame('20px', $styles['declarations']['padding']);
		$this->assertSame('0', $styles['declarations']['margin-top']);
		$this->assertSame('2rem', $styles['declarations']['margin-bottom']);
		$this->assertSame([], $styles['classes']);
	}

	public function test_get_styles_with_border_and_shadow()
	{
		$support = new BlockSupport([
			'style' => [
				'border' => [
					'width'  => '2px',
					'style'  => 'solid',
					'radius' => '8px',
				],
				'shadow' => '0 4px 6px rgba(0, 0, 0, 0.1)',
			],
		]);

		$styles = $support->get_styles(['border', 'shadow']);

		$this->assertSame('2px', $styles['declarations']['border-width']);
		$this->assertSame('solid', $styles['declarations']['border-style']);
		$this->assertSame('8px', $styles['declarations']['border-radius']);
		$this->assertSame('0 4px 6px rgba(0, 0, 0, 0.1)', $styles['declarations']['box-shadow']);
	}

	public function test_get_styles_with_min_height()
	{
		$support = new BlockSupport(['minHeight' => '400px']);

		$styles = $support->get_styles(['dimensions']);

		$this->assertSame('400px', $styles['declarations']['min-height']);
	}

	public function test_get_styles_adds_has_background_class_for_background_image()
	{
		$support = new BlockSupport([
			'style' => [
				'background' => [
					'backgroundImage' => ['url' => 'https://example.com/image.jpg'],
				],
			],
		]);

		$styles = $support->get_styles(['background']);

		$this->assertArrayHasKey('background-image', $styles['declarations']);
		$this->assertStringContainsString('https://example.com/image.jpg', $styles['declarations']['background-image']);
		$this->assertSame('cover', $styles['declarations']['background-size']);
		$this->assertSame(['has-background' => true], $styles['classes']);
		$this->assertSame('has-background', $styles['classnames']);
	}

	public function test_get_styles_has_background_class_is_unique()
	{
		$support = new BlockSupport([
			'style' => [
				'background' => [
					'backgroundImage' => ['url' => 'https://example.com/image.jpg'],
				],
				'color'      => [
					'background' => '#ff0000',
				],
			],
		]);

		$styles = $support->get_styles(['background', 'color']);

		$this->assertSame(1, substr_count($styles['classnames'], 'has-background'));
		$this->assertTrue($styles['classes']['has-background']);
	}

	// Get all styles.

	public function test_get_all_styles_includes_every_support()
	{
		$support = new BlockSupport([
			'backgroundColor' => 'primary',
			'minHeight'       => '400px',
			'style'           => [
				'spacing' => ['padding' => '20px'],
				'border'  => ['width' => '2px'],
				'shadow'  => 'var:preset|shadow|natural',
			],
		]);

		$styles = $support->get_all_styles();

		$this->assertArrayHasKey('has-primary-background-color', $styles['classes']);
		$this->assertSame('20px', $styles['declarations']['padding']);
		$this->assertSame('2px', $styles['declarations']['border-width']);
		$this->assertSame('var(--wp--preset--shadow--natural)', $styles['declarations']['box-shadow']);
		$this->assertSame('400px', $styles['declarations']['min-height']);
	}

	public function test_get_all_styles_respects_exclusions()
	{
		$attributes = [
			'backgroundColor' => 'primary',
			'style'           => [
				'spacing' => ['padding' => '20px'],
				'shadow'  => 'var:preset|shadow|natural',
			],
		];

		$support = new BlockSupport($attributes);
		$styles  = $support->get_all_styles(['color', 'shadow']);

		$this->assertArrayNotHasKey('has-primary-background-color', $styles['classes']);
		$this->assertArrayNotHasKey('box-shadow', $styles['declarations']);
		$this->assertSame('20px', $styles['declarations']['padding']);
	}

	public function test_get_all_styles_matches_get_styles_with_remaining_supports()
	{
		$support = new BlockSupport([
			'backgroundColor' => 'primary',
			'style'           => [
				'spacing' => ['padding' => '20px'],
				'border'  => ['width' => '2px'],
			],
		]);

		$this->assertSame(
			$support->get_styles(['background', 'color', 'spacing', 'shadow', 'dimensions']),
			$support->get_all_styles(['border'])
		);
	}
}
