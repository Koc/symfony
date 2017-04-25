<?php

namespace Symfony\Component\Metadata;

/**
 * {@inheritdoc}
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
abstract class GenericClassMetadata implements ClassMetadataInterface
{
    /**
     * @var string
     *
     * @internal This property is public in order to reduce the size of the
     *           class' serialized representation. Do not access it. Use
     *           {@link getName()} instead.
     */
    public $name;

    /**
     * @var \ReflectionClass
     */
    private $reflClass;

    /**
     * Constructs a metadata for the given class.
     *
     * @param string $class
     */
    public function __construct($class)
    {
        $this->name = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getReflectionClass()
    {
        if (!$this->reflClass) {
            $this->reflClass = new \ReflectionClass($this->getName());
        }

        return $this->reflClass;
    }

    /**
     * Returns the names of the properties that should be serialized.
     *
     * @return string[]
     */
    public function __sleep()
    {
        return array(
            'name',
        );
    }
}
