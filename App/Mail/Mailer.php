<?php

namespace App\Mail;

class Mailer
{
	static $logger;

	protected $container;

	protected $mailer;

	public function __construct($container, $mailer)
	{
		$this->container = $container;
		$this->mailer = $mailer;
	}

	/* The $callback parameter is a function that takes a \App\Mail\Message instance as a parameter and adds information. */
	public function send($template, $data, $callback)
	{
		self::$logger->addInfo('Creating email message.');
		$message = new Message($this->mailer);

		$message->body($this->container->view->fetch($template, $data));

		call_user_func($callback, $message);

		self::$logger->addInfo('Sending email.');
		$this->mailer->send();
	}
}