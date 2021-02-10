<?php

namespace App\Helpers;
use App\Models\User;

class Auth {
        
    private $user = false;
    
    
    public static function User(){
        return $this->user; 
    }
    
    public function getUser(){
        return $this->user;
    }

  
    public static function logout() {
        $_SESSION['user'] = null;
        session_regenerate_id();
        return true;
    }
    
    
    public function setUser($id){
       return $this->user = User::find($id); 
    }

    public function attempt($email,$password) {
                
        $user = User::whereEmail($email)->orWhere('username','=',$email)->first();
        
        if($user) {
            if(password_verify($password,$user->password)) {
                    session_start();
                    $_SESSION['auth'] = $user->id;
                    $_SESSION['role'] = $user->role;
                
                    return $user;
            } 
        }
        return false;
    }
    
    

    
    
    
    
}