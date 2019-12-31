<?php

/**
 * @see       https://github.com/laminas/laminas-code for the canonical source repository
 * @copyright https://github.com/laminas/laminas-code/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-code/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Code\Generator\DocBlock;

use Laminas\Code\Generator\AbstractGenerator;
use Laminas\Code\Reflection\DocBlock\Tag\TagInterface as ReflectionDocBlockTag;
use ReflectionClass;
use ReflectionMethod;

class Tag extends AbstractGenerator
{
    /**
     * @var array
     */
    protected static $typeFormats = array(
        array(
            'param',
            '@param <type> <variable> <description>'
        ),
        array(
            'return',
            '@return <type> <description>'
        ),
        array(
            'tag',
            '@<name> <description>'
        )
    );

    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var string
     */
    protected $description = null;

    /**
     * Build a Tag generator object from a reflection object
     *
     * @param  ReflectionDocBlockTag $reflectionTag
     * @return Tag
     */
    public static function fromReflection(ReflectionDocBlockTag $reflectionTag)
    {
        $tagName = $reflectionTag->getName();

        $codeGenDocBlockTag = new static();
        $codeGenDocBlockTag->setName($tagName);

        // transport any properties via accessors and mutators from reflection to codegen object
        $reflectionClass = new ReflectionClass($reflectionTag);
        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (substr($method->getName(), 0, 3) == 'get') {
                $propertyName = substr($method->getName(), 3);
                if (method_exists($codeGenDocBlockTag, 'set' . $propertyName)) {
                    $codeGenDocBlockTag->{'set' . $propertyName}($reflectionTag->{'get' . $propertyName}());
                }
            }
        }

        return $codeGenDocBlockTag;
    }

    /**
     * @param  string $name
     * @return Tag
     */
    public function setName($name)
    {
        $this->name = ltrim($name, '@');
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string $description
     * @return Tag
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function generate()
    {
        $output = '@' . $this->name
            . (($this->description != null) ? ' ' . $this->description : '');

        return $output;
    }
}
