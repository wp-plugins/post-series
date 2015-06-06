<?php
/*
Plugin Name: Post Series
Plugin URI: http://mikslatvis.com/wordpress-plugins/post-series/
Description: Allows you to add posts to a series and show the list by the posts.
Version: 0.5
Author: Miks Latvis
Author URI: http://mikslatvis.com
License: GPL2
*/

wp_enqueue_style( 'my-style', plugins_url( '/style.css', __FILE__ ), false, '1.0', 'all' ); // Inside a plugin

function ml_post_series_init() {
	// create a new taxonomy
	register_taxonomy(
		'series_of_posts',
		'post',
		array(
			'label' => __( 'Series of posts' ),
			'sort' => true,
			'args' => array( 'orderby' => 'term_order' ),
			'rewrite' => array( 'slug' => 'series_of_posts' )
		)
	);
}
add_action( 'init', 'ml_post_series_init' );

/* Output code */ 
function ml_post_series_output() {	
	if ( is_single() && has_term('', 'series_of_posts')) {
	
		$terms = get_the_terms($post->ID, 'series_of_posts');
		$count = count($terms);
		if ( $count > 0 ) {
			echo "<div id='series-of-posts-box'><ul>";
			foreach ( $terms as $term ) {
				echo "<p class='one-series'>" . $term->name . ":</p>";
				$series_of_posts = $term->slug; 
				$args = array(
					'orderby' => 'date',
					'order' => 'ASC',
					'tax_query' => array(
						array(
							'taxonomy' => 'series_of_posts',
							'field' => 'slug',
							'terms' => $series_of_posts
						)
					)
				);     
			$the_query = new WP_Query( $args );
// The Loop
			while ( $the_query->have_posts() ) : $the_query->the_post();
			echo '<li class="one-link">'; ?>
			<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
			<?php echo '</li>';
			endwhile;
			// Reset Post Data
			wp_reset_postdata(); 
			}
			echo "</ul></div>";
		}
	} else { };
};

/* Replaces the shortcode with list of posts */

function ml_post_series_has_shortcode() {
    return ml_post_series_output();
}
add_shortcode('post_series', 'ml_post_series_has_shortcode');

/* Shows list of post when no shortcode is provided */

function ml_series_of_posts_noshortcode() {
	global $post;
    if ( is_single() && has_term('', 'series_of_posts') && ! has_shortcode( $post->post_content , 'post_series' ) ) {
		return ml_post_series_output();
	
	} else { };
};
add_action( 'get_template_part_content', 'ml_series_of_posts_noshortcode' );

?>