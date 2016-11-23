<?php

class Mailer {
	private static $to;
	private static $subject;
	private static $body;

	static function set_to ($to) {
		// set the to address
		self::$to = $to;
	}

	static function set_subject ($subject) {
		// set the subject line for the email
		self::$subject = $subject;
	}

	static function set_body ($body) {
		// set the body of the email
		self::$body = $body;
	}

	static function send () {
		// use the PHPMailer library to send emails
		$mail = new PHPMailer;
		$mail->setFrom(APP_MAILER_FROM_ADDRESS, APP_MAILER_FROM_NAME);
		$mail->addAddress(self::$to);
		$mail->Subject = self::$subject;
		$mail->Body = self::$body;

		// use SMTP if the app mailer protocol is set to that
		if (APP_MAILER_PROTOCOL == 'SMTP') {
			$mail->isSMTP();
			$mail->Host = APP_MAILER_HOST;
			$mail->Port = APP_MAILER_PORT;
			$mail->SMTPAuth = true;
			$mail->Username = APP_MAILER_USERNAME;
			$mail->Password = APP_MAILER_PASSWORD;
		}

		// send the email, and echo errors if any occurred
		if (!$mail->send()) {
			Alerter::set_message('error', $mail->ErrorInfo);
		}
	}
}