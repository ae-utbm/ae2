<?php

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new AE2\Application(false);

$app->post('/', function (Request $request) use ($app) {
    $remoteip    = ip2long($_SERVER['HTTP_X_FORWARDED_FOR']);
    $allowednet  = ip2long("192.30.252.0");
    $allowedmask = ip2long("255.255.252.0");
    
    if ($remoteip === false || ($remoteip & $allowedmask) != $allowednet) {
      $app->abort(403, "Vous n'êtes pas les serveurs GitHub");
    }

    $commands = array(
      "git --git-dir=" . __DIR__ . "/.git/ reset --hard HEAD",
      "git --git-dir=" . __DIR__ . "/.git/ pull origin master",
    );

    $success = true;
    $buffer = "";

    foreach ($commands as $cmd) {
      exec($cmd, $output, $return_value);

      $buffer  .= implode($output) . "\n";
      $success &= ($return_value == 0);

      if (!$success) {
        break;
      }
    }

    return new Response($buffer, $success ? 200 : 500);
});

$app->run();