<?php

namespace Symfony\Component\Metadata\Loader;

use Symfony\Component\Metadata\ClassMetadataInterface;

/**
 * Loads {@link ClassMetadataInterface}.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
interface LoaderInterface
{
    /**
     * Load class metadata.
     *
     * @param ClassMetadataInterface $classMetadata A metadata
     *
     * @return bool
     */
    public function loadClassMetadata(ClassMetadataInterface $classMetadata);
}
