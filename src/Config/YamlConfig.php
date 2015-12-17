<?php

namespace kriskbx\wyn\Config;

use kriskbx\wyn\Contracts\Config\Config as ConfigContract;
use kriskbx\wyn\Exceptions\ConfigFileNotFoundException;
use kriskbx\wyn\Exceptions\InputNameNotFoundException;
use kriskbx\wyn\Exceptions\OutputNameNotFoundException;
use Symfony\Component\Yaml\Yaml;

class YamlConfig implements ConfigContract
{
    /**
     * @var Yaml
     */
    protected $yamlParser;

    /**
     * @var string
     */
    protected $configFile = '';

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Constructor.
     *
     * @param Yaml $yamlParser
     * @param $configFile
     *
     * @throws ConfigFileNotFoundException
     */
    public function __construct(Yaml $yamlParser, $configFile)
    {
        $this->yamlParser = $yamlParser;
        $this->configFile = $configFile;

        $this->fileExists();
        $this->readFile();
    }

    /**
     * Get the names of all configured inputs.
     *
     * @return array
     */
    public function getAllInputs()
    {
        return array_keys($this->data['input']);
    }

    /**
     * Get a configured input by name.
     *
     * @param string $name
     *
     * @return array
     *
     * @throws InputNameNotFoundException
     */
    public function getInput($name)
    {
        $this->inputExists($name);

        return $this->data['input'][$name];
    }

    /**
     * Get the names of all configured outputs.
     *
     * @return array
     */
    public function getAllOutputs()
    {
        return array_keys($this->data['output']);
    }

    /**
     * Get a configured output by name.
     *
     * @param $name
     *
     * @return array
     *
     * @throws OutputNameNotFoundException
     */
    public function getOutput($name)
    {
        $this->outputExists($name);

        return $this->data['output'][$name];
    }

    /**
     * Checks if the given input name exists in the config file.
     *
     * @param $name
     *
     * @throws InputNameNotFoundException
     */
    public function inputExists($name)
    {
        if ($this->keyExists($name)) {
            throw new InputNameNotFoundException($name);
        }
    }

    /**
     * Checks if the given output name exists in the config file.
     *
     * @param $name
     *
     * @throws OutputNameNotFoundException
     */
    public function outputExists($name)
    {
        if ($this->keyExists($name, 'output')) {
            throw new OutputNameNotFoundException($name);
        }
    }

    /**
     * Checks if a key in the config file exists.
     *
     * @param $name
     * @param string $type
     *
     * @return bool
     */
    protected function keyExists($name, $type = 'input')
    {
        return !isset($this->data[$type][$name]);
    }

    /**
     * Checks if the config file exists.
     *
     * @throws ConfigFileNotFoundException
     */
    protected function fileExists()
    {
        if (!file_exists($this->configFile)) {
            throw new ConfigFileNotFoundException($this->configFile);
        }
    }

    /**
     * Reads the config file contents into the $data property.
     */
    protected function readFile()
    {
        $this->data = $this->yamlParser->parse(file_get_contents($this->configFile));
    }
}
