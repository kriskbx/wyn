<?php

namespace kriskbx\wyn\Middleware;

use kriskbx\wyn\Application;
use kriskbx\wyn\Contracts\Sync\Sync as SyncContract;
use kriskbx\wyn\Contracts\Sync\SyncOutput as SyncOutputContract;
use kriskbx\wyn\Contracts\Sync\SyncSettings as SyncSettingsContract;
use kriskbx\wyn\Contracts\Versioning\Versioning as VersioningContract;
use kriskbx\wyn\Output\LocalOutput;
use kriskbx\wyn\Sync\Output\SyncOutput;
use kriskbx\wyn\Sync\SyncSettings;

class VersioningMiddleware extends Middleware
{
    /**
     * Priority.
     *
     * @var int
     */
    protected $priority = 20;

    /**
     * Git.
     *
     * @var VersioningContract
     */
    protected $git;

    /**
     * VersioningMiddleware Constructor.
     *
     * @param VersioningContract $git
     */
    public function __construct(VersioningContract $git)
    {
        $this->git = $git;
    }

    /**
     * Before process.
     *
     * @param SyncContract $sync
     */
    public function beforeProcess(SyncContract &$sync)
    {
        // Get settings and output
        $settings = $sync->getSettings();
        $output = $sync->getOutputHelper();
        $outputHandler = $sync->getOutput();

        // Local or remote?
        if ($outputHandler instanceof LocalOutput) {
            $this->local($sync, $output, $outputHandler, $settings);
        } else {
            $this->remote($sync, $output, $settings);
        }
    }

    /**
     * After process.
     *
     * @param SyncContract $sync
     */
    public function afterProcess(SyncContract &$sync)
    {
        // Get output
        $output = $sync->getOutputHelper();
        $outputHandler = $sync->getOutput();

        // Local or remote?
        if ($outputHandler instanceof LocalOutput) {
            $this->commit($output);
        }
    }

    /**
     * The output is a remote one.
     * So we need to sync to a local directory first,
     * commit the changes and sync to changes to the
     * actual output.
     *
     * @param SyncContract         $sync
     * @param SyncOutputContract   $output
     * @param SyncSettingsContract $settings
     */
    protected function remote(SyncContract &$sync, SyncOutputContract $output, SyncSettingsContract $settings)
    {
        // Display starting msg
        $output->out(SyncOutput::VERSIONING_SYNC_TO_LOCAL);

        // Set git to remote
        $this->git->remote();
        $this->git->init();

        // Sync to local git repo
        $localSettings = new SyncSettings(
            $settings->excludeInput(),
            [
                '.git/**/*',
                '**/.gitignore',
                '**/.gitkeep',
            ],
            $settings->skipInputErrors(),
            true,
            true
        );
        $localSync = Application::createSync($sync->getInput(), $this->git->getOutputHandler(), $localSettings, $sync->getOutputHelper());
        $localSync->init();
        $localSync->sync();

        // Free some RAM
        unset($localSync);

        $output->out(SyncOutput::MISC_LINE_BREAK);

        // Commit
        $this->commit($output);

        $output->out(SyncOutput::VERSIONING_SYNC_TO_OUTPUT);

        // Modify existing sync object to take the local git repository as input
        $sync->setInput($this->git->getInputHandler());
        $sync->setSettings(new SyncSettings([], $settings->excludeOutput(), true, $settings->skipOutputErrors(), true));
    }

    /**
     * This is a local one.
     * Just sync and commit then.
     *
     * @param SyncContract         $sync
     * @param SyncOutputContract   $output
     * @param LocalOutput          $outputHandler
     * @param SyncSettingsContract $settings
     */
    protected function local(SyncContract &$sync, SyncOutputContract $output, LocalOutput $outputHandler, SyncSettingsContract $settings)
    {
        // Set git to local
        $this->git->local($outputHandler);
        $this->git->init();

        // Override the settings for versioning
        $settings->setExcludeOutput(array_merge($settings->excludeOutput(), [
            '.git/**/*',
            '**/.gitignore',
            '**/.gitkeep',
        ]));
        $settings->setDelete(true);
        $sync->setSettings($settings);
    }

    /**
     * Commit.
     *
     * @param $output
     */
    protected function commit(SyncOutputContract $output)
    {
        if ($this->git->hasChanges()) {
            $commit = $this->git->commit();

            // Display the output
            if (isset($commit['name']) && isset($commit['data'])) {
                $output->out($commit['name'], $commit['data']);
            }
        }

        // Free some RAM
        unset($commit);
    }
}
