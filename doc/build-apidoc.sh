#!/bin/sh


SRCDIR="../Library/"
DEST="./apidoc"

#
# Create HTML output
#

BIN=$( which phpuml )
if [ -z $BIN ]
then
	echo "error: 'phpdoc' is not available"
	exit 1
fi


if [ -d "$DEST" ]
then
	rm -rf "$DEST/*"
else
	mkdir "$DEST"
fi


phpuml "$SRCDIR" -o "$DEST" -f html

cat ./apidoc-override.css >> ./apidoc/style.css