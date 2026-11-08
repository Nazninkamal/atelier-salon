<?php
if (!defined('ABSPATH')) { exit; }
define('ATELIER_VERSION', '3.0.1');
function atelier_setup() {
 load_theme_textdomain('atelier-salon', get_template_directory() . '/languages');
 add_theme_support('title-tag'); add_theme_support('post-thumbnails');
 add_theme_support('automatic-feed-links'); add_theme_support('responsive-embeds');
 add_theme_support('html5', array('search-form','comment-form','comment-list','gallery','caption','style','script'));
 add_theme_support('align-wide'); add_theme_support('wp-block-styles'); add_theme_support('editor-styles'); add_editor_style('assets/theme.css');
 add_theme_support('custom-logo', array('height'=>80,'width'=>240,'flex-height'=>true,'flex-width'=>true));
 register_nav_menus(array('primary'=>__('Primary navigation','atelier-salon')));
}
add_action('after_setup_theme','atelier_setup');
function atelier_assets() {
 wp_enqueue_style('atelier',get_template_directory_uri().'/assets/theme.css',array(),ATELIER_VERSION);
 wp_enqueue_script('atelier',get_template_directory_uri().'/assets/theme.js',array(),ATELIER_VERSION,true);
 if (is_singular() && comments_open() && get_option('thread_comments')) { wp_enqueue_script('comment-reply'); }
}
add_action('wp_enqueue_scripts','atelier_assets');
function atelier_image($name) { return get_template_directory_uri().'/assets/'.$name.'.jpg'; }
function atelier_link($slug) { $page=get_page_by_path($slug); return $page ? get_permalink($page) : (get_option('permalink_structure') ? home_url('/'.$slug.'/') : add_query_arg('pagename',$slug,home_url('/'))); }
function atelier_fallback_menu() {
 echo '<ul class="menu">';
 foreach(array('home'=>'Home','about'=>'About','services'=>'Services','portfolio'=>'Our work','our-team'=>'Team','blog'=>'Journal','contact'=>'Contact') as $slug=>$label) {
  echo '<li><a href="'.esc_url($slug==='home'?home_url('/'):atelier_link($slug)).'">'.esc_html($label).'</a></li>';
 } echo '</ul>';
}
function atelier_customize($c) {
 $c->add_section('atelier_details',array('title'=>__('Salon details','atelier-salon'),'priority'=>30));
 foreach(array('phone'=>'Phone number','address'=>'Salon address','hours'=>'Opening hours') as $key=>$label) {
  $c->add_setting('atelier_'.$key,array('default'=>'','sanitize_callback'=>$key==='booking_email'?'sanitize_email':'sanitize_text_field'));
  $c->add_control('atelier_'.$key,array('label'=>$label,'section'=>'atelier_details','type'=>$key==='booking_email'?'email':'text'));
 }
}
add_action('customize_register','atelier_customize');
function atelier_setup_notice() {
 if (!current_user_can('edit_theme_options') || get_option('atelier_demo_installed')) return;
 echo '<div class="notice notice-info"><p><strong>Atelier Salon</strong> — Create the starter pages and menu, then edit them in Pages. Existing pages are preserved. This changes your Reading settings. Use on a new site or back up first. Install the optional Atelier Appointments plugin to enable requests.</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'">';
 wp_nonce_field('atelier_install'); echo '<input type="hidden" name="action" value="atelier_install"><p><button class="button button-primary">Create starter pages</button></p></form></div>';
}
add_action('admin_notices','atelier_setup_notice');
function atelier_install() {
 if (!current_user_can('edit_theme_options')) wp_die('Permission denied');
 check_admin_referer('atelier_install');
 $data=require get_template_directory().'/starter-content.php'; $ids=array();
 foreach($data as $slug=>$item) {
  $existing=get_page_by_path($slug);
  if ($existing) { $ids[$slug]=$existing->ID; continue; }
  $id=wp_insert_post(array('post_type'=>'page','post_status'=>'publish','post_title'=>$item[0],'post_name'=>$slug,'post_content'=>$item[1]),true);
  if (is_wp_error($id)) wp_die(esc_html($id->get_error_message()));
  $ids[$slug]=$id;
 }
 update_option('show_on_front','page'); update_option('page_on_front',$ids['home']); update_option('page_for_posts',$ids['blog']);
 $locations=get_theme_mod('nav_menu_locations',array());
 if (empty($locations['primary'])) {
  $menu_name='Atelier Primary'; $suffix=2;
  while(wp_get_nav_menu_object($menu_name)) { $menu_name='Atelier Primary '.$suffix; $suffix++; }
  $menu=wp_create_nav_menu($menu_name);
  if (!is_wp_error($menu)) {
   foreach(array('home','about','services','portfolio','our-team','blog','contact') as $slug) {
    $parent=wp_update_nav_menu_item($menu,0,array('menu-item-title'=>$data[$slug][0],'menu-item-object'=>'page','menu-item-object-id'=>$ids[$slug],'menu-item-type'=>'post_type','menu-item-status'=>'publish'));
    if($slug==='services' && !is_wp_error($parent)) {
     foreach(atelier_service_page_slugs() as $service_slug) wp_update_nav_menu_item($menu,0,array('menu-item-title'=>$data[$service_slug][0],'menu-item-object'=>'page','menu-item-object-id'=>$ids[$service_slug],'menu-item-type'=>'post_type','menu-item-status'=>'publish','menu-item-parent-id'=>$parent));
    }
   }
   $locations['primary']=$menu; set_theme_mod('nav_menu_locations',$locations);
  }
 }
 update_option('atelier_demo_installed',1); wp_safe_redirect(admin_url('edit.php?post_type=page')); exit;
}
add_action('admin_post_atelier_install','atelier_install');

function atelier_booking_placeholder($content) {
 if (!shortcode_exists('atelier_booking') && strpos($content,'[atelier_booking]')!==false) {
  $content=str_replace('[atelier_booking]','<div class="form-notice">'.esc_html__('Please contact the salon directly to request an appointment.','atelier-salon').'</div>',$content);
 }
 return $content;
}
add_filter('the_content','atelier_booking_placeholder',9);
function atelier_patterns() {
 if (!function_exists('register_block_pattern')) return;
 register_block_pattern_category('atelier-salon',array('label'=>__('Atelier Salon','atelier-salon')));
 $pages=require get_template_directory().'/starter-content.php';
 foreach(array_merge(array('home','studio-home','about','services','portfolio','our-team','contact'),atelier_service_page_slugs()) as $slug) {
  register_block_pattern('atelier-salon/'.$slug,array('title'=>'Atelier — '.$pages[$slug][0],'categories'=>array('atelier-salon'),'content'=>$pages[$slug][1]));
 }
}
add_action('init','atelier_patterns');

function atelier_design_customizer($customizer) {
 $customizer->add_section('atelier_design',array('title'=>__('Atelier design','atelier-salon'),'priority'=>29));
 $customizer->add_setting('atelier_palette',array('default'=>'clay','sanitize_callback'=>'atelier_sanitize_palette'));
 $customizer->add_control('atelier_palette',array('section'=>'atelier_design','label'=>__('Color collection','atelier-salon'),'description'=>__('Applies to the Warm editorial studio direction.','atelier-salon'),'type'=>'select','choices'=>array('clay'=>__('Parchment & Clay','atelier-salon'),'olive'=>__('Ivory & Olive','atelier-salon'),'rose'=>__('Rose & Cocoa','atelier-salon'))));
 $customizer->add_setting('atelier_accent',array('default'=>'','sanitize_callback'=>'sanitize_hex_color'));
 $customizer->add_control(new WP_Customize_Color_Control($customizer,'atelier_accent',array('section'=>'atelier_design','label'=>__('Custom accent (optional)','atelier-salon'),'description'=>__('Overrides the collection accent. Check text contrast after changing.','atelier-salon'))));
}
add_action('customize_register','atelier_design_customizer');
function atelier_sanitize_palette($value) { return in_array($value,array('clay','olive','rose'),true) ? $value : 'clay'; }
function atelier_palette_styles() {
 $collections=array(
 'clay'=>array('#29251f','#f7f3ec','#655b51','#d9d0c3','#e7e8df','#84583e','#ebe3d7','#323e35'),
 'olive'=>array('#272a22','#f5f4eb','#59604d','#d2d5c4','#e3e7d7','#5b6445','#e8e6d8','#303c30'),
 'rose'=>array('#302426','#fbf4f0','#6c5554','#e0ceca','#ece4df','#865153','#efdfda','#493636')
 );
 $values=$collections[atelier_sanitize_palette(get_theme_mod('atelier_palette','clay'))];
 $custom=sanitize_hex_color(get_theme_mod('atelier_accent','')); if($custom) $values[5]=$custom;
 $keys=array('--ink','--paper','--muted','--line','--sage','--accent','--accent-soft','--forest');
 $css=':root{'; foreach($keys as $i=>$key) $css.=$key.':'.$values[$i].';'; $glows=array('clay'=>'#edddca','olive'=>'#e3e5cf','rose'=>'#eed9d1');
 $depths=array('clay'=>'#202e28','olive'=>'#202d25','rose'=>'#2e252c');
 $palette=atelier_sanitize_palette(get_theme_mod('atelier_palette','clay'));
 $css.='--cream:'.$values[1].';--gradient-glow:'.$glows[$palette].';--gradient-deep:'.$depths[$palette].';}';
 wp_add_inline_style('atelier',$css);
}
add_action('wp_enqueue_scripts','atelier_palette_styles',20);

function atelier_service_page_slugs() {
 return array('cut-finish','color-gloss','balayage','scalp-repair','occasion-styling','consultation');
}
function atelier_service_pages_notice() {
 if(!current_user_can('edit_theme_options') || !get_option('atelier_demo_installed')) return;
 $missing=false; foreach(atelier_service_page_slugs() as $slug) { if(!get_page_by_path($slug)) { $missing=true; break; } }
 if(!$missing) return;
 echo '<div class="notice notice-info"><p>'.esc_html__('Atelier: six dedicated service layouts are available. Add missing pages without replacing existing content, menus, or Reading settings.','atelier-salon').'</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'">';
 wp_nonce_field('atelier_add_services');
 echo '<input type="hidden" name="action" value="atelier_add_services"><p><button class="button button-primary">'.esc_html__('Add missing service pages','atelier-salon').'</button></p></form></div>';
}
add_action('admin_notices','atelier_service_pages_notice');
function atelier_create_missing_service_pages() {
 $data=require get_template_directory().'/starter-content.php'; $created=array();
 foreach(atelier_service_page_slugs() as $slug) {
  if(get_page_by_path($slug)) continue;
  $id=wp_insert_post(array('post_type'=>'page','post_status'=>'publish','post_title'=>$data[$slug][0],'post_name'=>$slug,'post_content'=>$data[$slug][1]),true);
  if(is_wp_error($id)) return $id;
  $created[]=$id;
 }
 return $created;
}
function atelier_add_service_pages() {
 if(!current_user_can('edit_theme_options')) wp_die(esc_html__('Permission denied.','atelier-salon'));
 check_admin_referer('atelier_add_services');
 $result=atelier_create_missing_service_pages();
 if(is_wp_error($result)) wp_die(esc_html($result->get_error_message()));
 wp_safe_redirect(admin_url('edit.php?post_type=page')); exit;
}
add_action('admin_post_atelier_add_services','atelier_add_service_pages');

function atelier_sanitize_direction($value){return in_array($value,array('noir','editorial'),true)?$value:'noir';}
function atelier_direction_customizer($c){
 $c->add_setting('atelier_direction',array('default'=>'noir','sanitize_callback'=>'atelier_sanitize_direction'));
 $c->add_control('atelier_direction',array('section'=>'atelier_design','label'=>__('Visual direction','atelier-salon'),'type'=>'select','choices'=>array('noir'=>__('Noir hair salon','atelier-salon'),'editorial'=>__('Warm editorial studio','atelier-salon'))));
}
add_action('customize_register','atelier_direction_customizer');
function atelier_direction_class($classes){
 if(atelier_sanitize_direction(get_theme_mod('atelier_direction','noir'))==='noir' && !is_page('studio-home')) $classes[]='atelier-noir';
 return $classes;
}
add_filter('body_class','atelier_direction_class');
