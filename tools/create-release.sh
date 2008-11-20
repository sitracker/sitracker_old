#!/bin/bash
## Generates Debian packages for SiT
## SiT (Support Incident Tracker) - Support call tracking system
## Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
##
## This software may be used and distributed according to the terms
## of the GNU General Public License, incorporated herein by reference.
##
## Authors: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
##          Paul Heaney <paulheaney[at]users.sourceforge.net>
##          Kieran Hogg <kieran_hogg[at]users.sourceforge.net>
# Requirements:
#    svn        svn2cl     wget

TMPDIR=/tmp/sit.$$/build
PUBDIR=/tmp/sit.$$/packages

mkdir -p $TMPDIR
cd $TMPDIR

args=("$@")

# See if a different branch or tag was specified, otherwise checkout trunk
if [ -z ${args[0]} ]; then
    SVNBRANCH="trunk"
else
    SVNBRANCH=${args[0]}
fi

echo "Checking out to $TMPDIR"

# We checkout rather than export so we can determine the svn revision
svn co --non-interactive https://sitracker.svn.sourceforge.net/svnroot/sitracker/$SVNBRANCH sit > /dev/null
cd sit

SVNREV=`svnversion .`

LASTRELVER=`wget -q -O - https://sourceforge.net/export/rss2_projfiles.php?group_id=160319 | grep -m1 "<title>stable" | grep -o "[0-9]\.[0-9][0-9]"`
if [ -z LASTRELVER ]; then
   LASTRELVER="3.30"
fi

# Get revision number of a branch
LASTRELREV=`svn info https://sitracker.svn.sourceforge.net/svnroot/sitracker/tags/release-$LASTRELVER 2> /dev/null | grep 'Rev:' | cut -d' ' -f4`
echo "Last release was $LASTRELVER rev $LASTRELREV"

#svn2cl -o DEBIAN/changelog
# svn2cl --reparagraph --include-rev -o DEBIAN/changelog
# svn2cl --group-by-day --include-rev -o DEBIAN/changelog
# -i -r "HEAD:{`date -d '7 days ago' '+%F %T'`}" 
#svn2cl ---revision HEAD:$LASTRELREV -group-by-day --include-rev --authors doc/AUTHORS -o DEBIAN/changelog

svn2cl --revision HEAD:$LASTRELREV --group-by-day --include-rev --authors "doc/AUTHORS" -o "$TMPDIR/changelog"

# Now grab the app version number
SITVER=$(grep ^\$application_version.= includes/functions.inc.php|awk -F "'|'" '{print $2}')

# Now prepend the version number to the changlog
echo -e "sit ($SITVER) unstable; urgency=low\n\n" > "DEBIAN/changelog"
cat "$TMPDIR/changelog" >> "DEBIAN/changelog"
rm "$TMPDIR/changelog"

# Now find out if this is a proper release
SITREV=$(grep ^\$application_revision.= includes/functions.inc.php|awk -F "'|'" '{print $2}')

SITSIZE=`du -sk | cut -f1`

# Now we've got version and rev Create the release name
if [ -n $SITREV ]; then
  	RELNAME="sit_$SITVER"
elif [ $SITREV = "beta1" ]; then
        RELNAME="sit_$SITVER+$SITREV"
elif [ $SITREV = "beta2" ]; then
        RELNAME="sit_$SITVER+$SITREV"
elif [ $SITREV = "beta3" ]; then
        RELNAME="sit_$SITVER+$SITREV"
elif [ $SITREV = "beta4" ]; then
        RELNAME="sit_$SITVER+$SITREV"
elif [ $SITREV = "svn" ]; then
        RELNAME="sit_$SITVER+$SITREV$SVNREV"
else
        RELNAME="sit_$SITVER+$SITREV"
fi



# Now we check there are no parse errors before building package

ERR=`find $1 -type f -name \*.php -exec sh -c 'php -l {} | grep -v "No syntax errors"' \;|tee /tmp/sit.$$/phpcheck|wc|awk '{print $1}'`
if [ $ERR -ne 0 ]; then
    echo "Syntax error detected, failing";
    echo "Please check /tmp/sit.$$/phpcheck";
    #exit 1;
fi

# Now we've got the revision we can get rid of all the ".svn" directories.
find -name "\.svn" | xargs rm -rf

echo "Creating release: $RELNAME"

cd ..
pwd

# Tidy the dirname
SITDIR="sit-$SITVER"
mv sit $SITDIR

mkdir -p "$PUBDIR"

# Create a source tar file
tar -czf "$RELNAME.orig.tar.gz" $SITDIR

echo "Create .deb files? y/n"
read -e DEB
if [$DEB != "n" ]; then
	# build a .deb package
	cd $SITDIR
	echo "Creating Ubuntu .deb..."
	mv debian/changelog.ubuntu debian/changelog
	rm -r tools/
	rm htdocs/scripts/prototype/*
	rm htdocs/scripts/scriptaculous/*
	dch -i
	echo "Upload to PPA repo? y/n"
	read -e PPA
	if [ $PPA != "n" ]; then
		debuild -S -sa
		dput sit-ppa ../sit_$SITVER-0ubuntu1_source.changes
		echo "Package uploaded"
	fi
	echo "Building Ubuntu package..."
	debuild

	echo "Building Debian package..."
	mv /debian/changelog.debian debian/changelog
	dch -i
	debuild
fi
# Make a tar.gz package
cp $TMPDIR/$RELNAME.orig.tar.gz $PUBDIR/$RELNAME.tar.gz

echo "Release created in /tmp/sit.$$/";
