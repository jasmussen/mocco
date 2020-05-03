<?php
/**
 * Mocco Functions.
 */

if ( ! function_exists( 'moccotheme_setup' ) ) {
	function moccotheme_setup() {

		// Base features.
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'responsive-embeds' );

		// Editor.
		// @todo: Not all of these are needed anymore.
		add_theme_support( 'custom-line-height' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'editor-styles' );
		//add_theme_support( 'wp-block-styles' );
		//add_theme_support( 'dark-editor-style' );
		add_theme_support( 'custom-units' );
		add_theme_support( 'custom-spacing' );

		register_nav_menus( array(
			'menu-1' => esc_html__( 'Primary', 'moccotheme' ),
		) );

		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Increase JPEG quality.
		add_filter( 'jpeg_quality', function( $arg ) { return 90; } );

		// Image sizes.
		add_image_size( 'custom-thumbnails', 1620, 720 );
		add_image_size( 'big-thumbnails', 2160, 1080 );

		// Experiment, load the actual stylesheet into the editor.
		add_editor_style( 'style.css' );
	}
}
add_action( 'after_setup_theme', 'moccotheme_setup' );

// Disable search autocomplete.
function search_disable_autocompete( $content ) {
 	$content = preg_replace( '/\binput\b/i', 'input autocomplete="off"', $content );
	return $content;
}
add_filter( 'get_search_form', 'search_disable_autocompete' );


/**
 * Favicon.
 */

function mocco_favicon() { ?>
<link rel="icon" type="image/svg+xml" href="<?php echo get_template_directory_uri(); ?>/assets/favicon.svg">
<?php }
add_action('wp_head', 'mocco_favicon');


/**
 * Fonts and styles.
 */

define( 'GOOGLE_FONTS_URL', 'https://fonts.googleapis.com/css2?family=Mulish:ital,wght@0,400;0,600;0,700;0,800;1,400;1,600;1,700;1,800&display=swap' );
function moccotheme_fonts() {
	wp_enqueue_style( 'moccotheme-fonts', GOOGLE_FONTS_URL );
}
add_action( 'wp_enqueue_scripts', 'moccotheme_fonts' );
add_action( 'admin_head', 'moccotheme_fonts' );
add_editor_style( GOOGLE_FONTS_URL );

function moccotheme_styles() {
	wp_enqueue_style( 'moccotheme-style', get_stylesheet_uri() );
	
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'moccotheme_styles' );


/**
 * Hack to load custom stylesheet into both editors with low specificity.
 * It's the only other way to load CSS into the site editor without .editor-styles-wrapper prefix.
 */

add_action('init', function() {
	wp_register_style('editor-hacks', get_template_directory_uri() . '/editor-hacks.css');
 
	register_block_type('mocco/editor-hacks', [
		'style' => 'editor-hacks',
	]);
});


/**
 * Custom post types and templates.
 * @todo: maybe extract to separate plugin.
 */

function mocco_custom_post_type() {
	register_post_type( 'work',
		array(
			'labels' => array(
				'name' => __( 'Work' ),
				'singular_name' => __( 'Work' )
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'work'),
			'show_in_rest' => true,
			'supports' => [
				'title', 'editor',
			],
		)
	);

	register_post_type( 'installment',
		array(
			'labels' => array(
				'name' => __( 'Installments' ),
				'singular_name' => __( 'Installment' )
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'installments'),
			'show_in_rest' => true,
			'supports' => [
				'title', 'editor',
			],
		)
	);
}
add_action( 'init', 'mocco_custom_post_type' );

// Assign a post template to our custom post type
function mocco_register_template() {
	$post_type_object = get_post_type_object( 'work' );
	$post_type_object->template = array(
		array( 'core/cover', array( 'content' => 'Description', ) ),
		array( 'core/gallery' ),
	);

	// Lock the template from being rearranged or items deleted.
	//$post_type_object->template_lock = 'all'; // Can be "all" or "insert", the latter allows moving blocks around.
}
add_action( 'init', 'mocco_register_template' );
