<?php
/**
 * @package InfinitePostsNavigation
 * @subpackage Main
 */

/**
 * Plugin Name: Infinite Next and Previous Posts Navigation
 * Plugin URI: https://edev.io
 * Description: Adds infinite next and previous posts buttons rathern than only next or previous when available
 * Author: Edward Robinson - edev.io
 * Version: 0.1
 * Requires at least: 3.9
 * Author URI: https://edev.io
 * License: GPL v3
 *
 * Infinite Next and Previous Posts in Wordpress
 * Copyright (C) 2016-, edev.io, erobinsondev@gmail.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Flexible Post Type Infinite Navigation
 *
 * Returns first post if no more Next posts and Last if no more Previous posts
 *
 */
function edev_get_infinite_adjacent_navigation( $insert_element = '', $previous_button_text = 'Previous', $next_button_text = 'Next' ){

	global $wp_query;

	$post_type = ( isset( $wp_query->queried_object->post_type ) ? $wp_query->queried_object->post_type : 'post' );

	// Get posts not in the same Tax Term, not excluding any and either previous or !previous (next)
	$prev_post  = get_adjacent_post( false, '', true );
	$next_post  = get_adjacent_post( false, '', false );

	$nav_output = NULL;

	// If have previous posts construct
	if ( ! empty( $prev_post ) ) {
		$link_text = ( $previous_button_text ? $previous_button_text : $prev_post->post_title );
		$nav_output .= "<a class='post-link previous-link' href='{$prev_post->guid}'>{$link_text}</a>";
	}
	// If no more previous posts i.e. If at start of all posts, query for 1 and last post
	else{
		$args = array(
			'post_type'   		=> $post_type,
			'post_per_page' 	=> 1,
			'order'			    	=> 'DESC'
		);

		$single_post = new WP_Query( $args ); $single_post->the_post();
		$link_text   = ( $previous_button_text ? $previous_button_text : get_the_title() );
		$nav_output .= '<a class="post-link previous-link" href="' . get_permalink() . '">' . $link_text .'</a>';
		wp_reset_query();

	}

	// Inserts any element in between previous and next posts. Useful for inserting items like generic 'back to index' link
	$nav_output .= $insert_element;

	// If have next posts construct
	if ( ! empty( $next_post ) ) {
		$link_text = ( $next_button_text ? $next_button_text : $next_post->post_title );
		$nav_output .= "<a class='post-link next-link icon-right-side-padding' href='{$next_post->guid}'>{$link_text}</a>";
	}
	// If no more next posts i.e. If at end of all posts, query for 1 and first post
	else{
		$args = array(
			'post_type'   		=> $post_type,
			'post_per_page' 	=> 1,
			'order'						=> 'ASC'
		);
		$single_post = new WP_Query( $args ); $single_post->the_post();
		$link_text   = ( $next_button_text ?  $next_button_text : get_the_title() );
		$nav_output .= '<a class="post-link next-link icon-right-side-padding" href="' . get_permalink() . '">' . $link_text . '</a>';
		wp_reset_query();
	}

	return $nav_output;

}

add_filter ('the_content', 'edev_insert_after_content');

function edev_insert_after_content( $content ) {
	$content .= edev_get_infinite_adjacent_navigation();
	return $content;
}
