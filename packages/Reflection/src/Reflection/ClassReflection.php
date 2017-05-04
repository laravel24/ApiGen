<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorInterface;
use phpDocumentor\Reflection\DocBlock;
use Roave\BetterReflection\Reflection\ReflectionClass;

final class ClassReflection implements ClassReflectionInterface
{
    /**
     * @var ReflectionClass
     */
    private $reflection;

    /**
     * @var DocBlock
     */
    private $docBlock;

    public function __construct(
        ReflectionClass $betterClassReflection,
        DocBlock $docBlock
    ) {
        $this->reflection = $betterClassReflection;
        $this->docBlock = $docBlock;
    }

    public function getName(): string
    {
        return $this->reflection->getName();
    }

    public function getShortName(): string
    {
        return $this->reflection->getShortName();
    }

    public function getStartLine(): int
    {
        return $this->reflection->getStartLine();
    }

    public function getEndLine(): int
    {
        return $this->reflection->getEndLine();
    }

    public function getNamespaceName(): string
    {
        return $this->reflection->getNamespaceName();
    }

    public function getPseudoNamespaceName(): string
    {
        if ($this->reflection->isInternal()) {
            return 'PHP';
        }

        if ($this->reflection->getNamespaceName()) {
            return $this->reflection->getNamespaceName();
        }

        return 'None';
    }

    public function getPrettyName(): string
    {
        return $this->reflection->getName() . '()';
    }

    /**
     * @return mixed[]
     */
    public function getAnnotation(string $name): array
    {
        return $this->docBlock->getTagsByName($name);
    }

    public function getDescription(): string
    {
        $description = $this->docBlock->getSummary()
            . AnnotationList::EMPTY_LINE
            . $this->docBlock->getDescription();

        return trim($description);
    }

    public function isDocumented(): bool
    {
        if ($this->reflection->isInternal()) {
            return false;
        }

        if ($this->hasAnnotation('internal')) {
            return false;
        }

        return true;
    }

    public function getParentClass(): ?ClassReflectionInterface
    {
        $parentClassName = $this->reflection->getParentClassName();

        if ($parentClassName) {
            return $this->getParsedClasses()[$parentClassName];
        }

        return null;
    }

    public function getParentClassName(): string
    {
        if ($this->reflection->getParentClass()) {
            return $this->reflection->getParentClass()
                ->getShortName();
        }

        return '';
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getParentClasses(): array
    {
        if ($this->parentClasses === null) {
            $this->parentClasses = array_map(function (IReflectionClass $class) {
                return $this->getParsedClasses()[$class->getName()];
            }, $this->reflection->getParentClasses());
        }

        return $this->parentClasses;
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getDirectSubClasses(): array
    {
        // TODO: Implement getDirectSubClasses() method.
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getIndirectSubClasses(): array
    {
        // TODO: Implement getIndirectSubClasses() method.
    }

    public function implementsInterface(string $interface): bool
    {
        return $this->reflection->implementsInterface($interface);
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getInterfaces(): array
    {
        // TODO: Implement getInterfaces() method.
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getOwnInterfaces(): array
    {
        // TODO: Implement getOwnInterfaces() method.
    }

    /**
     * @return string[]
     */
    public function getOwnInterfaceNames(): array
    {
        // TODO: Implement getOwnInterfaceNames() method.
    }

    /**
     * @return MethodReflectionInterface[]|VisibilityTrait[]
     */
    public function getMethods(): array
    {
        // TODO: Implement getMethods() method.
    }

    /**
     * @return MethodReflectionInterface[]|VisibilityTrait[]
     */
    public function getOwnMethods(): array
    {
        // TODO: Implement getOwnMethods() method.
    }

    /**
     * @return MethodReflectionInterface[]
     */
    public function getInheritedMethods(): array
    {
        // TODO: Implement getInheritedMethods() method.
    }

    /**
     * @return MethodReflectionInterface[]
     */
    public function getUsedMethods(): array
    {
        // TODO: Implement getUsedMethods() method.
    }

    /**
     * @return MethodReflectionInterface[]
     */
    public function getTraitMethods(): array
    {
        // TODO: Implement getTraitMethods() method.
    }

    public function getMethod(string $name): MethodReflectionInterface
    {
        // TODO: Implement getMethod() method.
    }

    public function hasMethod(string $name): bool
    {
        // TODO: Implement hasMethod() method.
    }

    /**
     * @return ConstantReflectionInterface[]
     */
    public function getConstants(): array
    {
        if ($this->constants === null) {
            $this->constants = [];
            foreach ($this->reflection->getConstantReflections() as $constant) {
                $apiConstant = $this->transformerCollector->transformReflectionToElement($constant);
                if (! $this->isDocumented() || $apiConstant->isDocumented()) {
                    /** @var ReflectionElement $constant */
                    $this->constants[$constant->getName()] = $apiConstant;
                }
            }
        }

        return $this->constants;
    }

    /**
     * @return ConstantReflectionInterface[]
     */
    public function getOwnConstants(): array
    {
        if (isset($this->getOwnConstants()[$name])) {
            return $this->getOwnConstants()[$name];
        }

        throw new InvalidArgumentException(sprintf(
            'Constant %s does not exist in class %s',
            $name,
            $this->reflection->getName()
        ));
    }

    /**
     * @return ConstantReflectionInterface[]
     */
    public function getInheritedConstants(): array
    {
        // TODO: Implement getInheritedConstants() method.
    }

    public function hasConstant(string $name): bool
    {
        // TODO: Implement hasConstant() method.
    }

    public function getConstant(string $name): ConstantReflectionInterface
    {
        // TODO: Implement getConstant() method.
    }

    public function getOwnConstant(string $name): ConstantReflectionInterface
    {
        // TODO: Implement getOwnConstant() method.
    }

    public function getVisibilityLevel(): int
    {
        // TODO: Implement getVisibilityLevel() method.
    }

    public function getTransformerCollector(): TransformerCollectorInterface
    {
        // TODO: Implement getTransformerCollector() method.
    }

    /**
     * @return ClassReflectionInterface[]|string[]
     */
    public function getTraits(): array
    {
        // TODO: Implement getTraits() method.
    }

    /**
     * @return ClassReflectionInterface[]|string[]
     */
    public function getOwnTraits(): array
    {
        // TODO: Implement getOwnTraits() method.
    }

    public function getOwnTraitNames(): array
    {
        // TODO: Implement getOwnTraitNames() method.
    }

    /**
     * @return string[]
     */
    public function getTraitAliases(): array
    {
        // TODO: Implement getTraitAliases() method.
    }

    /**
     * @return PropertyReflectionInterface[]
     */
    public function getProperties(): array
    {
        return [];
    }

    /**
     * @return PropertyReflectionInterface[]|VisibilityTrait[]
     */
    public function getOwnProperties(): array
    {
        // TODO: Implement getOwnProperties() method.
    }

    /**
     * @return PropertyReflectionInterface[]
     */
    public function getInheritedProperties(): array
    {
        // TODO: Implement getInheritedProperties() method.
    }

    /**
     * @return PropertyReflectionInterface[]
     */
    public function getTraitProperties(): array
    {
        return $this->classTraitElementExtractor->getTraitProperties();
    }

    public function getUsedProperties(): array
    {
        // TODO: Implement getUsedProperties() method.
    }

    public function getProperty(string $name): PropertyReflectionInterface
    {
        if ($this->hasProperty($name)) {
            return $this->properties[$name];
        }

        throw new InvalidArgumentException(sprintf(
            'Property %s does not exist in class %s',
            $name,
            $this->reflection->getName()
        ));
    }

    public function hasProperty(string $name): bool
    {
        // TODO: Implement hasProperty() method.
    }

    public function usesTrait(string $name): bool
    {
        // TODO: Implement usesTrait() method.
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getParsedClasses(): array
    {
        // TODO: Implement getParsedClasses() method.
    }

    public function isAbstract(): bool
    {
        // TODO: Implement isAbstract() method.
    }

    public function isFinal(): bool
    {
        // TODO: Implement isFinal() method.
    }

    public function isInterface(): bool
    {
        // TODO: Implement isInterface() method.
    }

    public function isException(): bool
    {
        // TODO: Implement isException() method.
    }

    public function isTrait(): bool
    {
        // TODO: Implement isTrait() method.
    }

    public function isSubclassOf(string $class): bool
    {
        // TODO: Implement isSubclassOf() method.
    }

    public function isDeprecated(): bool
    {
        // TODO: Implement isDeprecated() method.
    }

    /**
     * @return mixed[]
     */
    public function getAnnotations(): array
    {
        return $this->docBlock->getTags();
    }

    public function hasAnnotation(string $name): bool
    {
        return $this->docBlock->hasTag($name);
    }

    public function getFileName(): string
    {
        return $this->reflection->getFileName();
    }
}