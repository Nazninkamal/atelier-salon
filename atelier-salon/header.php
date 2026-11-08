<?php if (!defined('ABSPATH')) exit; ?><!doctype html>
<html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width,initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class(); ?>><?php wp_body_open(); ?><a class="skip-link" href="#main"><?php esc_html_e('Skip to content', 'atelier-salon'); ?></a>
<header class="site-header"><div class="brand"><?php if (has_custom_logo()) { the_custom_logo(); } else { ?><a href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html(get_bloginfo('name')); ?><span><?php esc_html_e('HAIR & BEAUTY STUDIO', 'atelier-salon'); ?></span></a><?php } ?></div>
<button class="menu-toggle" aria-expanded="false" aria-controls="primary-nav"><?php esc_html_e('Menu ', 'atelier-salon'); ?><span>☰</span></button>
<nav id="primary-nav" aria-label="<?php esc_attr_e('Main navigation', 'atelier-salon'); ?>"><?php wp_nav_menu(array('theme_location'=>'primary','container'=>false,'fallback_cb'=>'atelier_fallback_menu')); ?></nav>
<a class="header-book" href="<?php echo esc_url(atelier_link('book-appointment')); ?>"><?php esc_html_e('Book a visit ', 'atelier-salon'); ?><span>↗</span></a></header><main id="main">
