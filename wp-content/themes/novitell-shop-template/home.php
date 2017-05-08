<?php
/**
 * The template for displaying blog page.
 *
 * @package novitell
 */





    get_header(); ?>
<?php

?>
    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">



            <?php if ( have_posts() ) :

                get_template_part( 'loop' );

            else :

                get_template_part( 'content', 'none' );

            endif;

            ?>

        </main><!-- #main -->



    </div><!-- #primary -->
<?php if ( is_active_sidebar( 'blog_left_1' ) ) : ?>
    <div id="secondary" class="widget-area" role="complementary">

        <?php dynamic_sidebar( 'blog_left_1' ); ?>
    </div><!-- #primary-sidebar -->
<?php endif; ?>
<?php
get_footer();
