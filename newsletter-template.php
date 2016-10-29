<?php
get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

      <?php
      $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
      $loop = new WP_Query( array(
        'post_type' => 'newsletter' ,
        'posts_per_page' => 10,
        'paged' => $paged
      ) );
      $i = 1;
       if ( $loop->have_posts() ) :
           while ( $loop->have_posts() ) : $loop->the_post(); ?>
            <section <?php echo ( $i % 2 === 0) ? "class='color2'" : '' ?>>
              <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <h1 class="entry-title"><?php echo get_the_title(); ?></h1>

                <div class="wrap">
                   <?php  echo get_the_content();  ?>
                 </div>
              </article><!--newsletter_template.php-->

             </section>
           <?php
           $i++;
         endwhile;


         if (  $loop->max_num_pages > 1 ) : ?>
         <nav class="pagination">
           <?php

           $big = 999999999;

           echo paginate_links(array(
               'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
               'format' => '?paged=%#%',
               'current' => max(1, get_query_var('paged')),
               'total' => $loop->max_num_pages
           ));
           ?>
         </nav>
         <?php

       endif;
     else:
       get_template_part('template-parts/content', 'none');
     endif;
     wp_reset_postdata();
     ?>


  </main><!-- #main -->
</div><!-- #primary -->
 <?php
 get_sidebar();
 get_footer();
