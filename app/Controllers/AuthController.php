<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Models\User;


defined('BASEPATH') OR exit('No direct script access allowed');

class AuthController extends Controller {
    
    
    public function getLogin($request,$response) {
       return $this->container->view->render($response,'admin/auth/login.twig');
    } 
    
    
    public function login($request,$response) {


        $post = cleanify($request->getParams());
        
        
        // get the login credentials
        $user = $post['user_login'];
        $pass = $post['pass_login'];
        
        // admin login
        $auth = $this->auth->attempt($user,$pass);

        
        if($auth) {

                 if($auth->role == 'colaborator' ) {
                    $route = $response->withRedirect($this->container->router->pathFor('missions.ListForcolaborator'));
                }else {
                    $route = $response->withRedirect($this->container->router->pathFor('admin.index'));
                }
                return $route ;

            
        }
        
    
        $this->flasherror('المعلومات غير صحيحة');
        
        $route = $response->withRedirect($this->container->router->pathFor('admin.index'));
        return $route;
        
        
        
    }
    
    public function logout($request,$response) {
        unset($_SESSION['auth']);
        return $response->withRedirect($this->container->router->pathFor('admin.index'));
    }
 
}

