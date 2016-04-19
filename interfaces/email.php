<?php

	global $email_from, $admin_email;
	$email_from = 'From Email <from@example.com>';
	$admin_email = 'Admin Email <admin@example.com>';

	function send_email($to, $data, $template, $subject = 'No Subject', $reply_to = false, $attachments = false){
		global $email_from, $admin_email;
		$boundary = uniqid('veev');
		$boundar2 = uniqid('inner');
		$headers = 'From: '.$email_from.
				($reply_to == false ? '' :
					"\r\n".'Sender: '.$email_from.
					"\r\n".'Reply-To: '.$reply_to).
				"\r\n".'MIME-Version: 1.0'.
				"\r\n".'Content-Type: multipart/related; boundary='.$boundary."\r\n\r\n".
				'--'.$boundary.
				"\r\n".'Content-Type: multipart/alternative; boundary='.$boundar2."\r\n\r\n";
		//
		$message = render_view('email/'.$template.'.php', $data);
		//
		$message = '--'.$boundar2."\r\n".
				'Content-type: text/plain; charset=utf-8'."\r\n\r\n".
				strip_tags($message).
				"\r\n\r\n--".$boundar2."\r\n".
				'Content-type: text/html; charset=utf-8'.
				"\r\n\r\n".$message."\r\n\r\n".
				'--'.$boundar2.'--'."\r\n".
				'--'.$boundary;
		if ($attachments !== false)
			foreach ($attachments as $filename => $data){
				$message .= "\r\n".'Content-Type: application/octet-stream; name="'.$filename.'"'."\r\n".
							'Content-Transfer-Encoding: base64'."\r\n".
							'Content-Disposition: attachment'."\r\n\r\n".
							$data."\r\n--".$boundary;
			}
		$message .= '--';
		$mail_sent = @mail($to, $subject, $message, $headers);
		return $mail_sent;
	}

?>