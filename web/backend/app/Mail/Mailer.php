<?php

declare(strict_types=1);

namespace App\Mail;

use App\Globals\FrontEndParametersFacade;

use App\Models\User;

use PHPMailer\PHPMailer\PHPMailer;
use Slim\Views\Twig as View;

class Mailer
{
	public static \App\Logging\Logger $logger;

	protected PHPMailer $mailer;
	protected View $mailerView;

	public function __construct( PHPMailer $mailer, View $mailerView )
	{
		$this->mailerView = $mailerView;
		$this->mailer = $mailer;
	}

	/* The $callback parameter is a function that takes a \App\Mail\Message instance as a parameter and adds information. */
	public function send( User $user, string $subject, string $templateName, array $data ): void
	{
		$data['preferredName'] = $user->getPreferredName();
		$data['baseUrl'] = FrontEndParametersFacade::getBaseUrl();

		self::$logger->info( 'Creating an email to ' . $user->getUsername() . ' at ' . $user->getEmail() . '.' );
		self::$logger->info( 'Email being sent: ' . $templateName . '.' );
		$message = new Message( $this->mailer );

		$message->to( $user->getEmail(), $user->getPreferredName() );
		$message->subject( $subject );
		$message->body( $this->mailerView->fetch( $templateName, $data ) );

		self::$logger->info( 'Sending email.' );
		$this->mailer->send();
	}
}
