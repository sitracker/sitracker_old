/* Javascript/ECMAscript Functions for SiT (previously known as Webtrack) by Ivan Lucas */
/* Since v3.30 this requires prototype.js */

var popwin;

function incident_details_window(incidentid,win)
{
    // URL = "incident.php?popup=yes&id=" + incidentid;
    URL = "incident_details.php?id=" + incidentid + "&win=" + win;
    if(popwin) { popwin.close(); }
    popwin = window.open(URL, "sit_popup", "toolbar=yes,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=700,height=600");
}


function wt_winpopup(url,mini)
{
  if (mini=='mini')
    window.open(url, "sit_minipopup", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=700,height=600");
  else
    window.open(url, "sit_popup", "toolbar=yes,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=700,height=600");
}

// Yes/No dialog
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

// Enable/Disable the contact address
function togglecontactaddress()
{
    if (document.contactform.usesiteaddress.checked==true)
    {
        document.contactform.address1.disabled=false;
        document.contactform.address2.disabled=false;
        document.contactform.city.disabled=false;
        document.contactform.county.disabled=false;
        document.contactform.country.disabled=false;
        document.contactform.postcode.disabled=false;
    }
    else
    {
        document.contactform.address1.disabled=true;
        document.contactform.address2.disabled=true;
        document.contactform.city.disabled=true;
        document.contactform.county.disabled=true;
        document.contactform.country.disabled=true;
        document.contactform.postcode.disabled=true;
    }
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


function byId(id){
    return document.getElementById(id);
}

function get_and_display(page, component){
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
    if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
        try {
            xmlhttp = new XMLHttpRequest();
        } catch (e) {
            xmlhttp=false;
        }
    }
    if (!xmlhttp && window.createRequest) {
        try {
            xmlhttp = window.createRequest();
        } catch (e) {
            xmlhttp=false;
        }
    }

    xmlhttp.open("GET", page, true);

    var rsswindow = byId(component);

    xmlhttp.onreadystatechange=function() {
        //remove this in the future after testing
        if (xmlhttp.readyState==4) {
            if(xmlhttp.responseText != ""){
                //alert(xmlhttp.responseText);
                rsswindow.innerHTML = xmlhttp.responseText;
            }
        }
    }
    xmlhttp.send(null);
}

function toggleDiv(obj) {
    var el = document.getElementById(obj);
    if ( el.style.display != 'none' ) {
        el.style.display = 'none';
    }
    else {
        el.style.display = '';
    }
}