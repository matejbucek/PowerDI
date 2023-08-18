<?php
namespace SimpleFW\Security;

use SimpleFW\HttpBasics\HttpRequest;

interface UserDataBinder {
    public function getUser(HttpRequest $request): ?Principal;
}

