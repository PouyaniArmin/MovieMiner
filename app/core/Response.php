<?php 

namespace App\Core;

class Response{
    public function notFoundResponse(){
        http_response_code(404);
    }
}