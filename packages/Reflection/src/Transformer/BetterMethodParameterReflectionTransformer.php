<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer;

use ApiGen\Reflection\Reflection\MethodParameterReflection;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use Roave\BetterReflection\Reflection\ReflectionParameter;

final class BetterMethodParameterReflectionTransformer implements TransformerInterface
{
    /**
     * @var DocBlockFactoryInterface
     */
    private $docBlockFactory;

    public function __construct(DocBlockFactoryInterface $docBlockFactory)
    {
        $this->docBlockFactory = $docBlockFactory;
    }

    /**
     * @param object $reflection
     */
    public function matches($reflection): bool
    {
        return $reflection instanceof ReflectionParameter && $reflection->getClass();
    }

    /**
     * @param ReflectionParameter $reflection
     */
    public function transform($reflection): MethodParameterReflection
    {
        $docBlock = $this->docBlockFactory->create($reflection->getDocBlockTypes() . ' ');

        return new MethodParameterReflection($reflection, $docBlock);
    }
}