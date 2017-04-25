<?php

namespace Symfony\Component\Metadata;

/**
 * Stores metadata needed for serializing and deserializing objects of specific class.
 *
 * Primarily, the metadata stores the set of attributes to serialize or deserialize.
 *
 * There may only exist one metadata for each attribute according to its name.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
interface ClassMetadataInterface
{
    /**
     * Returns the name of the backing PHP class.
     *
     * @return string The name of the backing class
     */
    public function getName();

    /**
     * Merges a {@link ClassMetadataInterface} in the current one.
     *
     * @param ClassMetadataInterface $classMetadata
     */
    public function merge(ClassMetadataInterface $classMetadata);

    /**
     * Returns a {@link \ReflectionClass} instance for this class.
     *
     * @return \ReflectionClass
     */
    public function getReflectionClass();
}
