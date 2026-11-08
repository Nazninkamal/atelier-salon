<?php
$base=array('atelier_nonce'=>wp_create_nonce('atelier_booking'),'client_name'=>'Test client','client_email'=>'client@example.test','client_phone'=>'','service'=>'Cut & finish','date'=>wp_date('Y-m-d',time()+86400*2),'time'=>'Morning','message'=>'Compatibility transport test','consent'=>'1');
$tests=array('valid'=>array(),'invalid-nonce'=>array('atelier_nonce'=>'bad'),'past'=>array('date'=>'2000-01-01'),'bad-consent'=>array('consent'=>'yes'),'bad-email'=>array('client_email'=>'invalid'),'honeypot'=>array('website_url'=>'spam'),'array-input'=>array('client_name'=>array('bad')),'phone-too-long'=>array('client_phone'=>str_repeat('1',41)),'invalid-date'=>array('date'=>'2027-02-30'),'invalid-service'=>array('service'=>'bogus'),'invalid-time'=>array('time'=>'Night'));
$results=array();
add_filter('pre_wp_mail',function(){return true;});
foreach($tests as $case=>$change){$ip='192.0.2.'.(20+count($results));delete_transient('atelier_request_'.hash_hmac('sha256',$ip,wp_salt('nonce')));$result=atelier_appointments_submit(array_merge($base,$change),$ip);$passed=$case==='valid'?$result===true:is_wp_error($result);if(!$passed)throw new Exception('Failed '.$case);$results[$case]=$passed;}
$ip='192.0.2.200';set_transient('atelier_request_'.hash_hmac('sha256',$ip,wp_salt('nonce')),1,60);$results['throttle']=is_wp_error(atelier_appointments_submit($base,$ip));
remove_all_filters('pre_wp_mail');add_filter('pre_wp_mail',function(){return false;});$ip='192.0.2.201';delete_transient('atelier_request_'.hash_hmac('sha256',$ip,wp_salt('nonce')));$result=atelier_appointments_submit($base,$ip);$results['mail-failure']=is_wp_error($result)&&$result->get_error_code()==='mail_failed';
echo wp_json_encode($results);
