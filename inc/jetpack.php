<?php
/**
 * Jetpack Compatibility File
 * See: http://jetpack.me/
 *
 * @package Shape Shifter Theme
 */

/**
 * Add theme support for Infinite Scroll.
 * See: http://jetpack.me/support/infinite-scroll/
 */
function sstr_theme_infinite_scroll_setup() {
	add_theme_support( 'infinite-scroll', array(
		'container' => 'content',
		'footer'    => 'page',
	) );
}
add_action( 'after_setup_theme', 'sstr_theme_infinite_scroll_setup' );
