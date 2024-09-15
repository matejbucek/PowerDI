# How to define a controller

Controllers are the classes that handle the requests and responses of the application.
They are the entry point of the application and are responsible for processing the requests and returning the responses.

## Routes

The routes are the paths that the application will respond to.

The only way to define a route by now is to use the `#[Route]` annotation.

## Creating a Controller

To create a controller, you need to create a class that extends the `PowerDI\HttpBasics\AbstractController` class.

```php
<?php

namespace App\Controller;

use PowerDI\Core\Controller;
use PowerDI\Core\Route;
use PowerDI\HttpBasics\AbstractController;
use PowerDI\HttpBasics\HttpMethod;
use PowerDI\HttpBasics\HttpResponse;
use PowerDI\HttpBasics\HttpRequest;

#[Controller]
class MyController extends AbstractController {
    #[Route("/", methods: [HttpMethod::GET])]
    public function index(HttpRequest $request) : HttpResponse {
        return $this->render("index.latte", ["title" => "Hello, World!"]);
        //return $this->redirect("/home");
        //return $this->responseWithJson(["message" => "Hello, World!"]);
        //throw new \Exception("An error occurred");
    }
}
```

The array passed to the `render` method is the data that will be passed to the template.

## Error Handling

There is a global exception handler that will catch all exceptions thrown by the application.

When an exception is thrown, the application will call the `handleException` method defined in the ErrorController, that
has to be configured in the `config.yaml` file.