<?php
/**
 * Mocco Functions.
 */

if ( ! function_exists( 'moccotheme_setup' ) ) {
	function moccotheme_setup() {

		// Base features.
		// @todo, these can be retired after 11.8.
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'editor-styles' );

		// Increase JPEG quality.
		add_filter( 'jpeg_quality', function( $arg ) { return 90; } );

		// Image sizes.
		add_image_size( 'custom-thumbnails', 1620, 720 );
		add_image_size( 'big-thumbnails', 2160, 1080 );

		// Load the theme stylesheet into the editor.
		add_editor_style( 'style.css' );
	}
}
add_action( 'after_setup_theme', 'moccotheme_setup' );


/**
 * Disable search autocomplete.
 */

function search_disable_autocompete( $content ) {
	$content = preg_replace( '/\binput\b/i', 'input autocomplete="off"', $content );
	return $content;
}
add_filter( 'get_search_form', 'search_disable_autocompete' );


/**
 * Favicon & theme color.
 */

function mocco_favicon() { ?>
<link rel="icon" type="image/svg+xml" href="<?php echo get_template_directory_uri(); ?>/assets/favicon.svg">
<meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#000000" media="(prefers-color-scheme: dark)">
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
 * Custom post types and templates.
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
