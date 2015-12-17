<?php

namespace kriskbx\wyn\Versioning;

use GitWrapper\GitWrapper;
use kriskbx\wyn\Config\GlobalConfig;
use kriskbx\wyn\Contracts\Versioning\Versioning as VersioningContract;
use kriskbx\wyn\Input\LocalInput;
use kriskbx\wyn\Output\LocalOutput;
use kriskbx\wyn\Contracts\Input\Input as InputContract;
use kriskbx\wyn\Contracts\Output\Output as OutputContract;

class GitVersioning implements VersioningContract
{
    /**
     * Name of the configured input.
     *
     * @var string
     */
    protected $input;

    /**
     * Versioning Wrapper.
     *
     * @var VersioningContract
     */
    protected $git;

    /**
     * Output handler for the local git repository.
     *
     * @var LocalOutput
     */
    protected $outputHandler;

    /**
     * Input handler for the local git repository.
     *
     * @var LocalInput
     */
    protected $inputHandler;

    /**
     * Path to the directory to store repositories in.
     *
     * @var null|string
     */
    protected $gitDir;

    /**
     * GitWrapper.
     *
     * @var GitWrapper
     */
    protected $gitWrapper;

    /**
     * GitVersioning constructor.
     *
     * @param string $input
     * @param string $gitDir
     * @param int    $timeout
     */
    public function __construct($input, $gitDir = null, $timeout = 300)
    {
        $this->input = $input;
        $this->gitDir = $gitDir;
        $this->gitWrapper = new GitWrapper();
        $this->gitWrapper->setTimeout($timeout);
    }

    /**
     * Local output.
     *
     * @return $this;
     */
    public function local(LocalOutput $output)
    {
        $this->gitDir = $output->getPath();

        return $this;
    }

    /**
     * Remote output.
     *
     * @return $this;
     */
    public function remote()
    {
        $this->outputHandler = new LocalOutput($this->getLocalRepoPath(), ['.git/**/*', '.gitignore']);
        $this->inputHandler = new LocalInput($this->getLocalRepoPath());

        return $this;
    }

    /**
     * Get path to local git repository.
     *
     * @return string
     */
    public function getLocalRepoPath()
    {
        $gitDir = ((@file_exists($this->gitDir) && @is_dir($this->gitDir)) ? $this->gitDir : GlobalConfig::getGitDir().DIRECTORY_SEPARATOR.$this->input);

        return $gitDir.DIRECTORY_SEPARATOR;
    }

    /**
     * Has changes?
     *
     * @return bool
     */
    public function hasChanges()
    {
        return $this->git->hasChanges();
    }

    /**
     * Commit.
     */
    public function commit()
    {
        $this->git->add('.', ['all' => null])->commit('backup '.date('Y-m-d H:i:s'));

        return ['name' => 'gitCommit', 'data' => ['gitOutput' => $this->git->getOutput()]];
    }

    /**
     * Get outputHandler.
     *
     * @return OutputContract
     */
    public function getOutputHandler()
    {
        return $this->outputHandler;
    }

    /**
     * Get inputHandler.
     *
     * @return InputContract
     */
    public function getInputHandler()
    {
        return $this->inputHandler;
    }

    /**
     * Create directory if it does not exist and init the repository if it's not a repository.
     */
    public function init()
    {
        if (!file_exists($localGitPath = $this->getLocalRepoPath())) {
            mkdir($localGitPath);
        }

        if (!file_exists($localGitPath.'.git')) {
            $git = $this->gitWrapper->init($localGitPath);
            $git->config('user.name', 'backuppipes')
                ->config('user.email', 'backuppipes@domain.com');
        } else {
            $git = $this->gitWrapper->workingCopy($localGitPath);
        }

        $this->git = $git;
    }
}
