<?php
/**
 *  Shape Shifter Theme functions and definitions
 *
 * @package Shape Shifter Theme
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 640; /* pixels */

/*
 * Load Jetpack compatibility file.
 */
require( get_template_directory() . '/inc/jetpack.php' );

if ( ! function_exists( 'sstr_theme_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 */
 
 add_action( 'after_setup_theme', 'sstr_theme_setup',15 );
function sstr_theme_setup() {

        // launching operation cleanup
    add_action('init', 'sstr_theme_head_cleanup');
    // remove WP version from RSS
    add_filter('the_generator', 'sstr_theme_rss_version');
    
    // remove pesky injected css for recent comments widget
    add_filter( 'wp_head', 'sstr_theme_remove_wp_widget_recent_comments_style', 1 );
    
    // clean up comment styles in the head
    add_action('wp_head', 'sstr_theme_remove_recent_comments_style', 1);
    
    // clean up gallery output in wp
    add_filter('gallery_style', 'sstr_theme_gallery_style');
    
        // cleaning up random code around images
    add_filter('the_content', 'sstr_theme_filter_ptags_on_images');
    // cleaning up excerpt
    add_filter('excerpt_more', 'sstr_theme_excerpt_more');
    
    
    // enqueue base scripts and styles
    add_action('wp_enqueue_scripts', 'sstr_theme_scripts_and_styles', 999);
    
        // This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();
	
	//ensure that the output of the shortcode function does not get wrapped in <p> tags because of wpautop
	add_filter( 'widget_text', 'shortcode_unautop');
    add_filter( 'widget_text', 'do_shortcode', 11);

	/**
	 * Custom template tags for this theme.
	 */
	require( get_template_directory() . '/inc/template-tags.php' );

	/**
	 * Custom functions that act independently of the theme templates
	 */
	require( get_template_directory() . '/inc/extras.php' );

	/**
	 * Customizer additions
	 */
	require( get_template_directory() . '/inc/customizer.php' );

	/**
	 * Make theme available for translation
	 * Translations can be filed in the /languages/ directory
	 * If you're building a theme based on  Shape Shifter Theme, use a find and replace
	 * to change 'sstr_theme' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'sstr_theme', get_template_directory() . '/languages' );

	/**
	 * Add default posts and comments RSS feed links to head
	 */
	add_theme_support( 'automatic-feed-links' );

	/**
	 * Enable support for Post Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	/**
	 * This theme uses wp_nav_menu() in one location.
	 */
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'sstr_theme' ),
	) );

	/**
	 * Enable support for Post Formats
	 */
	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );
}
endif; // sstr_theme_setup



function sstr_theme_head_cleanup() {
	// category feeds
	// remove_action( 'wp_head', 'feed_links_extra', 3 );
	// post and comment feeds
	// remove_action( 'wp_head', 'feed_links', 2 );
	// EditURI link
	remove_action( 'wp_head', 'rsd_link' );
	// windows live writer
	remove_action( 'wp_head', 'wlwmanifest_link' );
	// index link
	remove_action( 'wp_head', 'index_rel_link' );
	// previous link
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	// start link
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
	// links for adjacent posts
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	// WP version
	remove_action( 'wp_head', 'wp_generator' );
  // remove WP version from css
  add_filter( 'style_loader_src', 'sstr_theme_remove_wp_ver_css_js', 9999 );
  // remove Wp version from scripts
  add_filter( 'script_loader_src', 'sstr_theme_remove_wp_ver_css_js', 9999 );

} /* end sstr_theme head cleanup */

// remove WP version from RSS
function sstr_theme_rss_version() { return ''; }

// remove WP version from scripts
function sstr_theme_remove_wp_ver_css_js( $src ) {
    if ( strpos( $src, 'ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}

// remove injected CSS for recent comments widget
function sstr_theme_remove_wp_widget_recent_comments_style() {
   if ( has_filter('wp_head', 'wp_widget_recent_comments_style') ) {
      remove_filter('wp_head', 'wp_widget_recent_comments_style' );
   }
}

// remove injected CSS from recent comments widget
function sstr_theme_remove_recent_comments_style() {
  global $wp_widget_factory;
  if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
    remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
  }
}

// remove injected CSS from gallery
function sstr_theme_gallery_style($css) {
  return preg_replace("!<style type='text/css'>(.*?)</style>!s", '', $css);
}

/*********************
PAGE NAVI
*********************/

// Numeric Page Navi (built into the theme by default)
function sstr_theme_page_navi($before = '', $after = '') {
	global $wpdb, $wp_query;
	$request = $wp_query->request;
	$posts_per_page = intval(get_query_var('posts_per_page'));
	$paged = intval(get_query_var('paged'));
	$numposts = $wp_query->found_posts;
	$max_page = $wp_query->max_num_pages;
	if ( $numposts <= $posts_per_page ) { return; }
	if(empty($paged) || $paged == 0) {
		$paged = 1;
	}
	$pages_to_show = 7;
	$pages_to_show_minus_1 = $pages_to_show-1;
	$half_page_start = floor($pages_to_show_minus_1/2);
	$half_page_end = ceil($pages_to_show_minus_1/2);
	$start_page = $paged - $half_page_start;
	if($start_page <= 0) {
		$start_page = 1;
	}
	$end_page = $paged + $half_page_end;
	if(($end_page - $start_page) != $pages_to_show_minus_1) {
		$end_page = $start_page + $pages_to_show_minus_1;
	}
	if($end_page > $max_page) {
		$start_page = $max_page - $pages_to_show_minus_1;
		$end_page = $max_page;
	}
	if($start_page <= 0) {
		$start_page = 1;
	}
	echo $before.'<nav class="page-navigation"><ol class="sstr_theme_page_navi clearfix">'."";
	if ($start_page >= 2 && $pages_to_show < $max_page) {
		$first_page_text = esc_attr__('First','sstr_theme');
		echo '<li class="bpn-first-page-link"><a href="'.get_pagenum_link().'" title="'.$first_page_text.'">'.$first_page_text.'</a></li>';
	}
	echo '<li class="bpn-prev-link">';
	previous_posts_link('<<');
	echo '</li>';
	for($i = $start_page; $i  <= $end_page; $i++) {
		if($i == $paged) {
			echo '<li class="bpn-current">'.$i.'</li>';
		} else {
			echo '<li><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
		}
	}
	echo '<li class="bpn-next-link">';
	next_posts_link('>>');
	echo '</li>';
	if ($end_page < $max_page) {
		$last_page_text = esc_attr__('Last','sstr_theme');
		echo '<li class="bpn-last-page-link"><a href="'.get_pagenum_link($max_page).'" title="'.$last_page_text.'">'.$last_page_text.'</a></li>';
	}
	echo '</ol></nav>'.$after."";
} /* end page navi */

/*********************
RANDOM CLEANUP ITEMS
*********************/

// remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
function sstr_theme_filter_ptags_on_images($content){
   return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

// This removes the annoying […] to a Read More link
function sstr_theme_excerpt_more($more) {
	global $post;
	// edit here if you like
	return '...  <a href="'. get_permalink($post->ID) . '" title="'.esc_attr__('Read','sstr_theme').' '.get_the_title($post->ID).'">'.esc_attr__('Read more &raquo;','sstr_theme').'</a>';
}

/**
 * Setup the WordPress core custom background feature.
 *
 * Use add_theme_support to register support for WordPress 3.4+
 * as well as provide backward compatibility for WordPress 3.3
 * using feature detection of wp_get_theme() which was introduced
 * in WordPress 3.4.
 *
 * @todo Remove the 3.3 support when WordPress 3.6 is released.
 *
 * Hooks into the after_setup_theme action.
 */
function sstr_theme_register_custom_background() {
	$args = array(
		'default-color' => 'ffffff',
		'default-image' => '',
	);

	$args = apply_filters( 'sstr_theme_custom_background_args', $args );

	if ( function_exists( 'wp_get_theme' ) ) {
		add_theme_support( 'custom-background', $args );
	} else {
		define( 'BACKGROUND_COLOR', $args['default-color'] );
		if ( ! empty( $args['default-image'] ) )
			define( 'BACKGROUND_IMAGE', $args['default-image'] );
		add_custom_background();
	}
}
add_action( 'after_setup_theme', 'sstr_theme_register_custom_background' );

/**
 * Register widgetized area and update sidebar with default widgets
 */
function sstr_theme_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'sstr_theme' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'sstr_theme_widgets_init' );

/**
 * Enqueue scripts and styles
 */
function sstr_theme_scripts_and_styles() {
	wp_enqueue_style( 'sstr_theme-style', get_stylesheet_uri() );

	wp_enqueue_script( 'sstr_theme-navigation', get_template_directory_uri() . '/library/js/navigation.js', array(), '20120206', true );

	wp_enqueue_script( 'sstr_theme-skip-link-focus-fix', get_template_directory_uri() . '/library/js/skip-link-focus-fix.js', array(), '20130115', true );
    
	    // register main stylesheet
    wp_register_style( 'sstr_theme-stylesheet', get_stylesheet_directory_uri() . '/library/css/style.css', array(), '', 'all' );
	
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( 'sstr_theme-keyboard-image-navigation', get_template_directory_uri() . '/library/js/keyboard-image-navigation.js', array( 'jquery' ), '20120202' );
	}
	
	
	 // enqueue styles and scripts
    wp_enqueue_style( 'sstr_theme-stylesheet' );

	
	
}
add_action( 'wp_enqueue_scripts', 'sstr_theme_scripts_and_styles' );

/**
 * Implement the Custom Header feature
 */
//require( get_template_directory() . '/inc/custom-header.php' );