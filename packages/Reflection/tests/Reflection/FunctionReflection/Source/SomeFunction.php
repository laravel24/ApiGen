<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\FunctionReflection\Source;

/**
 * Some description.
 *
 * And more lines!
 *
 * @param int $number and it's description
 * @param string|null $name and it's description
 * @param string $arguments and their description
 *
 * @return string
 */
function someAloneFunction(int $number, ?string $name = null, string ...$arguments): string
{
    return 'hi';
}