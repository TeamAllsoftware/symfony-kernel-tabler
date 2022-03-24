<?php

namespace Allsoftware\SymfonyKernelTabler\Helper;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

class EntityHelper
{
    public function __construct(public string $class)
    {
        if (class_exists($class) === false) throw new \LogicException("Class '$class' is unknown !");
    }

    /**
     * Check existence d'une propriété sur une entité
     * @throws ReflectionException
     */
    public function getAttributeInstances(string $attribute_class): array
    {
        $reflectionClass = new ReflectionClass($this->class);
        $attributes = $reflectionClass->getAttributes(
            $attribute_class,
            ReflectionAttribute::IS_INSTANCEOF
        );

        /** @var $attribute_class[] $attributeInstances */
        $attributeInstances = [];
        foreach ($attributes as $quillMentionAttr) {
            $attributeInstances[] = $quillMentionAttr->newInstance();
        }

        return $attributeInstances;
    }

    /**
     * @throws ReflectionException
     */
    static function searchAttributeInstances(string $class, string $attribute_class): array
    {
        return (new EntityHelper($class))->getAttributeInstances($attribute_class);
    }

    /**
     * Check existence d'une propriété sur une entité
     */
    public function resolveEntityProperty(string $property) : string {

        $propertyInfo = new PropertyInfoExtractor([new ReflectionExtractor()]);
        $properties = $propertyInfo->getProperties($this->class);

        if (in_array($property, $properties) === false) throw new \LogicException("Property '$property' in '$this->class' is unknown !");

        return $property;
    }

    /**
     * @param string[] $properties
     * @return string[]
     */
    public function resolveEntityProperties(array $properties) : array {
        foreach ($properties as $property) {
            $this->resolveEntityProperty($property);
        }

        return $properties;
    }
}
