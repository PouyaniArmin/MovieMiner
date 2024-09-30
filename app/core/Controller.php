<?php 
<<<<<<< HEAD

=======
>>>>>>> feature/routing
namespace App\Core;
class Controller extends View{
    public function renderView($view,$params=[]){
        return $this->renderViews($view,$params);
    }
}