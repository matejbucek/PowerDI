<?php
namespace PowerDI\Security;

interface UserDataBinder {
    public function getUser(): ?Principal;
}

