<?php

namespace kriskbx\wyn\Contracts\Sync;

interface SyncSettings
{
    /**
     * Get options of this input.
     *
     * @return array
     */
    public static function getOutputBaseOptions();

    /**
     * Get options of this input.
     *
     * @return array
     */
    public static function getInputBaseOptions();
}
