<?php

namespace App\Controllers;
use \App\Classes as classes;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Models\User;




defined('BASEPATH') OR exit('No direct script access allowed');

class UsersController extends Controller{
    
    
        
    
    public $route = [ 'index' => 'users' , 'create' => 'users.create'  ];
    public $model = 'user';
    public $folder = 'users';
    
   
    
    public function delete($request,$response,$args) {
        
        // get the id & the post
        $id = rtrim($args['id'], '/');
        $user = User::find($id);
        if($user) {
            $user->delete();
            $this->flashsuccess('success');
        }
        
        // redirect to users Home
        return $response->withRedirect($this->router->pathFor('users'));  
    }
    

    
    public function store($request,$response, $args){

        $post = cleanify($request->getParams());

        // the route to redirect for errors
        $route = $response->withRedirect($this->router->pathFor('users'));

        // Clean the variables & set the username & email to lowercase
        $username   = strtolower($post['username']);
        $email      = strtolower($post['email']);

        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->password = password_hash($post['password'],PASSWORD_DEFAULT);
        $user->role = $post['role'];
        $user->phone = $post['phone'];
        $user->cin = $post['cin'];
        $user->ville = $post['ville'];
        $user->banck = $post['banck'];
        $user->rib = $post['rib'];
        $user->save();
        $this->flashsuccess('l\'utilisateur a été créé avec succès');
        return $route;
             
    }


    
    public function account($request,$response) {
       return $this->container->view->render($response,'admin/account.twig');
    } 
    

    public function update($request,$response,$args){
            
        // Get the parameters Sent by the Form & initialize the helper & the fileupldader
        $post = cleanify($request->getParams());

        $route = $response->withRedirect($this->router->pathFor('users'));

        $user = User::find($args['id']);

        // update the user info
        $user->password   = !empty($post['password']) ? password_hash($post['password'],PASSWORD_DEFAULT) : $user->password;
        $user->username   = strtolower($post['username']);;
        $user->email      = strtolower($post['email']);
        $user->cin = $post['cin'];
        $user->ville = $post['ville'];
        $user->banck = $post['banck'];
        $user->rib = $post['rib'];
        $user->save();

        
        $this->flashsuccess('les détails de l\'utilisateur ont été modifiés avec succès');
        
        return $route;    
    }

      

}
