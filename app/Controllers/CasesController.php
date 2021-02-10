<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Models\{ User , Mission , Sinistre , Payment};


defined('BASEPATH') OR exit('No direct script access allowed');

class CasesController extends Controller {
    
    
    public $route = [ 'index' => 'sinistres' , 'create' => 'sinistre.create'  ];
    public $model = 'Sinistre';
    public $folder = 'sinitre';
  
    public function index($request,$response) {
       $colaborators = User::where('role','colaborator')->get();
       $assistante   = User::where('role','assistante')->get();
       $sinsitres    = Sinistre::query();


        // Pagination settings
        $count          = $sinsitres->count();         
        $page           = ($request->getParam('page', 0) > 0) ? $request->getParam('page') : 1;
        $limit          = 10; 
        $lastpage       = (ceil($count / $limit) == 0 ? 1 : ceil($count / $limit));   
        $skip           = ($page - 1) * $limit;

        // get the result
        $sinsitres       = $sinsitres->skip($skip)->take($limit)->orderBy('created_at', 'DESC');

        $sinsitres      = $sinsitres->get();
        $urlPattern     = "?page=(:num)";
        
        $pagination     = new \App\Helpers\Paginator($count, $limit, $page, $urlPattern);

       return $this->container->view->render($response,'admin/sinitre/index.twig',compact('pagination','colaborators','sinsitres','assistante'));
    } 

    public function edit($request,$response,$args) {
        $colaborators = User::where('role','colaborator')->get();
        $sinistre     = $this->init()::find($args['id']);
        return $this->container->view->render($response,'admin/sinitre/edit.twig',compact('colaborators','sinistre'));
    } 

    public function setPaied($request,$response,$args) {
        $sinistre           = Sinistre::find($args['id']);
        $data               = ['sinistre_id'=>$sinistre->id, 'user_id'=>$sinistre->colaborator_id ];
        $sinistre->paied_at = \Carbon\Carbon::Now();
        Payment::create($data);
        $sinistre->save();
        return $response->withRedirect($this->router->pathFor('sinistres'));
    } 



    
    public function search($request,$response) {

        // get the form content & filter
        $post        = cleanify($request->getParams());

        $search      = $post['q'];
        $colaborator = @$post['colaborator_selector'];
        $assistant   = @$post['assistant'] ;
        $date        = @$post['date'];
        $company     = @$post['company'];

        $model = $this->init();
       




        // get by search result
        if(!empty(($search))) {


         
        $result = Sinistre::
              where('telephone', 'LIKE', "%$search%")
          ->orWhere('assure', 'LIKE', "%$search%")
          ->orWhere('matricule', 'LIKE', "%$search%")
          ->orWhere('vehicule', 'LIKE', "%$search%");


          

        }else {
           $result = $model::whereNotNull('id');
        }
       

        if(!empty($company)){
           $result->where('company', $company);
        }
   
        if(!empty($date)){
          $result->where('date_sinistre', $date);
        }


        

        if(!empty($colaborator) and is_numeric($colaborator)){
          $result->where('colaborator_id', $colaborator); 
        }

        // check if the word start with ww
        $ww = substr( $search, 0, 2 );
        if( $ww == "WW"){
           $key =  substr($search, 2);
           $result = $model::where('matricule_ww', 'LIKE', "%$key%");
        }


        // Getting the results
        $result->orderBy('created_at', 'desc');

        $count =  $result->count();   
        $colaborators = User::where('role','colaborator')->get();
        $sinsitres = $result->get();
      
        // show the results
        return $this->container->view->render($response,'admin/sinitre/index.twig',compact('colaborators','sinsitres','count'));

    }
      
    
    /**
     * show the form of creating a sinistre
     * @author TakiDDine
     */
    public function create($request,$response) {
       $colaborators = User::where('role','colaborator')->get();
       return $this->container->view->render($response,'admin/sinitre/create.twig',compact('colaborators'));
    } 

    
    /**
     * save the sinistre in database
     * @author TakiDDine
     */
    public function store($request,$response, $args) {
        
          // save the sinitre
            $post = cleanify($request->getParams());

            $sinistre = New \App\Models\Sinistre();
            foreach ($post as $key => $value) {
              $sinistre->$key =  $value;
            }
            if(isset($post['nouveau_compagnie']) and !empty($post['nouveau_compagnie'])){
                $sinistre->company =  $post['nouveau_compagnie'];
            }
            $sinistre->save();
            
           return $response->withRedirect($this->router->pathFor('sinistres'));
      }
        


    /**
     * Update siniste Info
     * @author TakiDDine
     */
    public function update($request,$response,$args) {
        
            // save the sinitre
            $post = cleanify($request->getParams());

            // save the updates
            $sinistre = Sinistre::find($args['id']);
            foreach ($post as $key => $value) {
              $sinistre->$key =  $value;
            }
            $sinistre->save();
                
           $this->flashsuccess('Success');
           return $response->withRedirect($this->router->pathFor('sinistres'));
    }
        

    /**
     * delete the sinistre
     * delete all the sinistre mission
     * delte the folder that contain the imgs
     * @author TakiDDine
     */
    public function delete($request,$response,$args) {
        
        // delete the sinistre
        $sinistre = Sinistre::find($args['id']);
        $sinistre_id = $sinistre->id;
        if($sinistre){
            $sinistre->delete();
        }

        // delete all siniste mission
        $missions = Mission::where('sinitre_id',$sinistre_id)->get();
        foreach ($missions as $mission) {
          $mission->delete();
        }
       

        // delete the folder of the sinistre
        $dir        = $this->dir('sinistres').'sinistre_'.$sinistre_id;
        array_map( 'unlink', array_filter((array) glob($dir."/*") ) ); 
        $this->flashsuccess('Success');
        return $response->withRedirect($this->router->pathFor('sinistres'));
    }
    

   
}