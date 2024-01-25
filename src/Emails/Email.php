<?php

namespace PowerDI\Emails;

class Email {
    private Address $from;
    private array $recipients;
    private string $subject;
    private string|TemplatedBody $body;
    private bool $isHtml;


    /**
     * @param Address $from
     * @param array $recipients
     * @param string $subject
     * @param string | TemplatedBody $body
     */
    public function __construct(Address $from, array $recipients, string $subject, string|TemplatedBody $body, bool $isHtml) {
        $this->from = $from;
        $this->recipients = $recipients;
        $this->subject = $subject;
        $this->body = $body;
        $this->isHtml = $isHtml;
    }

    public function isHtml(): bool {
        return $this->isHtml;
    }

    public function getFrom(): Address {
        return $this->from;
    }

    public function setFrom(Address $from): void {
        $this->from = $from;
    }

    public function getRecipients(): array {
        return $this->recipients;
    }

    public function setRecipients(array $recipients): void {
        $this->recipients = $recipients;
    }

    public function getSubject(): string {
        return $this->subject;
    }

    public function setSubject(string $subject): void {
        $this->subject = $subject;
    }

    public function getBody(): string|TemplatedBody {
        return $this->body;
    }

    public function setBody(string|TemplatedBody $body): void {
        $this->body = $body;
    }
}