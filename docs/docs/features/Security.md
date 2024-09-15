# How to secure your application?

The PowerDI framework provides a simple way to secure your application using the firewall configuration.

## The internal user representation

The user is represented by the `PowerDI\Security\Principal` class, which contains the user's id, roles and other user specific data.

## How to bind the user?

The binding is a process of binding a request to a user. The binding is done by a service that implements the `PowerDI\Security\UserDataBinder` interface.

## A simple session-based user binder

This binder is actually a part of the framework itself, but it might be an inspiration for your own implementation.

You might also want to take a look at the `PowerDI\Security\JwtUserBinder` class.

```php
<?php
namespace App\Services;

use PowerDI\Core\Autowired;
use PowerDI\Core\Service;
use PowerDI\Core\SessionContext;
use PowerDI\Security\Principal;
use PowerDI\Security\UserDataBinder;

#[Service]
class SessionUserBinder implements UserDataBinder {

    #[Autowired("@SessionContext")]
    private SessionContext $sessionContext;
    public function getUser(): ?Principal {
        if($this->sessionContext->get("USER_ID") !== null) {
            return unserialize($this->sessionContext->get("USER_PRINCIPAL"));
        }

        return null;
    }

    public function setUser(Principal $user) {
        $this->sessionContext->put("USER_ID", serialize($user));
        $this->sessionContext->put("USER_PRINCIPAL", serialize($user));
    }

    public function destroy() {
        $this->sessionContext->destroy();
    }
}
```

### Using the binder

You can use the binder in any of your services by injecting it.

```php
<?php

namespace App\Services;

use PowerDI\Core\Autowired;
use PowerDI\Core\Service;
use PowerDI\Security\Principal;
use App\Services\MySessionUserBinder;

#[Service]
class MyAuthService {

    #[Autowired("@MySessionUserBinder")]
    private MySessionUserBinder $userBinder;

    public function login($username, $password) {
        $this->userBinder->setUser(new Principal($username, ["admin"]));
    }

    public function logout() {
        $this->userBinder->destroy();
    }
}
```

## Firewall Configuration

The firewall configuration file is located in `config/firewall.yaml`.

```yaml
firewall:
  status: on
  user:
    binder: "@SessionUserBinder"
  routes:
    admin:
      path: /admin/**
      hasRole: "admin"
    internal:
      path: /internal/**
      hasAnyRole: [ "admin", "user" ]
    methodGET:
      path: /user
      method: GET
      permitAll: true
    methodPOST:
      path: /user
      method: POST
      authenticated: true
    default:
      default: true
      permitAll: true
```