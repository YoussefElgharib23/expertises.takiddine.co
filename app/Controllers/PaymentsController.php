<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Models\User;


defined('BASEPATH') OR exit('No direct script access allowed');

class PaymentsController extends Controller {
    
    
    
    public $model = 'Payment';
    public $route = [ 'index' => 'notifications' ];
    public $control = __CLASS__ ;


    public function index($request,$response) {
       $roles = $this->roles();
       $colaborators  = $roles['colaborators'];
       $assistante = $roles['assistante'];
       $payements = $this->init('Payment')::orderBy('created_at', 'DESC')->get();
       if(isset($_GET['colaborateur']) and is_numeric($_GET['colaborateur'])){
          $payements = $this->init('Payment')::where('user_id',$_GET['colaborateur'])->orderBy('created_at', 'DESC')->get();
       }
       return $this->container->view->render($response,'admin/pages/payments.twig',compact('colaborators','assistante','payements'));
    } 

 
}

