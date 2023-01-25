<?php

namespace Appsas;

class FS
{
    private string $fileContent;

    public function __construct(private ?string $fileName = null)
    {
    }

    public function getFileContent(): string
    {
        if (empty($this->fileContent)) {
            $this->fileContent = file_get_contents($this->fileName);
        }

        return $this->fileContent;
    }

    public function setFile(string $fileName): void
    {
        $this->fileName = $fileName;
    }
}