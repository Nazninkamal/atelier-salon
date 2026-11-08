<?php
/** Optional Elementor Free pages. Native block pages are always retained. */
if (!defined('ABSPATH')) exit;
function atelier_elementor_page_active(){return atelier_elementor_ready() && get_post_meta(get_queried_object_id(),'_elementor_edit_mode',true)==='builder';}
function atelier_elementor_ready(){return did_action('elementor/loaded') && class_exists('\\Elementor\\Plugin');}
function atelier_elementor_node($type,$settings,$children=array()){
 $id=substr(md5(wp_json_encode($settings).wp_json_encode($children).wp_rand()),0,7);
 return array('id'=>$id,'elType'=>$type==='container'?'container':'widget','settings'=>$settings,'elements'=>$children)+($type==='container'?array():array('widgetType'=>$type));
}
function atelier_elementor_blocks($blocks){
 $out=array();
 foreach($blocks as $block){
  $name=$block['blockName'];$attrs=$block['attrs'];$html=$block['innerHTML'];$class=isset($attrs['className'])?$attrs['className']:'';
  if(!$name && !trim($html))continue;
  if($name==='core/group' || $name==='core/columns' || $name==='core/column'){
   $tag=isset($attrs['tagName'])?$attrs['tagName']:'div';
   $out[]=atelier_elementor_node('container',array('html_tag'=>$tag,'content_width'=>'full','css_classes'=>'wp-block-group '.$class,'_title'=>trim(wp_strip_all_tags($html))?:__('Editable section','atelier-salon')),atelier_elementor_blocks($block['innerBlocks']));
  }elseif($name==='core/heading'){
   $level=isset($attrs['level'])?(int)$attrs['level']:2;
   $title=preg_replace('/^\s*<h[1-6][^>]*>|<\/h[1-6]>\s*$/','',trim($html));
   $out[]=atelier_elementor_node('heading',array('title'=>$title,'header_size'=>'h'.$level,'typography_typography'=>'custom','typography_font_family'=>'Atelier Editorial','typography_font_weight'=>'400','title_color'=>'inherit','_css_classes'=>$class,'__globals__'=>array('title_color'=>'','typography_typography'=>'')));
  }elseif($name==='core/image' && preg_match('/<img[^>]+src="([^"]+)"/',$html,$m)){
   preg_match('/alt="([^"]*)"/',$html,$alt);
   $out[]=atelier_elementor_node('image',array('image'=>array('url'=>html_entity_decode($m[1]),'id'=>0),'image_size'=>'full','image_alt'=>isset($alt[1])?html_entity_decode($alt[1]):'','_css_classes'=>'wp-block-image '.$class));
  }elseif($name==='core/shortcode'){
   $out[]=atelier_elementor_node('shortcode',array('shortcode'=>trim($html)));
  }else{
   $content=$block['innerBlocks']?render_block($block):$html;
   if($name==='core/paragraph' && $class!=='service-row-arrow')$content=preg_replace('/(<p)\s+class="[^"]*"/','$1',$content,1);
   $out[]=atelier_elementor_node('text-editor',array('editor'=>$content,'typography_typography'=>'custom','typography_font_family'=>'Atelier Sans','typography_font_weight'=>'400','text_color'=>'inherit','_css_classes'=>$class==='service-row-arrow'?'atelier-service-arrow':$class,'__globals__'=>array('text_color'=>'','typography_typography'=>'')));
  }
 }
 return $out;
}
// Keep semantic heading classes from the source patterns on the editable Elementor heading.
add_filter('elementor/widget/render_content',function($content,$widget){
 if($widget->get_name()==='heading'){
  $classes=$widget->get_settings('_css_classes');
  if($classes)$content=preg_replace('/class="elementor-heading-title/', 'class="'.esc_attr($classes).' elementor-heading-title',$content,1);
 }
 if($widget->get_name()==='image'){
  $settings=$widget->get_data('settings');
  if(empty($settings['image']['id']) && !empty($settings['image_alt']))$content=preg_replace('/alt="[^"]*"/','alt="'.esc_attr($settings['image_alt']).'"',$content,1);
 }
 return $content;
},10,2);
function atelier_elementor_find($key){
 $posts=get_posts(array('post_type'=>'page','post_status'=>array('publish','draft','private','pending','future','trash'),'meta_key'=>'_atelier_elementor_key','meta_value'=>$key,'numberposts'=>1));return $posts?$posts[0]:null;
}
function atelier_elementor_create($assign_home=false){
 if(!current_user_can('edit_theme_options'))return new WP_Error('forbidden',__('Permission denied.','atelier-salon'));
 if(!atelier_elementor_ready())return new WP_Error('missing_plugin',__('Install and activate Elementor Free first.','atelier-salon'));
 $lock=(int)get_option('atelier_elementor_lock');if($lock && $lock<time()-300)delete_option('atelier_elementor_lock');
 if(!add_option('atelier_elementor_lock',time(),'',false))return new WP_Error('busy',__('Elementor setup is already running. If it was interrupted, retry in five minutes.','atelier-salon'));
 $ids=array();$new=array();
 try{
  $pages=require get_template_directory().'/starter-content.php';
  foreach($pages as $key=>$page){
   if(in_array($key,array('blog','privacy-policy'),true))continue;
   $existing=atelier_elementor_find($key);
   if($existing){$ids[$key]=$existing->ID;continue;}
   $id=wp_insert_post(array('post_type'=>'page','post_name'=>'atelier-elementor-'.$key,'post_title'=>$page[0],'post_status'=>'publish','post_content'=>$page[1],'comment_status'=>'closed'),true);
   if(is_wp_error($id))return $id;
   update_post_meta($id,'_atelier_elementor_key',$key);$ids[$key]=$id;$new[$key]=$id;
  }
  foreach($new as $key=>$id){
   $content=$pages[$key][1];
   foreach($ids as $slug=>$page_id)$content=str_replace('href="'.esc_url(atelier_link($slug)).'"','href="'.esc_url(get_permalink($page_id)).'"',$content);
   $nodes=atelier_elementor_blocks(parse_blocks($content));
   if(!in_array($key,array('home','studio-home'),true))array_unshift($nodes,atelier_elementor_node('heading',array('title'=>esc_html($pages[$key][0]),'header_size'=>'h1','typography_typography'=>'custom','typography_font_family'=>'Atelier Editorial','typography_font_weight'=>'400','title_color'=>'inherit','_css_classes'=>'atelier-page-title','__globals__'=>array('title_color'=>'','typography_typography'=>''))));
   update_post_meta($id,'_elementor_edit_mode','builder');update_post_meta($id,'_elementor_template_type','wp-page');update_post_meta($id,'_elementor_version',ELEMENTOR_VERSION);
   update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($nodes)));
   update_post_meta($id,'_elementor_page_settings',array('hide_title'=>'yes'));
   update_post_meta($id,'_wp_page_template','page-elementor.php');
   \Elementor\Core\Files\CSS\Post::create($id)->update();
  }
  if($assign_home && isset($ids['home']) && get_post_status($ids['home'])==='publish'){update_option('show_on_front','page');update_option('page_on_front',$ids['home']);}
  \Elementor\Plugin::$instance->files_manager->clear_cache();return $ids;
 }finally{delete_option('atelier_elementor_lock');}
}
add_action('admin_post_atelier_elementor_create',function(){
 if(!current_user_can('edit_theme_options'))wp_die(esc_html__('Permission denied.','atelier-salon'));
 check_admin_referer('atelier_elementor_create');$result=atelier_elementor_create(!empty($_POST['assign_home']));
 if(is_wp_error($result))wp_die(esc_html($result->get_error_message()));
 wp_safe_redirect(admin_url('themes.php?page=atelier-elementor'));exit;
});
add_action('admin_menu',function(){add_theme_page(__('Atelier Elementor setup','atelier-salon'),__('Atelier Elementor setup','atelier-salon'),'edit_theme_options','atelier-elementor','atelier_elementor_panel');});
function atelier_elementor_panel(){
 if(!current_user_can('edit_theme_options'))return;
 echo '<div class="wrap"><h1>'.esc_html__('Atelier Elementor setup','atelier-salon').'</h1><p>'.esc_html__('Create 14 separate pages editable with Elementor Free. Existing pages, menus and edits are preserved. Header and footer stay in the theme.','atelier-salon').'</p>';
 if(!atelier_elementor_ready())echo '<p><a href="'.esc_url(admin_url('plugin-install.php?s=elementor&tab=search&type=term')).'">'.esc_html__('Install and activate Elementor Free','atelier-salon').'</a></p>';
 else{
  echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'">';wp_nonce_field('atelier_elementor_create');
  echo '<input type="hidden" name="action" value="atelier_elementor_create"><p><label><input type="checkbox" name="assign_home" value="1"> '.esc_html__('Use the new Elementor Home as my homepage','atelier-salon').'</label></p><button class="button button-primary">'.esc_html__('Create Elementor pages','atelier-salon').'</button></form>';
 }
 $pages=require get_template_directory().'/starter-content.php';echo '<ul>';
 foreach($pages as $key=>$page){$post=atelier_elementor_find($key);if(!$post || $post->post_status==='trash')continue;
 echo '<li><a href="'.esc_url(add_query_arg(array('post'=>$post->ID,'action'=>'elementor'),admin_url('post.php'))).'">'.esc_html(sprintf(__('Edit %s with Elementor','atelier-salon'),$page[0])).'</a></li>';}
 echo '</ul><p>'.esc_html__('Support: najninkamalrimi@gmail.com','atelier-salon').'</p></div>';
}
add_filter('body_class',function($classes){
 $key=get_post_meta(get_queried_object_id(),'_atelier_elementor_key',true);if($key==='studio-home')$classes=array_diff($classes,array('atelier-noir'));
 if(atelier_elementor_page_active() && $key)$classes[]='atelier-elementor';return array_values($classes);
});
add_action('wp_enqueue_scripts',function(){if(atelier_elementor_ready())wp_enqueue_style('atelier-elementor',get_template_directory_uri().'/assets/elementor.css',array('atelier'),ATELIER_VERSION);},99);

add_filter('elementor/fonts/additional_fonts',function($fonts){$fonts['Atelier Editorial']='system';$fonts['Atelier Sans']='system';return $fonts;});
