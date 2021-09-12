<?php declare( strict_types = 1 );

namespace App\Mail;

use PHPMailer\PHPMailer\PHPMailer;

class Message
{
	protected PHPMailer $mailer;

	public function __construct(PHPMailer $mailer)
	{
		$this->mailer = $mailer;
	}

	public function to(string $address, string $name): void
	{
		$this->mailer->addAddress($address, $name);
	}

	public function subject(string $subject): void
	{
		$this->mailer->Subject = $subject;
	}

	public function body(string $body): void
	{
		$this->mailer->Body = $body;
	}
}
