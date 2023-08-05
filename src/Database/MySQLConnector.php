<?php
namespace SimpleFW\Database;

class MySQLConnector implements TransactionalConnector {
    private \PDO $pdo;
    private string $dsn;
    private string $username;
    private string $password;
    
    public function __construct(string $dsn, string $username, string $password){
        $this->dsn = $dsn;
        $this->usernam = $username;
        $this->password = $password;
        $this->pdo = new \PDO($this->dsn, $username, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
    
    public function __desctruct(){
        $this->pdo = NULL;
    }
    
    public function getPDO(): \PDO{
        return $this->pdo;
    }
    
    public function query($stmt, $option = NULL): \PDOStatement{
        return $this->pdo->query($stmt, $option);
    }
    
    public function prepare($stmt): \PDOStatement{
        return $this->pdo->prepare($stmt);
    }
    
    public function begin(){
        return $this->pdo->beginTransaction();
    }
    
    public function commit(){
        return $this->pdo->commit();
    }
    
    public function rollBack(){
        return $this->pdo->rollBack();
    }

    public function getType(): RepositoryType {
        return RepositoryType::MySQL;
    }
}

