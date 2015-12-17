<?php

namespace kriskbx\wyn\Helper;

use kriskbx\wyn\Contracts\Input\Input as InputContract;
use kriskbx\wyn\Contracts\Output\Output as OutputContract;
use kriskbx\wyn\Exceptions\InvalidProviderException;
use kriskbx\wyn\Exceptions\MissingArgumentException;
use kriskbx\wyn\Exceptions\MissingProviderException;
use ReflectionClass;
use ReflectionParameter;

class IOGenerator
{
    /**
     * @var string
     */
    protected $namespace = 'kriskbx\\wyn\\';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $params;

    /**
     * Construct.
     *
     * @param string $name
     * @param array  $data
     * @param string $type
     */
    public function __construct($name, $data, $type = 'input')
    {
        $this->name = $name;
        $this->data = $data;
        $this->type = ucfirst($type);

        $this->assertProvider();
        $this->assertProviderClass();
    }

    /**
     * Validate the given data by reading the required params from the constructor of the selected provider.
     *
     * @return $this
     *
     * @throws InvalidProviderException
     * @throws MissingProviderException
     */
    public function validate()
    {
        $params = $this->getConstructorParams();
        foreach ($params as $param) {
            $this->validateParam($param);
        }

        return $this;
    }

    /**
     * Make the Input or Output object.
     *
     * @return InputContract|OutputContract
     */
    public function make()
    {
        $reflection = new ReflectionClass($this->getProviderClassName());

        return $reflection->newInstanceArgs($this->params);
    }

    /**
     * Assert that the provider is set.
     *
     * @throws MissingProviderException
     */
    protected function assertProvider()
    {
        if (!isset($this->data['provider'])) {
            throw new MissingProviderException($this->name);
        }
    }

    /**
     * Assert that the provider is valid and the class exists.
     *
     * @throws InvalidProviderException
     */
    protected function assertProviderClass()
    {
        if (!class_exists($this->getProviderClassName())) {
            throw new InvalidProviderException($this->data['provider']);
        }
    }

    /**
     * Get the class name of the provider.
     *
     * @return string
     */
    protected function getProviderClassName()
    {
        return $this->namespace.$this->type.'\\'.ucfirst($this->data['provider']).$this->type;
    }

    /**
     * Get the constructor params of the the given class name.
     *
     * @return \ReflectionParameter[]
     */
    protected function getConstructorParams()
    {
        $reflection = new ReflectionClass($this->getProviderClassName());
        $params = $reflection->getConstructor()->getParameters();

        return $params;
    }

    /**
     * Validate a constructor param by the given config data and add a valid param to the params property.
     *
     * @param ReflectionParameter $param
     *
     * @return mixed
     *
     * @throws MissingArgumentException
     */
    protected function validateParam($param)
    {
        // No default value and nothing set in the config
        if (!$param->isDefaultValueAvailable() && !isset($this->data[ $param->getName() ])) {
            throw new MissingArgumentException($param->getName(), $this->name);
        }

        // Default value is available and nothing set in the config
        if ($param->isDefaultValueAvailable() && !isset($this->data[ $param->getName() ])) {
            return $this->params[ $param->getName() ] = $param->getDefaultValue();
        }

        // Something is set in the config
        return $this->params[ $param->getName() ] = $this->data[ $param->getName() ];
    }
}
