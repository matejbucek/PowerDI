# How does the dependency injection work?

The dependency injection allows you to define services and parameters, that can be injected into class variables or the constructor.

The dependency injection in PowerDI has a central container that stores the services and parameters. 

## How to define and inject a service and a parameter?

### In the `dependency.yaml` file

```yaml
parameters:
  my_parameter: "value" # The parameter to inject
  
services:
  MyService: # The name of the service
    class: MyNamespace\MyService # The class of the service
    arguments: [ "%my_parameter%" ] # The arguments to pass to the constructor
```

### Using annotations

```php
<?php

namespace MyNamespace;

use PowerDI\Core\Service;
use PowerDI\Core\Autowired;

class MyService {
    public function __construct(private string $parameter) {}
}

#[Service]
class MyAnnotatedService {
    #[Autowired("%my_parameter%")] // To inject a parameter, we use the %% notation
    private string $parameter;
    
    #[Autowired("@MyService")] // To inject a service, we use the @ notation
    private MyService $myService;
}
```