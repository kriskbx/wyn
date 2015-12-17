<?php

namespace kriskbx\wyn\Contracts\Middleware;

use kriskbx\wyn\Contracts\Sync\Sync as SyncContract;

interface Middleware
{
    /**
     * Before process.
     *
     * @param SyncContract $sync
     */
    public function beforeProcess(SyncContract &$sync);

    /**
     * After process.
     *
     * @param SyncContract $sync
     */
    public function afterProcess(SyncContract &$sync);
}
