<?php

namespace kalanis\kw_afterload\Manage;


class FileInput
{
    /** @var string */
    public $fileName = '';
    /** @var int */
    public $filePosition = 0;
    /** @var string */
    public $targetDir = '';
    /** @var bool */
    public $enabled = false;

    public function setData(string $targetDir, string $fileName, bool $enabled): self
    {
        $this->parseName($fileName);
        $this->targetDir = $targetDir;
        $this->enabled = $enabled;
        return $this;
    }

    protected function parseName(string $name): void
    {
        if (preg_match('#^(\d+)_(.*)$#', $name, $match)) {
            $this->filePosition = intval($match[1]);
            $this->fileName = $match[2];
        } else {
            $this->fileName = $name;
        }
    }

    public function getName(): string
    {
        return $this->filePosition ? sprintf('%03d_%s', intval($this->filePosition), $this->fileName) : $this->fileName ;
    }

    public function getFullName(): string
    {
        return $this->targetDir . DIRECTORY_SEPARATOR . $this->getName() ;
    }
}
