# Getting Started

This is a guide to help you get started with PowerDI, implementing your first project.

## Installation

## Create a new project

```bash
powerdi new project my-project
```

This command will create a new project with the name `my-project`,
it will contain the basic structure of a PowerDI project.

## Project Structure

```
my-project
├── src
│   ├── Controllers
│   │   ├── MainController.php
│   │   └── ErrorController.php
│   ├── Services
│   │   └── MainService.php
│   └── Kernel.php
├── config
│   ├── .env
│   ├── config.yaml // Main configuration file
│   ├── dependency.yaml // Dependency injection configuration
│   ├── firewall.yaml // Firewall configuration
│   └── routes.yaml // Routes configuration
├── public
│   ├── index.php
│   ├── .htaccess
│   └── assets
├── templates
│   └── index.latte
├── composer.json
└── .gitignore
```

## Configuration

### Main Configuration

The main configuration file is located in `config/config.yaml`, it contains the main configuration of the project.

```yaml
app:
  name: My Project
  version: 1.0.0
  debug: true
  timezone: UTC
  locale: en
  lookup:
    prefix: src/
    paths: # Look to note 1.
      - Controllers
      - Services
      - Repositories
      - Entities
    templates:
      path: templates/
      temp: tmp/
  errors:
    name: "ErrorController"
    method: handleError
  cache:
    service: "CacheService"
```

__Note 1:__ The `paths` key is used to define the directories where the `Kernel` will look for the classes.
The correct setup is necessary for DI to work properly. Classes with the DI annotations outside these directories will
not be found.

### Dependency Injection Configuration

The dependency injection configuration file is located in `config/dependency.yaml`, it contains the configuration of the
services and prope

```yaml
parameters: # The injectable properties
  db:
    default:
      dsn: "mysql:host={DB_HOST};dbname={DB_NAME}"
      user: "{DB_USER}"
      password: "{DB_PASS}"
services:
  MySQLConnector: # Name of the service
    class: PowerDI\Database\MySQLConnector
    arguments: [ "%db.default.dsn%", "%db.default.username%", "%db.default.password%" ]
  EntityManager:
    class: PowerDI\Database\EntityManager
    arguments: [ "@MySQLConnector" ]
    instantiate: "always" # Instantiate the service always
```

### Firewall Configuration

The firewall configuration file is located in `config/firewall.yaml`, it contains the configuration of the security of
the project.

```yaml
firewall:
  status: on
  user:
    binder: "@MyUserBinder" # The service that will bind the user
  routes:
    admin:
      path: /admin/**
      authenticated: true
    default:
      default: true
      permitAll: true
```

### Routes Configuration

The routes configuration file is located in `config/routes.yaml`, it contains the configuration of the routes of the
project.

The route configuration is not yet implemented, but it will be soon.

## Running the project

### Development

You can either use the PHP built-in server, or use a local server like Apache or Nginx.

```bash
php -S localhost:8000 -t public
```

### Production

Make sure to configure your server to point to the `public` directory.

The `.htaccess` file is already configured to redirect all requests to the `index.php` file.

## What's next?

- [Controllers](features/Controllers.md)
- [Injection](features/Injection.md)
- [Database](features/Database.md)
- [Security](features/Security.md)
- [Cache](features/Cache.md)
- [Help us improve](development/index.md)
