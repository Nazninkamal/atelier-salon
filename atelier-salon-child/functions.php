<?php
if (!defined('ABSPATH')) exit;
add_action('wp_enqueue_scripts',function(){
 wp_enqueue_style('atelier-child',get_stylesheet_uri(),array('atelier'),wp_get_theme()->get('Version'));
},20);
