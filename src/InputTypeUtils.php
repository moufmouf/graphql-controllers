<?php


namespace TheCodingMachine\GraphQLite;

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Object_;
use ReflectionMethod;

class InputTypeUtils
{
    /**
     * @var AnnotationReader
     */
    private $annotationReader;
    /**
     * @var NamingStrategyInterface
     */
    private $namingStrategy;

    public function __construct(AnnotationReader $annotationReader,
                                NamingStrategyInterface $namingStrategy)
    {
        $this->annotationReader = $annotationReader;
        $this->namingStrategy = $namingStrategy;
    }

    /**
     * Returns an array with 2 elements: [ $inputName, $className ]
     *
     * @param ReflectionMethod $method
     * @return string[]
     */
    public function getInputTypeNameAndClassName(ReflectionMethod $method): array
    {
        $fqsen = ltrim((string) $this->validateReturnType($method), '\\');
        $factory = $this->annotationReader->getFactoryAnnotation($method);
        if ($factory === null) {
            throw new \RuntimeException($method->getDeclaringClass()->getName().'::'.$method->getName().' has no @Factory annotation.');
        }
        return [$this->namingStrategy->getInputTypeName($fqsen, $factory), $fqsen];
    }

    private function validateReturnType(ReflectionMethod $refMethod): Fqsen
    {
        $returnType = $refMethod->getReturnType();
        if ($returnType === null) {
            throw MissingTypeHintException::missingReturnType($refMethod);
        }

        if ($returnType->allowsNull()) {
            throw MissingTypeHintException::nullableReturnType($refMethod);
        }

        $type = (string) $returnType;

        $typeResolver = new \phpDocumentor\Reflection\TypeResolver();

        $phpdocType = $typeResolver->resolve($type);
        if (!$phpdocType instanceof Object_) {
            throw MissingTypeHintException::invalidReturnType($refMethod);
        }

        return $phpdocType->getFqsen();
    }
}
