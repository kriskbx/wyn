# wyn roadmap

## Planned platforms to backup from

* Mail via IMAP
* Mail via POP3
* ~~MySQL via direct connection~~ **since 0.4**
* ~~MySQL via socket~~ **since 0.4**
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
* ~~FTP~~ **since 0.2**
* ~~SFTP~~ **since 0.1**
* GridFS
* Rackspace
* WebDAV
* PHPCR
* ~~Local Filesystem~~ **since 0.1**
* Openshift

## Planned platforms to backup to

* ~~Dropbox~~ **since 0.6**
* Amazon S3 V2/V3
* Azure
* ~~Copy.com~~ **since 0.6**
* ~~FTP~~ **since 0.2**
* ~~SFTP~~ **since 0.2**
* ~~GridFS~~ **since 0.6**
* Rackspace
* WebDAV
* PHPCR
* ~~Local Filesystem~~ **since 0.1**

## Upcoming features

* ~~Encrypt backup~~ **since 0.1**
* Archive backup
* SVN Versioning
* Tar ball Versioning
* ~~Define one or multiple outputs for each input in config~~ **since 0.3**
* Define multiple outputs for single command
* ~~Add `all` command to backup all inputs with preconfigured outputs~~ **since 0.3**
* ~~Add `cron` command and add configuration options to backup inputs only at specified times/days/weeks~~ **since 0.3**
* At least 50% coverage
* ~~Make a build script to create a phar for a composer-less but easy installation~~ **since 0.5**