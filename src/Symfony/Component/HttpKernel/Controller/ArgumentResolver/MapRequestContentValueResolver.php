<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Controller\ArgumentResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestContent;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Konstantin Myakshin <molodchick@gmail.com>
 */
final class MapRequestContentValueResolver implements ArgumentValueResolverInterface, ValueResolverInterface
{
    private const CONTEXT = [
        DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS => true,
    ];

    public function __construct(
        private readonly ?SerializerInterface $serializer,
        private readonly ?ValidatorInterface $validator,
    ) {
    }

    /**
     * @deprecated since Symfony 6.2, use resolve() instead
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        @trigger_deprecation('symfony/http-kernel', '6.2', 'The "%s()" method is deprecated, use "resolve()" instead.', __METHOD__);

        return 1 === \count($argument->getAttributesOfType(MapRequestContent::class));
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $attributes = $argument->getAttributesOfType(MapRequestContent::class);

        if (!$attributes) {
            return [];
        }

        /** @var MapRequestContent $attribute */
        $attribute = $attributes[0];

        $type = $argument->getType();
        if (!$type) {
            throw new \LogicException(sprintf('Could not resolve the "$%s" controller argument: argument should be typed.', $argument->getName()));
        }

        $payload = $this->getSerializer->deserialize(
            $request->getContent(),
            $type,
            $attribute->format,
            $attribute->context + self::CONTEXT,
        );

        if ($this->validator) {
            $violations = $this->validator->validate($payload);

            if (\count($violations)) {
                throw new ValidationFailedException($payload, $violations);
            }
        }

        return [$payload];
    }

    private function getSerializer(): SerializerInterface
    {
        if (!class_exists(SerializerInterface::class)) {
            throw new \LogicException(sprintf('The "symfony/serializer" component is required to use the "%s" validator. Try running "composer require symfony/serializer".',
                __CLASS__));
        }

        return $this->serializer;
    }
}
