<?php
namespace SimpleFW\Templates;
use Latte\Engine;

class LatteTemplater extends AbstractTemplater
{
    private $latte;
    protected function init($temp)
    {
        $this->latte = new Engine();
        $this->latte->setTempDirectory($temp);
    }
    public function renderToString($templatePath, $params)
    {
        return $this->latte->renderToString($templatePath, $params);
    }
}

