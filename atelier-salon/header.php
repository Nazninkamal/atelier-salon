<?php if (!defined('ABSPATH')) exit; ?><!doctype html>
<html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width,initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class(); ?>><?php wp_body_open(); ?><a class="skip-link" href="#main">Skip to content</a>
<header class="site-header"><div class="brand"><?php if (has_custom_logo()) { the_custom_logo(); } else { ?><a href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html(get_bloginfo('name')); ?><span>HAIR &amp; BEAUTY STUDIO</span></a><?php } ?></div>
<button class="menu-toggle" aria-expanded="false" aria-controls="primary-nav">Menu <span>☰</span></button>
<nav id="primary-nav" aria-label="Main navigation"><?php wp_nav_menu(array('theme_location'=>'primary','container'=>false,'fallback_cb'=>'atelier_fallback_menu')); ?></nav>
<a class="header-book" href="<?php echo esc_url(atelier_link('book-appointment')); ?>">Book a visit <span>↗</span></a></header><main id="main">
