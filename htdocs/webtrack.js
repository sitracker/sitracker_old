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


function byId(id)
{
    return document.getElementById(id);
}


// www.sean.co.uk
function pausecomp(millis)
{
    var date = new Date();
    var curDate = null;

    do { curDate = new Date(); }
    while (curDate-date < millis);
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


function toggleDiv(obj)
{
    var el = document.getElementById(obj);
    if ( el.style.display != 'none' )
    {
        el.style.display = 'none';
    }
    else
    {
        el.style.display = '';
    }
}

function confirm_submit(text)
{
    //return window.confirm('Are you sure you want to make these changes?');
    return window.confirm(text);
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

function changeTextAreaLength ( e )
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

