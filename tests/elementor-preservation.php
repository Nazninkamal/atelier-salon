<?php
wp_set_current_user(1);$before=array();$posts=get_posts(array('post_type'=>'page','numberposts'=>-1,'meta_key'=>'_atelier_elementor_key'));foreach($posts as $post)$before[$post->ID]=get_post_meta($post->ID,'_elementor_data',true);
$home=(int)get_option('page_on_front');$menu=get_theme_mod('nav_menu_locations');$result=atelier_elementor_create();
$after=get_posts(array('post_type'=>'page','numberposts'=>-1,'meta_key'=>'_atelier_elementor_key'));
$assertions=array('fourteen_pages'=>count($result)===14,'no_duplicates'=>count($before)===count($after),'home_preserved'=>$home===(int)get_option('page_on_front'),'menu_preserved'=>$menu===get_theme_mod('nav_menu_locations'),'edits_preserved'=>true);
foreach($before as $id=>$data)if($data!==get_post_meta($id,'_elementor_data',true))$assertions['edits_preserved']=false;
wp_set_current_user(0);$assertions['unauthorized_blocked']=is_wp_error(atelier_elementor_create());foreach($assertions as $key=>$value)if(!$value)throw new Exception($key);echo wp_json_encode($assertions);
