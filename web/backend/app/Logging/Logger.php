<?php

namespace App\Logging;

class Logger extends \Monolog\Logger
{
	private const FORMAT = '[%datetime%] %channel%.%level_name%: %message% %context% %extra%' . "\n";
	private const DATEFORMAT = "Y-m-d\tH:i:s.u";

	public function __construct($logger_config)
	{
		parent::__construct($logger_config['channel_name']);
		
		$file_handler = new \Monolog\Handler\StreamHandler($logger_config['path']);

		/* Parameters: format, dateFormat, allowInlineLineBreaks, ignoreEmptyContextAndExtra */
		$formatter    = new \Monolog\Formatter\LineFormatter(static::FORMAT, static::DATEFORMAT, false, true);

		$file_handler->setFormatter($formatter);
		$this->pushHandler($file_handler);
	}

	public function dump($obj)
	{
		ob_start();
		var_dump($obj);
		$content = ob_get_contents();
		ob_end_clean();
		$this->addDebug($content);
		return $content;
	}
}