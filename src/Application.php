<?php

namespace kriskbx\wyn;

use kriskbx\wyn\Contracts\Middleware\Middleware as MiddlewareContract;
use kriskbx\wyn\Contracts\Sync\Sync as SyncContract;
use kriskbx\wyn\Contracts\Input\Input as InputContract;
use kriskbx\wyn\Contracts\Output\Output as OutputContract;
use kriskbx\wyn\Contracts\Sync\SyncOutput as SyncOutputContract;
use kriskbx\wyn\Contracts\Sync\SyncSettings as SyncSettingsContract;
use kriskbx\wyn\Exceptions\PropertyNotSetException;
use kriskbx\wyn\Sync\Output\SyncNullOutput;
use kriskbx\wyn\Sync\Sync;
use kriskbx\wyn\Sync\SyncManager;
use kriskbx\wyn\Sync\SyncSettings;
use kriskbx\wyn\Sync\SyncWorker;

/**
 * wyn.
 *
 * All-in-one Backup CLI. I made this thing because standard-mysqldump-rsync-backup-stuff sucks and I'm tired of
 * incomplete backup-tools from other devs/vendors/whatever.
 * Alles muss man selber machen lassen - https://www.youtube.com/watch?v=dapqMeQCdcs
 */
class Application
{
    /**
     * Middlewares.
     *
     * @var MiddlewareContract[]
     */
    protected $middleware = [];

    /**
     * Sync.
     *
     * @var SyncContract
     */
    protected $sync;

    /**
     * Application Constructor.
     *
     * @param SyncContract $sync
     */
    public function __construct(SyncContract $sync = null)
    {
        $this->sync = $sync;
        date_default_timezone_set('UTC');
    }

    /**
     * Create a new default sync instance.
     *
     * @param InputContract        $inputHandler
     * @param OutputContract       $outputHandler
     * @param SyncOutputContract   $outputHelper
     * @param SyncSettingsContract $settings
     *
     * @return SyncContract
     */
    public static function createSync(
        InputContract $inputHandler, OutputContract $outputHandler, SyncSettingsContract $settings = null, SyncOutputContract &$outputHelper = null
    ) {
        if (is_null($outputHelper)) {
            $outputHelper = new SyncNullOutput();
        }

        if (!$settings) {
            $settings = new SyncSettings();
        }

        $inputHandler->applySettings($settings);
        $outputHandler->applySettings($settings);

        return new Sync(
            new SyncManager($inputHandler, $outputHandler, $settings),
            new SyncWorker($inputHandler, $outputHandler, $settings),
            $outputHelper,
            $settings
        );
    }

    /**
     * Create a new default sync instance and save it on this object.
     *
     * @param InputContract        $inputHandler
     * @param OutputContract       $outputHandler
     * @param SyncOutputContract   $outputHelper
     * @param SyncSettingsContract $settings
     *
     * @return $this
     */
    public function create(
        InputContract $inputHandler, OutputContract $outputHandler, SyncSettingsContract $settings = null, SyncOutputContract &$outputHelper = null
    ) {
        $this->sync = self::createSync(
            $inputHandler,
            $outputHandler,
            $settings,
            $outputHelper
        );
    }

    /**
     * Add Middleware.
     *
     * @param MiddlewareContract $middleware
     *
     * @return $this
     */
    public function middleware(MiddlewareContract $middleware)
    {
        $this->middleware[] = $middleware;

        return $this;
    }

    /**
     * Run the Middleware processes and then our actual sync process.
     *
     * @throws PropertyNotSetException
     */
    public function run()
    {
        if (!isset($this->sync)) {
            throw new PropertyNotSetException('sync');
        }

        $this->sortMiddleware();

        foreach ($this->middleware as $index => $middleware) {
            $middleware->beforeProcess($this->sync);
        }

        $this->sync->init();
        $this->sync->sync();

        foreach ($this->middleware as $index => $middleware) {
            $middleware->afterProcess($this->sync);
        }
    }

    /**
     * Sort middleware by priority.
     */
    protected function sortMiddleware()
    {
        usort($this->middleware, function ($a, $b) {
            if ($a->priority() == $b->priority()) {
                return 0;
            }

            return ($a->priority() < $b->priority()) ? -1 : 1;
        });
    }
}
