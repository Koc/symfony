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

/**
 * Controller parameter tag to map Query String to typed object and validate it.
 *
 * @author Konstantin Myakshin <molodchick@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
final class MapQueryString
{
    public function __construct(public readonly array $context = [])
    {
    }
}
