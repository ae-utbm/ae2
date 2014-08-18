<?php

namespace AE2\Provider;

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../Application.php';

use Silex\Application;
use Silex\ServiceProviderInterface;

class PhpRendererServiceProvider implements ServiceProviderInterface
{
	private $app;
	
	public function register(Application $app)
	{
		$app['renderer'] = $this;
	}

	public function boot(Application $app)
	{
		$this->app = $app;
	}

	public function render($filename, array $variables)
	{
		$app = $this->app;

		foreach ($variables as $key => $value)
			$$key = $value;

		ob_start();

		include $filename;

		return ob_get_clean();
	}
}
