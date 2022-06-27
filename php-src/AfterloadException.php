<?php

namespace kalanis\kw_afterload;

use Exception;


/**
 * Class AfterloadException
 * @package kalanis\kw_afterload
 * Exceptions thrown during run
 * Can and do chain exceptions on-the-fly
 */
final class AfterloadException extends Exception
{
    /** @var Exception|null */
    protected $prev = null;

    public function addPrev(?Exception $exception = null): self
    {
        $this->prev = $exception;
        return $this;
    }

    public function getPrev(): ?Exception
    {
        return $this->prev;
    }
}
