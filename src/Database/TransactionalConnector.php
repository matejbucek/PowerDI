<?php

namespace PowerDI\Database;

interface TransactionalConnector extends Connector {

    public function begin();
    public function commit();
    public function rollBack();
}