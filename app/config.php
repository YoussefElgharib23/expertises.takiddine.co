<?php

defined('BASEPATH') OR exit('No direct script access allowed');


define('SCRIPTURL','http://basiri.mediaman.website/');
define('SCRIPTDIR', BASEPATH.'/');

return  [
    
    'app' => [
        'debug'              => false,
		 'version_css'       => 3.9,
    ],
    
    
    
      
    'db_live' => [
        'driver'             => 'mysql',
        'host'               => 'localhost',
        'name'               => 'medizodx_basiri',
        'username'           => 'root',
        'password'           => 'fsm@rtboy7A',
        'charset'            => 'utf8',
        'collation'          => 'utf8_general_ci',
        'strict'             => 'false',
    ],
    
    
    'db_sandbox' => [
        'driver'             => 'mysql',
        'host'               => 'localhost',
        'name'               => 'bassiri',
        'username'           => 'root',
        'password'           => '',
        'charset'            => 'utf8',
        'collation'          => 'utf8_general_ci',
        'strict'             => 'false'
    ],
    
    
    'url' => [
        'base'               => SCRIPTURL,
        'assets'             => '/assets/',
        'sinistres'          => SCRIPTURL.'uploads/sinistres',  
        'zip'                => SCRIPTURL.'uploads/zip/', 
    ],
    
    'dir' => [
        'base'               => SCRIPTDIR,
        'sinistres'          => SCRIPTDIR.'public/uploads/sinistres/',
        'zip'                => SCRIPTDIR.'public/uploads/zip/',
    ],

];