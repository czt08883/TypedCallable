<?php

namespace Czt08883\TypedCallable;

use Czt08883\TypedCallable\Exception\TypedCallableSignatureMismatchException;

/**
 * Class TypedCallable
 * @package Czt08883\TypedCallable
 */
abstract class TypedCallable implements TypedCallableInterface
{
    /**
     * @var callable
     */
    private $func;

    /**
     * @param callable $func
     *
     * @throws TypedCallableSignatureMismatchException
     */
    public function __construct(callable $func)
    {
        if (!$this->isCompatible($func)) {
            throw new TypedCallableSignatureMismatchException(
                "Callable signature mismatch. "
                . "Callable must be: "
                . "function(" . $this->getParametersString() . "){...}"
            );
        }
        $this->func = $func;
    }

    /**
     * Invoke magic
     */
    public function __invoke()
    {
        call_user_func_array($this->func, func_get_args());
    }

    /**
     * @Contract\Ensure("is_callable($__result)")
     *
     * return callable
     */
    abstract public function useTemplate();

    /**
     * @return string
     */
    private function getParametersString()
    {
        $templateReflection = new \ReflectionFunction($this->useTemplate());
        $templateParameters = $templateReflection->getParameters();
        $parameterStringsArray = array_map([$this,'getParameterSignature'], $templateParameters);

        return implode(', ', $parameterStringsArray);
    }

    /**
     * @param \ReflectionParameter $param
     *
     * @return mixed
     */
    private function getParameterSignature(\ReflectionParameter $param)
    {
        $fullString = $param->__toString();
        $extractRegex = "/^.*\[\s*<\S*>\s*(\S+\s*\S+)\s*\].*$/isu";
        preg_match_all($extractRegex, $fullString, $matches);

        return $matches[1][0];
    }

    /**
     * @param callable $func
     *
     * @return bool
     */
    private function isCompatible(callable $func)
    {
        $templateReflection = new \ReflectionFunction($this->useTemplate());
        $funcReflection = new \ReflectionFunction($func);

        $templateParameters = $templateReflection->getParameters();
        $funcParameters = $funcReflection->getParameters();

        $compatible = true;
        if (count($templateParameters) != count($funcParameters)) {
            $compatible = false;
        } else {
            foreach ($templateParameters as $i=>$templateParameter) {
                $templateParamSignature = $this->getParameterSignature($templateParameter);
                $templateParamSignatureParts = explode(" ", $templateParamSignature);

                $funcParamSignature = $this->getParameterSignature($funcParameters[$i]);
                $funcParamSignatureParts = explode(" ", $funcParamSignature);

                if ($funcParamSignatureParts[0] != $templateParamSignatureParts[0]) {
                    $compatible = false;
                    break;
                }
            }
        }

        return $compatible;
    }
}
