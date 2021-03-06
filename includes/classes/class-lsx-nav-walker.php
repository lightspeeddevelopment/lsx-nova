<?php
/**
 * LSX functions and definitions - Navigation Walker.
 *
 * @package    lsx
 * @subpackage navigation
 * @category   bootstrap-walker
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Walker_Nav_Menu' ) ) {
	return;
}

if ( ! class_exists( 'LSX_Nav_Walker' ) ) :

	/**
	 * Cleaner walker for wp_nav_menu()
	 *
	 * Walker_Nav_Menu (WordPress default) example output:
	 *   <li id="menu-item-8" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-8"><a href="/">Home</a></li>
	 *   <li id="menu-item-9" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-9"><a href="/sample-page/">Sample Page</a></l
	 *
	 * LSX_Nav_Walker example output:
	 *   <li class="menu-home"><a href="/">Home</a></li>
	 *   <li class="menu-sample-page"><a href="/sample-page/">Sample Page</a></li>
	 *
	 * @package    lsx
	 * @subpackage navigation
	 * @category   bootstrap-walker
	 */
	class LSX_Nav_Walker extends Walker_Nav_Menu {

		function check_current( $classes ) {
			return preg_match( '/(current[-_])|active|dropdown/', $classes );
		}

		function start_lvl( &$output, $depth = 0, $args = array() ) {
			$output .= "\n<ul class=\"dropdown-menu\">\n";
		}

		function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			$item_html = '';

			if ( isset( $item->title ) ) {
				parent::start_el( $item_html, $item, $depth, $args );

				if ( $item->is_dropdown && ( 0 === $depth ) ) {
					$item_html = str_replace( '<a', '<a class="dropdown-toggle" data-target="#"', $item_html );
					$item_html = str_replace( '</a>', ' <b class="caret"></b></a>', $item_html );
				} elseif ( stristr( $item_html, 'li class="divider"' ) ) {
					$item_html = preg_replace( '/<a[^>]*>.*?<\/a>/iU', '', $item_html );
				} elseif ( stristr( $item_html, 'li class="dropdown-header"' ) ) {
					$item_html = preg_replace( '/<a[^>]*>(.*)<\/a>/iU', '$1', $item_html );
				}

				$item_html = apply_filters( 'lsx_wp_nav_menu_item', $item_html );
				$output .= $item_html;
			}
		}

		function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {
			$element->is_dropdown = ( ( ! empty( $children_elements[ $element->ID ] ) && ( ( $depth + 1 ) < $max_depth || ( 0 === $max_depth ) ) ) );

			if ( $element->is_dropdown ) {
				if ( $depth > 0 ) {
					$element->classes[] = 'dropdown-submenu';
				} else {
					$element->classes[] = 'dropdown';
				}
			}

			parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
		}

	}

endif;
