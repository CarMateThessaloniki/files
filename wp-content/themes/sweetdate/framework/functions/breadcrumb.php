<?php
/**
 * @package SeventhQueen Framework
 * @subpackage Breadcrumb
 * @since sQueen 1.0
 *
 * Shows a breadcrumb for all types of pages.
 */


if (!function_exists('kleo_breadcrumb')):
/** 
 * Displays the breadcrumb for the current page
 * @since 1.0
 * @access public
 * @param array $args Mixed arguments for the menu.
 * @return string Output of the breadcrumb menu.
 */
function kleo_breadcrumb( $args = array() ) {

	/* Empty variable for the breadcrumb. */
	$breadcrumb = '';

	/* Default arguments for the breadcrumb. */
	$defaults = array(
		'container'  => 'ul', // div, nav, p, etc.
		'container_class' => '',
		'item_tag' => 'li',
		'separator'  => '',
		'before'     => '',
		'after'      => false,
		'front_page' => true,
		'show_home'  => __( 'Home', 'kleo_framework' ),
		'network'    => false,
		'echo'       => true
	);

	/* Allow singular post views to have a taxonomy's terms prefixing the trail. */
	if ( is_singular() ) {
		$post = get_queried_object();
		$defaults["singular_{$post->post_type}_taxonomy"] = false;
	}

	/* Apply filters to the arguments. */
	$args = apply_filters( 'breadcrumb_trail_args', $args );

	/* Parse the arguments and extract them for easy variable naming. */
	$args = wp_parse_args( $args, $defaults );

	/* Get the trail items. */
	$trail = breadcrumb_trail_get_items( $args );

	/* Connect the breadcrumb trail if there are items in the trail. */
	if ( !empty( $trail ) && is_array( $trail ) ) {

		/* Open the breadcrumb trail containers. */
		$breadcrumb = '<' . tag_escape( $args['container'] ) . (!empty($args['container_class'])?' class="'.$args['container_class'].'"':'').'>';

		/* If $before was set, wrap it in a container. */
		$breadcrumb .= ( !empty( $args['before'] ) ?  $args['before'] : '' );

		/* Format the separator. */
		$separator = ( !empty( $args['separator'] ) ? '<span class="sep">' . $args['separator'] . '</span>' : '' );

		/* Join the individual trail items into a single string. */
		$breadcrumb .= '<'. $args['item_tag'].'>'.join( " {$separator} ".'</'. $args['item_tag'].'>'.'<'. $args['item_tag'].'>', $trail).'</'. $args['item_tag'].'>';

		/* If $after was set, wrap it in a container. */
		$breadcrumb .= ( !empty( $args['after'] ) ? $args['after'] : '' );

		/* Close the breadcrumb trail containers. */
		$breadcrumb .= '</' . tag_escape( $args['container'] ) . '>';
	}

	/* Allow developers to filter the breadcrumb trail HTML. */
	$breadcrumb = apply_filters( 'kleo_breadcrumb', $breadcrumb, $args );

	/* Output the breadcrumb. */
	if ( $args['echo'] )
		echo $breadcrumb;
	else
		return $breadcrumb;
}
endif;

/**
 * Gets the items for the breadcrumb trail.  This is the heart of the script.  It checks the current page 
 * being viewed and decided based on the information provided by WordPress what items should be
 * added to the breadcrumb trail.
 *
 * @since 0.4.0
 * @todo Build in caching based on the queried object ID.
 * @access public
 * @param array $args Mixed arguments for the menu.
 * @return array List of items to be shown in the trail.
 */
function breadcrumb_trail_get_items( $args = array() ) {
	global $wp_rewrite;

	/* Set up an empty trail array and empty path. */
	$trail = array();
	$path = '';

	/* If $show_home is set and we're not on the front page of the site, link to the home page. */
	if ( !is_front_page() && $args['show_home'] ) {

		if ( is_multisite() && true === $args['network'] ) {
			$trail[] = '<a href="' . network_home_url() . '">' . $args['show_home'] . '</a>';
			$trail[] = '<a href="' . home_url() . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" rel="home" class="trail-begin">' . get_bloginfo( 'name' ) . '</a>';
		} else {
			$trail[] = '<a href="' . home_url() . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" rel="home" class="trail-begin">' . $args['show_home'] . '</a>';
		}
	}

    /* if Buddypress exists */
	if (class_exists('BP_Core_user') && !bp_is_blog_page() ) {
		$trail = array_merge( $trail, breadcrumb_trail_get_buddypress_items() );
	}
	/* If bbPress is installed and we're on a bbPress page. */
	elseif ( function_exists( 'is_bbpress' ) && is_bbpress() ) {
		$trail = array_merge( $trail, breadcrumb_trail_get_bbpress_items() );
	}

	/* If viewing the front page of the site. */
	elseif ( is_front_page() ) {

		if ( !is_paged() && $args['show_home'] && $args['front_page'] ) {

			if ( is_multisite() && true === $args['network'] ) {
				$trail[] = '<a href="' . network_home_url() . '">' . $args['show_home'] . '</a>';
				$trail[] = get_bloginfo( 'name' );
			} else {
				$trail[] = $args['show_home'];
			}
		}

		elseif ( is_paged() && $args['show_home'] && $args['front_page'] ) {

			if ( is_multisite() && true === $args['network'] ) {
				$trail[] = '<a href="' . network_home_url() . '">' . $args['show_home'] . '</a>';
				$trail[] = '<a href="' . home_url() . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" rel="home" class="trail-begin">' . get_bloginfo( 'name' ) . '</a>';
			} else {
				$trail[] = '<a href="' . home_url() . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" rel="home" class="trail-begin">' . $args['show_home'] . '</a>';
			}
		}
	}

	/* If viewing the "home"/posts page. */
	elseif ( is_home() ) {
		$home_page = get_page( get_queried_object_id() );

		$trail = array_merge( $trail, breadcrumb_trail_get_parents( $home_page->post_parent, '' ) );

		if ( is_paged() )
			$trail[]  = '<a href="' . get_permalink( $home_page->ID ) . '" title="' . esc_attr( get_the_title( $home_page->ID ) ) . '">' . get_the_title( $home_page->ID ) . '</a>';
		else
			$trail[] = "<span>".get_the_title( $home_page->ID )."</span>";
	}

	/* If viewing a singular post (page, attachment, etc.). */
	elseif ( is_singular() ) {

		/* Get singular post variables needed. */
		$post = get_queried_object();
		$post_id = absint( get_queried_object_id() );
		$post_type = $post->post_type;
		$parent = absint( $post->post_parent );

		/* Get the post type object. */
		$post_type_object = get_post_type_object( $post_type );

		/* If viewing a singular 'post'. */
		if ( 'post' == $post_type ) {

			/* If $front has been set, add it to the $path. */
			$path .= trailingslashit( $wp_rewrite->front );

			/* If there's a path, check for parents. */
			if ( !empty( $path ) )
				$trail = array_merge( $trail, breadcrumb_trail_get_parents( '', $path ) );

			/* Map the permalink structure tags to actual links. */
			$trail = array_merge( $trail, breadcrumb_trail_map_rewrite_tags( $post_id, get_option( 'permalink_structure' ), $args ) );
		}

		/* If viewing a singular 'attachment'. */
		elseif ( 'attachment' == $post_type ) {

			/* Get the parent post ID. */
			$parent_id = $post->post_parent;

			/* If the attachment has a parent (attached to a post). */
			if ( 0 < $parent_id ) {

				/* Get the parent post type. */
				$parent_post_type = get_post_type( $parent_id );

				/* If the post type is 'post'. */
				if ( 'post' == $parent_post_type ) {

					/* If $front has been set, add it to the $path. */
					$path .= trailingslashit( $wp_rewrite->front );

					/* If there's a path, check for parents. */
					if ( !empty( $path ) )
						$trail = array_merge( $trail, breadcrumb_trail_get_parents( '', $path ) );

					/* Map the post (parent) permalink structure tags to actual links. */
					$trail = array_merge( $trail, breadcrumb_trail_map_rewrite_tags( $post->post_parent, get_option( 'permalink_structure' ), $args ) );
				}

				/* Custom post types. */
				elseif ( 'page' !== $parent_post_type ) {

					$parent_post_type_object = get_post_type_object( $parent_post_type );

					/* If $front has been set, add it to the $path. */
					if ( $parent_post_type_object->rewrite['with_front'] && $wp_rewrite->front )
						$path .= trailingslashit( $wp_rewrite->front );

					/* If there's a slug, add it to the $path. */
					if ( !empty( $parent_post_type_object->rewrite['slug'] ) )
						$path .= $parent_post_type_object->rewrite['slug'];

					/* If there's a path, check for parents. */
					if ( !empty( $path ) )
						$trail = array_merge( $trail, breadcrumb_trail_get_parents( '', $path ) );

					/* If there's an archive page, add it to the trail. */
					if ( !empty( $parent_post_type_object->has_archive ) ) {

						/* Add support for a non-standard label of 'archive_title' (special use case). */
						$label = !empty( $parent_post_type_object->labels->archive_title ) ? $parent_post_type_object->labels->archive_title : $parent_post_type_object->labels->name;

						$trail[] = '<a href="' . get_post_type_archive_link( $parent_post_type ) . '" title="' . esc_attr( $label ) . '">' . $label . '</a>';
					}
				}
			}
		}

		/* If a custom post type, check if there are any pages in its hierarchy based on the slug. */
		elseif ( 'page' !== $post_type ) {

			/* If $front has been set, add it to the $path. */
			if ( $post_type_object->rewrite['with_front'] && $wp_rewrite->front )
				$path .= trailingslashit( $wp_rewrite->front );

			/* If there's a slug, add it to the $path. */
			if ( !empty( $post_type_object->rewrite['slug'] ) )
				$path .= $post_type_object->rewrite['slug'];

			/* If there's a path, check for parents. */
			if ( !empty( $path ) )
				$trail = array_merge( $trail, breadcrumb_trail_get_parents( '', $path ) );

			/* If there's an archive page, add it to the trail. */
			if ( !empty( $post_type_object->has_archive ) ) {

				/* Add support for a non-standard label of 'archive_title' (special use case). */
				$label = !empty( $post_type_object->labels->archive_title ) ? $post_type_object->labels->archive_title : $post_type_object->labels->name;

				$trail[] = '<a href="' . get_post_type_archive_link( $post_type ) . '" title="' . esc_attr( $label ) . '">' . $label . '</a>';
			}
		}

		/* If the post type path returns nothing and there is a parent, get its parents. */
		if ( ( empty( $path ) && 0 !== $parent ) || ( 'attachment' == $post_type ) )
			$trail = array_merge( $trail, breadcrumb_trail_get_parents( $parent, '' ) );

		/* Or, if the post type is hierarchical and there's a parent, get its parents. */
		elseif ( 0 !== $parent && is_post_type_hierarchical( $post_type ) )
			$trail = array_merge( $trail, breadcrumb_trail_get_parents( $parent, '' ) );

		/* Display terms for specific post type taxonomy if requested. */
		if ( !empty( $args["singular_{$post_type}_taxonomy"] ) && $terms = get_the_term_list( $post_id, $args["singular_{$post_type}_taxonomy"], '', ', ', '' ) )
			$trail[] = "<span>".$terms."</span>";

		/* End with the post title. */
		$post_title = single_post_title( '', false );

		if ( 1 < get_query_var( 'page' ) && !empty( $post_title ) )
			$trail[] = '<a href="' . get_permalink( $post_id ) . '" title="' . esc_attr( $post_title ) . '">' . $post_title . '</a>';

		elseif ( !empty( $post_title ) )
			$trail[] = '<span>'.$post_title.'</span>';
	}

	/* If we're viewing any type of archive. */
	elseif ( is_archive() ) {

		/* If viewing a taxonomy term archive. */
		if ( is_tax() || is_category() || is_tag() ) {

			/* Get some taxonomy and term variables. */
			$term = get_queried_object();
			$taxonomy = get_taxonomy( $term->taxonomy );

			/* Get the path to the term archive. Use this to determine if a page is present with it. */
			if ( is_category() )
				$path = get_option( 'category_base' );
			elseif ( is_tag() )
				$path = get_option( 'tag_base' );
			else {
				if ( $taxonomy->rewrite['with_front'] && $wp_rewrite->front )
					$path = trailingslashit( $wp_rewrite->front );
				$path .= $taxonomy->rewrite['slug'];
			}

			/* Get parent pages by path if they exist. */
			if ( $path )
				$trail = array_merge( $trail, breadcrumb_trail_get_parents( '', $path ) );

			/* Add post type archive if its 'has_archive' matches the taxonomy rewrite 'slug'. */
			if ( $taxonomy->rewrite['slug'] ) {

				/* Get public post types that match the rewrite slug. */
				$post_types = get_post_types( array( 'public' => true, 'has_archive' => $taxonomy->rewrite['slug'] ), 'objects' );

				/**
				 * If any post types are found, loop through them to find one that matches.
				 * The reason for this is because WP doesn't match the 'has_archive' string 
				 * exactly when calling get_post_types(). I'm assuming it just matches 'true'.
				 */
				if ( !empty( $post_types ) ) {

					foreach ( $post_types as $post_type_object ) {

						if ( $taxonomy->rewrite['slug'] === $post_type_object->has_archive ) {

							/* Add support for a non-standard label of 'archive_title' (special use case). */
							$label = !empty( $post_type_object->labels->archive_title ) ? $post_type_object->labels->archive_title : $post_type_object->labels->name;

							/* Add the post type archive link to the trail. */
							$trail[] = '<a href="' . get_post_type_archive_link( $post_type_object->name ) . '" title="' . esc_attr( $label ) . '">' . $label . '</a>';

							/* Break out of the loop. */
							break;
						}
					}
				}
			}

			/* If the taxonomy is hierarchical, list its parent terms. */
			if ( is_taxonomy_hierarchical( $term->taxonomy ) && $term->parent )
				$trail = array_merge( $trail, breadcrumb_trail_get_term_parents( $term->parent, $term->taxonomy ) );

			/* Add the term name to the trail end. */
			if ( is_paged() )
				$trail[] = '<a href="' . esc_url( get_term_link( $term, $term->taxonomy ) ) . '" title="' . esc_attr( single_term_title( '', false ) ) . '">' . single_term_title( '', false ) . '</a>';
			else
				$trail[] = '<span>'.single_term_title( '', false ).'</span>';
		}

		/* If viewing a post type archive. */
		elseif ( is_post_type_archive() ) {

			/* Get the post type object. */
			$post_type_object = get_post_type_object( get_query_var( 'post_type' ) );

			/* If $front has been set, add it to the $path. */
			if ( $post_type_object->rewrite['with_front'] && $wp_rewrite->front )
				$path .= trailingslashit( $wp_rewrite->front );

			/* If there's a slug, add it to the $path. */
			if ( !empty( $post_type_object->rewrite['slug'] ) )
				$path .= $post_type_object->rewrite['slug'];

			/* If there's a path, check for parents. */
			if ( !empty( $path ) )
				$trail = array_merge( $trail, breadcrumb_trail_get_parents( '', $path ) );

			/* Add the post type [plural] name to the trail end. */
			if ( is_paged() )
				$trail[] = '<a href="' . esc_url( get_post_type_archive_link( $post_type_object->name ) ) . '" title="' . esc_attr( post_type_archive_title( '', false ) ) . '">' . post_type_archive_title( '', false ) . '</a>';
			else
				$trail[] = "<span>".post_type_archive_title( '', false )."</span>";
		}

		/* If viewing an author archive. */
		elseif ( is_author() ) {

			/* Get the user ID. */
			$user_id = get_query_var( 'author' );

			/* If $front has been set, add it to $path. */
			if ( !empty( $wp_rewrite->front ) )
				$path .= trailingslashit( $wp_rewrite->front );

			/* If an $author_base exists, add it to $path. */
			if ( !empty( $wp_rewrite->author_base ) )
				$path .= $wp_rewrite->author_base;

			/* If $path exists, check for parent pages. */
			if ( !empty( $path ) )
				$trail = array_merge( $trail, breadcrumb_trail_get_parents( '', $path ) );

			/* Add the author's display name to the trail end. */
			if ( is_paged() )
				$trail[] = '<a href="'. esc_url( get_author_posts_url( $user_id ) ) . '" title="' . esc_attr( get_the_author_meta( 'display_name', $user_id ) ) . '">' . get_the_author_meta( 'display_name', $user_id ) . '</a>';
			else
				$trail[] = "<span>".get_the_author_meta( 'display_name', $user_id )."</span>";
		}

		/* If viewing a time-based archive. */
		elseif ( is_time() ) {

			if ( get_query_var( 'minute' ) && get_query_var( 'hour' ) )
				$trail[] = get_the_time( __( 'g:i a', 'kleo_framework' ) );

			elseif ( get_query_var( 'minute' ) )
				$trail[] = sprintf( __( 'Minute %1$s', 'kleo_framework' ), get_the_time( __( 'i', 'kleo_framework' ) ) );

			elseif ( get_query_var( 'hour' ) )
				$trail[] = get_the_time( __( 'g a', 'kleo_framework' ) );
		}

		/* If viewing a date-based archive. */
		elseif ( is_date() ) {

			/* If $front has been set, check for parent pages. */
			if ( $wp_rewrite->front )
				$trail = array_merge( $trail, breadcrumb_trail_get_parents( '', $wp_rewrite->front ) );

			if ( is_day() ) {
				$trail[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . get_the_time( esc_attr__( 'Y', 'kleo_framework' ) ) . '">' . get_the_time( __( 'Y', 'kleo_framework' ) ) . '</a>';
				$trail[] = '<a href="' . get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) . '" title="' . get_the_time( esc_attr__( 'F', 'kleo_framework' ) ) . '">' . get_the_time( __( 'F', 'kleo_framework' ) ) . '</a>';

				if ( is_paged() )
					$trail[] = '<a href="' . get_day_link( get_the_time( 'Y' ), get_the_time( 'm' ), get_the_time( 'd' ) ) . '" title="' . get_the_time( esc_attr__( 'd', 'kleo_framework' ) ) . '">' . get_the_time( __( 'd', 'kleo_framework' ) ) . '</a>';
				else
					$trail[] = get_the_time( __( 'd', 'kleo_framework' ) );
			}

			elseif ( get_query_var( 'w' ) ) {
				$trail[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . get_the_time( esc_attr__( 'Y', 'kleo_framework' ) ) . '">' . get_the_time( __( 'Y', 'kleo_framework' ) ) . '</a>';

				if ( is_paged() )
					$trail[] = get_archives_link( add_query_arg( array( 'm' => get_the_time( 'Y' ), 'w' => get_the_time( 'W' ) ), home_url() ), sprintf( __( 'Week %1$s', 'kleo_framework' ), get_the_time( esc_attr__( 'W', 'kleo_framework' ) ) ), false );
				else
					$trail[] = sprintf( __( 'Week %1$s', 'kleo_framework' ), get_the_time( esc_attr__( 'W', 'kleo_framework' ) ) );
			}

			elseif ( is_month() ) {
				$trail[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . get_the_time( esc_attr__( 'Y', 'kleo_framework' ) ) . '">' . get_the_time( __( 'Y', 'kleo_framework' ) ) . '</a>';

				if ( is_paged() )
					$trail[] = '<a href="' . get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) . '" title="' . get_the_time( esc_attr__( 'F', 'kleo_framework' ) ) . '">' . get_the_time( __( 'F', 'kleo_framework' ) ) . '</a>';
				else
					$trail[] = get_the_time( __( 'F', 'kleo_framework' ) );
			}

			elseif ( is_year() ) {

				if ( is_paged() )
					$trail[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . esc_attr( get_the_time( __( 'Y', 'kleo_framework' ) ) ) . '">' . get_the_time( __( 'Y', 'kleo_framework' ) ) . '</a>';
				else
					$trail[] = get_the_time( __( 'Y', 'kleo_framework' ) );
			}
		}
	}

	/* If viewing search results. */
	elseif ( is_search() ) {

		if ( is_paged() )
			$trail[] = '<a href="' . get_search_link() . '" title="' . sprintf( esc_attr__( 'Search results for &quot;%1$s&quot;', 'kleo_framework' ), esc_attr( get_search_query() ) ) . '">' . sprintf( __( 'Search results for &quot;%1$s&quot;', 'kleo_framework' ), esc_attr( get_search_query() ) ) . '</a>';
		else
			$trail[] = "<span>".sprintf( __( 'Search results for &quot;%1$s&quot;', 'kleo_framework' ), esc_attr( get_search_query() ) )."</span>";
	}

	/* If viewing a 404 error page. */
	elseif ( is_404() ) {
		$trail[] = "<span>".__( '404 Not Found', 'kleo_framework' )."</span>";
	}

            

	/* Check for pagination. */
	if ( is_paged() )
		$trail[] = "<span>".sprintf( __( 'Page %d', 'kleo_framework' ), absint( get_query_var( 'paged' ) ) )."</span>";
	elseif ( is_singular() && 1 < get_query_var( 'page' ) )
		$trail[] = "<span>".sprintf( __( 'Page %d', 'kleo_framework' ), absint( get_query_var( 'page' ) ) )."</span>";

	/* Allow devs to step in and filter the $trail array. */
	return apply_filters( 'breadcrumb_trail_items', $trail, $args );
}



/**
 * Generates the Buddypress breadcrumbs if Buddypress is installed
 *
 * @global Object $bp The BuddyPress $bp object
 * @param array $trail A reference to the trail array from the above function
 * @return array
 */
function breadcrumb_trail_get_buddypress_items($args = array()) 
{
	global $bp;

	// generate the BuddyPress trail
	$trail = array();
	$trail_end = '';
	if ( !empty( $bp->displayed_user->fullname ) ) { // looking at a user or self
		$trail[] ='<a href="'.bp_get_members_directory_permalink().'">'.__('Members', 'buddypress').'</a>';
		$trail[] = '<a href="'. $bp->displayed_user->domain .'" title="'. strip_tags( $bp->displayed_user->userdata->display_name) .'">'. strip_tags( $bp->displayed_user->userdata->display_name ) .'</a>';
		
	} else if ( $bp->is_single_item ) { // we're on a single item page
		$trail[] = '<a href="'.  get_bloginfo('url').'/'. $bp->current_component .'" title="'. ucwords( get_the_title() ) .'">'. ucwords( get_the_title() ) .'</a>';
		$trail[] = '<a href="'.  get_bloginfo('url').'/'. $bp->current_component .'/'. $bp->current_item .'" title="'.$bp->bp_options_title.'">'. $bp->bp_options_title .'</a>';		
                
	} else if ( $bp->is_directory ) { // this is a top level directory page
		$trail_end = get_the_title();
	} else if ( bp_is_register_page() ) {
		$trail_end = __( 'Create an Account', 'buddypress' );
	} else if ( bp_is_activation_page() ) {
		$trail_end = __( 'Activate your Account', 'buddypress' );
	} else if ( bp_is_group_create() ) {
		$trail[] = '<a href="'.  get_bloginfo('url').'/'. $bp->current_component .'" title="'. ucwords(get_the_title()) .'">'. get_the_title() .'</a>';
		if ($bp->action_variables) {
			$trail[] = '<a href="'.  get_bloginfo('url').'/'. $bp->current_component . '/' . $bp->current_action .'" title="'. __( 'Create a Group', 'buddypress' ) .'">'. __( 'Create a Group', 'buddypress' ) .'</a>';
		} else {
			$trail_end = __( 'Create a Group', 'buddypress' );
		}
	} else if ( bp_is_create_blog() ) {
		$trail[] = '<a href="'.  get_bloginfo('url').'/'. $bp->current_component .'" title="'. ucwords($bp->current_component) .'">'. ucwords($bp->current_component) .'</a>';
		$trail_end = __( 'Create a Blog', 'buddypress' );
	}
	if ($trail_end) {
		$trail[] = '<span>'. $trail_end.'</span>';
	}
	/* Return the Buddypress breadcrumb trail items. */
	return apply_filters( 'breadcrumb_trail_get_buddypress_items', $trail, $args );
}


/**
 * Gets the items for the breadcrumb trail if bbPress is installed.
 *
 * @since 0.5.0
 * @access public
 * @param array $args Mixed arguments for the menu.
 * @return array List of items to be shown in the trail.
 */
function breadcrumb_trail_get_bbpress_items( $args = array() ) {

	/* Set up a new trail items array. */
	$trail = array();

	/* Get the forum post type object. */
	$post_type_object = get_post_type_object( bbp_get_forum_post_type() );

	/* If not viewing the forum root/archive page and a forum archive exists, add it. */
	if ( !empty( $post_type_object->has_archive ) && !bbp_is_forum_archive() )
		$trail[] = '<a href="' . get_post_type_archive_link( bbp_get_forum_post_type() ) . '">' . bbp_get_forum_archive_title() . '</a>';

	/* If viewing the forum root/archive. */
	if ( bbp_is_forum_archive() ) {
		$trail[] = "<span>".bbp_get_forum_archive_title()."</span>";
	}

	/* If viewing the topics archive. */
	elseif ( bbp_is_topic_archive() ) {
		$trail[] = "<span>".bbp_get_topic_archive_title()."</span>";
	}

	/* If viewing a topic tag archive. */
	elseif ( bbp_is_topic_tag() ) {
		$trail[] = "<span>".bbp_get_topic_tag_name()."</span>";
	}

	/* If viewing a topic tag edit page. */
	elseif ( bbp_is_topic_tag_edit() ) {
		$trail[] = '<a href="' . bbp_get_topic_tag_link() . '">' . bbp_get_topic_tag_name() . '</a>';
		$trail[] = "<span>".__( 'Edit', 'kleo_framework' )."</span>";
	}

	/* If viewing a "view" page. */
	elseif ( bbp_is_single_view() ) {
		$trail[] = '<span>'.bbp_get_view_title().'</span>';
	}

	/* If viewing a single topic page. */
	elseif ( bbp_is_single_topic() ) {

		/* Get the queried topic. */
		$topic_id = get_queried_object_id();

		/* Get the parent items for the topic, which would be its forum (and possibly forum grandparents). */
		$trail = array_merge( $trail, breadcrumb_trail_get_parents( bbp_get_topic_forum_id( $topic_id ) ) );

		/* If viewing a split, merge, or edit topic page, show the link back to the topic.  Else, display topic title. */
		if ( bbp_is_topic_split() || bbp_is_topic_merge() || bbp_is_topic_edit() )
			$trail[] = '<a href="' . bbp_get_topic_permalink( $topic_id ) . '">' . bbp_get_topic_title( $topic_id ) . '</a>';
		else
			$trail[] ='<span>'. bbp_get_topic_title( $topic_id ).'</span>';

		/* If viewing a topic split page. */
		if ( bbp_is_topic_split() )
			$trail[] = "<span>".__( 'Split', 'kleo_framework' )."</span>";

		/* If viewing a topic merge page. */
		elseif ( bbp_is_topic_merge() )
			$trail[] = "<span>".__( 'Merge', 'kleo_framework' )."</span>";

		/* If viewing a topic edit page. */
		elseif ( bbp_is_topic_edit() )
			$trail[] = "<span>".__( 'Edit', 'kleo_framework' )."</span>";
	}

	/* If viewing a single reply page. */
	elseif ( bbp_is_single_reply() ) {

		/* Get the queried reply object ID. */
		$reply_id = get_queried_object_id();

		/* Get the parent items for the reply, which should be its topic. */
		$trail = array_merge( $trail, breadcrumb_trail_get_parents( bbp_get_reply_topic_id( $reply_id ) ) );

		/* If viewing a reply edit page, link back to the reply. Else, display the reply title. */
		if ( bbp_is_reply_edit() ) {
			$trail[] = '<a href="' . bbp_get_reply_url( $reply_id ) . '">' . bbp_get_reply_title( $reply_id ) . '</a>';
			$trail[] = "<span>".__( 'Edit', 'kleo_framework' )."</span>";

		} else {
			$trail[] = '<span>'.bbp_get_reply_title( $reply_id ).'</span>';
		}

	}

	/* If viewing a single forum. */
	elseif ( bbp_is_single_forum() ) {

		/* Get the queried forum ID and its parent forum ID. */
		$forum_id = get_queried_object_id();
		$forum_parent_id = bbp_get_forum_parent_id( $forum_id );

		/* If the forum has a parent forum, get its parent(s). */
		if ( 0 !== $forum_parent_id)
			$trail = array_merge( $trail, breadcrumb_trail_get_parents( $forum_parent_id ) );

		/* Add the forum title to the end of the trail. */
		$trail[] = '<span>'.bbp_get_forum_title( $forum_id ).'</span>';
	}

	/* If viewing a user page or user edit page. */
	elseif ( bbp_is_single_user() || bbp_is_single_user_edit() ) {

		if ( bbp_is_single_user_edit() ) {
			$trail[] = '<a href="' . bbp_get_user_profile_url() . '">' . bbp_get_displayed_user_field( 'display_name' ) . '</a>';
			$trail[] = "<span>".__( 'Edit', 'kleo_framework' )."</span>";
		} else {
			$trail[] = '<span>'.bbp_get_displayed_user_field( 'display_name' ).'</span>';
		}
	}

	/* Return the bbPress breadcrumb trail items. */
	return apply_filters( 'breadcrumb_trail_get_bbpress_items', $trail, $args );
}

/**
 * Turns %tag% from permalink structures into usable links for the breadcrumb trail.  This feels kind of
 * hackish for now because we're checking for specific %tag% examples and only doing it for the 'post' 
 * post type.  In the future, maybe it'll handle a wider variety of possibilities, especially for custom post
 * types.
 *
 * @since 0.4.0
 * @access public
 * @param int $post_id ID of the post whose parents we want.
 * @param string $path Path of a potential parent page.
 * @param array $args Mixed arguments for the menu.
 * @return array $trail Array of links to the post breadcrumb.
 */
function breadcrumb_trail_map_rewrite_tags( $post_id = '', $path = '', $args = array() ) {

	/* Set up an empty $trail array. */
	$trail = array();

	/* Make sure there's a $path and $post_id before continuing. */
	if ( empty( $path ) || empty( $post_id ) )
		return $trail;

	/* Get the post based on the post ID. */
	$post = get_post( $post_id );

	/* If no post is returned, an error is returned, or the post does not have a 'post' post type, return. */
	if ( empty( $post ) || is_wp_error( $post ) || 'post' !== $post->post_type )
		return $trail;

	/* Trim '/' from both sides of the $path. */
	$path = trim( $path, '/' );

	/* Split the $path into an array of strings. */
	$matches = explode( '/', $path );

	/* If matches are found for the path. */
	if ( is_array( $matches ) ) {

		/* Loop through each of the matches, adding each to the $trail array. */
		foreach ( $matches as $match ) {

			/* Trim any '/' from the $match. */
			$tag = trim( $match, '/' );

			/* If using the %year% tag, add a link to the yearly archive. */
			if ( '%year%' == $tag )
				$trail[] = '<a href="' . get_year_link( get_the_time( 'Y', $post_id ) ) . '" title="' . get_the_time( esc_attr__( 'Y', 'kleo_framework' ), $post_id ) . '">' . get_the_time( __( 'Y', 'kleo_framework' ), $post_id ) . '</a>';

			/* If using the %monthnum% tag, add a link to the monthly archive. */
			elseif ( '%monthnum%' == $tag )
				$trail[] = '<a href="' . get_month_link( get_the_time( 'Y', $post_id ), get_the_time( 'm', $post_id ) ) . '" title="' . get_the_time( esc_attr__( 'F Y', 'kleo_framework' ), $post_id ) . '">' . get_the_time( __( 'F', 'kleo_framework' ), $post_id ) . '</a>';

			/* If using the %day% tag, add a link to the daily archive. */
			elseif ( '%day%' == $tag )
				$trail[] = '<a href="' . get_day_link( get_the_time( 'Y', $post_id ), get_the_time( 'm', $post_id ), get_the_time( 'd', $post_id ) ) . '" title="' . get_the_time( esc_attr__( 'F j, Y', 'kleo_framework' ), $post_id ) . '">' . get_the_time( __( 'd', 'kleo_framework' ), $post_id ) . '</a>';

			/* If using the %author% tag, add a link to the post author archive. */
			elseif ( '%author%' == $tag )
				$trail[] = '<a href="' . get_author_posts_url( $post->post_author ) . '" title="' . esc_attr( get_the_author_meta( 'display_name', $post->post_author ) ) . '">' . get_the_author_meta( 'display_name', $post->post_author ) . '</a>';

			/* If using the %category% tag, add a link to the first category archive to match permalinks. */
			elseif ( '%category%' == $tag && 'category' !== $args["singular_{$post->post_type}_taxonomy"] ) {

				/* Get the post categories. */
				$terms = get_the_category( $post_id );

				/* Check that categories were returned. */
				if ( $terms ) {

					/* Sort the terms by ID and get the first category. */
					usort( $terms, '_usort_terms_by_ID' );
					$term = get_term( $terms[0], 'category' );

					/* If the category has a parent, add the hierarchy to the trail. */
					if ( 0 !== $term->parent )
						$trail = array_merge( $trail, breadcrumb_trail_get_term_parents( $term->parent, 'category' ) );

					/* Add the category archive link to the trail. */
					$trail[] = '<a href="' . get_term_link( $term, 'category' ) . '" title="' . esc_attr( $term->name ) . '">' . $term->name . '</a>';
				}
			}
		}
	}

	/* Return the $trail array. */
	return $trail;
}

/**
 * Gets parent pages of any post type or taxonomy by the ID or Path.  The goal of this function is to create 
 * a clear path back to home given what would normally be a "ghost" directory.  If any page matches the given 
 * path, it'll be added.  But, it's also just a way to check for a hierarchy with hierarchical post types.
 *
 * @since 0.3.0
 * @access public
 * @param int $post_id ID of the post whose parents we want.
 * @param string $path Path of a potential parent page.
 * @return array $trail Array of parent page links.
 */
function breadcrumb_trail_get_parents( $post_id = '', $path = '' ) {

	/* Set up an empty trail array. */
	$trail = array();

	/* Trim '/' off $path in case we just got a simple '/' instead of a real path. */
	$path = trim( $path, '/' );

	/* If neither a post ID nor path set, return an empty array. */
	if ( empty( $post_id ) && empty( $path ) )
		return $trail;

	/* If the post ID is empty, use the path to get the ID. */
	if ( empty( $post_id ) ) {

		/* Get parent post by the path. */
		$parent_page = get_page_by_path( $path );

		/* If a parent post is found, set the $post_id variable to it. */
		if ( !empty( $parent_page ) )
			$post_id = $parent_page->ID;
	}

	/* If a post ID and path is set, search for a post by the given path. */
	if ( $post_id == 0 && !empty( $path ) ) {

		/* Separate post names into separate paths by '/'. */
		$path = trim( $path, '/' );
		preg_match_all( "/\/.*?\z/", $path, $matches );

		/* If matches are found for the path. */
		if ( isset( $matches ) ) {

			/* Reverse the array of matches to search for posts in the proper order. */
			$matches = array_reverse( $matches );

			/* Loop through each of the path matches. */
			foreach ( $matches as $match ) {

				/* If a match is found. */
				if ( isset( $match[0] ) ) {

					/* Get the parent post by the given path. */
					$path = str_replace( $match[0], '', $path );
					$parent_page = get_page_by_path( trim( $path, '/' ) );

					/* If a parent post is found, set the $post_id and break out of the loop. */
					if ( !empty( $parent_page ) && $parent_page->ID > 0 ) {
						$post_id = $parent_page->ID;
						break;
					}
				}
			}
		}
	}

	/* While there's a post ID, add the post link to the $parents array. */
	while ( $post_id ) {

		/* Get the post by ID. */
		$page = get_page( $post_id );

		/* Add the formatted post link to the array of parents. */
		$parents[]  = '<a href="' . get_permalink( $post_id ) . '" title="' . esc_attr( get_the_title( $post_id ) ) . '">' . get_the_title( $post_id ) . '</a>';

		/* Set the parent post's parent to the post ID. */
		$post_id = $page->post_parent;
	}

	/* If we have parent posts, reverse the array to put them in the proper order for the trail. */
	if ( isset( $parents ) )
		$trail = array_reverse( $parents );

	/* Return the trail of parent posts. */
	return $trail;
}

/**
 * Searches for term parents of hierarchical taxonomies.  This function is similar to the WordPress 
 * function get_category_parents() but handles any type of taxonomy.
 *
 * @since 0.3.0
 * @access public
 * @param int $parent_id The ID of the first parent.
 * @param object|string $taxonomy The taxonomy of the term whose parents we want.
 * @return array $trail Array of links to parent terms.
 */
function breadcrumb_trail_get_term_parents( $parent_id = '', $taxonomy = '' ) {

	/* Set up some default arrays. */
	$trail = array();
	$parents = array();

	/* If no term parent ID or taxonomy is given, return an empty array. */
	if ( empty( $parent_id ) || empty( $taxonomy ) )
		return $trail;

	/* While there is a parent ID, add the parent term link to the $parents array. */
	while ( $parent_id ) {

		/* Get the parent term. */
		$parent = get_term( $parent_id, $taxonomy );

		/* Add the formatted term link to the array of parent terms. */
		$parents[] = '<a href="' . get_term_link( $parent, $taxonomy ) . '" title="' . esc_attr( $parent->name ) . '">' . $parent->name . '</a>';

		/* Set the parent term's parent as the parent ID. */
		$parent_id = $parent->parent;
	}

	/* If we have parent terms, reverse the array to put them in the proper order for the trail. */
	if ( !empty( $parents ) )
		$trail = array_reverse( $parents );

	/* Return the trail of parent terms. */
	return $trail;
}

?>