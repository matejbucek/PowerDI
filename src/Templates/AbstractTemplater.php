<?php
namespace PowerDI\Templates;

abstract class AbstractTemplater
{
    private array $templatePath;
    
    public function __construct(array $templatePath){
        $this->templatePath = $templatePath;
        $this->init("");
    }
    
    protected abstract function init($temp);
    public abstract function renderToString($templatePath, $params);
}

