// This JavaScript date picker  came from an article at
// http://www.dagblastit.com/~tmcclure/dhtml/calendar.html
// The website states
// "You may use the strategies and code in these articles license and royalty free unless otherwise directed.
// "If I helped you build something cool I'd like to hear about it. Drop me a line at tom@dagblastit.com."

// Some functions from here have been moved to calendar.js, the rest are left here unused and
// commented out in case they're needed in the future

/*
// overly simplistic test for IE
isIE = (document.all ? true : false);
// both IE5 and NS6 are DOM-compliant
isDOM = (document.getElementById ? true : false);

// get the true offset of anything on NS4, IE4/5 & NS6, even if it's in a table!
function getAbsX(elt) { return (elt.x) ? elt.x : getAbsPos(elt,"Left"); }
function getAbsY(elt) { return (elt.y) ? elt.y : getAbsPos(elt,"Top"); }
function getAbsPos(elt,which) {
 iPos = 0;
 while (elt != null) {
  iPos += elt["offset" + which];
  elt = elt.offsetParent;
 }
 return iPos;
}

// fixPosition() attaches the element named eltname
// to an image named eltname+'Pos'
//

function fixPosition(divname) {
divstyle = getDivStyle(divname);
positionerImgName = divname + 'Pos';
// hint: try setting isPlacedUnder to false
isPlacedUnder = false;
if (isPlacedUnder) {
setPosition(divstyle,positionerImgName,true);
} else {
setPosition(divstyle,positionerImgName)
}
}




// annoying detail: IE and NS6 store elt.top and elt.left as strings.
function moveBy(elt,deltaX,deltaY) {
 elt.left = parseInt(elt.left) + deltaX;
 elt.top = parseInt(elt.top) + deltaY;
}



function setPosition(elt,positionername,isPlacedUnder) {
 var positioner;
 if (isIE) {
  positioner = document.all[positionername];
 } else {
  if (isDOM) {
    positioner = document.getElementById(positionername);
  } else {
    // not IE, not DOM (probably NS4)
    // if the positioner is inside a netscape4 layer this will *not* find it.
    // I should write a finder function which will recurse through all layers
    // until it finds the named image...
    positioner = document.images[positionername];
  }
 }
 elt.left = getAbsX(positioner);
 elt.top = getAbsY(positioner) + (isPlacedUnder ? positioner.height : 0);
}
*/