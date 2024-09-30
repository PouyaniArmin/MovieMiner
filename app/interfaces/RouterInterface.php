<?php 

namespace App\Interfaces;

interface RouterInterface{
    public function get(string $path,$callback):void;
    public function post(string $path,$callback):void;
    // public function put(string $path,$callback):void;
    // public function patch(string $path,$callback):void;
    // public function delete(string $path,$callback):void;
}