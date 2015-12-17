<?php

namespace kriskbx\wyn\Contracts\Versioning;

use kriskbx\wyn\Contracts\Input\Input as InputContract;
use kriskbx\wyn\Contracts\Output\Output as OutputContract;
use kriskbx\wyn\Output\LocalOutput;

interface Versioning
{
    /**
     * Create directory if it does not exist and init the repository if it's not a repository.
     */
    public function init();

    /**
     * Get path to local repo.
     *
     * @return string
     */
    public function getLocalRepoPath();

    /**
     * Has changes?
     *
     * @return bool
     */
    public function hasChanges();

    /**
     * Commit.
     */
    public function commit();

    /**
     * @return OutputContract
     */
    public function getOutputHandler();

    /**
     * @return InputContract
     */
    public function getInputHandler();

    /**
     * Set settings for local output.
     *
     * @return $this;
     */
    public function local(LocalOutput $output);

    /**
     * Set settings for remote output.
     *
     * @return $this;
     */
    public function remote();
}
