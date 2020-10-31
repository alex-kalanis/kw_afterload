<?php

namespace kalanis\kw_afterload\Manage;

use kalanis\kw_afterload\AfterloadException;


class Files
{
    /** @var string */
    protected $enabledPath = '';
    /** @var string */
    protected $disabledPath = '';
    /** @var FileInput */
    protected $fileInput = null;
    /** @var FileInput[] */
    protected $files = [];
    /** @var int */
    protected $highest = 0;

    public function __construct()
    {
        $this->fileInput = new FileInput();
        $this->generatePaths();
        $this->loadFiles();
    }

    protected function generatePaths(): void
    {
        $this->enabledPath = realpath(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'data']));
        $this->disabledPath = realpath(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'data', 'disabled']));
    }

    protected function loadFiles(): void
    {
        $this->files = [];
        if ($this->enabledPath) {
            foreach (scandir($this->enabledPath) as $item) {
                if (is_file($this->enabledPath . DIRECTORY_SEPARATOR . $item) && ('.' != $item[0])) {
                    $fileInput = clone $this->fileInput;
                    $fileInput->setData($this->enabledPath, $item, true);
                    $this->files[] = $fileInput;
                }
            }
        }
        if ($this->disabledPath) {
            foreach (scandir($this->disabledPath) as $item) {
                if (is_file($this->disabledPath . DIRECTORY_SEPARATOR . $item) && ('.' != $item[0])) {
                    $fileInput = clone $this->fileInput;
                    $fileInput->setData($this->disabledPath, $item, false);
                    $this->files[] = $fileInput;
                }
            }
        }
        foreach ($this->files as $file) {
            $this->highest = max($file->filePosition, $this->highest);
        }
    }

    /**
     * @return FileInput[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param string $name
     * @param string $content
     * @return $this
     * @throws AfterloadException
     */
    public function addInput(string $name, string $content): self
    {
        $fileInfo = clone $this->fileInput;
        $fileInfo->setData($this->disabledPath, $name, false);
        if (false === @file_put_contents($fileInfo->getFullName(), $content)) {
            throw new AfterloadException(sprintf('Cannot write file %s', $name));
        }
        $this->files[] = $fileInfo;
        return $this;
    }

    /**
     * @param string $name
     * @return string
     * @throws AfterloadException
     */
    public function getInput(string $name): string
    {
        $found = $this->searchFile($name);
        $content = @file_get_contents($found->getFullName());
        if (false === $content) {
            throw new AfterloadException(sprintf('File %s cannot read', $name));
        }
        return $content;
    }

    /**
     * @param string $name
     * @param string $content
     * @param bool $withUnlink
     * @return $this
     * @throws AfterloadException
     */
    public function updateInput(string $name, string $content, bool $withUnlink = true): self
    {
        $found = $this->searchFile($name);
        if ($withUnlink && !@unlink($found->getFullName())) {
            // @codeCoverageIgnoreStart
            throw new AfterloadException(sprintf('Cannot remove old file %s', $name));
            // @codeCoverageIgnoreEnd
        }
        if (false === @file_put_contents($found->getFullName(), $content)) {
            throw new AfterloadException(sprintf('Cannot write file %s', $name));
        }
        return $this;
    }

    public function removeInput(string $name): self
    {
        // need also position, so extra
        $found = null;
        $position = null;
        foreach ($this->files as $pos => $file) {
            if ($name == $file->fileName) {
                $found = $file;
                $position = $pos;
                break;
            }
        }
        if ($found) {
            unlink($found->getFullName());
            unset($this->files[$position]);
        }
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     * @throws AfterloadException
     */
    public function enable(string $name): self
    {
        $found = $this->searchFile($name);
        if (!$found->enabled) {
            $this->highest = $found->filePosition = $this->highest + 1;
            rename($found->targetDir . DIRECTORY_SEPARATOR . $found->fileName, $this->enabledPath . DIRECTORY_SEPARATOR . $found->getName());
            $found->targetDir = $this->enabledPath;
            $found->enabled = true;
        }
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     * @throws AfterloadException
     */
    public function disable(string $name): self
    {
        $found = $this->searchFile($name);
        if ($found->enabled) {
            rename($found->getFullName(), $this->disabledPath . DIRECTORY_SEPARATOR . $found->fileName);
            $found->filePosition = 0;
            $found->targetDir = $this->disabledPath;
            $found->enabled = false;
        }
        return $this;
    }

    /**
     * @param string $name
     * @return FileInput
     * @throws AfterloadException
     */
    protected function &searchFile(string $name): FileInput
    {
        foreach ($this->files as $file) {
            if ($name == $file->fileName) {
                return $file;
            }
        }
        throw new AfterloadException(sprintf('File %s not found', $name));
    }
}