<?php

namespace Symfony\Component\Metadata\Factory;

use Symfony\Component\Metadata\ClassMetadataInterface;
use Symfony\Component\Metadata\Exception\InvalidArgumentException;

/**
 * @author Luis Ramón López <lrlopez@gmail.com>
 */
interface MetadataFactoryInterface
{
    /**
     * If the method was called with the same class name (or an object of that
     * class) before, the same metadata instance is returned.
     *
     * If the factory was configured with a cache, this method will first look
     * for an existing metadata instance in the cache. If an existing instance
     * is found, it will be returned without further ado.
     *
     * Otherwise, a new metadata instance is created. If the factory was
     * configured with a loader, the metadata is passed to the
     * {@link \Symfony\Component\Serializer\Mapping\Loader\LoaderInterface::loadClassMetadata()} method for further
     * configuration. At last, the new object is returned.
     *
     * @param string|object $value
     *
     * @return ClassMetadataInterface
     *
     * @throws InvalidArgumentException If no metadata exists for the given value
     */
    public function getMetadataFor($value);

    /**
     * Returns whether the class is able to return metadata for the given value.
     *
     * @param mixed $value Some value
     *
     * @return bool Whether metadata can be returned for that value
     */
    public function hasMetadataFor($value);
}
