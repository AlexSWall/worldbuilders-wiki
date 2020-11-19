<?php

namespace App\Mail;

use App\Globals\FrontEndParametersFacade;

class Mailer
{
	static $logger;

	protected $mailer;
	protected $mailerView;

	public function __construct($mailer, $mailerView)
	{
		$this->mailerView = $mailerView;
		$this->mailer = $mailer;
	}

	/* The $callback parameter is a function that takes a \App\Mail\Message instance as a parameter and adds information. */
	public function send($user, $subject, $templateName, $data)
	{
		$data['preferredName'] = $user->getPreferredName();
		$data['baseUrl'] = FrontEndParametersFacade::getBaseUrl();

		self::$logger->addInfo('Creating an email to ' . $user->getUsername() . ' at ' . $user->getEmail() . '.');
		self::$logger->addInfo('Email being sent: ' . $templateName . '.');
		$message = new Message($this->mailer);

		$message->to($user->getEmail(), $user->getPreferredName());
		$message->subject($subject);
		$message->body($this->mailerView->fetch($templateName, $data));

		self::$logger->addInfo('Sending email.');
		$this->mailer->send();
	}
}