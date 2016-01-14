# wyn docs

## Requirements

* PHP 5.6 or later
* ext-openssl
* ext-mcrypt

### Supported platforms to backup from

* Local filesystem
* FTP
* SFTP

### Supported platforms to backup to

* Local filesystem
* FTP
* SFTP

## Installation

### Via composer

1. Make sure you already have composer installed globally. If not, check out [this guide](https://getcomposer.org/doc/00-intro.md#globally).
2. Make sure `~/.composer/vendor/bin/` is in your `PATH` environment variable.
3. Run this to install wyn:

```
composer global require kriskbx/wyn
```

### Other install methods

Coming soon.

## Usage

### General

Create a global configuration file and open it in your default editor by running this command:

```
wyn edit
```

Backup from a single input to a single output as specified in the global configuration:

```
wyn backup:single input output
```

Or use a specific config file:

```
wyn backup:single input output /path/to/config/file.yml
```

If you configured one or multiple outputs for the given input in your config file you can also run this:

```
wyn backup:single input
```

Or you can backup all inputs that got configured outputs:

```
wyn backup:all
```

### Cron

You can specify cron expressions for every input in your config file. Run `crontab -e` and add this command to your crontab file then:

```
* * * * * wyn backup:cron
```

wyn will run all inputs with configured cron expressions now on a regular basis. You can even log the output to a file of your choice (I recommend using the more verbose output there):

```
* * * * * wyn backup:cron --verbose >> /path/to/your/logfile 2>&1
```

## Update

Run this to update wyn to the newest version:

```
composer global update
```

## Example config

```
options:
  timeout: 600
input:
  uniqueNameForThisInput:
    provider: local
    path: /home/username/sync-input
    ignore: true
    exclude:
     - .git/**/*
     - **/.gitignore
     to: uniqueNameForThisOutput
output:
  uniqueNameForThisOutput:
    provider: local
    path: /home/username/sync-output
    ignore: true
    delete: true
    exclude:
     - .git/**/*
     - vendor/**/*
    versioning: false
    encrypt: false

```

## Options

### General

| Name | Description | Type | Default | Required |
|------|-------------|------|---------|----------|
| `timeout` | Timeout in seconds. If you killed a running job by hand this is the time till you can start the same job again | Integer | 600 | N |
| `timezone` | PHP-Timezone Identifier | String | 'Europe/Berlin' | N |
| `cronConfig` | Path to the directory where the cron-system stores it temporary data | String | '~/.wyn/' | N |

### Input

| Name | Description | Type | Default | Required |
|------|-------------|------|---------|----------|
| `provider` | Specifies the provider | String | - | Y |
| `ignore` | Skip errors when reading the input, otherwise stop the whole process | Boolean | true | N |
| `exclude` | Exclude files and folders that match the given globs | Array | - | N |
| `to` | Specify one or multiple outputs for this input | Array/String | - | N |
| `cron` | Cron expression, make sure to escape it using quotes | String | - | N |

### Output

| Name | Description | Type | Default | Required |
|------|-------------|------|---------|----------|
| `provider` | Specifies the provider | String | - | Y |
| `ignore` | Skip errors when writing the output, otherwise stop the whole process | Boolean | true | N |
| `exclude` | Exclude files and folders that match the given globs | Array | - | N |
| `delete` | Delete files that are not present on the input side anymore. If versioning is used this will be ignored. | Boolean | true | N |
| `versioning` | Choose from one of the following versioning engines: `git` | String/Booleam | false | N |
| `encrypt` | If `true` it uses a global encryption key stored in *~/.wyn/encryption_key* (wyn will create one for you automagically), if it's an `absolute path` it will take the contents of the provided file, if it's a simple `string` it will use the string to encrypt your data. | String/Boolean | false | N |

## Shared Provider Options

### Local Filesystem

| Name | Description | Type | Default | Required |
|------|-------------|------|---------|----------|
| `path` | Absolute path | String | - | Y |

### SFTP

| Name | Description | Type | Default | Required |
|------|-------------|------|---------|----------|
| `path` | Absolute path on server | String | - | Y |
| `host` | Server hostname | String | - | Y |
| `username` | Username | String | - | Y |
| `password` | Password | String | - | N |
| `privateKey` | Absolute path to private key file | String | - | N |
| `timeout` | Connection timeout in seconds | Integer | 10 | N |
| `port` | SSH port | Integer | 22 | N |

### FTP

| Name | Description | Type | Default | Required |
|------|-------------|------|---------|----------|
| `root` | Absolute root path on server | String | / | Y |
| `host` | Server hostname | String | - | Y |
| `username` | Username | String | - | Y |
| `password` | Password | String | - | N |
| `timeout` | Connection timeout in seconds | Integer | 30 | N |
| `ssl` | Use SSL? | Boolean | true | N |
| `passive` | Use passive mode | Boolean | true | N |
| `port` | SSH port | Integer | 21 | N |

## Using wyn within a framework or existing application

You can use wyn as a library in your custom application. Filesystem indexing and copying can last long, you will probably reach the max execution timeout - so use it only within a CLI.

### Installation via composer

Add this to your `composer.json` and run `composer install` afterwards:

```
"require": {
   "kriskbx/wyn": "0.1.*"
}

```

### Usage

#### Default

If you want to use the default functionality, simply create input- and outputhandlers and add them to a new application:

```
use kriskbx\wyn\Application;
use kriskbx\wyn\Input\LocalInput;
use kriskbx\wyn\Output\LocalOutput;

// Define handlers
$input = new LocalInput('/home/username/sync-input');
$output = new LocalOutput('/home/username/sync-output');

// Create application
$app = new Application();
$app->create($input, $output);

// Run the sync
$app->run();
```

Take a look at the constructors of both input and output to configure everything to your needs. It's pretty self-explanatory. You can override the configuration stored in input and output by passing a *SyncSettings* object to the `$app->create()` method. This is used in the real application to override the configuration with command line options.

#### Using middleware

wyn comes with a concept called `Middleware`. Middlewares logic runs before and after the actual sync and can alter/change the `Sync` object. `GitVersioning` is the only middleware available at the moment. In the future there will be encryption/decryption/archiving and many more.

If you want to use versioning, create a new middleware and add it to your app before calling the `run()` method on it:

```

// ...

$app->middleware(new VersioningMiddleware(new GitVersioning('uniqueNameForThisGitRepository'))));

$app->run();

```

#### Custom outputhandler

If you want to use the output of the sync process in your app, make sure to create a new class that extends `kriskbx\wyn\Sync\SyncOutput`:

```
use kriskbx\wyn\Sync\Output\SyncOutput;

class MyCustomOutput extends SyncOutput
{

    /**
     * @param string|array $input
     * @param bool         $newLine
     *
     * @return string
     */
    public function write($input, $newLine = true) {
    	// do something with the input string, process it, log it, whatever you want
    }

    /**
     * @param string $name
     * @param array $data
     *
     * @return string
     */
    public function getText($name, $data = []) {
    	// $name will be one of the following: newFiles, filesToUpdate, filesToDelete, newFile, updateFile, deleteFile, lineBreak, gitInit, gitCommit, gitSync
    	// $data includes additional data from the current process
    }

}

```

You should take a look at the existing output classes in `src/Sync/Output` they will help you a lot creating your own outputhandler.

Then, add it to your app as the fourth argument:

```
// ...

$app->create($input, $output, [new SyncSettings() || null], $outputHandler)

// ...

```

#### Modifing the application to your needs

Most of the classes in wyn are coded to an interface. So you can replace them with your own. For example: you don't want wyn to check which files are new, which need an update and which should be deleted. You can create the `Sync` object on your own and pass it to the constructor of the `Application`. Provide a custom `SnycManager` (this class is responsible for doing the exact thing mentioned above) that gets the file-list from the source of your choice.

Take a closer look at the command classes to see how I used the whole thing there.

## Contributing

At the moment a lot of functionality is not yet implemented. We need a lot more middlewares for encryption/decryption/archiving/svn-versioning/tarball-versioning/etc. We also need a lot more input- and outputhandlers and a ton more unit tests. I already started with some tests, but I suck at writing tests - so this is the best thing you can contribute. :D

I've created a gulp task to automate the testing process. Just install the node dependencies `npm install` and run the gulp task `gulp`. It will automatically monitor file changes and runs phpspec.

### Rules

* Make sure to work in a new branch that describes your feature: `unit-tests` or `svn-middleware`
* If you're contributing new features, write some simple tests too
* Create a pull-request with a poem in it (just kidding)

## FAQs

### What about restoring?

This is even more frustrating than backing things up. The answer is **no**. I won't to do that in the near future. You can rollback things by hand if you need to.

## License

GPL v2
