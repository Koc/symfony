<?php

namespace Symfony\Component\Metadata\Loader;

abstract class FileLoader implements LoaderInterface
{
    /**
     * @var string
     */
    protected $file;

    /**
     * Constructor.
     *
     * @param string $file The mapping file to load
     *
     * @throws MappingException if the mapping file does not exist or is not readable
     */
    public function __construct($file)
    {
        if (!is_file($file)) {
            throw new MappingException(sprintf('The mapping file %s does not exist', $file));
        }

        if (!is_readable($file)) {
            throw new MappingException(sprintf('The mapping file %s is not readable', $file));
        }

        $this->file = $file;
    }
}
