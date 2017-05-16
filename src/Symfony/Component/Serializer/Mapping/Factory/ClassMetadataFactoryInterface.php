<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Mapping\Factory;

use Symfony\Component\Metadata\Factory\MetadataFactoryInterface;
use Symfony\Component\Serializer\Mapping\ClassMetadataInterface;

/**
 * Returns a {@see ClassMetadataInterface}.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 *
 * @method ClassMetadataInterface getMetadataFor($value)
 */
interface ClassMetadataFactoryInterface extends MetadataFactoryInterface
{
}
