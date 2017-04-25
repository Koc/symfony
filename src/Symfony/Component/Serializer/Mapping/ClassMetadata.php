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

use Symfony\Component\Metadata\GenericClassMetadata;

/**
 * {@inheritdoc}
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class ClassMetadata extends GenericClassMetadata
{
    /**
     * @var AttributeMetadataInterface[]
     *
     * @internal This property is public in order to reduce the size of the
     *           class' serialized representation. Do not access it. Use
     *           {@link getAttributesMetadata()} instead.
     */
    public $attributesMetadata = array();

    /**
     * {@inheritdoc}
     */
    public function addAttributeMetadata(AttributeMetadataInterface $attributeMetadata)
    {
        $this->attributesMetadata[$attributeMetadata->getName()] = $attributeMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributesMetadata()
    {
        return $this->attributesMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(\Symfony\Component\Metadata\ClassMetadataInterface $classMetadata)
    {
        foreach ($classMetadata->getAttributesMetadata() as $attributeMetadata) {
            if (isset($this->attributesMetadata[$attributeMetadata->getName()])) {
                $this->attributesMetadata[$attributeMetadata->getName()]->merge($attributeMetadata);
            } else {
                $this->addAttributeMetadata($attributeMetadata);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __sleep()
    {
        return array_merge(
            parent::__sleep(),
            array('attributesMetadata')
        );
    }
}
