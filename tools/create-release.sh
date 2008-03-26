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
# Requirements:
#    svn2cl

TMPDIR=/tmp/sit.$$/build
PUBDIR=/tmp/sit.$$/packages

mkdir -p $TMPDIR
cd $TMPDIR

# We checkout rather than export so we can determine the svn revision
svn co --non-interactive https://sitracker.svn.sourceforge.net/svnroot/sitracker/trunk sit
cd sit

SVNREV=`svnversion .`

# Now we've got the revision we can get rid of all the ".svn" directories.
find -name "\.svn" | xargs rm -rf

# Now grab the app version number
SITVER=$(grep ^\$application_version.= includes/functions.inc.php|awk -F "'|'" '{print $2}')

# Now find out if this is a proper release
SITREV=$(grep ^\$application_revision.= includes/functions.inc.php|awk -F "'|'" '{print $2}')

SITSIZE=`du -sk | cut -f1`

# Now we've got version and rev Create the release name
if [ -n $SITREV ]; then
  	RELNAME="sit_$SITVER"
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


echo "Creating release: $RELNAME"

cd ..
pwd

# Tidy the dirname
SITDIR="sit-$SITVER"
mv sit $SITDIR

mkdir -p "$PUBDIR"

# Create a source tar file
tar -czf "$RELNAME.orig.tar.gz" $SITDIR

echo "sit ($SITVER) unstable; urgency=low" > "$SITDIR/DEBIAN/changelog"

# TODO determine the svn rev of the previous release so we can...
# TODO append the svn changelog *since the last release only* into the debian/changelog file
#svn2cl -o debian/changelog

# build a .deb package

sed -i 's/!SITVERSION!/'$SITVER'/g' $TMPDIR/$SITDIR/DEBIAN/control
sed -i 's/!SITSIZE!/'$SITSIZE'/g' $TMPDIR/$SITDIR/DEBIAN/control

mkdir -p /tmp/sit.$$/deb/etc/
mkdir -p /tmp/sit.$$/deb/usr/share/sit/

cp -r $TMPDIR/$SITDIR/attachments /tmp/sit.$$/deb/usr/share/sit/
cp -r $TMPDIR/$SITDIR/dashboard /tmp/sit.$$/deb/usr/share/sit/
cp -r $TMPDIR/$SITDIR/doc /tmp/sit.$$/deb/usr/share/sit/
cp -r $TMPDIR/$SITDIR/htdocs /tmp/sit.$$/deb/usr/share/sit/
cp -r $TMPDIR/$SITDIR/includes /tmp/sit.$$/deb/usr/share/sit/
cp -r $TMPDIR/$SITDIR/index.php /tmp/sit.$$/deb/usr/share/sit/
cp -r $TMPDIR/$SITDIR/plugins /tmp/sit.$$/deb/usr/share/sit/
cp -r $TMPDIR/$SITDIR/README /tmp/sit.$$/deb/usr/share/sit/
cp -r $TMPDIR/$SITDIR/DEBIAN /tmp/sit.$$/deb/
cp -r $TMPDIR/$SITDIR/conf/etc /tmp/sit.$$/deb/

sudo chown -R root:root /tmp/sit.$$/deb/usr
sudo chown -R root:root /tmp/sit.$$/deb/etc
chmod 755 /tmp/sit.$$/deb/DEBIAN/post*

# Make a debian package
dpkg -b /tmp/sit.$$/deb/ $PUBDIR/$SITDIR.deb

# Make a tar.gz package
cp $TMPDIR/$RELNAME.orig.tar.gz $PUBDIR/$RELNAME.tar.gz
