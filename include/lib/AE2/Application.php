<?php

/* Copyright 2007
 * - Ludovic Henry < ludovichenry DOT utbm AT gmail DOT com >
 *
 * Ce fichier fait partie du site de l'Association des étudiants de
 * l'UTBM, http://ae.utbm.fr.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA
 * 02111-1307, USA.
 */

namespace AE2;

require __DIR__ . '/../../../vendor/autoload.php';

use Silex;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

$topdir = __DIR__ . '/../../../';

require_once __DIR__ . '/../../site.inc.php';

class Application extends Silex\Application
{
	public function	__construct($debug = FALSE, array $values = array())
	{
		parent::__construct ($values);

		$this ['debug'] = $debug;

		$this->register (new Silex\Provider\DoctrineServiceProvider(), array(
		    'db.options' => array(
		        'driver'   => 'pdo_mysql',
		        'dbname'   => \mysqlae::$database,
		        'host'     => \mysqlae::$host,
		        'user'     => \mysqlae::$login_read_only,
		        'password' => \mysqlae::$mdp_read_only,
		    ),
		));

		$this->register(new Silex\Provider\ServiceControllerServiceProvider());
		$this->register(new Silex\Provider\TwigServiceProvider());
		$this->register(new Silex\Provider\UrlGeneratorServiceProvider());

		$this->register(new Silex\Provider\WebProfilerServiceProvider(), array(
		    'profiler.cache_dir' => '/tmp/profiler',
		    'profiler.mount_prefix' => '/_profiler', // this is the default
		));

		$this->register(new Provider\PhpRendererServiceProvider());

		$this->before(function (Request $request) {
			$request->attributes->set('site', new \site());
		}, Application::EARLY_EVENT);

		$this->error(function (\Exception $e, $code) use ($app) {
		    if ($this['debug'])
		        return;

		    return new Response($code);
		});
	}

	public static function check_user_is_valid (Request $request)
	{
	        $site = $request->attributes->get('site');

	        if (!$site->user->is_valid())
	        	return new RedirectResponse('/connexion.php?redirect_to=' . urlencode($request->getUri()));
	}
}