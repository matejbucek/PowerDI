<?php
namespace SimpleFW\Containers;

class SessionContext implements Context
{
    
    public function __construct(){
        session_start();
    }

    public function get($key)
    {
        if(isset($_SESSION[$key]))
            return $_SESSION[$key];
        else 
            return NULL;
    }

    public function put($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    
    public function destroy(){
        session_destroy();
    }
}

