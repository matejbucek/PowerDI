<?php

namespace PowerDI\HttpBasics;

class File {
    private string $name;
    private string $path;
    private ?string $fileType;
    private ?array $multipleFiles;
    private int $size;
    private int $error;

    public function __construct(string $name, string $path, int $size, string $fileType, int $error) {
        $this->name = $name;
        $this->path = $path;
        $this->size = $size;
        $this->fileType = $fileType;
        $this->error = $error;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getPath(): string {
        return $this->path;
    }

    public function getFileType(): string {
        return $this->fileType;
    }

    public function getMultipleFiles(): ?array {
        return $this->multipleFiles;
    }

    public function getSize(): int {
        return $this->size;
    }

    public function getError(): int {
        return $this->error;
    }

    public static function createFromGlobals(): array {
        $files = [];
        foreach($_FILES as $name => $file) {
            if($file["error"] != 0) {
                $files[$name] = new File($file["name"], "", 0, "", $file["error"]);
                continue;
            }
            $path = $file["tmp_name"];
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileType = finfo_file($fileInfo, $path);
            $files[$name] = new File($file["name"], $path, filesize($path), $fileType, $file["error"]);
        }
        return $files;
    }

    public function uploadCheckAllowedExtension(array $allowedExtensions, string $to, string $nameWithoutExtension, int $permissions): FileUploadStatus {
        if(!array_key_exists($this->fileType, $allowedExtensions)) {
            return FileUploadStatus::error(FileUploadStatus::EXTENSION_MISS_MATCH, "The file extension is not allowed!");
        }

        $path = $to . $nameWithoutExtension . "." . $allowedExtensions[$this->fileType];

        $this->upload($path, $permissions);
        return FileUploadStatus::ok($path, $nameWithoutExtension . "." . $allowedExtensions[$this->fileType]);
    }

    public function upload(string $to, int $permissions): void {
        move_uploaded_file($this->path, $to);
        chmod($to, $permissions);
    }
}