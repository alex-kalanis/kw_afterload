<?php

namespace kalanis\kw_afterload;


/**
 * Class Afterload
 * @package kalanis\kw_afterload
 * Process all prepared files with settings in specified directory
 * Because Bootstrap is too static
 *
 * - list all files in datadir
 * - order them by their names
 * - process them one-by-one
 */
class Afterload
{
    public static function run(): void
    {
        $self = new static();
        $self->process();
    }

    final public function __construct()
    {
    }

    /**
     * @throws AfterloadException
     */
    public function process(): void
    {
        $exception = null;
        $files = $this->listFiles();
        sort($files);
        foreach ($files as $file) {
            try {
                require_once $file;
            } catch (AfterloadException $ex) {
                $exception = $ex->addPrev($exception);
            }
        }
        if (!empty($exception)) {
            throw $exception;
        }
    }

    /**
     * @return array<string> array of paths
     */
    protected function listFiles(): array
    {
        $path = realpath(implode(DIRECTORY_SEPARATOR, $this->path()));
        if (!$path) {
            return [];
        }
        $available = [];
        $files = scandir($path);
        if (false !== $files) {
            foreach ($files as $file) {
                if (
                    is_file($path . DIRECTORY_SEPARATOR . $file)
                    && strpos($file, '.php')
                ) {
                    $available[] = $path . DIRECTORY_SEPARATOR . $file;
                }
            }
        }
        return $available;
    }

    /**
     * @return array<string>
     * @codeCoverageIgnore could be set outside - like from tests
     */
    protected function path(): array
    {
        return [__DIR__, '..', 'data'];
    }
}
