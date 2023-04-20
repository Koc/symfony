<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Attribute;

use Symfony\Component\HttpKernel\Controller\ArgumentResolver\QueryParameterValueResolver;
use Symfony\Component\Validator\Constraint;

/**
 * Can be used to pass a query parameter to a controller argument.
 *
 * @author Ruud Kamphuis <ruud@ticketswap.com>
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
final class MapQueryParameter extends ValueResolver
{
    /**
     * @see https://php.net/filter.filters.validate for filter, flags and options
     *
     * @param string|null $name The name of the query parameter. If null, the name of the argument in the controller will be used.
     * @param Constraint|Constraint[]|null $constraints
     */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?int $filter = null,
        public readonly int $flags = 0,
        public readonly array $options = [],
        public Constraint|array|null $constraints = null,
        string $resolver = QueryParameterValueResolver::class,
    ) {
        parent::__construct($resolver);
    }
}
