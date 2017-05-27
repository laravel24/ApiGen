<?php declare(strict_types=1);

namespace ApiGen\Tests\Annotation;

use ApiGen\Annotation\AnnotationDecorator;
use ApiGen\Annotation\AnnotationList;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;
use ApiGen\Tests\Annotation\AnnotationDecoratorSource\SomeClassWithReturnTypes;

final class AnnotationDecoratorTest extends AbstractParserAwareTestCase
{
    /**
     * @var AnnotationDecorator
     */
    private $annotationDecorator;

    /**
     * @var ClassMethodReflectionInterface
     */
    private $methodReflection;

    protected function setUp(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/AnnotationDecoratorSource']);
        $this->annotationDecorator = $this->container->getByType(AnnotationDecorator::class);

        $classReflection = $this->reflectionStorage->getClassReflections()[SomeClassWithReturnTypes::class];
        $this->methodReflection = $classReflection->getOwnMethods()['returnArray'];
    }

    public function testClassArray(): void
    {
        $returnAnnotation = $this->methodReflection->getAnnotation(AnnotationList::RETURN_)[0];

        $this->assertSame(
            '<code><a href="class-ApiGen.Tests.Annotation.AnnotationDecoratorSource.ReturnedClass.html">ReturnedClass</a>[]</code>',
            $this->annotationDecorator->decorate($returnAnnotation, $this->methodReflection)
        );
    }

    public function testDoubleTypes()
    {
        $param1Annotation = $this->methodReflection->getAnnotation(AnnotationList::PARAM)[0];

        $this->assertSame(
            'int|string[]',
            $this->annotationDecorator->decorate($param1Annotation, $this->methodReflection)
        );
    }

    /**
     * @return $this
     */
    public function testDoubleWithSelfReference()
    {
        $param2Annotation = $this->methodReflection->getAnnotation(AnnotationList::PARAM)[1];

        // @todo: it doesn't make sense to link itself here, since it's the same page
        $this->assertSame(
            'string|<code><a href="class-ApiGen.Tests.Annotation.AnnotationDecoratorSource.SomeClassWithReturnTypes.html">$this</a></code>',
            $this->annotationDecorator->decorate($param2Annotation, $this->methodReflection)
        );
    }
}