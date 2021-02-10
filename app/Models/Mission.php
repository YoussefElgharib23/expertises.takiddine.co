<?php

namespace App\Models;
use illuminate\database\eloquent\model;

class Mission extends model{
    
    protected $table = 'mission';
    
    protected $guarded = ['id', 'created_at', 'updated_at'];
    
    
    public function sinistre(){
        return $this->belongsTo('App\Models\Sinistre','sinitre_id');
    }
    
    public function colaborator(){
        return $this->belongsTo('App\Models\User','colaborator_id');
    }

    public function assistant(){
        return $this->belongsTo('App\Models\User','assistant_id');
    }
   

    public function imgs(){
        if(!empty($this->gallery)){
                return json_decode($this->gallery);    
        }
        return [];        
    }

    public function imgs_url(){
      return  'sinistre_'.$this->sinitre_id.'/'.$this->etape.'/';
    }

}