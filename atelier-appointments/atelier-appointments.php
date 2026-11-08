<?php
/**
 * Plugin Name: Atelier Appointments
 * Plugin URI: https://github.com/Nazninkamal/atelier-salon
 * Description: Emails appointment requests. Availability is confirmed by the salon.
 * Version: 1.1.0
 * Author: Naznin Kamal
 * Author URI: https://github.com/Nazninkamal
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: atelier-appointments
 * Domain Path: /languages
 */
if (!defined('ABSPATH')) exit;
add_action('init',function(){load_plugin_textdomain('atelier-appointments',false,dirname(plugin_basename(__FILE__)).'/languages');});
function atelier_appointments_length($value) { return function_exists('mb_strlen') ? mb_strlen($value) : preg_match_all('/./us',$value,$matches); }
function atelier_appointments_services() {
 return array('Cut & finish'=>__('Cut & finish','atelier-appointments'),'Color & gloss'=>__('Color & gloss','atelier-appointments'),'Balayage'=>__('Balayage','atelier-appointments'),'Scalp & repair'=>__('Scalp & repair','atelier-appointments'),'Styling'=>__('Styling','atelier-appointments'),'Consultation'=>__('Consultation','atelier-appointments'));
}
function atelier_appointments_times() {
 return array('Morning'=>__('Morning','atelier-appointments'),'Afternoon'=>__('Afternoon','atelier-appointments'),'Evening'=>__('Evening','atelier-appointments'));
}
function atelier_appointments_url() {
 $page=get_page_by_path('book-appointment');
 return $page && get_post_status($page)==='publish' ? get_permalink($page) : home_url('/');
}
function atelier_booking_shortcode() {
 // Logged-in and logged-out nonces must not be cached together or beyond their lifetime.
 if (!defined('DONOTCACHEPAGE')) define('DONOTCACHEPAGE',true);
 if (!headers_sent()) nocache_headers();
 $status=isset($_GET['appointment']) && is_string($_GET['appointment']) ? sanitize_key(wp_unslash($_GET['appointment'])) : '';
 ob_start();
 if ($status==='sent') echo '<div class="form-notice" role="status">'.esc_html__('Your request has been sent. Your appointment is confirmed only when the salon replies.','atelier-appointments').'</div>';
 elseif ($status==='error') echo '<div class="form-notice" role="alert">'.esc_html__('We could not send your request. Check all fields and try again, or contact the salon directly.','atelier-appointments').'</div>';
 ?>
 <form class="booking-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
 <input type="hidden" name="action" value="atelier_booking"><?php wp_nonce_field('atelier_booking','atelier_nonce'); ?>
 <div class="form-grid">
 <label><?php esc_html_e('Your name','atelier-appointments'); ?><input name="client_name" autocomplete="name" required maxlength="100"></label>
 <label><?php esc_html_e('Email address','atelier-appointments'); ?><input name="client_email" type="email" autocomplete="email" required maxlength="254"></label>
 <label><?php esc_html_e('Phone (optional)','atelier-appointments'); ?><input name="client_phone" type="tel" autocomplete="tel" maxlength="40"></label>
 <label><?php esc_html_e('Service','atelier-appointments'); ?><select name="service" required><option value=""><?php esc_html_e('Select a service','atelier-appointments'); ?></option><?php foreach(atelier_appointments_services() as $value=>$label): ?><option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option><?php endforeach; ?></select></label>
 <label><?php esc_html_e('Preferred date','atelier-appointments'); ?><input name="date" type="date" min="<?php echo esc_attr(wp_date('Y-m-d')); ?>" required></label>
 <label><?php esc_html_e('Preferred time','atelier-appointments'); ?><select name="time" required><option value=""><?php esc_html_e('Select a time','atelier-appointments'); ?></option><?php foreach(atelier_appointments_times() as $value=>$label): ?><option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option><?php endforeach; ?></select></label>
 </div>
 <label><?php esc_html_e('Anything we should know? (optional)','atelier-appointments'); ?><textarea name="message" rows="4" maxlength="2000"></textarea></label>
 <div class="honeypot" hidden aria-hidden="true"><label><?php esc_html_e('Leave empty','atelier-appointments'); ?><input name="website_url" tabindex="-1" autocomplete="off"></label></div>
 <label class="consent"><input type="checkbox" name="consent" value="1" required> <?php esc_html_e('I agree to be contacted about this appointment request.','atelier-appointments'); ?></label>
 <p class="small"><?php esc_html_e('This is a request, not a confirmed appointment. Availability is confirmed by the salon.','atelier-appointments'); ?></p><button class="button" type="submit"><?php esc_html_e('Request appointment','atelier-appointments'); ?> <span aria-hidden="true">↗</span></button>
 </form>
 <?php return ob_get_clean();
}
add_shortcode('atelier_booking','atelier_booking_shortcode');
/** Validate and send without redirects so integrations can inspect a WP_Error. */
function atelier_appointments_submit($input,$ip) {
 $error=new WP_Error('invalid_request',__('Check all fields and try again, or contact the salon directly.','atelier-appointments'));
 if (!is_array($input)) return $error;
 foreach(array('atelier_nonce','client_name','client_email','client_phone','service','date','time','message','consent','website_url') as $key) {
  if (isset($input[$key]) && !is_string($input[$key])) return $error;
 }
 if (empty($input['atelier_nonce']) || !wp_verify_nonce($input['atelier_nonce'],'atelier_booking') || !empty($input['website_url'])) return $error;
 $v=array();foreach(array('client_name','client_email','client_phone','service','date','time','message') as $key) $v[$key]=isset($input[$key])?sanitize_textarea_field($input[$key]):'';
 $date=DateTime::createFromFormat('!Y-m-d',$v['date']);
 if (!$v['client_name'] || atelier_appointments_length($v['client_name'])>100 || strlen($v['client_email'])>254 || !is_email($v['client_email']) || atelier_appointments_length($v['client_phone'])>40 || !isset(atelier_appointments_services()[$v['service']]) || !isset(atelier_appointments_times()[$v['time']]) || !$date || $date->format('Y-m-d')!==$v['date'] || $v['date']<wp_date('Y-m-d') || !isset($input['consent']) || $input['consent']!=='1' || atelier_appointments_length($v['message'])>2000) return $error;
 // Use the direct peer address; do not trust a client-supplied forwarding header.
 $key='atelier_request_'.hash_hmac('sha256',(string)$ip,wp_salt('nonce'));
 if (get_transient($key)) return new WP_Error('rate_limited',__('Please wait a minute before trying again.','atelier-appointments'));
 set_transient($key,1,60);
 $email=sanitize_email(get_option('atelier_appointment_email') ?: get_option('admin_email'));
 if (!is_email($email)) return new WP_Error('mail_failed',__('Contact the salon directly to request an appointment.','atelier-appointments'));
 $labels=array('client_name'=>__('Name','atelier-appointments'),'client_email'=>__('Email','atelier-appointments'),'client_phone'=>__('Phone','atelier-appointments'),'service'=>__('Service','atelier-appointments'),'date'=>__('Preferred date','atelier-appointments'),'time'=>__('Preferred time','atelier-appointments'),'message'=>__('Message','atelier-appointments'));
 $body=__('Appointment request (not confirmed)','atelier-appointments')."\n\n";
 foreach($v as $key=>$val) $body.=$labels[$key].': '.$val."\n";
 $sent=wp_mail($email,__('New salon appointment request','atelier-appointments'),$body,array('Reply-To: '.sanitize_email($v['client_email'])));
 return $sent ? true : new WP_Error('mail_failed',__('Contact the salon directly to request an appointment.','atelier-appointments'));
}
function atelier_booking() {
 $input=wp_unslash($_POST);
 $result=atelier_appointments_submit($input,isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'unknown');
 wp_safe_redirect(add_query_arg('appointment',is_wp_error($result)?'error':'sent',atelier_appointments_url()));exit;
}
add_action('admin_post_atelier_booking','atelier_booking');add_action('admin_post_nopriv_atelier_booking','atelier_booking');
function atelier_appointments_settings() {
 register_setting('general','atelier_appointment_email',array('type'=>'string','sanitize_callback'=>'sanitize_email','default'=>''));
 add_settings_field('atelier_appointment_email',__('Appointment email','atelier-appointments'),function(){
 echo '<input type="email" class="regular-text" name="atelier_appointment_email" value="'.esc_attr(get_option('atelier_appointment_email')).'"><p class="description">'.esc_html__('Appointment requests are emailed here. Leave blank to use the site administrator email.','atelier-appointments').'</p>';
 },'general');
}
add_action('admin_init','atelier_appointments_settings');
