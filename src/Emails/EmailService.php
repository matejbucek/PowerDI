<?php

namespace PowerDI\Emails;

interface EmailService {
    public function send(Email $email);

}