#!/bin/bash

# Generates Debian packages for SiT
# Authors: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
#          Paul Heaney <paulheaney[at]users.sourceforge.net>

# Requirements:
#    svn2cl

TMPDIR=/tmp/sit/build
PUBDIR=/tmp/sit/packages

mkdir -p $TMPDIR
cd $TMPDIR

# We checkout rather than export so we can determine the svn revision
svn co --non-interactive https://sitracker.svn.sourceforge.net/svnroot/sitracker/trunk sit
cd sit

SVNREV=`svnversion .`

# Now we've got the revision we can get rid of all the ".svn" directories.
find -name "\.svn" | xargs rm -rf

# Now grab the app version number
SITVER=$(grep ^\$application_version= includes/functions.inc.php|awk -F "'|'" '{print $2}')

# Now find out if this is a proper release 
SITREV=$(grep ^\$application_revision= includes/functions.inc.php|awk -F "'|'" '{print $2}')

# Now we've got version and rev Create the release name
if [ -n $SITREV ]; then
  	RELNAME="sit_$SITVER"
elif [ $SITREV = "svn" ]; then
	RELNAME="sit_$SITVER+$SITREV$SVNREV"
else 
	RELNAME="sit_$SITVER+$SITREV"
fi



# Now we check there are no parse errors before building package
## for i in $( find . -name "*.php"  ); do php -l $i; done | grep -v "No syntax err"
## TODO make this quit if it finds an error

echo "Creating release: $RELNAME"

cd ..
pwd

# Tidy the dirname
SITDIR="sit-$SITVER"
mv sit $SITDIR

mkdir -p "$PUBDIR"

# Create a source tar file
tar -czf "$RELNAME.orig.tar.gz" $SITDIR

echo "sit ($SITVER) unstable; urgency=low" > "$SITDIR/debian/changelog"

# TODO determine the svn rev of the previous release so we can...
# TODO append the svn changelog *since the last release only* into the debian/changelog file
#svn2cl -o debian/changelog

# TODO build a .deb package
