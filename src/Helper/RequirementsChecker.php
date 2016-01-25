<?php

namespace kriskbx\wyn\Helper;

use kriskbx\wyn\Exceptions\RequirementNotFulfilledException;
use kriskbx\wyn\Exceptions\WrongArgumentException;
use Symfony\Component\Process\ExecutableFinder;

class RequirementsChecker {
	/**
	 * @var array
	 */
	protected static $globalRequirements = [
		'phpVersion' => 'PHP version must be >= 5.6',
		'extOpenssl' => 'PHP extension openssl must be installed and loaded',
		'extMCrypt'  => 'PHP extension mcrypt must be installed and loaded',
	];

	/**
	 * @var array
	 */
	protected static $specificRequirements = [
		'PDO'  => 'PHP extension PDO must be installed and loaded',
		'git'  => 'Git must be installed and running',
		'ssh2' => 'PECL ssh2 must be >= 0.9.0'
	];

	/**
	 * Check globalRequirements or a single specific requirement.
	 *
	 * @param string|array $name
	 *
	 * @throws RequirementNotFulfilledException
	 * @throws WrongArgumentException
	 */
	public static function check( $name = null ) {
		if ( ! $name ) {
			foreach ( static::$globalRequirements as $requirement => $message ) {
				self::checkSingle( $requirement );
			}

			return;
		}

		if ( is_string( $name ) ) {
			self::checkSingle( $name );

			return;
		}

		if ( is_array( $name ) ) {
			foreach ( $name as $requirement ) {
				self::checkSingle( $requirement );
			}

			return;
		}

		throw new WrongArgumentException( 'name' );
	}

	/**
	 * Check a single requirement by the given name.
	 *
	 * @param $requirement
	 *
	 * @throws RequirementNotFulfilledException
	 */
	protected static function checkSingle( $requirement ) {
		if ( ! call_user_func( 'self::' . $requirement ) ) {
			$message = ( isset( static::$globalRequirements[ $requirement ] ) ? static::$globalRequirements[ $requirement ] : static::$specificRequirements[ $requirement ] );
			throw new RequirementNotFulfilledException( $message );
		}
	}

	/**
	 * @return bool
	 */
	protected static function ssh2() {
		return extension_loaded( 'ssh2' ) && phpversion( 'ssh2' ) >= '0.9.0';
	}

	/**
	 * @return bool
	 */
	protected static function git() {
		$finder = new ExecutableFinder();

		return $finder->find( 'git' ) ? true : false;
	}

	/**
	 * @return bool
	 */
	protected static function PDO() {
		return extension_loaded( 'PDO' ) && extension_loaded( 'pdo_mysql' );
	}

	/**
	 * @return bool
	 */
	protected static function extMCrypt() {
		return extension_loaded( 'mcrypt' );
	}

	/**
	 * @return bool
	 */
	protected static function extOpenssl() {
		return extension_loaded( 'openssl' );
	}

	/**
	 * @return bool
	 */
	protected static function phpVersion() {
		return phpversion() >= '5.6.0';
	}
}
