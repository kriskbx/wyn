<?php

namespace kriskbx\wyn\Sync;

use Exception;
use kriskbx\wyn\Contracts\Input\Input as InputContract;
use kriskbx\wyn\Contracts\Output\Output as OutputContract;
use kriskbx\wyn\Contracts\Sync\SyncSettings as SyncSettingsContract;

/**
 * Trait SyncSettingsConsumer, should only be used on an input or output handler.
 */
trait SyncSettingsConsumer
{
    /**
     * Apply the given settings to a consumer: either input or output.
     *
     * @param SyncSettingsContract $settings
     *
     * @throws Exception
     */
    public function applySettings(SyncSettingsContract $settings)
    {
        if (in_array(InputContract::class, class_implements($this))) {
            $type = 'Input';
        } elseif (in_array(OutputContract::class, class_implements($this))) {
            $type = 'Output';
        } else {
            throw new Exception('SyncSettingsConsumer must be used on an input or output.');
        }

        $options = call_user_func([$settings, 'get'.$type.'BaseOptions']);

        foreach ($settings as $key => $setting) {
            if (strstr($key, 'exclude') || strstr($key, 'ignore')) {
                $key = str_replace($type, '', $key);
            }

            if (in_array($key, $options)) {
                call_user_func_array([$this, 'setConfig'], [$key, $setting]);
            }
        }
    }
}
