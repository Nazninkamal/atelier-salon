<?php
/**
 * Plugin Name: Atelier Appointments
 * Description: Appointment request form for Atelier Salon. Requests are emailed, not confirmed bookings.
 * Version: 1.0.0
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 * Text Domain: atelier-appointments
 */
if (!defined('ABSPATH')) exit;
function atelier_appointments_url() {
 $page=get_page_by_path('book-appointment');
 return $page ? get_permalink($page) : home_url('/');
}
function atelier_booking_shortcode() {
 ob_start(); $status=isset($_GET['appointment']) ? sanitize_key(wp_unslash($_GET['appointment'])) : '';
 if ($status==='sent') echo '<div class="form-notice" role="status">Your request has been sent. Your appointment is confirmed only when the salon replies.</div>';
 elseif ($status==='error') echo '<div class="form-notice" role="alert">We could not send your request. Check all fields and try again, or contact the salon directly.</div>';
 ?>
 <form class="booking-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
 <input type="hidden" name="action" value="atelier_booking"><?php wp_nonce_field('atelier_booking','atelier_nonce'); ?>
 <div class="form-grid"><label>Your name<input name="client_name" autocomplete="name" required maxlength="100"></label><label>Email address<input name="client_email" type="email" autocomplete="email" required maxlength="254"></label><label>Phone <span>(optional)</span><input name="client_phone" type="tel" autocomplete="tel" maxlength="40"></label><label>Service<select name="service" required><option value="">Select a service</option><option>Cut &amp; finish</option><option>Color &amp; gloss</option><option>Balayage</option><option>Scalp &amp; repair</option><option>Styling</option><option>Consultation</option></select></label><label>Preferred date<input name="date" type="date" min="<?php echo esc_attr(wp_date('Y-m-d')); ?>" required></label><label>Preferred time<select name="time" required><option value="">Select a time</option><option>Morning</option><option>Afternoon</option><option>Evening</option></select></label></div>
 <label>Anything we should know? <span>(optional)</span><textarea name="message" rows="4" maxlength="2000"></textarea></label>
 <div class="honeypot" aria-hidden="true"><label>Leave empty<input name="website_url" tabindex="-1" autocomplete="off"></label></div>
 <label class="consent"><input type="checkbox" name="consent" value="1" required> I agree to be contacted about this appointment request.</label>
 <p class="small">This is a request, not a confirmed appointment. Availability is confirmed by the salon.</p><button class="button" type="submit">Request appointment <span>↗</span></button>
 </form>
 <?php return ob_get_clean();
}
add_shortcode('atelier_booking','atelier_booking_shortcode');
function atelier_booking() {
 $target=atelier_appointments_url();
 $fail=function() use($target) { wp_safe_redirect(add_query_arg('appointment','error',$target)); exit; };
 if (!isset($_POST['atelier_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['atelier_nonce'])),'atelier_booking')) $fail();
 if (!empty($_POST['website_url'])) $fail();
 $v=array(); foreach(array('client_name','client_email','client_phone','service','date','time','message') as $k) $v[$k]=isset($_POST[$k]) ? sanitize_textarea_field(wp_unslash($_POST[$k])) : '';
 $services=array('Cut & finish','Color & gloss','Balayage','Scalp & repair','Styling','Consultation');
 $date=DateTime::createFromFormat('!Y-m-d',$v['date']);
 if (!$v['client_name'] || strlen($v['client_name'])>100 || !is_email($v['client_email']) || !in_array($v['service'],$services,true) || !in_array($v['time'],array('Morning','Afternoon','Evening'),true) || !$date || $date->format('Y-m-d')!==$v['date'] || $v['date']<wp_date('Y-m-d') || empty($_POST['consent']) || strlen($v['message'])>2000) $fail();
 // Per-visitor throttle. WordPress nonces prevent forged requests, not spam.
 $key='atelier_request_'.hash_hmac('sha256',isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'unknown',wp_salt('nonce'));
 if (get_transient($key)) $fail(); set_transient($key,1,60);
 $email=get_option('atelier_appointment_email') ?: get_option('admin_email');
 $body="Appointment request (not confirmed)\n\n";
 foreach($v as $k=>$val) $body.=str_replace('_',' ',ucfirst($k)).': '.$val."\n";
 $sent=wp_mail($email,'New salon appointment request',$body,array('Reply-To: '.sanitize_email($v['client_email'])));
 wp_safe_redirect(add_query_arg('appointment',$sent?'sent':'error',$target)); exit;
}
add_action('admin_post_atelier_booking','atelier_booking'); add_action('admin_post_nopriv_atelier_booking','atelier_booking');

function atelier_appointments_settings() {
 register_setting('general','atelier_appointment_email',array('type'=>'string','sanitize_callback'=>'sanitize_email','default'=>''));
 add_settings_field('atelier_appointment_email',__('Appointment email','atelier-appointments'),function(){
 echo '<input type="email" class="regular-text" name="atelier_appointment_email" value="'.esc_attr(get_option('atelier_appointment_email')).'"><p class="description">Appointment requests are emailed here. Leave blank to use the site administrator email.</p>';
 },'general');
}
add_action('admin_init','atelier_appointments_settings');
