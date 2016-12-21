<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

  <section>
    <article id="post-<?php the_ID(); ?>">
      <header>
        <h1><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
      </header>
      <section>
        <?php the_content('Read more on "'.the_title('', '', false).'" &raquo;'); ?>
      </section>
    </article>

  </section>

<?php endwhile; else: ?>

  <section>
    <article>
      <p>Sorry, no events matched your criteria.</p>
    </article>
  </section>

<?php endif; ?>

<?php get_footer(); ?>