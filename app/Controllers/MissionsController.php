<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Models\{Mission, User , Sinistre };


defined('BASEPATH') OR exit('No direct script access allowed');

class MissionsController extends Controller {

    public $folder  = 'missions' ;
    public $model   = 'Mission';
    public $route   = [ 'index' => 'missions' ];
    public $control = __CLASS__ ;
    
    public function draftClean ($request,$response) { 
       unset($_SESSION['draft-mission']);
       return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('missions.ListForcolaborator'));
    }


  /**
     * see the mission info
     * @author TakiDDine
     */
    public function view($request,$response,$args) {
       $mission = Mission::find($args['id']);
       $colaborators = User::where('role','colaborator')->get();
       $view = 'admin/missions/view.twig';
       return $this->container->view->render($response,$view,compact('mission','colaborators'));
    } 
    


    /**
     * upload the images of mission and notify the assistante about the uploads
     * @author TakiDDine
     */
    public function upload_gallery($request,$response) {
        
        
        $mission    = Mission::find($_POST['mission_id']);
        
        $sinitre_id = $mission->sinitre_id;
        $etape      = $mission->etape;

        // set the upload dir logic
        $dir        = $this->dir('sinistres').'sinistre_'.$sinitre_id.'/'.$etape.'/';
        
        // set the gallery array and the uploader
        $gallery    = [];
        $uploader   = new \App\Helpers\Upload('start');
        
        $uploaded = $uploader->file($_FILES['file'])->dir($dir)->save();
        $_SESSION['draft-mission']['url']        = $this->url('sinistres').'/sinistre_'.$sinitre_id.'/'.$etape.'/';
        $_SESSION['draft-mission']['mission_id'] = $mission->id;
        $_SESSION['draft-mission']['gallery'][rand(1000,20000)] = $uploaded;
      } 
   
    


    public function listForcolaborator($request,$response) {
      
      if($this->user->role !== 'colaborator') {
            die('Not allowed');
      }
      $colaborators = User::where('role','colaborator')->get();
      $missions =  Mission::where('colaborator_id',$this->user->id)
      ->whereNull('validated_at')
      ->whereNull('gallery')
      ->orderby('created_at','DESC')->get();

      $result =  Mission::where('colaborator_id',$this->user->id)->whereNotNull('resend')->get();

      $missions = $missions->merge($result);



      $draft = @$_SESSION['draft-mission'];
      return $this->container->view->render($response,'admin/missions/colaborator.twig',compact('draft','missions','colaborators'));
    } 

    



    /**
     * List the missions
     * @author TakiDDine
     */
    public function index($request,$response) {

        $type         = $request->getParam('type');
        $roles        = $this->roles();
        $colaborators = $roles['colaborators'];
        $assistante   = $roles['assistante'];
        $orderBy      = 'uploaded_at';


        $missions     = Mission::query();

        if(isset($type) and !empty($type) and (in_array($type, ['attente','reviser','validee']))){

            if($type =='attente'){
              $missions->whereNull('gallery');
            }

            if($type =='reviser'){
              $missions->whereNotNull('gallery')->whereNull('validated_at');
              $orderBy = 'uploaded_at';
            }

            if($type =='validee'){
              $missions->whereNotNull('validated_at');
              $orderBy = 'validated_at';
            }                      
        }

        if(isset($_GET['colaborateur']) and is_numeric($_GET['colaborateur'])){
          $missions = $missions->where('colaborator_id',$_GET['colaborateur']);
        }

        if(isset($_GET['assisstante']) and is_numeric($_GET['assisstante'])){
          $missions = $missions->where('assistant_id',$_GET['assisstante']);
        }


        // Pagination settings
        $count          = $missions->count();         
        $page           = ($request->getParam('page', 0) > 0) ? $request->getParam('page') : 1;
        $limit          = 10; 
        $lastpage       = (ceil($count / $limit) == 0 ? 1 : ceil($count / $limit));   
        $skip           = ($page - 1) * $limit;

        // get the result
        $missions       = $missions->skip($skip)->take($limit)->orderBy($orderBy, 'desc');

        $missions = $missions->get();

        $url = $request->getParams();
        unset($url['page']);
        
        $URLparams  = http_build_query($url);
        
        $urlPattern =  !empty($URLparams) ? '?'.$URLparams. "&page=(:num)" : "?page=(:num)";
        
        $pagination = new \App\Helpers\Paginator($count, $limit, $page, $urlPattern);
        
        $view       = 'admin/missions/index.twig';
        return $this->container->view->render($response,$view,compact('count','colaborators','assistante','missions','pagination'));
    } 


    public function downloadFromURLToUSerCompuer ($url) { 
        $file = ($url);
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"".$file_name."\""); 
        readfile($file);
        exit;
    }







    public function delete($request,$response,$args) {
        

      $mission = $this->init()::find(rtrim($args['id'], '/'));
      $sinitre_id = $mission->sinitre_id;

       $sinistre = Sinistre::find($sinitre_id);

       if($mission->etape == 'apres') {
          $sinistre->apres_id = NULL;
        }

        if($mission->etape == 'avant') {
          $sinistre->avant_id = NULL;
        }

        if($mission->etape == 'encoure') {
          $sinistre->encoure_id = NULL;
        }

      $sinistre->save();

      $mission->delete();
      return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('missions'));
    }    
   



    public function deleteZip($request,$response,$args) {

            $mission    = Mission::find($args['id']);
            $sinitre_id = $mission->sinitre_id;
            $etape      = $mission->etape;

            // get the directory where images exists
            $dir        = $this->dir('sinistres').'sinistre_'.$sinitre_id.'/'.$etape.'/';
            $zip        = $this->dir('zip').'mission_'.$args['id'].'.zip';

            if(file_exists($zip)){
              unlink($zip);
            }
            $mission->zip = NULL;
            $mission->save();
            return $response->withRedirect($this->router->pathFor('missions'));

    }





    public function download($request,$response,$args) {

            $mission    = Mission::find($args['id']);
            $sinitre_id = $mission->sinitre_id;
            $etape      = $mission->etape;

            // get the directory where images exists
            $dir        = $this->dir('sinistres').'sinistre_'.$sinitre_id.'/'.$etape.'/';
            $zip        = $this->dir('zip').'mission_'.$args['id'].'.zip';

            if(!file_exists($zip)){
                $mission->zip = '1';
                $mission->save();

                // zip the files
                Zip($dir,$zip);    
            }
            
          $file = $this->dir('zip').'mission_'.$args['id'].'.zip';

          if(file_exists($file)){
          $fh = fopen($file, 'rb');

          $stream = new \Slim\Http\Stream($fh); 

          return $response->withHeader('Content-Type', 'application/force-download')
                          ->withHeader('Content-Type', 'application/octet-stream')
                          ->withHeader('Content-Type', 'application/download')
                          ->withHeader('Content-Description', 'File Transfer')
                          ->withHeader('Content-Transfer-Encoding', 'binary')
                          ->withHeader('Content-Disposition', 'attachment; filename="' . basename($file) . '"')
                          ->withHeader('Expires', '0')
                          ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                          ->withHeader('Pragma', 'public')
                          ->withBody($stream); 
          }
          


          
    }





    /**
     * create new mission with the etape given
     * notify the colaborator
     */
    public function send($request,$response) {
          $post = cleanify($request->getParams());
        
           // create the mission with statue avant + validated at = null
          $mission = [
            'sinitre_id'     =>  $post['sinistre_id'], 
            'colaborator_id' =>  $post['colaborator_id'] ,  
            'etape'          =>  $post['etape'], 
            'assistant_id'   =>  $this->user->id
          ];
          $mission = \App\Models\Mission::create($mission);   

           // Send the Notifications To colaborator
            $notification = [
              'content'    => 'Nouveau Sinitre de l\'etape ' . $post['etape'] .' vous attend a envoyer les images' , 
              'user_id'    =>  $post['colaborator_id'],  
              'sinitre_id' => $post['sinistre_id'] , 
              'mission_id' =>  $mission->id 
            ];
            \App\Models\Notification::create($notification);


            // save etape in sinistre
            $sinistre = Sinistre::find( $post['sinistre_id'] );
            if($post['etape'] == 'apres') {
              $sinistre->apres_id = $mission->id;
            }

            if($post['etape'] == 'avant') {
              $sinistre->avant_id = $mission->id;
            }

            if($post['etape'] == 'encoure') {
              $sinistre->encoure_id = $mission->id;
            }

            $sinistre->save();


            return $response->withRedirect($this->router->pathFor('sinistres'));
    } 


      public function resend($request,$response) {
          
          $post = cleanify($request->getParams());
        
          $mission = Mission::find($post['mission_id']);
        
          $mission->resend = 'true';
          $mission->colaborator_id = $post['colaborator_id'];
          $mission->save();
          
          return $response->withRedirect($this->router->pathFor('sinistres'));
    } 
    

    



    public function multiple_file_upload($my_files){
       $files = array();
        foreach ($my_files as $k => $l) {
         foreach ($l as $i => $v) {
         if (!array_key_exists($i, $files))
           $files[$i] = array();
           $files[$i][$k] = $v;
         }
        } 
        return $files;
    }


    /**
     *  1 - get the files and save them in the database
     *  2 - nofify the assistante
     * @author TakiDDine
     */
    public function confirm($request,$response){

          $mission    = Mission::find($_POST['id']);
          $sinitre_id = $mission->sinitre_id;
          $etape      = $mission->etape;

          // get the directory where images exists
          $dir        = $this->dir('sinistres').'sinistre_'.$sinitre_id.'/'.$etape.'/';
          $files = scandir($dir);
          $files = array_diff(scandir($dir), array('.', '..'));

          // save the files in database
          $mission->gallery = json_encode(array_values($files));
          $mission->uploaded_at = \Carbon\Carbon::now();
          $mission->resend = NULL;
          $mission->save();
            
          // get the assistante to notify
          $assistante =  Sinistre::find($sinitre_id)->assistant_id;

          // notify the assistante about the uploads
          $notification = [
              'content'    => 'le collaborateur a pris les images , merci de confirmer' , 
              'user_id'    => $assistante,  
              'sinitre_id' => $sinitre_id , 
              'mission_id' => $mission->id 
          ];
          \App\Models\Notification::create($notification);

          $_SESSION['draft-mission'] = [];
          unset($_SESSION['draft-mission']);
          
    }


    /**
     * Reject the Mission Images 
     * Delte All the images from database and in files
     * return to missions page
     * this mission will apear again to the collaborator to upload the images again
     * @author TakiDDine
     */
    public function reject($request,$response,$args) {
        
        // remove the gallery from database
        $mission          = Mission::find($args['id']);
        $mission->gallery = NULL;

        $mission->save();
        
        // remove the real images 
        $sinitre_id       =  $mission->sinitre_id;

        // reject la mission 
        $siniste = Sinistre::find($sinitre_id);
        if($mission->etape == 'encoure') {
          $siniste->encoure_id = NULL;
        }
        if($mission->etape == 'apres') {
          $siniste->apres_id = NULL;
        }
        if($mission->etape == 'avant') {
         $siniste->avant_id = NULL; 
        }
        $siniste->save();

        $etape            =  $mission->etape;
        $dir              = $this->dir('sinistres').'sinistre_'.$sinitre_id.'/'.$etape.'/';
        array_map( 'unlink', array_filter((array) glob($dir."/*") ) );

        // Send the Notifications To colaborator
        $notification = [
          'content'    => 'La mission a ete rejeter' , 
          'user_id'    => $mission->colaborator_id,  
          'sinitre_id' => $mission->sinitre_id , 
          'mission_id' => $mission->id 
        ];
        \App\Models\Notification::create($notification);

        // return to missions page
        return $response->withRedirect($this->router->pathFor('missions'));
    } 
    
  
    /**
     * Validate the mission
     * @author TakiDDine
     */
    public function validate($request,$response,$args) {
       $mission               = Mission::find($args['id']);
       $mission->validated_at = \Carbon\Carbon::Now();
       $mission->save();

      // Send the Notifications To colaborator
      $notification = [
        'content'    => 'La mission a ete valide' , 
        'user_id'    => $mission->colaborator_id,  
        'sinitre_id' => $mission->sinitre_id , 
        'mission_id' => $mission->id 
      ];
      \App\Models\Notification::create($notification);


     if($mission->etape == 'apres'){
        
            $sinitre = Sinistre::find($mission->sinitre_id);
            $sinitre->completed_at = \Carbon\Carbon::now();
            $sinitre->save();
      }
       return $response->withRedirect($this->router->pathFor('missions'));
    }   
    


    public function search($request,$response) {

        // get the form content & filter
        $post        = cleanify($request->getParams());
        $search      = $post['q'];
        $colaborator = isset($post['colaborator']) ?? false;
        $assistant   = isset($post['assistant']) ?? false;
        $date        = @$post['date'];
        $company     = @$post['company'];

        $model = $this->init('Sinistre');
       

        // get by search result
        if(!empty(($search))) {

         
        $result = \App\Models\Sinistre::
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


        if(!empty($colaborator) and $colaborator != 1 ){
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
        return $this->container->view->render($response,'admin/sinitre/colaborator_search.twig',compact('colaborators','sinsitres','count'));

    }
      


}

