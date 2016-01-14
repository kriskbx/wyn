<?php

namespace kriskbx\wyn\Contracts\Config;

use kriskbx\wyn\Exceptions\InputNameNotFoundException;
use kriskbx\wyn\Exceptions\OutputNameNotFoundException;

interface Config {
	/**
	 * Get the names of all configured inputs.
	 *
	 * @return array
	 */
	public function getAllInputs();

	/**
	 * Get the data of a configured input by name.
	 *
	 * @param string $name
	 *
	 * @return array
	 *
	 * @throws InputNameNotFoundException
	 */
	public function getInput( $name );

	/**
	 * Get the names of all configured outputs.
	 *
	 * @return array
	 */
	public function getAllOutputs();

	/**
	 * Get the data of a configured output by name.
	 *
	 * @param $name
	 *
	 * @return array
	 *
	 * @throws OutputNameNotFoundException
	 */
	public function getOutput( $name );

	/**
	 * Checks if the given input name exists in the config.
	 *
	 * @param $name
	 *
	 * @throws InputNameNotFoundException
	 */
	public function inputExists( $name );

	/**
	 * Checks if the given output name exists in the config.
	 *
	 * @param $name
	 *
	 * @throws OutputNameNotFoundException
	 */
	public function outputExists( $name );

	/**
	 * Get general option.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function getOption( $key );

	/**
	 * Has general option?
	 *
	 * @param string $key
	 *
	 * @return bool
	 */
	public function hasOption( $key );
}
