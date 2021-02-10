<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Models\{User, Sinistre ,Mission ,Payment };


defined('BASEPATH') OR exit('No direct script access allowed');

class StatsController extends Controller {
    



   public function index($request,$response) {
    

       if($request->getParam('from')){
        $from = \Carbon\Carbon::parse($request->getParam('from'));
       }

       if($request->getParam('to')){
        $to = \Carbon\Carbon::parse($request->getParam('to'));
       }



       $colaborators  = $this->roles()['colaborators'];
       $colaborators_stats = [];
       foreach ($colaborators as $colaborator) {
        if(!empty($from) and !empty($to)) {
            $colaborators_stats[] = $this->colaborator($colaborator->username,$colaborator->id,$from,$to);          
        }else {
            $colaborators_stats[] = $this->colaborator($colaborator->username,$colaborator->id);          
        }
       }



       $du = $from ?? '' ;
       $a = $to ?? '';
    
       if(!empty($from) and !empty($to)) {

         $data = [
            'all'          => $this->AllStats($from,$to),
            'compagnies'   => $this->getCompaniesStats($from,$to),
            'colaborators' => $colaborators_stats,
            'du' => $du,
            'a' => $a
          ];
       }else {
         $data = [
            'all'          => $this->AllStats(),
            'compagnies'   => $this->getCompaniesStats(),
            'colaborators' => $colaborators_stats,
            'du' => $du,
            'a' => $a
          ];
       }
      

       

       return $this->container->view->render($response,'admin/pages/stats.twig',$data);
    } 



    public function colaborator($name,$id,$from=false,$to=false){

       
        $Total_Sinistre_Payent    = Sinistre::where('colaborator_id',$id)->whereNotNull('paied_at');
        $Total_Mission            = Mission::where('colaborator_id',$id);
        $Total_Mission_Completes  = Mission::where('colaborator_id',$id)->whereNotNull('validated_at');
        $Total_Mission_en_attente = Mission::where('colaborator_id',$id)->whereNull('gallery');


        if(!empty($from) and !empty($to)){
            $Total_Sinistre_Payent    = $Total_Sinistre_Payent->whereBetween('paied_at', [$from, $to]);
            $Total_Mission            = $Total_Mission->whereBetween('created_at', [$from, $to]);
            $Total_Mission_Completes  = $Total_Mission_Completes->whereBetween('created_at', [$from, $to]);
            $Total_Mission_en_attente = $Total_Mission_en_attente->whereBetween('created_at', [$from, $to]);
        }
        return [
            'name'                     => $name,
            'Total_Sinistre_Payent'    => $Total_Sinistre_Payent->count(),
            'Total_Mission'            => $Total_Mission->count(),
            'Total_Mission_Completes'  => $Total_Mission_Completes->count(),
            'Total_Mission_en_attente' => $Total_Mission_en_attente->count(),
        ];
    }



    public static function AllStats($from=false,$to=false){
        
        $membres             = User::whereNotNull('id');
        $colaborators        = User::where('role','colaborator');
        $assistantes         = User::where('role','assistante');
        $payment             = Payment::whereNotNull('id');
        $sinistre            = Sinistre::whereNotNull('id');
        $sinistre_payant     = Sinistre::whereNotNull('paied_at');
        $sinistre_non_payant = Sinistre::whereNull('paied_at');
        $sinistre_complete   = Sinistre::whereNotNull('avant_id')->whereNotNull('encoure_id')->whereNotNull('apres_id');
        $mission             = Mission::whereNotNull('id');

        if(!empty($from) and !empty($to)){
          $membres             = $membres->whereBetween('created_at', [$from, $to]);
          $colaborators        = $colaborators->whereBetween('created_at', [$from, $to]);
          $assistantes         = $assistantes->whereBetween('created_at', [$from, $to]);
          $payment             = $payment->whereBetween('created_at', [$from, $to]);
          $sinistre            = $sinistre->whereBetween('created_at', [$from, $to]);
          $sinistre_payant     = $sinistre_payant->whereBetween('created_at', [$from, $to]);
          $sinistre_non_payant = $sinistre_non_payant->whereBetween('created_at', [$from, $to]);
          $sinistre_complete   = $sinistre_complete->whereBetween('created_at', [$from, $to]);
          $mission             = $mission->whereBetween('created_at', [$from, $to]);
        }

        return [
            'membres'             => $membres->count(),
            'colaborators'        => $colaborators->count(),
            'assistantes'         => $assistantes->count(),
            'payment'             => $payment->count(),
            'sinistre'            => $sinistre->count(),
            'sinistre_payant'     => $sinistre_payant->count(),
            'sinistre_non_payant' => $sinistre_non_payant->count(),
            'sinistre_complete'   => $sinistre_complete->count(),
            'mission'             => $mission->count(),
        ];
    }









    public function getCompaniesStats($from=false,$to=false){

        $axa     = Sinistre::where('company','Axa assurance maroc');
        $Allianz = Sinistre::where('company','Allianz');
        $Saham   = Sinistre::where('company','Saham assurance');
        $Sanad   = Sinistre::where('company','Sanad');
        $Mamda   = Sinistre::where('company','Mamda');
        $Mcma    = Sinistre::where('company','Mcma');
        $Matu    = Sinistre::where('company','Matu');
        $Cat     = Sinistre::where('company','Cat');
        $Atlanta = Sinistre::where('company','Atlanta');
        $wafa    = Sinistre::where('company','wafa assurence');     

        if(!empty($from) and !empty($to)){
            $axa     = $axa->whereBetween('paied_at', [$from, $to]);
            $Allianz = $Allianz->whereBetween('created_at', [$from, $to]);
            $Saham   = $Saham->whereBetween('created_at', [$from, $to]);
            $Sanad   = $Sanad->whereBetween('created_at', [$from, $to]);
            $Mamda   = $Mamda->whereBetween('created_at', [$from, $to]);
            $Mcma    = $Mcma->whereBetween('created_at', [$from, $to]);
            $Matu    = $Matu->whereBetween('created_at', [$from, $to]);
            $Matu    = $Matu->whereBetween('created_at', [$from, $to]);
            $Cat     = $Cat->whereBetween('created_at', [$from, $to]);
            $Atlanta = $Atlanta->whereBetween('created_at', [$from, $to]);
            $wafa    = $wafa->whereBetween('created_at', [$from, $to]);
        }

        return [
            'Axa assurance maroc' => $axa->count(),
            'Allianz'             => $Allianz->count(),
            'Saham assurance'     => $Saham->count(),
            'Sanad'               => $Sanad->count(),
            'Mamda'               => $Mamda->count(),
            'Mcma'                => $Mcma->count(),
            'Matu'                => $Matu->count(),
            'Cat'                 => $Cat->count(),
            'Atlanta'             => $Atlanta->count(),
            'wafa assurence'      => $wafa->count(),
        ];
        
    }
    


    
    

 
}

