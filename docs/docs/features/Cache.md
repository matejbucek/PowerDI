# How to use the Cache feature

The Cache feature is a powerful tool that allows you to store the rendered output of a page in a in-memory cache.
This can be useful for pages that are expensive to render, or for pages that are accessed frequently.

As of now, the Cache feature only supports Memcached.

## Enabling the Cache feature

Define the CacheService in your `dependency.yaml` file:

```yaml
properties:
  cache:
    servers:
      - host: "{CACHE_HOST}"
        port: "{CACHE_PORT}"
services:
  CacheService:
    class: PowerDI\Cache\MemcachedService
    arguments: [ "%cache%" ]
```

Then tell PowerDI to use the Cache feature in your `config.yaml` file:

```yaml
app:
  cache:
    service: "CacheService" # The name of the CacheService defined in dependency.yaml
```

## Using the Cache feature

To cache a specific route, add the `cacheConfig` property to the `#Route` annotation of the route you want to cache:

```php
<?php

namespace App\Controllers;

use PowerDI\Core\Autowired;
use PowerDI\Core\CacheType;
use PowerDI\Core\Controller;
use PowerDI\Core\Route;

#[Controller]
class MainController extends AbstractController {
    #[Route(path: "/", cacheConfig: ["type" => CacheType::ManuallyCacheable, "ttl" => 60, "key" => "home_page"])]
    public function index(): HttpResponse {
        return $this->render("home.latte");
    }

    #[Route(path: "/contacts", cacheConfig: ["type" => CacheType::AutoCacheable, "ttl" => 60])]
    public function contacts(): HttpResponse {
        return $this->render("contacts.latte");
    }

    #[Route(path: "/uncacheable", cacheConfig: ["type" => CacheType::Uncacheable])]
    public function uncacheable(): HttpResponse {
        return $this->render("uncacheable.latte");
    }
}
```

### CacheType

There are three types of cache:

- AutoCacheable: The page will be cached without a need to configure anything else.
- ManuallyCacheable: The page will be cached, but you need to specify the cache key, which you can then use to
  invalidate or interact with the cache.
- Uncacheable: The page will not be cached.