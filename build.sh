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

git add -A .
git commit -m "release $newversion"
git tag "$newversion"