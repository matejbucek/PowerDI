<?php

namespace PowerDI\HttpBasics;

use mysql_xdevapi\SqlStatementResult;

class FileUploadStatus {
    private int $status;
    private ?string $error;
    private ?string $path;
    private ?string $name;

    public const UPLOAD_OK = 0;
    public const EXTENSION_MISS_MATCH = 1;

    private function __construct(int $status, ?string $path, ?string $name, ?string $error) {
        $this->status = $status;
        $this->path = $path;
        $this->name = $name;
        $this->error = $error;
    }

    public static function ok(string $path, string $name): FileUploadStatus {
        return new FileUploadStatus(self::UPLOAD_OK, $path, $name, null);
    }

    public static function error(int $status, string $error): FileUploadStatus {
        return new FileUploadStatus($status, null, null, $error);
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

    public function getName(): ?string {
        return $this->name;
    }
}