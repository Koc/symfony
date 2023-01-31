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
 * Controller parameter tag to map Request Input to typed object and validate it.
 *
 * @author Thomas Hanke <thomas@han.ke>
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
final class MapRequestInput
{
    public function __construct(public readonly array $context = [])
    {
    }
}
