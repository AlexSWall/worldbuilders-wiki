<?php

namespace App\Mail;

class Mailer
{
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
		$this->container->logger->addInfo('Creating email message.');
		$message = new Message($this->mailer);

		$message->body($this->container->view->fetch($template, $data));

		call_user_func($callback, $message);

		$this->container->logger->addInfo('Sending email.');
		$this->mailer->send();
	}
}