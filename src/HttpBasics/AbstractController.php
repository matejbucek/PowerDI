<?php
namespace PowerDI\HttpBasics;
use PowerDI\Core\Autowired;
use PowerDI\Loaders\PathResolver;
use PowerDI\Templates\AbstractTemplater;

abstract class AbstractController{
    private $templater;
    private $pathResolver;
    #[Autowired("@AbstractTemplater")]
    protected $templ;
    public function __construct(AbstractTemplater $templater, PathResolver $pathResolver){
        $this->templater = $templater;
        $this->pathResolver = $pathResolver;
    }
    
    protected function render($template, array $params = []){
        return new HttpResponse($this->templater->renderToString($this->pathResolver->resolveTemplate($template), $params));
    }
    
    protected function response($data){
        return new HttpResponse($data);
    }
    
    protected function responseWithJson(mixed $object, int $status = 200): HttpResponse{
        http_response_code($status);
        return new HttpResponse(json_encode($object));
    }
    
    protected function redirect($url){
        $response = new HttpResponse();
        $response->setRedirect($url);
        return $response;
    }
}