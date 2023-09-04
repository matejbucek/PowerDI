<?php
namespace PowerDI\Templates;

abstract class AbstractTemplater
{
    private $templatePath;
    
    public function __construct($templatePath){
        $this->templatePath = $templatePath;
        $this->init("");
    }
    
    protected abstract function init($temp);
    public abstract function renderToString($templatePath, $params);
}

