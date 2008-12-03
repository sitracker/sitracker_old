#!/bin/bash
kdiff3 --merge $1.merge-left.r* $1.merge-right.* $1.working -o $1 && svn resolved $1

