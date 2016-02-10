#!/bin/bash

###########
# CS FIXING
###########
echo "Started CS fixing process..."
vendor/bin/php-cs-fixer fix .
echo ""

###########
# ENTER VERSION
###########
lastversion=$(<VERSION)
echo "Last version was: $lastversion"
echo "Enter the new version:"
read newversion
echo "$newversion" > VERSION
echo ""

###########
# CHANGELOG
###########
echo "Enter changelog added: (press CTRL+D when finished)"
added=$(cat)
echo "Enter changelog changed: (press CTRL+D when finished)"
changed=$(cat)
echo "Enter changelog fixed: (press CTRL+D when finished)"
fixed=$(cat)

echo "" >> CHANGELOG.md
DATE=`date +%Y-%m-%d`
echo "## [$newversion] - $DATE" >> CHANGELOG.md

if [ -n "$added" ]; then
echo "### Added
$added" >> CHANGELOG.md
fi

if [ -n "$fixed" ]; then
echo "### Fixed
$fixed" >> CHANGELOG.md
fi

if [ -n "$fixed" ]; then
echo "### Changed
$changed" >> CHANGELOG.md
fi

###########
# CREATE PHAR
###########

echo "Creating phar..."
php -d phar.readonly=off resource/bin/phar-composer.phar build . ./build/wyn.phar

###########
# CREATE DOCKER IMAGE
###########

echo "Creating Docker Image"
docker build -t kriskbx/wyn:$newversion .
docker rm wyn || echo "container doesn't exists"
docker run --name wyn -t -i kriskbx/wyn:$newversion /bin/bash

###########
# GIT TAG
###########
git add -A .
git commit -m "release $newversion"
git tag "$newversion"