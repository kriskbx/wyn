<?php

namespace kriskbx\wyn\Sync;

use kriskbx\wyn\Contracts\Sync\SyncSettings as SyncSettingsContract;
use ReflectionClass;

/**
 * Class SyncSettings.
 */
class SyncSettings implements SyncSettingsContract
{
    /**
     * Skip errors on the input side?
     *
     * @belongsTo input
     *
     * @var bool
     */
    public $ignoreInput;

    /**
     *  Skip errors on the output side?
     *
     * @belongsTo output
     *
     * @var bool
     */
    public $ignoreOutput;

    /**
     * Delete files?
     *
     * @belongsTo output
     *
     * @var bool
     */
    public $delete;

    /**
     * Exclude on the input side.
     *
     * @belongsTo input
     *
     * @var array
     */
    public $excludeInput;

    /**
     * Exclude on the output side.
     *
     * @belongsTo output
     *
     * @var array
     */
    public $excludeOutput;

    /**
     * @belongsTo output
     *
     * @var bool
     */
    public $versioning;

    /**
     * @belongsTo output
     *
     * @var bool
     */
    public $encrypt;

    /**
     * SyncSettings constructor.
     *
     * @param array $excludeInput
     * @param array $excludeOutput
     * @param bool  $skipInputErrors
     * @param bool  $skipOutputErrors
     * @param bool  $delete
     * @param bool  $versioning
     * @param bool  $encrypt
     */
    public function __construct(
            $excludeInput = [], $excludeOutput = [], $skipInputErrors = true, $skipOutputErrors = true,
            $delete = true, $versioning = false, $encrypt = false
    ) {
        $this->excludeInput = $excludeInput;
        $this->excludeOutput = $excludeOutput;
        $this->ignoreInput = $skipInputErrors;
        $this->ignoreOutput = $skipOutputErrors;
        $this->delete = $delete;
        $this->versioning = $versioning;
        $this->encrypt = $encrypt;
    }

    /**
     * Automagically set and get properties.
     *
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        if ($this->hasProperty($name)) { // GET
            return $this->$name;
        } elseif (
            substr($name, 0, 3) === 'set'
            && $this->hasProperty($realName = lcfirst(substr($name, 3)))
        ) { // SET
            if (isset($arguments[0])) {
                $this->$realName = $arguments[0];
            }
        } else {
            throw new \BadMethodCallException();
        }
    }

    /**
     * Has this class the given property?
     *
     * @param $name
     *
     * @return bool
     */
    protected function hasProperty($name)
    {
        $reflection = new ReflectionClass(__CLASS__);

        return $reflection->hasProperty($name);
    }

    /**
     * Get options of this input.
     *
     * @return array
     */
    public static function getOutputBaseOptions()
    {
        return ['exclude', 'ignore', 'delete', 'versioning', 'encrypt'];
    }

    /**
     * Get options of this input.
     *
     * @return array
     */
    public static function getInputBaseOptions()
    {
        return ['exclude', 'ignore'];
    }
}
