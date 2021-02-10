<?php

namespace App\Models;
use illuminate\database\eloquent\model;

class Payment extends model{
    
    protected $table = 'payments';
    
    protected $guarded = ['id', 'created_at', 'updated_at'];
    
    public function sinistre(){
        return $this->belongsTo('App\Models\Sinistre','	sinistre_id');
    }


    public function colaborator(){
        return $this->belongsTo('App\Models\User','user_id');
    }
   
}