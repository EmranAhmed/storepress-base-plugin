# StorePress Plugin file skeletons

Replace `<Root>` (e.g. `StorePress\Base`), `<Sub>` (e.g. `Base`), `<slug>`, `<prefix>`, `Foo`.
Tabs, not spaces. Keep the one-tab indent on everything above the class line.

Every class that uses `StorePress\AdminUtils\Traits\SingletonTrait` carries
`@phpstan-consistent-constructor` in its class docblock.

## Main class docblock — `includes/Plugin.php`

The `Plugin` class docblock is always exactly this, replacing any existing one:

```php
	/**
	 * Class Plugin.
	 *
	 * @name Plugin
	 * @phpstan-consistent-constructor
	 */
class Plugin {
```

## Feature class — `includes/Features/Foo.php`

```php
<?php
	/**
	 * Foo Feature.
	 *
	 * @package    StorePress/<Sub>
	 * @since      1.0.0
	 * @version    1.0.0
	 */

	declare( strict_types=1 );

	namespace <Root>\Features;

	defined( 'ABSPATH' ) || die( 'Keep Silent' );

	use StorePress\AdminUtils\Traits\SingletonTrait;
	use <Root>\Traits\PluginUtilityTrait;

	/**
	 * Adds Foo behavior to the site.
	 *
	 * @name  Foo
	 * @since 1.0.0
	 * @phpstan-consistent-constructor
	 */
class Foo {

	use SingletonTrait;
	use PluginUtilityTrait;

	// =====================================================================
	// Service Lifecycle Methods
	// =====================================================================

	/**
	 * Initialise class.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		$this->hooks();
		$this->init();

		/**
		 * Action to signal that Foo has finished loading.
		 *
		 * @param Foo $instance Foo object.
		 *
		 * @since 1.0.0
		 */
		do_action( '<prefix-short>_foo_loaded', $this );
	}

	/**
	 * Registers WordPress action and filter hooks.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function hooks(): void {
		// Enqueue frontend assets.
		add_action( 'wp_enqueue_scripts', array( $this, 'handle_frontend_scripts' ) );
	}

	/**
	 * Initializes included classes.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function init(): void {
	}

	// =====================================================================
	// Hook Callbacks
	// =====================================================================

	/**
	 * Registers frontend scripts.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function handle_frontend_scripts(): void {
		$asset = $this->get_script_asset( $this->build_path() . '/foo.asset.php' );

		wp_register_script( '<slug>-foo', $this->build_url() . '/foo.js', $asset['dependencies'], $asset['version'], array( 'strategy' => 'defer' ) );
	}

	// =====================================================================
	// Utility Methods
	// =====================================================================
}
```

## Service provider — `includes/ServiceProviders/FooServiceProvider.php`

```php
<?php
	/**
	 * Foo Service Provider.
	 *
	 * @package    StorePress/<Sub>
	 * @since      1.0.0
	 * @version    1.0.0
	 */

	declare( strict_types=1 );

	namespace <Root>\ServiceProviders;

	defined( 'ABSPATH' ) || die( 'Keep Silent' );

	use StorePress\AdminUtils\Abstracts\AbstractServiceProvider;
	use StorePress\AdminUtils\Traits\SingletonTrait;
	use <Root>\Features\Foo;
	use <Root>\Traits\PluginUtilityTrait;

	/**
	 * Binds and boots the Foo service.
	 *
	 * @name FooServiceProvider
	 * @phpstan-consistent-constructor
	 */
class FooServiceProvider extends AbstractServiceProvider {

	use SingletonTrait;
	use PluginUtilityTrait;

	// =====================================================================
	// Service Lifecycle Methods
	// =====================================================================

	/**
	 * Binds the Foo service into the container.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function register(): void {

		$this->get_container()->register(
			Foo::class,
			function () {
				return Foo::instance();
			}
		);
	}

	/**
	 * Resolves and activates the Foo service.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function boot(): void {
		$this->get_container()->get( Foo::class );
	}
}
```

Then in `Plugin::get_service_providers()` add (keep `=>` aligned):

```php
FooServiceProvider::class => FooServiceProvider::class,
```

Factory variant (new object per call) — `register()` binds:

```php
$this->get_container()->register(
	Thing::class,
	function () {
		return static function ( array $args ): Thing {
			return new Thing( $args );
		};
	}
);
```

and `boot(): void {}` stays empty, all Services which have no wp hooks also have empty `boot(): void {}`.

## Accessor — `includes/functions.php`

```php
/**
 * Returns the Foo service from the DI container.
 *
 * @return Foo
 * @since  1.0.0
 * @see    get_container()
 */
function get_foo(): Foo {
	return get_container()->get( Foo::class );
}
```

## Settings tab — `includes/Services/Settings.php`

```php
// In add_settings():
'foo' => 'Foo',

/**
 * Returns field definitions for the Foo settings tab.
 *
 * @return array<int, array<string, mixed>>
 * @since 1.0.0
 */
public function add_foo_settings_fields(): array {
	return array(
		array(
			'type'        => 'section',
			'title'       => 'Foo',
			'description' => 'Foo options',
		),

		array(
			'id'      => 'enable_foo',
			'type'    => 'toggle',
			'title'   => 'Enable Foo',
			'default' => 'no',
		),
	);
}
```

Reading a value (always guard the type):

```php
$value  = $this->get_settings()->get_option( 'enable_foo', 'no' );
$enable = is_string( $value ) ? sanitize_key( $value ) : 'no';
```

## Block — `src/foo-block/`

`block.json`

```json
{
	"$schema" : "https://schemas.wp.org/trunk/block.json",
	"apiVersion" : 3,
	"name" : "storepress/foo-block",
	"version" : "0.1.0",
	"title" : "Foo Block",
	"category" : "storepress",
	"icon" : "sticky",
	"description" : "StorePress Foo Block",
	"attributes" : {
		"size" : {
			"type" : "number",
			"default" : 100
		}
	},
	"supports" : {
		"html" : false
	},
	"textdomain" : "<slug>",
	"editorScript" : "file:./index.js",
	"editorStyle" : "file:./index.css",
	"style" : "file:./style-index.css",
	"viewScript" : "file:./view.js",
	"render" : "file:./render.php"
}
```

`index.js`

```js
/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import Edit from './edit';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => <InnerBlocks.Content />
} );
```

`render.php`

```php
<?php
	/**
	 * Dynamic Block Template.
	 *
	 * @package    StorePress/<Sub>
	 * @since      1.0.0
	 * @version    1.0.0
	 */

	declare( strict_types=1 );

	namespace <Root>;

	defined( 'ABSPATH' ) || die( 'Keep Silent' );	

	use WP_Block;

	/**
	 * The following variables are exposed to the file:
	 *
	 * @var array{size: int} $attributes - A clean associative array of block attributes.
	 * @var WP_Block         $block      - The block instance.
	 * @var string           $content    - The block inner HTML.
	 *
	 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
	 */

	$storepress_classes = array(
		'storepress-foo-block' => true,
	);

	$storepress_styles = array(
		'--foo-size' => $attributes['size'] . 'px',
	);

	$storepress_wrapper_attributes = get_block_wrapper_attributes(
		array(
			'class' => esc_attr( get_blocks()->get_css_classes( $storepress_classes ) ),
			'style' => esc_attr( get_blocks()->get_inline_styles( $storepress_styles ) ),
		)
	);

	$storepress_allowed_html = get_blocks()->get_kses_allowed_html();
	?>
<div <?php echo wp_kses_data( $storepress_wrapper_attributes ); ?>>
	<?php echo wp_kses( $content, $storepress_allowed_html ); ?>
</div>
```

`style.scss` / `editor.scss`

```scss
@charset "UTF-8";

@use "~@wordpress/base-styles/colors" as colors;

.storepress-foo-block {
	background-color: transparent;
}
```

## Frontend Plugin pattern — `src/plugins/Foo.js`

```js
/**
 * External dependencies
 */
import { getOptionsFromAttribute } from '@storepress/utils';

function Foo( element, options ) {
	// Default Settings
	const DEFAULTS = {
		speed: 300,
	};

	// Collecting settings from html attribute
	const ATTRIBUTE = 'foo-settings';

	const onClick = ( event ) => {
		window.console.log( event.target );
	};

	const addEvents = () => {
		this.element.addEventListener( 'click', onClick );
	};

	const removeEvents = () => {
		this.element.removeEventListener( 'click', onClick );
	};

	// Expose to public.
	const expose = () => ( {
		element: this.element,
		removeEvents,
	} );

	const init = () => {
		this.element = element;
		this.settings = Object.assign(
			{},
			DEFAULTS,
			options,
			getOptionsFromAttribute( this.element, ATTRIBUTE )
		);

		addEvents();

		return expose();
	};

	return init();
}

export { Foo };
```

Instantiate in an entry with `createPluginInstance( '.foo', options, Foo )` inside `domReady`,
and wire `foo_init` / `foo_destroy` `document` events.

## PHP unit test — `tests/php/Unit/Features/FooTest.php`

```php
<?php

declare(strict_types=1);

namespace Unit\Features;

use <Root>\Features\Foo;
use WP_UnitTestCase;

class FooTest extends WP_UnitTestCase
{

	public function set_up(): void
	{
		parent::set_up();
	}

	public function tear_down(): void
	{
		parent::tear_down();
	}

	public function test_did_action_foo_loaded()
	{
		$this->assertSame(1, did_action('<prefix-short>_foo_loaded'));
	}

	public function test_has_action_wp_enqueue_scripts()
	{
		$this->assertSame(10, has_action('wp_enqueue_scripts', [Foo::instance(), 'handle_frontend_scripts']));
	}

	public function test_check_same_instance()
	{
		$this->assertSame(<prefix>()->get_container()->get(Foo::class), Foo::instance());
	}
}
```
