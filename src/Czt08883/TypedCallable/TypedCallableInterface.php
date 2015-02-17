<?php

namespace Czt08883\TypedCallable;

use Czt08883\TypedCallable\Exception\TypedCallableSignatureMismatchException;

/**
 * Interface TypedCallableInterface
 * @package Czt08883\TypedCallable
 */
interface TypedCallableInterface
{

    /**
     * @param callable $func
     *
     * @throws TypedCallableSignatureMismatchException
     */
    public function __construct(callable $func);

    /**
     * @Contract\Ensure("is_callable($__result)")
     *
     * return callable
     */
    public function useTemplate();
}
