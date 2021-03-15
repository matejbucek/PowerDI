<?php
namespace SimpleFW\Security;

interface UserDataBinder
{
    public function getUser(): ?Principal;
}

