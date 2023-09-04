<?php
namespace PowerDI\Security;

use PowerDI\HttpBasics\HttpRequest;

interface UserDataBinder {
    public function getUser(): ?Principal;
}

