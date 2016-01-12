# BackupPipes Roadmap

## Planned platforms to backup from

* Mail via IMAP
* Mail via POP3
* MySQL via direct connection
* MySQL via socket
* MySQL via SSH (direct connection to 127.0.0.1 or socket)
* MySQL via FTP and HTTP (uploads a simple php script that acts as an API to the database, get the data via multiple http requests. should work even on crappy shared-hosts.)
* Calendar via CalDAV (Google, Owncloud, Baikal, etc.)
* Contacts via CardDAV (Google, Owncloud, Baikal, etc.)
* Gitlab.com or selfhosted Gitlab repository with Issues, Wiki, Merge Requests, Snippets
* Github.com repository with Issues, Pull Requests
* Dropbox
* Amazon S3 V2/V3
* Azure
* Copy.com
* FTP **since 0.2**
* SFTP **since 0.1**
* GridFS
* Rackspace
* WebDAV
* PHPCR
* Local Filesystem **since 0.1**
* Openshift

## Planned platforms to backup to

* Dropbox
* Amazon S3 V2/V3
* Azure
* Copy.com
* FTP **since 0.2**
* SFTP **since 0.2**
* GridFS
* Rackspace
* WebDAV
* PHPCR
* Local Filesystem **since 0.1**
* Local ZipArchive

## Upcoming features

* Encrypt backup **since 0.1**
* Archive backup
* Archive Versioning
* SVN Versioning
* Define one or multiple outputs for each input in config
* Define multiple outputs for single command
* Add `all` command to backup all inputs with preconfigured outputs
* Add `cron` command and add configuration options to backup inputs only at specified times/days/weeks
* At least 50% coverage
* Make a build script to create a backuppipes.phar for a composer-less but easy installation