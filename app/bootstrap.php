<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use PHPtricks\Orm\Database;
use Noodlehaus\Config;
use Illuminate\Database\Capsule\Manager as Capsule;

session_start();
date_default_timezone_set('Europe/Warsaw');


// define base route & path
define('INC_ROOT',dirname(__DIR__));
define('BASEPATH',dirname(__DIR__));

// Load the libraries
require INC_ROOT.'/vendor/autoload.php';
require INC_ROOT.'/app/helpers.php';

$config = ['settings' => [ 'displayErrorDetails' => true,        'debug'               => true,
], ];

// initialize the slim
$app = new \Slim\App($config);

// Call files
require INC_ROOT.'/app/container.php';



// call routes
require INC_ROOT.'/app/Routes/admin.php';