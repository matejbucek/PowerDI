<?php

namespace SimpleFW\Database\SQL\Query;

enum QueryType {
    case SELECT;
    case INSERT;
    case UPDATE;
    case DELETE;

    public function toString(): string {
        return match ($this) {
            QueryType::SELECT => "SELECT",
            QueryType::INSERT => "INSERT",
            QueryType::UPDATE => "UPDATE",
            QueryType::DELETE => "DELETE"
        };
    }
}
