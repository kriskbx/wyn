#!/bin/bash

echo "Started CS fixing process..."

vendor/bin/php-cs-fixer fix .

echo ""

lastversion=$(<VERSION)
echo "Last version was: $lastversion"

echo "Enter the new version:"
read newversion
echo "$newversion" > VERSION

echo ""

DATE=`date +%Y-%m-%d`

echo "Enter changelog added: (press CTRL+D when finished)"
added=$(cat)

echo "Enter changelog changed: (press CTRL+D when finished)"
changed=$(cat)

echo "Enter changelog fixed: (press CTRL+D when finished)"
fixed=$(cat)

echo "" >> CHANGELOG.md
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

git add -A .
git commit -m "release $newversion"
git tag "$newversion"