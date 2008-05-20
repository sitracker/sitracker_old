/* Javascript/ECMAscript Functions for SiT (previously known as Webtrack) by Ivan Lucas */
/* Since v3.30 this requires prototype.js */

var popwin;

function incident_details_window(incidentid,win)
{
    // URL = "incident.php?popup=yes&id=" + incidentid;
    URL = "incident_details.php?id=" + incidentid + "&win=" + win;
    if (popwin) { popwin.close(); }
    popwin = window.open(URL, "sit_popup", "toolbar=yes,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=700,height=600");
}


function wt_winpopup(url,mini)
{
    if (mini=='mini')
    {
        window.open(url, "sit_minipopup", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=700,height=600");
    }
    else
    {
        window.open(url, "sit_popup", "toolbar=yes,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=700,height=600");
    }
}


// Yes/No dialog
// @param msg string - A message to display
// @returns bool TRUE or false, depending on which button was pressed, yes = true, false = no
function confirm_action(msg)
{
    if (msg == '') msg = 'Are you sure?';
    return window.confirm(msg);
}


function message_window(userid)
{
    URL = "messages.php?userid=" + userid;
    window.open(URL, "message_window", "toolbar=yes,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=700,height=600");
}


function help_window(helpid)
{
    URL = "/help.php?id=" + helpid;
    window.open(URL, "help_window", "toolbar=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=500,height=500");
}


function appointment(id)
{

    if ($(id).style.visibility=='visible')
    {
        $(id).style.visibility='hidden';
        $(id).style.display='none';
    }
    else
    {
        var parent = $(id).ancestors();
        parent[0].makePositioned();
        $(id).style.visibility='visible';
        $(id).style.display='block';
    }
}


// www.sean.co.uk
function pausecomp(millis)
{
    var date = new Date();
    var curDate = null;

    do
    {
        curDate = new Date();
    } while (curDate-date < millis);
}

function get_and_display(page, component)
{
    pausecomp(25);

    var xmlhttp=false;
    /*@cc_on @*/
    /*@if (@_jscript_version >= 5)
    // JScript gives us Conditional compilation, we can cope with old IE versions.
    // and security blocked creation of the objects.
    try {
    xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
    try {
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    } catch (E) {
    xmlhttp = false;
    }
    }
    @end @*/
    if (!xmlhttp && typeof XMLHttpRequest!='undefined')
    {
        try
        {
            xmlhttp = new XMLHttpRequest();
        }
        catch (e)
        {
            xmlhttp=false;
        }
    }

    if (!xmlhttp && window.createRequest)
    {
        try
        {
            xmlhttp = window.createRequest();
        }
        catch (e)
        {
            xmlhttp=false;
        }
    }

    xmlhttp.open("GET", page, true);

    var rsswindow = byId(component);

    xmlhttp.onreadystatechange=function()
    {
        //remove this in the future after testing
        if (xmlhttp.readyState==4)
        {
            if (xmlhttp.responseText != "")
            {
                //alert(xmlhttp.responseText);
                rsswindow.innerHTML = xmlhttp.responseText;
            }
        }
    }
    xmlhttp.send(null);
}


// This Javascript code placed in the public domain at http://www.irt.org/script/1265.htm
// "Code examples on irt.org can be freely copied and used."

function deleteOption(object,index)
{
    object.options[index] = null;
}

function addOption(object,text,value)
{
    var defaultSelected = true;
    var selected = true;
    var optionName = new Option(text, value, defaultSelected, selected)
    object.options[object.length] = optionName;
}


function copySelected(fromObject,toObject)
{
    for (var i=0, l=fromObject.options.length;i < l;i++)
    {
        if (fromObject.options[i].selected)
        {
            addOption(toObject,fromObject.options[i].text,fromObject.options[i].value);
        }
    }
    for (var i=fromObject.options.length-1;i >-1;i-- )
    {
        if (fromObject.options[i].selected) deleteOption(fromObject,i);
    }
}


function copyAll(fromObject,toObject)
{
    for (var i=0, l=fromObject.options.length;i < l;i++)
    {
        addOption(toObject,fromObject.options[i].text,fromObject.options[i].value);
    }
    for (var i=fromObject.options.length-1;i > -1;i--)
    {
        deleteOption(fromObject,i);
    }
}


function populateHidden(fromObject,toObject)
{
    var output = '';
    for (var i=0, l=fromObject.options.length;i < l;i++)
    {
        output += escape(fromObject.name) + '=' + escape(fromObject.options[i].value) + '&';
    }
    // alert(output);
    toObject.value = output;
}
// ========== END irt.org code


var MIN_ROWS = 3 ;
var MAX_ROWS = 10 ;
var MIN_COLS = 40 ;
var MAX_COLS = 80 ;

function changeTextAreaLength( e )
{
    var txtLength = e.value.length;
    var numRows = 0 ;
    var arrNewLines = e.value.split("\n");

    for(var i=0; i<=arrNewLines.length-1; i++)
    {
        numRows++;
        if (arrNewLines[i].length > MAX_COLS-5)
        {
            numRows += Math.floor(arrNewLines[i].length/MAX_COLS)
        }
    }

    if (txtLength == 0)
    {
        e.cols = MIN_COLS ;
        e.rows = MIN_ROWS ;
    } else
    {
        if (numRows <= 1)
        {
            e.cols = (txtLength % MAX_COLS) + 1 >= MIN_COLS ? ((txtLength % MAX_COLS) + 1) : MIN_COLS ;
        }
        else
        {
            e.cols = MAX_COLS ;
            e.rows = numRows > MAX_ROWS ? MAX_ROWS : numRows ;
        }
    }
}


function resetTextAreaLength ( e )
{
    e.cols = MIN_COLS ;
    e.rows = MIN_ROWS ;
}


function get_random()
{
    var ranNum= Math.floor(Math.random()*1000000000000);
    return ranNum;
}


// Display/Hide the time to next action fields
// Author: Ivan Lucas
function update_ttna() {
    if ($('ttna_time').checked)
    {
        $('ttnacountdown').show();
        $('ttnadate').hide();
    }

    if ($('ttna_date').checked)
    {
        $('ttnacountdown').hide();
        $('ttnadate').show();
    }

    if ($('ttna_none').checked)
    {
        $('ttnacountdown').hide();
        $('ttnadate').hide();
    }
}


function hidecontexthelp(event) {
  var element = event.element();
   element.firstDescendant().style.display = 'none';
  //element.addClassName('active');
//   alert(element);
    element.stopObserving('blur', hidecontexthelp);
    element.stopObserving('click', hidecontexthelp);
}


// find the real position of an element
// http://www.quirksmode.org/js/findpos.html
function findPos(obj) {
    var curleft = curtop = 0;
    if (obj.offsetParent) {
        do {
            curleft += obj.offsetLeft;
            curtop += obj.offsetTop;

        } while (obj = obj.offsetParent);
    }
    return [curleft,curtop];
}


function contexthelp(elem, context)
{
    elem.firstDescendant().style.display = 'block';
    var loadmsg = "Loading...";
    elem.firstDescendant().innerHTML = loadmsg;
    var epos = findPos(elem.firstDescendant());
    var vwidth = document.viewport.getWidth();
    var vheight = document.viewport.getHeight();
    if (epos[0] + 135 > vwidth)
    {
        elem.firstDescendant().style.left = '-125px';
    }
    if (epos[1] + 200 > vheight)
    {
        elem.firstDescendant().style.top = '-200px';
        elem.firstDescendant().style.left = '5px';
    }
    if (elem.firstDescendant().innerHTML == '' || elem.firstDescendant().innerHTML == loadmsg)
    {
        new Ajax.Request(application_webpath + 'ajaxdata.php?action=contexthelp&context=' + context + '&rand=' + get_random(),
        //new Ajax.Request('ajaxdata.php?action=contexthelp&context=' + context + '&rand=' + get_random(),
            {
                method:'get',
                    onSuccess: function(transport)
                    {
                        var response = transport.responseText || "no response text";
                        if (transport.responseText)
                        {
                            elem.firstDescendant().innerHTML = transport.responseText;
                        }
                    },
                    onFailure: function(){ alert('Context Help Error\nSorry, we could not retrieve the help tip') }
            });
    }
    elem.observe('mouseout', hidecontexthelp);
}


function jumpto()
{
    incident_details_window(document.jumptoincident.incident.value, 'incident'+document.jumptoincident.incident.value);
}


function clearjumpto()
{
    document.jumptoincident.incident.value = "";
}

// Unfinished - INL 14May08
function autocomplete(elem, id)
{
    // create a new div if it doesn't already exist
    if ( ! document.getElementById( id ) )
    {
        var newNode = document.createElement( "div" );
        newNode.style.cursor = "pointer";
        newNode.style.border = "1px solid #000";
        newNode.style.zIndex = 10000;
        var suggestDiv = document.body.appendChild( newNode );
        suggestDiv.id = id;
        suggestDiv.className = "autocomplete";
    }
    else
    {
        suggestDiv = $(id);
    }

    if (elem.value == '')  { suggestDiv.innerHTML = 'empty'; }
    else { suggestDiv.innerHTML = 'Code Not finished yet'; }



    var x = elem.offsetLeft;
    var y = elem.offsetTop + elem.offsetHeight;
    var parent = elem;

    while (parent.offsetParent) {
      parent = parent.offsetParent;
      x += parent.offsetLeft;
      y += parent.offsetTop;
    }
    suggestDiv.style.position = "absolute";
    suggestDiv.style.left = x + "px";
    suggestDiv.style.top = y + "px"

}


function confirm_addword()
{
  return window.confirm("If you add this word to the dictionary, all future spell checks will use this as the correct spelling for all users.  Are you sure you want to continue?");
}

function email_window(incidentid)
{
  URL = "email_incident.php?menu=hide&id=" + incidentid;
  window.open(URL, "email_window", "toolbar=yes,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=700,height=600");
}

function close_window(incidentid)
{
  URL = "close_incident.php?menu=hide&id=" + incidentid;
  window.open(URL, "email_window", "toolbar=yes,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=700,height=600");
}

function help_window(helpid)
{
  URL = "help.php?id=" + helpid;
  window.open(URL, "help_window", "toolbar=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=500,height=500");
}

// INL - switch tab, see php function draw_tabs_submit()
function gotab(tab) {
    document.actiontabs.action.value=tab;
    document.actiontabs.submit();
}

function close_page_redirect(url)
{
    window.opener.location=url;
    window.close();
}


/**
 * Below used for selecting GroupMembership from a select field
*/

function doSelect(select, element)
{
    var includes = document.getElementById(element);
    for(i = 0; i < includes.length; i++)
    {
        includes[i].selected = select;
    }
}

function groupMemberSelect(group)
{
    doSelect(false, 'include');
    var includes = document.getElementById('include');
    for(i = 0; i < includes.length; i++)
    {
        if(includes[i].text.indexOf("("+group+")") > -1)
        {
             includes[i].selected = true;
        }
    }
}

function togglePlusMinus(div)
{
    if ($(div).innerHTML == "[+]")
    {
        $(div).innerHTML = '[-]';
    }
    else
    {
        $(div).innerHTML = '[+]';
    }
}

/*
    Collapses or expands kb article sections as needed during edit
    Requires scriptaculous/effects.js
*/
function kbSectionCollapse()
{
    var sections = ['summary', 'symptoms', 'cause', 'question', 'answer', 'solution',
                    'workaround', 'status', 'additionalinfo', 'references'];

    for (var i=0; i <sections.length; i++)
    {
        var span = sections[i] + 'span';
        var section = sections[i] + 'section';
        if ($(sections[i]).value.length > 0)
        {
            //$(sections[i]).show();
            if ($(section).display == 'none') Effect.BlindDown(section, { duration: 0.2 });
            $(span).innerHTML = '[-]';
        }
        else
        {
            //$(sections[i]).hide();
            if ($(section).display != 'none') Effect.BlindUp(section, { duration: 0.2 });
            $(span).innerHTML = '[+]';
        }
    }
}

/*
    Inserts BBCode to a textarea or input
*/
function insertBBCode(element, tag, endtag)
{
    if (element.length > 0)
    {
        var start = $(element).selectionStart;
        var end = $(element).selectionEnd;
        //             alert('start:' + start + '  end: ' + end + 'len: ' + $(element).textLength);
        $(element).value = $(element).value.substring(0, start) + tag + $(element).value.substring(start, end) + endtag + $(element).value.substring(end, $(element).textLength);
    }
    $(element).focus();
    var caret = end + tag.length + endtag.length;
    $(element).selectionStart = caret;
    $(element).selectionEnd = caret;
}
