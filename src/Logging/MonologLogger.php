<?php

namespace SimpleFW\Logging;

use Monolog\Handler\StreamHandler;
use Monolog\Level;

class MonologLogger implements Logger {
    private \Monolog\Logger $logger;

    public function __construct(string $name, string $file, string $level) {
        $this->logger = new \Monolog\Logger($name);
        $this->logger->pushHandler(new StreamHandler($file, $level));
    }

    public function emergency(string $text): void {
        $this->logger->emergency($text);
    }

    public function alert(string $text): void {
        $this->logger->alert($text);
    }

    public function critical(string $text): void {
        $this->logger->critical($text);
    }

    public function error(string $text): void {
        $this->logger->error($text);
    }

    public function warning(string $text): void {
        $this->logger->warning($text);
    }

    public function notice(string $text): void {
        $this->logger->notice($text);
    }

    public function info(string $text): void {
        $this->logger->info($text);
    }

    public function debug(string $text): void {
        $this->logger->debug($text);
    }
}