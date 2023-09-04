<?php

namespace PowerDI\Storage;

class LocalStorage implements Storage {

    private string $path;
    private mixed $data;

    public function __construct(string $appBase, string $path) {
        $this->path = $appBase . $path;
        $this->data = json_decode(file_get_contents($this->path), true);
        if($this->data == null)
            $this->data = json_decode("{}", true);
    }

    public function __destruct() {
        $file = fopen($this->path, "w");
        if(!$file)
            throw new CouldntOpenFileException();
        fwrite($file, json_encode($this->data));
        fclose($file);
    }

    public function has(string $property): bool {
        return isset($this->data[$property]);
    }

    public function get(string $property) {
        return $this->data[$property];
    }

    public function set(string $property, $value): void {
        $this->data[$property] = $value;
    }

    public function delete(string $property): void {
        unset($this->data[$property]);
    }
}