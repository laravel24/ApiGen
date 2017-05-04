<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer;

use ApiGen\Reflection\Reflection\InterfaceReflection;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use phpDocumentor\Reflection\DocBlockFactory;
use Roave\BetterReflection\Reflection\ReflectionClass;

final class BetterInterfaceReflectionTransformer implements TransformerInterface
{
    /**
     * @var DocBlockFactory
     */
    private $docBlockFactory;

    public function __construct(DocBlockFactory $docBlockFactory)
    {
        $this->docBlockFactory = $docBlockFactory;
    }

    /**
     * @param object $reflection
     */
    public function matches($reflection): bool
    {
        return $reflection instanceof ReflectionClass && $reflection->isInterface();
    }

    /**
     * @param ReflectionClass $reflection
     */
    public function transform($reflection): InterfaceReflection
    {
        $docBlock = $this->docBlockFactory->create($reflection->getDocComment() . ' ');
        return new InterfaceReflection($reflection, $docBlock);
    }
}