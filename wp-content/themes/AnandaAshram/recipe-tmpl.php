<?php
/*
Template Name: Recipe  Book
*/
?>
<?php get_header(); ?>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<article class="post" id="post-<?php the_ID(); ?>">
		  <h2><?php the_title(); ?></h2>
			<div class="entry">
				<?php the_content(); ?>

        <?php $terms = get_terms( 'course' );

        echo '<ul class=accordion>';

        foreach ( $terms as $term ) {
          if ( is_wp_error( $term_link ) ) {
            continue;
          }
	        $args = array(
		        'post_type' => 'recipe',
		        'tax_query' => array(
			        'relation' => 'AND',
			        array(
				        'taxonomy' => 'course',
				        'field' => 'slug',
				        'terms' => array( $term->slug )
			        )
		        )
	        );

          $posts = get_posts($args);
          echo '<li><a href="javascript:void(0)" class=js-accordion-trigger>' . $term->name . '</a>
          <ul class=submenu>';
            foreach($posts as $post) { // begin cycle through posts of this category

              echo '<li>';?>
            <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title() ?></a>
              <?php echo '</li>';
            }
            echo '</ul>
          </li>';
        }

        echo '</ul>';?>
			</div>
		</article>
		<?php endwhile; endif; ?>

<?php get_footer(); ?>
