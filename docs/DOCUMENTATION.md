# wyn docs

## Table of contents

1. [Requirements](#requirements)
    1. [Docker install](#docker-install)
    2. [Composer or Phar install](#composer-or-phar-install)
2. [Features](#features)
    1. [Supported platforms to backup from](#supported-platforms-to-backup-from)
    2. [Supported platforms to backup to](#supported-platforms-to-backup-to)
3. [Installation](#installation)
    1. [Using Docker (recommended)](#using-docker-recommended)
    2. [As a Phar](#as-a-phar)
    3. [Via composer](#via-composer)
4. [Usage](#usage)
    1. [General](#general)
    2. [Cron](#cron)
5. [Example config](#example-config)
6. [Options](#options)
    1. [General](#general-1)
    2. [Input](#input)
    3. [Output](#output)
7. [Shared Provider Options](#shared-provider-options)
    1. [Local Filesystem](#local-filesystem)
    2. [SFTP](#sftp)
    3. [FTP](#ftp)
    4. [Dropbox](#dropbox)
    5. [Copy.com](#copycom)
    6. [GridFS](#gridfs)
8. [Input Provier Options](#input-provider-options)
    1. [MySQL](#mysql)
9. [Using wyn within a framework or existing application](#using-wyn-within-a-framework-or-existing-application)
10. [Contributing](#contributing)
11. [FAQs](#faqs)
12. [License](#license)

## Requirements

### Docker install

* Docker, yay

### Composer or Phar install

* Git
* PHP 5.6 or later
* PDO
* ext-openssl
* ext-mcrypt
* ext-ssh2 >= 0.9.0

## Features

### Supported platforms to backup from

* MySQL via direct connection or socket
* Local filesystem
* FTP
* SFTP

### Supported platforms to backup to

* Local filesystem
* FTP
* SFTP
* Dropbox
* copy.com
* GridFS

## Installation

### Using Docker (recommended)

Install Docker first:

```
curl -sSL https://get.docker.com/ | sh
```

#### Store config inside container

You should mount a data directory when adding the container if you want to store data on your host machine. Replace `/path/to/data/on/host` with the absolute path to the storage on your host machine.

```
docker \
    run -d --name wyn --restart always \
    -v /path/to/data/on/host:/var/wyn \
    kriskbx/wyn:latest
```

You can edit the configuration by running:

```
docker exec -it wyn wyn edit
```

#### Store config on host machine (recommended)

OR you can mount a config directory and store the configuration on your host machine. Replace `/path/to/config/on/host` with an absolute path to your config directory.

```
docker \
    run -d --name wyn --restart always \
    -v /path/to/data/on/host:/var/wyn \
    -v /path/to/config/on/host:/root/.wyn \
    kriskbx/wyn:latest
```

### As a phar

On unix system run this:

```
curl -sS https://raw.githubusercontent.com/kriskbx/wyn/master/build/wyn.phar > /usr/local/bin/wyn && chmod a+x /usr/local/bin/wyn
```

Or download it manually and move it into your `$PATH`.

### Via composer

1. Make sure you already have composer installed globally. If not, check out [this guide](https://getcomposer.org/doc/00-intro.md#globally).
2. Make sure `~/.composer/vendor/bin/` is in your `$PATH`.
3. Run this to install wyn:

```
composer global require kriskbx/wyn
```

## Usage

### General

Create a global configuration file and open it in your default editor by running this command:

```
# Manual install
wyn edit

# Docker install
docker exec -it wyn wyn edit
```

Backup from a single input to a single output as specified in the global configuration:

```
# Manual install
wyn backup:single input output

# Docker install
docker exec -it wyn wyn backup:single input output
```

Or use a specific config file:

```
# Manual install
wyn backup:single input output /path/to/config/file.yml

# Docker install
docker exec -it wyn wyn backup:single input output /path/to/config/file.yml
```

If you configured one or multiple outputs for the given input in your config file you can also run this:

```
# Manual install
wyn backup:single input

# Docker install
docker exec -it wyn wyn backup:single input
```

Or you can backup all inputs that got configured outputs:

```
# Manual install
wyn backup:all

# Docker install
docker exec -it wyn wyn backup:all
```

### Cron

If you installed wyn using Docker you don't need to setup anything. It will run the cron command automatically every minute for you as long as the container runs.

You can specify cron expressions for every input in your config file. Run `crontab -e` and add this command to your crontab file then:

```
* * * * * wyn backup:cron
```

wyn will backup all inputs with configured cron expressions now on a regular basis. You can even log the output to a file of your choice (I recommend using the more verbose output here):

```
* * * * * wyn backup:cron --verbose >> /path/to/your/logfile 2>&1
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
    cron: '* 0 * * *'
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
| `timeout` | Timeout in seconds. If you killed a running job by hand or a job timed out this is the time till you can start the same job again | Integer | 600 | N |
| `timezone` | PHP-Timezone Identifier | String | Europe/Berlin | N |
| `cronConfig` | Path to the directory where the cron-system stores it temporary data | String | ~/.wyn/ | N |

### Input

| Name | Description | Type | Default | Required |
|------|-------------|------|---------|----------|
| `provider` | Specifies the provider | String | - | Y |
| `ignore` | Skip errors when reading the input, otherwise stop the whole process | Boolean | true | N |
| `exclude` | Exclude files and folders that match the given globs | Array | - | N |
| `to` | Specify one or multiple outputs for this input | Array/String | - | N |
| `cron` | Cron expression, make sure to escape it using quotes | String | - | N |
| `checkFileSize` | Compare file size of input and output files | Boolean | true | N |

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

### Dropbox

| Name | Description | Type | Default | Required |
|------|-------------|------|---------|----------|
| `accessToken` | Dropbox API access token | String | / | Y |
| `appSecret` | Dropbox API app secret | String | / | Y |
| `prefix` | Path prefix | String | / | N |

### Copy.com

| Name | Description | Type | Default | Required |
|------|-------------|------|---------|----------|
| `consumerKey` | Consumer key | String | / | Y |
| `consumerSecret` | Consumer secret | String | / | Y |
| `accessToken` | Access token | String | / | Y |
| `tokenSecret` | Token secret | String | / | Y |
| `prefix` | Path prefix | String | / | N |

### GridFS

| Name | Description | Type | Default | Required |
|------|-------------|------|---------|----------|
| `database` | Database | String | / | Y |

## Input Provider Options

### MySQL

| Name | Description | Type | Default | Required |
|------|-------------|------|---------|----------|
| `host` | Host name | String | / | Y |
| `user` | User name | String | / | Y |
| `password` | Password | String | / | Y |
| `database` | Name of the database or an Array of database names | String|Array | / | N |
| `port` | Name of the database or an Array of database names | Integer | 3306 | N |
| `socket` | Will be used if set | String | / | N |
| `compress` | Compress the output. Can be either: None, Gzip, Bzip | String | None | N |
| `noData` | Skip dumping the data and only dump table structure | Boolean | false | N |
| `addDropTable` | Add drop table clauses | Boolean | false | N |
| `singleTransaction` | Single transaction | Boolean | true | N |
| `lockTables` | Lock tables | Boolean | false | N |
| `addLocks` | Add locks | Boolean | true | N |
| `extendedInsert` | Extended insert | Boolean | true | N |
| `disableKeys` | Disable Keys | Boolean | true | N |
| `noCreateInfo` | No create info | Boolean | false | N |
| `skipTriggers` | Skip triggers | Boolean | false | N |
| `addDropTrigger` | Skip triggers | Boolean | true | N |
| `routines` | Routines | Boolean | false | N |
| `hexBlob` | Hex Blob | Boolean | true | N |
| `skipTzUtc` | Skip tc utc | Boolean | false | N |
| `noAutoCommit` | No auto commit | Boolean | true | N |
| `skipComments` | Skip comments | Boolean | false | N |
| `skipDumpDate` | Skip dump date | Boolean | false | N |
| `defaultCharacterSet` | Default charset | String | 'utf8' | N |

## Using wyn within a framework or existing application

You can use wyn as a library in your custom application. Filesystem indexing and copying can last long, you will probably reach the max execution timeout - so use it only within a CLI.

### Installation via composer

Add this to your `composer.json` and run `composer install` afterwards:

```
"require": {
   "kriskbx/wyn": "0.3.*"
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

### Why PHP and not [INSERT RANDOM LANGUAGE HERE]?

Coz I like PHP.

## License

GPL v2
