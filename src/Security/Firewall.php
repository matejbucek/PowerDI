<?php

namespace PowerDI\Security;

use PowerDI\HttpBasics\Exceptions\AccessForbiddenException;
use PowerDI\HttpBasics\HttpRequest;
use PowerDI\Security\Exceptions\UserNotLoggedInException;

class Firewall {

    private UserDataBinder $binder;

    private $config;

    public function __construct(UserDataBinder $binder, $config) {
        $this->binder = $binder;
        $this->config = $config;
    }

    public function canAccess($request): bool {
        $user = $this->binder->getUser();
        return $this->resolve($request, $user);
    }

    private function resolve(HttpRequest $request, ?Principal $user) {
        $default = NULL;
        foreach ($this->config["firewall"]["routes"] as $entry) {
            if (isset($entry["default"]) && $entry["default"]) {
                $default = $entry;
            }

            if (isset($entry['path'])) {
                if ($this->belongs($request->getPath(), $entry["path"])) {
                    return $this->entryCheck($entry, $user, $request);
                }
            }
        }

        if ($default == NULL) {
            throw new AccessForbiddenException();
        }

        return $this->entryCheck($default, $user, $request);
    }

    private function entryCheck($entry, $user, $request) {
        if (isset($entry["methods"])) {
            if (!in_array($request->getMethod(), $entry["methods"]))
                throw new AccessForbiddenException();
        }

        if (isset($entry["permitAll"]) && $entry["permitAll"] == TRUE) {
            return TRUE;
        }

        if (isset($entry["authenticated"]) && $entry["authenticated"] == TRUE) {
            if ($user == NULL) {
                throw new UserNotLoggedInException();
            }
        }

        if (isset($entry["hasRole"])) {
            if ($user == NULL) {
                throw new UserNotLoggedInException();
            }
            foreach ($entry["hasRole"] as $role) {
                if (!in_array($role, $user->getRoles())) {
                    throw new AccessForbiddenException();
                }
            }
        }

        if (isset($entry["hasAnyRole"])) {
            if ($user == NULL) {
                throw new UserNotLoggedInException();
            }
            if (count(array_intersect($entry["hasAnyRole"], $user->getRoles())) <= 0) {
                throw new AccessForbiddenException();
            }
        }

        return TRUE;
    }

    private function belongs($path, $matcher): bool {
        if ($path == $matcher) {
            return TRUE;
        }

        if (preg_match("/.*\/\*\*/", $matcher)) {
            $pathChars = str_split($path);
            $matcherChars = str_split($matcher);
            for ($i = 0; $i < count($pathChars); $i++) {
                if ($matcherChars[$i] == '*') {
                    return TRUE;
                }
                if ($pathChars[$i] !== $matcherChars[$i]) {
                    return FALSE;
                }
            }

            return TRUE;
        }

        return FALSE;
    }
}

