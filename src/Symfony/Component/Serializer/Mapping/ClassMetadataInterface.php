<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Mapping;

use Symfony\Component\Metadata\ClassMetadataInterface as BaseClassMetadataInterface;
/**
 * Stores metadata needed for serializing and deserializing objects of specific class.
 *
 * Primarily, the metadata stores the set of attributes to serialize or deserialize.
 *
 * There may only exist one metadata for each attribute according to its name.
 *
 * @internal
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
interface ClassMetadataInterface extends BaseClassMetadataInterface
{
    /**
     * Adds an {@link AttributeMetadataInterface}.
     *
     * @param AttributeMetadataInterface $attributeMetadata
     */
    public function addAttributeMetadata(AttributeMetadataInterface $attributeMetadata);

    /**
     * Gets the list of {@link AttributeMetadataInterface}.
     *
     * @return AttributeMetadataInterface[]
     */
    public function getAttributesMetadata();
}
