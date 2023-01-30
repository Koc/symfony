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
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Konstantin Myakshin <molodchick@gmail.com>
 */
final class MapQueryStringValueResolver implements ArgumentValueResolverInterface, ValueResolverInterface
{
    private const CONTEXT = [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true];

    public function __construct(
        private readonly ?DenormalizerInterface $normalizer,
        private readonly ?ValidatorInterface $validator,
    ) {
    }

    /**
     * @deprecated since Symfony 6.2, use resolve() instead
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        @trigger_deprecation('symfony/http-kernel', '6.2', 'The "%s()" method is deprecated, use "resolve()" instead.', __METHOD__);

        return 1 === \count($argument->getAttributesOfType(MapQueryString::class, ArgumentMetadata::IS_INSTANCEOF));
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $attributes = $argument->getAttributesOfType(MapQueryString::class, ArgumentMetadata::IS_INSTANCEOF);

        if (!$attributes) {
            return [];
        }

        /** @var MapQueryString $attribute */
        $attribute = $attributes[0];

        $type = $argument->getType();
        if (!$type) {
            throw new \LogicException(sprintf('Could not resolve the "$%s" controller argument: argument should be typed.', $argument->getName()));
        }

        $payload = $this->getNormalizer()->denormalize(
            $request->query->all(),
            $type,
            'json',
            $attribute->context + self::CONTEXT
        );

        if ($this->validator) {
            $violations = $this->validator->validate($payload);

            if (\count($violations)) {
                throw new ValidationFailedException($payload, $violations);
            }
        }

        return [$payload];
    }

    private function getNormalizer(): DenormalizerInterface
    {
        if (!class_exists(DenormalizerInterface::class)) {
            throw new \LogicException(sprintf('The "symfony/serializer" component is required to use the "%s" validator. Try running "composer require symfony/serializer".',
                __CLASS__));
        }

        return $this->normalizer;
    }
}
