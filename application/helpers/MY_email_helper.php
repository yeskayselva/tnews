<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
function sendEmail($to,$subject,$template,$redirect = NULL) 
{    
		$CI =& get_instance();
		$config['protocol'] = 'smtp';
		$config['smtp_host'] = $CI->config->item("smtp_host");
		$config['smtp_user'] = $CI->config->item("smtp_user"); 
		$config['smtp_pass'] = $CI->config->item("smtp_pass");
		$config['smtp_port'] = 587;
		$config['crlf'] = "\r\n";
		$config['newline'] = "\r\n";
		$config['mailtype']="html";
		$CI->load->library('email', $config);
		$CI->email->set_newline("\r\n");
		$CI->email->from($CI->config->item("smtp_user"),"ARM-Admin");
		$CI->email->to($to);
		$CI->email->subject($subject);
		$CI->email->message($template);
		$result=$CI->email->send(); 
		/*if($result)
		redirect($redirect,"refresh");*/
}

function sendEmail_new($email_data) 
{    
		
      //array('to'=>$email,'template'=>1,'attachment'=>$attachment,'content' => array('#CONSULTANT#'=>$agentname,'#REFEREED#'=>$refereed_id),'subject' => array('#CONSULTANT#'=>$agentname,'#REFEREED#'=>$refereed_id) );
		$CI =& get_instance();
		$template_data = get_data('email_template',array('id'=>$email_data['template']))->row_array();
		$subject = isset($email_data['subject']) ? email_cont_replace($email_data['subject'],$template_data['subject']) :  $template_data['subject'];
		$content = isset($email_data['content']) ? email_cont_replace($email_data['content'],$template_data['content']) :  $template_data['content'];
		$to = isset($email_data['to']) ? $email_data['to'] :  $template_data['to'];
		$email_from = isset($email_data['email_from']) ? $email_data['email_from'] :  explode(',',$template_data['email_from']);
		$cc = isset($email_data['cc']) ? $email_data['cc'] :  explode(',',$template_data['cc']);
		$config['protocol'] = 'smtp';
		$config['smtp_host'] = $CI->config->item("smtp_host");
		$config['smtp_user'] = $CI->config->item("smtp_user"); 
		$config['smtp_pass'] = $CI->config->item("smtp_pass");
		$config['smtp_port'] = 587;
		$config['crlf'] = "\r\n";
		$config['newline'] = "\r\n";
		$config['mailtype']="html";
		$frm = explode(',',$template_data['email_from']);
		$CI->load->library('email', $config);
		$CI->email->clear(TRUE);
		$CI->email->set_newline("\r\n");
		$CI->email->from($email_from[0],$email_from[1]);
		if($cc != "")
		$CI->email->cc($cc);
		$CI->email->to($to);
		$CI->email->subject($subject);
		$CI->email->message($content);
		if(isset($email_data['attachment']))
		$CI->email->attach($email_data['attachment'],'inline');	
		return $result=$CI->email->send(); 

}

function email_cont_replace($arr,$subject){
	$subject = str_replace(array_keys($arr),array_values($arr),$subject);
	return $subject;
}


?>