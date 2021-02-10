<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Models\User;


defined('BASEPATH') OR exit('No direct script access allowed');

class NotificationsController extends Controller {
    

    public $folder  = 'notifications' ;
    public $model = 'Notification';
    public $route = [ 'index' => 'notifications' ];
    public $control = __CLASS__ ;
    
    
    public $messages = [
        'created'           => 'Notification a has been created successfully',
        'deleted'           => 'Ads has been deleted successfully',
        'updated'           => 'Ads has been updated successfully',
        'bulkDelete'        => 'All Adss deleted successfully',
        'cloned'            => 'Ads has been duplicated successfully',  
    ];



    public function index($request,$response) {
       $roles = $this->roles();
       $colaborators  = $roles['colaborators'];
       $assistante = $roles['assistante'];
       $notifications = $this->init()::all();
       return $this->container->view->render($response,'admin/pages/notifications.twig',compact('assistante','colaborators','notifications'));
    } 
    
    public function notifyColaborator($request,$response) {
       return $this->container->view->render($response,'admin/auth/login.twig');
    } 
    
    public function notifyAssistant($request,$response) {
       return $this->container->view->render($response,'admin/auth/login.twig');
    } 
    
    public function setAsSeen($request,$response) {
       $notifications = \App\Models\Notification::where('user_id',$this->user->id)->whereNull('seen')->get(); 
       foreach ($notifications as $item ) {
         $item->seen = 1;
         $item->save();
       }
    } 
    
    public function filter($params) {
       return $this->container->view->render($response,'admin/auth/login.twig');
    } 
    
 
}

