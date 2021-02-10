<?php

namespace App\Models;
use illuminate\database\eloquent\model;

class Notification extends model{
    
    protected $table = 'notifications';
    
    protected $guarded = ['id', 'created_at', 'updated_at'];
    
    
   public function recevoir(){
            return $this->belongsTo('App\Models\User','user_id');
   }

   
}