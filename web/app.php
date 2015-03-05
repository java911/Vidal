<?php

error_reporting(0);

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;

# память, необходимая админке Сонаты
if (strpos($_SERVER['REQUEST_URI'], '/admin/vidal/') !== false) {
	ini_set('memory_limit', -1);
}

$loader = require_once __DIR__ . '/../app/bootstrap.php.cache';

// Use APC for autoloading to improve performance.
// Change 'sf2' to a unique prefix in order to prevent cache key conflicts
// with other applications also using APC.
/*
$loader = new ApcClassLoader('sf2', $loader);
$loader->register(true);
*/

require_once __DIR__ . '/../app/AppKernel.php';
//require_once __DIR__.'/../app/AppCache.php';

if (strpos($_SERVER['REQUEST_URI'], 'otvety_specialistov') !== false) {
	$kernel = new AppKernel('prod', true);
	exit;
}
else {
	$kernel = new AppKernel('prod', false);
}

$kernel->loadClassCache();
//$kernel = new AppCache($kernel);
$request  = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
