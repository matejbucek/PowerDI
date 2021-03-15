<?php
namespace SimpleFW\HttpBasics;

class HttpResponse
{

    private $data;
    private $headers;
    private $status;
    private $redirect;
    
    public function __construct($data = null){
        $this->data = $data;
    }
    
    public function setRedirect($url){
        $this->redirect = $url;
    }
    
    public function send(){
        if (isset($this->redirect)) {
            header("Location: $this->redirect");
            return;
        }
        echo $this->data;
    }
}

