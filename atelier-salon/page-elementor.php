<?php
/** Template Name: Atelier Elementor Full Width */
get_header();
while(have_posts()):the_post(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('atelier-builder-content'); ?>><div class="entry-content"><?php if(!atelier_elementor_page_active() && !in_array(get_post_meta(get_the_ID(),'_atelier_elementor_key',true),array('home','studio-home'),true)){echo '<header class="page-heading"><h1>';the_title();echo '</h1></header>';} the_content(); ?></div></article>
<?php endwhile;get_footer();
