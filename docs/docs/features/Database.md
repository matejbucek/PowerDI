# How to use database in PowerDI

## Understanding the databases in PowerDI

The database API in PowerDI is similar to Spring Data JPA.

### Entities

Entities are the classes that represent the tables in the database. They are annotated with the `@Entity` annotation.

```php
<?php

namespace App\Entities;

use PowerDI\Database\Entity;
use PowerDI\Database\SQL\Table;

#[Entity] // This annotation is used to define that this class is an entity
#[Table("users")] // This annotation is used to define the table name
class User implements \JsonSerializable {
    #[ID] // This annotation is used to define the primary key
    private ?int $id;
    private ?string $email;
    private ?string $password;
    private ?string $fullname;

    /**
     * @param ?string $email
     * @param ?string $password
     * @param ?string $fullname
     */
    public function __construct(?string $email = null, ?string $password = null, ?string $fullname = null) {
        $this->id = null;
        $this->email = $email;
        $this->password = $password;
        $this->fullname = $fullname;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function getFullname(): string {
        return $this->fullname;
    }

    /**
     * @param string|null $password
     */
    public function setPassword(?string $password): void {
        $this->password = $password;
    }
}
``` 

### Repositories

Repositories are the classes that interact with the database. They are annotated with the `@Repository` annotation.

Note that there are multiple types of repositories in PowerDI, the most common one is the `SqlRepository`.

```php
<?php

namespace App\Repositories;

use App\Entities\User;
use PowerDI\Database\DatabaseException;
use PowerDI\Database\Repository;
use PowerDI\Database\SQL\Query\SQLQueryBuilder;
use PowerDI\Database\SQL\Query\WhereOperators;
use PowerDI\Database\SQL\SqlRepository;

#[Repository(User::class)] // This annotation is used to define that this class is a repository and the entity it manages
class UserRepository extends SqlRepository {
    public function getUsers(): array {
        return $this->findAll();
    }

    /**
     * @throws DatabaseException
     */
    public function getUserByEmail(string $email): ?User {
        $query = (new SQLQueryBuilder( $this->class))->select(["*"])->where("email", WhereOperators::Equal, $email)->build();
        return $this->get($query->query, $query->arguments);
    }

    public function getUserById(int $id): ?User {
        return $this->find($id);
    }
}
```

## Using the database

### Connecting to the database

To connect to the database, you need to define the `Connector` (specific to the database you are using) and the
`EntityManager`.

The `Connector` is used to connect to the database and execute queries. The `EntityManager` is used to manage the
entities and repositories.

```yaml
parameters: # The injectable properties
  db:
    default:
      dsn: "mysql:host={DB_HOST};dbname={DB_NAME}" # {VARIABLE} will be replaced by the environment variable specified in the .env file
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

### Using the repositories

To use the repositories, you need to inject them into the classes where you want to use them.

```php
<?php

namespace App\Services;

use App\Entities\User;
use App\Repositories\UserRepository;
use PowerDI\Core\Autowired;

class UserService {
    #[Autowired("@UserRepository")] // This annotation is used to inject the UserRepository
    private UserRepository $userRepository;

    public function getUserByEmail(string $email): ?User {
        return $this->userRepository->getUserByEmail($email);
    }
}
```