<?php
namespace SimpleFW\Security;

class Principal
{
    protected string $name;
    protected array $roles;
    
    public function __construct(string $name, array $roles){
        $this->name = $name;
        $this->roles = $roles;
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function getRoles(){
        return $this->roles;
    }
}

