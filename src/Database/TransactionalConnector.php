<?php

namespace SimpleFW\Database;

interface TransactionalConnector extends Connector {

    public function begin();
    public function commit();
    public function rollBack();
}