<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
namespace App\Middleware;

defined('BASEPATH') OR exit('No direct script access allowed');

class AssistanteMiddleware extends Middleware {
    
    public function __invoke($request, $response, $next)
    {

        if(isset($_SESSION['auth'])) {
            if(isset($_SESSION['role']) and ($_SESSION['role'] != 'assistante')) {
                            return $response->withRedirect('/missions/colaborator/');
            }else {
                 $response = $next($request, $response);
                 return $response;
            }
        }else {
            $response = $next($request, $response);
            return $response;
        }
    }

}