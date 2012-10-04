#!/bin/sh


SRCDIR="../Library/"
DEST="./api-phpdoc"

#
# Create HTML output
#

BIN=$( which phpdoc )
if [ -z $BIN ]
then
	echo "error: 'phpdoc' is not available"
	exit 1
fi


if [ -d "$DEST" ]
then
	rm -rf "$DEST/*"
fi



FILES="*/*.php";

IGNORE="*/tx_next_debuglib.php"


echo "Cmp3 Api Doc"
phpdoc \
	--defaultpackagename Cmp3 \
	--title 'Cmp3' \
	--output HTML:frames:default \
	--ignore "$IGNORE" \
	--directory "$SRCDIR" \
	--filename "$FILES" \
	--target "$DEST"

#	--examplesdir ./source/ \

echo "finished"

