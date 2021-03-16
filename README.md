# SimpleFW
This is really simple PHP 8 framework. Thats where the name comes from.

---

### Simple example

You can find a simple example of use [here](https://github.com/matejbucek/SimpleApp).

---

### About

This framework supports: 
1. Dependency Injection
2. MVC with Latte
3. Firewall
4. Simple DB Connector

---

### Functionalities

1. Dependency Injection

We make use of PHP 8 Attributes, so that is one way to register Service or Controller.

```php
#[Service("MyService")]
class MyService{
  ...
}

#[Controller("MyController")]
class MyController extends AbstractController {
  ...
}

```

You can use the `dependency.yaml` file to define Services and Parameters. See more in WIKI.

Note: It is not possible to register Controller using this file yet.

You can also use Attribute to inject Dependency or Parameter to your Service:

```php
#[Autowired("@MyService")]
private MyService $service;

#[Autowired("%my.property%")]
private string $property;
```

2. MVC with Latte

In your Controller, you can use Attribute `Route` to define a Route mapping.

```php
#[Route("/my/route", methods: ["GET"])]
public function getMyRoute(HttpRequest $request): HttpResponse{
  ...
  return $this->render("template.latte", ["myParam" => "Hello"]);
  //return $this->response("<h1>Hello, world!</h1>");
  //return $this->responseWithJson($myObject);
  //return $this->redirect("/");
}

`AbstractController` automatically resolves path to your template based on Configuration and your specified name.

3. Firewall

Now, you can secure your app using our `Firewall`. You can configure it in `firewall.yaml` file.

The login mechanism works thorugh the `UserDataBinder` and `Principal` classes.

In `firewall.yaml`, you have to specify name of your `UserDataBinder` implementation. You can e. g. use our 'SessionContext' to store the `Principal`.

4. Simple DB Connector

In the `dependency.yaml` file you can specify Parameters and your EntityManager instance:

```yaml
parameters:
  db:
    default:
      dsn: "mysql:host=localhost;dbname=mydb"
      username: "MyDBUser"
      password: "MySecurePassword123?"
services:
  EntityManager:
    class: SimpleFW\Database\EntityManager
    arguments: ["%db.default.dsn%", "%db.default.username%", "%db.default.password%"]
```

The `EntityManager` is just a fancy name for simple Wrapper class, that contains the `PDO`.

---
### Authors:
  * Matěj Bucek
---

MIT License

Copyright (c) 2021 Matěj Bucek

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
