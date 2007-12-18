<?php
// kb_edit_article.php - Form to edit kb article
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


// Authors: Ivan Lucas <ivanlucas[at]users.sourceforge.net>, Tom Gerrard
@include ('set_include_path.inc.php');
$permission=54; // view KB

// uses superglobals.  see http://www.php.net/manual/en/reserved.variables.php#reserved.variables.post

require ('db_connect.inc.php');
require ('functions.inc.php');
// This page requires authentication
require ('auth.inc.php');

// External variables
$process = cleanvar($_REQUEST['process']);
$id = cleanvar($_REQUEST['id']);

// FIXME i18n, KB article sections, summary, symptoms etc.
$sections[]='Summary';
$sections[]='Symptoms';
$sections[]='Cause';
$sections[]='Question';
$sections[]='Answer';
$sections[]='Solution';
$sections[]='Workaround';
$sections[]='Status';
$sections[]='Additional';
$sections[]='References';

if (empty($_POST['process']))
{
    if (empty($id))
    {
        header("Location: browse_kb.php");
        exit;
    }
    $docid = $id;
    include ('htmlheader.inc.php');

    ?>
    <script type="text/javascript">

    </script>
    <?php

    $sql = "SELECT * FROM kbarticles WHERE docid='{$docid}' LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $kbarticle = mysql_fetch_object($result);

    echo "<table summary='Knowledge Base Article' width='98%' align='center' border='0'><tr><td>";

    if (empty($_REQUEST['user']) OR $_REQUEST['user']=='current') $user=$sit[2];
    else $user=$_REQUEST['user'];

    $softsql = "SELECT * FROM kbsoftware, software WHERE kbsoftware.softwareid=software.id AND kbsoftware.docid='{$docid}' ORDER BY name";
    $softresult = mysql_query($softsql);
    if (mysql_error()) trigger_error("MySQL Error: ".mysql_error(),E_USER_ERROR);
    if (mysql_num_rows($result) >= 1)
    {
        while ($software = mysql_fetch_object($softresult))
        {
            $expertise[]=$software->id;
        }
    }
    echo "<p align='center'>Select the skills that apply to this article</p>"; // FIXME i18n, select skills that apply
    echo "<form name='kbform' action='{$_SERVER['PHP_SELF']}' method='post' onsubmit=\"populateHidden(document.kbform.elements['expertise[]'],document.kbform.choices)\">";
    echo "<table align='center'>";
    echo "<tr><th>Does NOT apply</th><th>&nbsp;</th><th>Applies</th></tr>"; // FIXME i18n applies, does not apply
    echo "<tr><td align='center' width='300' class='shade1'>";
    $listsql = "SELECT * FROM software ORDER BY name";
    $listresult = mysql_query($listsql);
    if (mysql_error()) trigger_error("MySQL Error: ".mysql_error(),E_USER_ERROR);
    if (mysql_num_rows($listresult) >= 1 )
    {
        echo "<select name='software' multiple='multiple' size='7'>";
        while ($software = mysql_fetch_object($listresult))
        {
            if (is_array($expertise)) { if (!in_array($software->id,$expertise)) echo "<option value='{$software->id}'>$software->name</option>\n";  }
            else  echo "<option value='{$software->id}'>{$software->name}</option>\n";
        }
        echo "</select>";
    }
    else echo "<p class='error'>No software found</p>";
    echo "</td>";
    echo "<td class='shade1'>";
    echo "<input type='button' value='&gt;' onclick=\"copySelected(this.form.software,this.form.elements['expertise[]'])\" /><br />";
    echo "<input type='button' value='&lt;' onclick=\"copySelected(this.form.elements['expertise[]'],this.form.software)\" /><br />";
    echo "<input type='button' value='&gt;&gt;' onclick=\"copyAll(this.form.software,this.form.elements['expertise[]'])\" /><br />";
    echo "<input type='button' value='&lt;&lt;' onclick=\"copyAll(this.form.elements['expertise[]'],this.form.software)\" /><br />";
    echo "</td>";
    echo "<td width='300' class='shade2'>";
    $softsql = "SELECT * FROM kbsoftware, software WHERE kbsoftware.softwareid=software.id AND docid='{$docid}' ORDER BY name";
    $softresult = mysql_query($softsql);
    if (mysql_error()) trigger_error("MySQL Error: ".mysql_error(),E_USER_ERROR);
    echo "<select name='expertise[]' multiple='multiple' size='7'>";
    if (mysql_num_rows($softresult) < 1) echo "<option value=''></option>\n";
    while ($software = mysql_fetch_object($softresult))
    {
        echo "<option value='{$software->id}' selected='selected'>{$software->name}</option>\n";
    }
    // echo "<option value='0'>---</option>\n";
    echo "</select>";
    echo "<input type='hidden' name='userid' value='{$user}' />";
    echo "</td></tr>\n";
    ?>
    </table>
    <br />
    <input type="hidden" name="choices" />
    <!-- <input name="submit" type="submit" value="Save" /> -->

    <!-- </form> -->
    <?php

    // Lookup what software this applies to
    /*
    $ssql = "SELECT * FROM kbsoftware, software WHERE kbsoftware.softwareid=software.id AND kbsoftware.docid='{$docid}' ORDER BY software.name";
    $sresult = mysql_query($ssql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    if (mysql_num_rows($sresult) >= 1)
    {
        echo "<p>The information in this article applies to:</p>\n";
        echo "<ul>\n";
        while ($kbsoftware = mysql_fetch_object($sresult))
        {
            echo "<li>{$kbsoftware->name}</li>\n";
        }
        echo "</ul>\n";
    }
    else
    {
        echo "<p>This article is not linked to any software</p>";
    }
    */

    // ---
    // echo "<form name='kbform' action='{$_SERVER['PHP_SELF']}' method='post' >";
    echo "<p align='center'>{$strTitle}:<br /><input type='text' name='title' size='60' value=\"{$kbarticle->title}\" /></p>";
    echo "<p align='center'>{$strKeywords}:<br /><input type='text' name='keywords' value='{$kbarticle->keywords}' size='60' /></p>";

    echo "\n<script type=\"text/javascript\">\n";
    echo "<!--\n";
    echo "function change_header(element,headertext)\n";
    echo "{\n";
    echo "  var headerelement = 'header' + element; \n";
    // echo "alert (headerelement);";
    echo "  var headersize = document.getElementById(headerelement).options[document.getElementById(headerelement).selectedIndex].value; \n";
    // echo "alert (headersize);";
    // echo "  var headersize = \"h3\"; \n";
    echo "  var content=\"<\" + headersize + \" style='margin:0px; display:inline'>\" + headertext + \"</\" + headersize + \">\"; \n";

    echo "  document.getElementById(element).innerHTML=content;  \n";
    echo "}\n";
    echo "// -->\n";
    echo "</script>\n";

    echo "<table summary='' width='95%' align='center'>";
    foreach ($sections AS $section)
    {
        // summary
        $csql = "SELECT * FROM kbcontent WHERE docid='{$docid}' AND header='$section' ";
        $cresult = mysql_query($csql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        $num_rows = mysql_num_rows($cresult);
        if ($num_rows >= 1)
        {
            while ($kbcontent = mysql_fetch_object($cresult))
            {
                $element=$kbcontent->id;
                echo "<tr><th class='shade1' valign='top'>";
                echo "{$kbcontent->header}:</th>";
                echo "<td class='shade2'>";
                echo "<textarea name='content$element' rows='10'  cols='100' title='Full Details'>";
                echo $kbcontent->content;
                echo "</textarea>\n<br /><br />\n";
                $author[]=$kbcontent->ownerid;
                $id_array[]= $kbcontent->id;
                echo "</td><td class='shade1'>";
                echo distribution_listbox("distribution$element",$kbcontent->distribution);
                echo "<br /><label><input type='checkbox' name='delete{$element}' value='yes' />{$strDelete}</label></td></tr>";
            }
        }
        else
        {
            echo "<tr><th valign='top'>";
            echo "$section:";
            echo "</th><td class='shade2'>";
            echo "<textarea name='content{$section}' rows='2' cols='100' title='Full Details' onfocus=\"myInterval = window.setInterval('changeTextAreaLength(document.kbform.content{$section})', 300);\" onblur=\"window.clearInterval(myInterval); resetTextAreaLength(document.kbform.content{$section});\">";
            echo "</textarea>\n<br /><br />\n";
            echo "</td><td class='shade1'><label><input type='checkbox' name='add$section' value='yes' />{$strAdd}</label></td></tr>\n";
        }
    }
    reset ($sections);
    if (is_array($id_array)) $id_list=implode(",",$id_array);
    else $id_array='';
    echo "<tr><td class='shade1' colspan='3'>";
    echo "<input type='hidden' name='articleid' value='{$docid}' />";
    echo "<input type='hidden' name='idlist' value='$id_list' />";
    echo "<input type='hidden' name='process' value='true' />";
    echo "</td></tr></table>";
    echo "<p><input type='submit' value='{$strSave}' /></p>";
    echo "</form>";

    echo "</td></tr></table>";
    include ('htmlfooter.inc.php');
}
else
{
    // Update the database

    // External variables
    $idlist = $_POST['idlist'];
    $title = cleanvar($_POST['title']);
    $keywords = cleanvar($_POST['keywords']);
    $articleid = cleanvar($_POST['articleid']);

    $allowable_html_tags="<em><strong><cite><dfn><code><samp><kbd><var><abbr><acronym><q><blockquote><sub><sup><p><br /><ins><del><ul><li><ol><pre>";
    $idlist=explode(',',$idlist);
    foreach ($idlist AS $id)
    {
        $cfieldname = "content$id";
        $dfieldname = "delete$id";
        $sql = "UPDATE kbarticles SET title='$title', keywords='$keywords' WHERE docid='{$articleid}'";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        $content = cleanvar($_REQUEST[$cfieldname],FALSE,FALSE);
        $content = strip_tags($content,$allowable_html_tags);
        $hfieldname = "header$id";
        $headerstyle = cleanvar($_REQUEST[$hfieldname]);
        $distfield = "distribution$id";
        $distribution = cleanvar($_REQUEST[$distfield]);
        if (empty($headerstyle)) $headerstyle='h3';
        if ($_REQUEST[$dfieldname]!='yes') {

            $sql = "UPDATE kbcontent SET content='$content', headerstyle='h1', distribution='$distribution' WHERE id='$id' AND docid='{$articleid}' ";
        }
        else
            $sql = "DELETE FROM kbcontent WHERE id='$id' AND docid='{$_REQUEST['articleid']}' ";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    }
    // Add new content if any

    foreach ($sections AS $section)
    {
        if ($_REQUEST["add$section"]=='yes')
        {
            $content = mysql_real_escape_string($_REQUEST["content$section"]);
            $content = strip_tags($content,$allowable_html_tags);
            $sql = "INSERT into kbcontent (content, header, headerstyle, distribution, docid) VALUES ('$content','$section','h1','private','{$articleid}') ";
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        }
    }

    // Remove associated software ready for re-assocation
    $sql = "DELETE FROM kbsoftware WHERE docid='{$articleid}'";
    mysql_query($sql);

    if (is_array($_POST['expertise']))
    {
        $expertise=array_unique(($_POST['expertise']));
        foreach ($expertise AS $value)
        {
            $sql = "INSERT INTO kbsoftware (docid, softwareid) VALUES ('{$articleid}', '$value')";
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        }
    }
    header("Location: kb_view_article.php?id={$articleid}");
    exit;
}
?>
