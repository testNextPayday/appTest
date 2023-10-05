<?php
namespace App\Traits;

trait staticCallable

{

    public static function __callStatic($name, $arguments)
   {
     
     
     $class = get_called_class();
     $instance =  new $class;
      if(! method_exists($instance,$name)){
        throw new \Exception("Method $name not found on class $instance");
      }
      
      return $instance->$name(...$arguments);
   }

   
}