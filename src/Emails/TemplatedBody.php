<?php

namespace PowerDI\Emails;

class TemplatedBody {
    private string $template;
    private array $arguments;

    /**
     * @param string $template
     * @param array $arguments
     */
    public function __construct(string $template, array $arguments) {
        $this->template = $template;
        $this->arguments = $arguments;
    }

    public function getTemplate(): string {
        return $this->template;
    }

    public function getArguments(): array {
        return $this->arguments;
    }
}