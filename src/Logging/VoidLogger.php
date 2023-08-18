<?php

namespace SimpleFW\Logging;

class VoidLogger implements Logger {

    public function emergency(string $text) {
    }

    public function alert(string $text) {
    }

    public function critical(string $text) {
    }

    public function error(string $text) {
    }

    public function warning(string $text) {
    }

    public function notice(string $text) {
    }

    public function info(string $text) {
    }

    public function debug(string $text) {
    }
}