<?php

namespace PowerDI\HttpBasics;

class FileUploadStatus {
    private int $status;
    private ?string $error;
    private ?string $path;

    public const UPLOAD_OK = 0;
    public const EXTENSION_MISS_MATCH = 1;

    private function __construct(int $status, ?string $path, ?string $error) {
        $this->status = $status;
        $this->path = $path;
        $this->error = $error;
    }

    public static function ok(string $path): FileUploadStatus {
        return new FileUploadStatus(self::UPLOAD_OK, $path, null);
    }

    public static function error(int $status, string $error): FileUploadStatus {
        return new FileUploadStatus($status, null, $error);
    }

    public function getStatus(): int {
        return $this->status;
    }

    public function getPath(): string {
        return $this->path;
    }

    public function getError(): ?string {
        return $this->error;
    }
}