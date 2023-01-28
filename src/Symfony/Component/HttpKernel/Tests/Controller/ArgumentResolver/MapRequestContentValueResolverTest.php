<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\Controller\ArgumentResolver;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestContent;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\MapRequestContentValueResolver;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MapRequestContentValueResolverTest extends TestCase
{
    /**
     * @group legacy
     */
    public function testSupports()
    {
        $resolver = new MapRequestContentValueResolver(
            $this->createMock(SerializerInterface::class),
            $this->createMock(ValidatorInterface::class),
        );

        $request = Request::create('/');

        $argument = new ArgumentMetadata('dummy', \stdClass::class, false, false, null);
        $this->assertFalse($resolver->supports($request, $argument));

        $argument = new ArgumentMetadata('dummy', \stdClass::class, false, false, null, false, [
            MapRequestContent::class => new MapRequestContent(),
        ]);
        $this->assertTrue($resolver->supports($request, $argument));
    }

    public function testNotTypedArgument()
    {
        $resolver = new MapRequestContentValueResolver(
            $this->createMock(SerializerInterface::class),
            $this->createMock(ValidatorInterface::class),
        );

        $argument = new ArgumentMetadata('notTyped', null, false, false, null, false, [
            MapRequestContent::class => new MapRequestContent(),
        ]);
        $request = Request::create('/');

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Could not resolve the "$notTyped" controller argument: argument should be typed.');

        $resolver->resolve($request, $argument);
    }

    public function testValidationNotPassed()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList([new ConstraintViolation('Test', null, [], '', null, '')]));

        $resolver = new MapRequestContentValueResolver($serializer, $validator);

        $argument = new ArgumentMetadata('invalid', \stdClass::class, false, false, null, false, [
            MapRequestContent::class => new MapRequestContent(),
        ]);
        $request = Request::create('/');

        $this->expectException(ValidationFailedException::class);

        $resolver->resolve($request, $argument);
    }

    public function testValidationPassed()
    {
        $serializer = $this->createMock(SerializerInterface::class);

        $content = new QueryString(50);
        $serializer->expects($this->once())
            ->method('deserialize')
            ->willReturn($content);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $resolver = new MapRequestContentValueResolver($serializer, $validator);

        $argument = new ArgumentMetadata('invalid', \stdClass::class, false, false, null, false, [
            MapRequestContent::class => new MapRequestContent(),
        ]);
        $request = Request::create('/');

        self::assertEquals($content, $resolver->resolve($request, $argument)[0]);
    }
}

class RequestContent
{
    public function __construct(public readonly int $quantity)
    {
    }
}
