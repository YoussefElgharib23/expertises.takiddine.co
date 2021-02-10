<?php




namespace App\Models;
use illuminate\database\eloquent\model;


class User extends model{
    
    


   
    protected $admin_role = 2;
    protected $table = 'users';
    
    protected $guarded = ['id', 'created_at', 'updated_at'];
    
    /* *************
     * Display User Role 
     * *************
    */
    public function role(){
        
        $role = User::where('username',$this->username)->first();
        
        if($role){
            if($role->role == 1 ) {
                echo '<span class="label label-primary">'.$this->lang['panelang']['1'].'</span>';
            }
            if($role->role == 2 ) {
                echo '<span class="label label label-success">'.$this->lang['panelang']['2'].'</span>';
            }
            if($role->role == 3 ) {
                echo '<span class="label label-primary">'.$this->lang['panelang']['3'].'</span>';
            } 
        }
        echo  " ";

    }
    
    /* *************
     * Display User Statue 
     * *************
    */  
    public function statue(){
        $statue = self::where('username',$this->username)->first();
        
        if($statue){
            if($statue->statue == 1 ) {
                echo '<span class="label border-left-success label-striped">'.$this->lang['panelang']['4'].'</span>';
            }
            if($statue->statue == 2 ) {
                echo '<span class="label border-left-primary label-striped">'.$this->lang['panelang']['5'].'</span>';
            }
            if($statue->statue == 3 ) {
                echo '<span class="label border-left-danger label-striped">'.$this->lang['panelang']['6'].'</span>';
            }   
        }
         echo  " ";
    }
    
    
    public function gender(){
        if($this->gender == 'male') {
            return $this->lang['panelang']['7'];
        }else{
            return $this->lang['panelang']['8'];
        }
    }
    
    
    
    public function is_admin(){
        
        if($this->role == $this->admin_role){
            return true;
        }
        
        return false;
    }
}