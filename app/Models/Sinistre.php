<?php

namespace App\Models;
use illuminate\database\eloquent\model;

class Sinistre extends model{

        protected $table = 'sinistre';

        protected $guarded = ['id', 'created_at', 'updated_at'];

        public function colaborator(){
            return $this->belongsTo('App\Models\User','colaborator_id');
        }

        public function assistant(){
            return $this->belongsTo('App\Models\User','assistant_id');
        }

        public function avant(){
            return $this->belongsTo('App\Models\Mission','avant_id');
        }

        public function apres(){
            return $this->belongsTo('App\Models\Mission','apres_id');
        }

        public function encours(){
            return $this->belongsTo('App\Models\Mission','encoure_id');
        }

}