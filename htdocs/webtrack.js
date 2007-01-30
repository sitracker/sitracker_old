/* Javascript/ECMAscript Functions for SiT (previously known as Webtrack) by Ivan Lucas */

function incident_details_window(incidentid,win)
{
    // URL = "incident.php?popup=yes&id=" + incidentid;
    if(win=='holdingview')
        URL = "incident_details.php?id=" + incidentid + "&javascript=enabled&view=lockedview";
    else
        URL = "incident_details.php?id=" + incidentid + "&javascript=enabled";
    window.open(URL, "sit_popup", "toolbar=yes,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=700,height=600");
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
