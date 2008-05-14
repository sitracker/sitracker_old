<?php
// kb_article.php - Form to add a knowledgebase article
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Authors: Kieran Hogg, <kieran_hogg[at]users.sourceforge.net>
//          Ivan Lucas <ivanlucas[at]users.sourceforge.net>
//          Tom Gerrard <tomgerrard[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
$permission = 54; // view KB

require ('db_connect.inc.php');
require ('functions.inc.php');
// This page requires authentication
require ('auth.inc.php');
if (!empty($_GET['id']))
{
    $mode = 'edit';
    $kbid = intval($_GET['id']);
}
else
{
    $mode = 'new';
}

if (isset($_POST['submit']) AND $kbid > 0)
{
    echo 'edit';
    //edit
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
        $sql = "UPDATE `{$dbKBArticles}` SET title='{$title}', keywords='{$keywords}' WHERE docid='{$articleid}'";
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

            $sql = "UPDATE `{$dbKBContent}` SET content='{$content}', headerstyle='h1', distribution='{$distribution}' WHERE id='$id' AND docid='{$articleid}' ";
        }
        else
            $sql = "DELETE FROM `{$dbKBContent}` WHERE id='$id' AND docid='{$_REQUEST['articleid']}' ";
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
            $sql = "INSERT INTO `{$dbKBContent}` (content, header, headerstyle, distribution, docid) VALUES ('$content','$section','h1','private','{$articleid}') ";
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        }
    }

    // Remove associated software ready for re-assocation
    $sql = "DELETE FROM `{$dbKBSoftware}` WHERE docid='{$articleid}'";
    mysql_query($sql);

    if (is_array($_POST['expertise']))
    {
        $expertise=array_unique(($_POST['expertise']));
        foreach ($expertise AS $value)
        {
            $sql = "INSERT INTO `{$dbKBSoftware}` (docid, softwareid) VALUES ('{$articleid}', '$value')";
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        }
    }
    header("Location: kb_view_article.php?id={$articleid}");
    exit;
}
elseif(isset($_POST['submit']))
{
    //new
    $title = cleanvar($_POST['title']);
    $distribution = cleanvar($_POST['distribution']);
    $keywords = cleanvar($_POST['keywords']);
    $summary = cleanvar($_POST['summary'],FALSE,FALSE);
    $symptoms = cleanvar($_POST['symptoms'],FALSE,FALSE);
    $cause = cleanvar($_POST['cause'],FALSE,FALSE);
    $question = cleanvar($_POST['question'],FALSE,FALSE);
    $answer = cleanvar($_POST['answer'],FALSE,FALSE);
    $solution = cleanvar($_POST['solution'],FALSE,FALSE);
    $workaround = cleanvar($_POST['workaround'],FALSE,FALSE);
    $status = cleanvar($_POST['status'],FALSE,FALSE);
    $additional = cleanvar($_POST['additional'],FALSE,FALSE);
    $references = cleanvar($_POST['references'],FALSE,FALSE);

    $_SESSION['formdata']['kb_add_article'] = $_POST;

    $errors = 0;
    if ($title == "")
    {
        $_SESSION['formerrors']['kb_add_article']['title'] = "Title cannot be empty";
        $errors++;
    }
    if ($keywords == "")
    {
        $_SESSION['formerrors']['kb_add_article']['keywords'] = "Keywords cannot be empty";
        $errors++;
    }

    if ($errors == '0')
    {
        $sql = "INSERT INTO `{$dbKBArticles}` (doctype, title, distribution, author, published, keywords) VALUES ";
        $sql .= "('1', ";
        $sql .= "'{$title}', ";
        $sql .= "'{$distribution}', ";
        $sql .= "'{$sit[2]}', ";
        $sql .= "'".date('Y-m-d H:i:s', mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('Y')))."', ";
        $sql .= "'{$keywords}') ";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        $docid = mysql_insert_id();

        // Force private if not specified
        if (empty($_POST['distribution'])) $_POST['distribution']='private';

        if (!empty($summary)) $query[]="INSERT INTO `{$dbKBContent}` (docid, ownerid, headerstyle, header, contenttype, content, distribution) VALUES ('$docid', '".mysql_real_escape_string($sit[2])."', 'h1', 'Summary', '1', '{$summary}', '{$distribution}') ";
        if (!empty($symptoms)) $query[]="INSERT INTO `{$dbKBContent}` (docid, ownerid, headerstyle, header, contenttype, content, distribution) VALUES ('$docid', '".mysql_real_escape_string($sit[2])."', 'h1', 'Symptoms', '1', '{$symptoms}', '{$distribution}') ";
        if (!empty($cause)) $query[]="INSERT INTO `{$dbKBContent}` (docid, ownerid, headerstyle, header, contenttype, content, distribution) VALUES ('$docid', '".mysql_real_escape_string($sit[2])."', 'h1', 'Cause', '1', '{$cause}', '{$distribution}') ";
        if (!empty($question)) $query[]="INSERT INTO `{$dbKBContent}` (docid, ownerid, headerstyle, header, contenttype, content, distribution) VALUES ('$docid', '".mysql_real_escape_string($sit[2])."', 'h1', 'Question', '1', '{$question}', '{$distribution}') ";
        if (!empty($answer)) $query[]="INSERT INTO `{$dbKBContent}` (docid, ownerid, headerstyle, header, contenttype, content, distribution) VALUES ('$docid', '".mysql_real_escape_string($sit[2])."', 'h1', 'Answer', '1', '{$answer}', '{$distribution}') ";
        if (!empty($solution)) $query[]="INSERT INTO `{$dbKBContent}` (docid, ownerid, headerstyle, header, contenttype, content, distribution) VALUES ('$docid', '".mysql_real_escape_string($sit[2])."', 'h1', 'Solution', '1', '{$solution}', '{$distribution}') ";
        if (!empty($workaround)) $query[]="INSERT INTO `{$dbKBContent}` (docid, ownerid, headerstyle, header, contenttype, content, distribution) VALUES ('$docid', '".mysql_real_escape_string($sit[2])."', 'h1', 'Workaround', '1', '{$workaround}', '{$distribution}') ";
        if (!empty($status)) $query[]="INSERT INTO `{$dbKBContent}` (docid, ownerid, headerstyle, header, contenttype, content, distribution) VALUES ('$docid', '".mysql_real_escape_string($sit[2])."', 'h1', 'Status', '1', '{$status}', '{$distribution}') ";
        if (!empty($additional)) $query[]="INSERT INTO `{$dbKBContent}` (docid, ownerid, headerstyle, header, contenttype, content, distribution) VALUES ('$docid', '".mysql_real_escape_string($sit[2])."', 'h1', 'Additional Information', '1', '{$additional}', '{$distribution}') ";
        if (!empty($references)) $query[]="INSERT INTO `{$dbKBContent}` (docid, ownerid, headerstyle, header, contenttype, content, distribution) VALUES ('$docid', '".mysql_real_escape_string($sit[2])."', 'h1', 'References', '1', '{$references}', '{$distribution}') ";

        if (count($query) < 1) $query[] = "INSERT INTO `{$dbKBContent}` (docid, ownerid, headerstyle, header, contenttype, content, distribution) VALUES ('$docid', '".mysql_real_escape_string($sit[2])."', 'h1', 'Summary', '1', 'Enter details here...', 'restricted') ";

        foreach ($query AS $sql)
        {
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        }

        $id = mysql_insert_id();
        //DEPRECATED 3.40
        //journal(CFG_LOGGING_NORMAL, 'KB Article Added', "KB Article $id was added", CFG_JOURNAL_KB, $id);
        trigger("TRIGGER_KB_CREATED", array('title' => $title));

        unset($_SESSION['formerrors']['kb_add_article']);
        unset($_SESSION['formdata']['kb_add_article']);
        header("Location: kb_view_article.php?id=$docid");
        exit;
    }
    else
    {
        include 'htmlheader.inc.php';
        html_redirect("kb_add_article.php", FALSE);
    }
}
else
{
    //show form
    $title = $strEditKBArticle;
    $pagescripts = array('scriptaculous/scriptaculous.js','scriptaculous/effects.js');
    require 'htmlheader.inc.php';

    if ($mode == 'edit')
    {
        echo "<h2>".icon('kb', 32)." {$strEditKBArticle}</h2>";
        $sql = "SELECT * FROM `{$dbKBArticles}` WHERE docid='{$kbid}'";
        $result = mysql_query($sql);
        $kbobj = mysql_fetch_object($result);

        $sections = array('Summary', 'Symptoms', 'Cause', 'Question', 'Answer',
                          'Solution', 'Workaround', 'Status', 'Additionalinfo',
                          'References');

        foreach($sections AS $section)
        {
            $secsql = "SELECT * FROM `{$dbKBContent}` ";
            $secsql .= "WHERE docid='{$kbobj->docid}' ";
            $secsql .= "AND header='{$section}'";
            if($secresult = mysql_query($secsql))
            {
                $secobj = mysql_fetch_object($secresult);
                if (!empty($secobj->content))
                {
                    $sections[$section] = $secobj->content;
                }
            }
        }
    }
    else
    {
        echo "<h2>".icon('kb', 32)." {$strNewKBArticle}</h2>";
    }

    echo "<div id='kbarticle'>";
    echo "<form action='{$_SERVER['PHP_SELF']}?id={$id}' method='post'>";
    echo "<h3>{$strTitle}</h3>";
    echo "<input class='required' name='title' id='title' size='50' value='{$kbobj->title}'/> ";
    echo "<span class='required'>{$strRequired}</span";

    echo "<h3>{$strKeywords}</h3>";
    echo "<input name='keywords' id='keywords' size='60' value='{$kbobj->keywords}' />";
    echo help_link('SeparatedBySpaces');

    echo "<h3>{$strDistribution}</h3>";
    echo "<select name='distribution'> ";

    echo "<option value='public' ";
    if ($kbobj->distribution == 'public')
    {
        echo " selected='selected' ";
    }
    echo ">{$strPublic}</option>";

    echo "<option value='private' style='color: blue;'";
    if ($kbobj->distribution == 'private')
    {
        echo " selected='selected' ";
    }
    echo ">{$strPrivate}</option>";

    echo "<option value='restricted' style='color: red;";
    if ($kbobj->distribution == 'restricted')
    {
        echo " selected='selected' ";
    }
    echo "'>{$strRestricted}</option>";
    echo "</select> ";
    echo help_link('KBDistribution');

    echo "<h3><a href=\"javascript:void(0);\" onclick=\"Effect.toggle('summary', 'blind');";
    echo "togglePlusMinus('summaryspan');\">";
    echo "{$strSummary} <span id='summaryspan'>[+]</span></a></h3>";
    echo "<textarea id='summary' name='summary' cols='100' rows='8' ";
    echo "style='display: none; overflow: visible; white-space: nowrap;' onchange='kbSectionCollapse();'>{$sections['Summary']}";
    echo "</textarea>";

    echo "<h3><a href=\"javascript:void(0);\" onclick=\"$('symptoms').toggle();";
    echo "togglePlusMinus('symptomsspan');\">";
    echo "{$strSymptoms} <span id='symptomsspan'>[+]</span></a></h3>";
    echo "<textarea id='symptoms' name='symptoms' cols='100' rows='8' ";
    echo "style='display: none' onchange='kbSectionCollapse();'>{$sections['Symptoms']}";
    echo "</textarea>";

    echo "<h3><a href=\"javascript:void(0);\" onclick=\"$('cause').toggle();";
    echo "togglePlusMinus('causespan');\">";
    echo "{$strCause} <span id='causespan'>[+]</span></a></h3>";
    echo "<textarea id='cause' name='cause' cols='100' rows='8' ";
    echo "style='display: none' onchange='kbSectionCollapse();'>{$sections['Cause']}";
    echo "</textarea>";

    echo "<h3><a href=\"javascript:void(0);\" onclick=\"$('question').toggle();";
    echo "togglePlusMinus('questionspan');\">";
    echo "{$strQuestion} <span id='questionspan'>[+]</span></a></h3>";
    echo "<textarea id='question' name='question' cols='100' rows='8' ";
    echo "style='display: none' onchange='kbSectionCollapse();'>{$sections['Question']}";
    echo "</textarea>";

    echo "<h3><a href=\"javascript:void(0);\" onclick=\"$('answer').toggle();";
    echo "togglePlusMinus('answerspan');\">";
    echo "{$strAnswer} <span id='answerspan'>[+]</span></a></h3>";
    echo "<textarea id='answer' name='answer' cols='100' rows='8' ";
    echo "style='display: none' onchange='kbSectionCollapse();'>{$sections['Answer']}";
    echo "</textarea>";

    echo "<h3><a href=\"javascript:void(0);\" onclick=\"$('solution').toggle();";
    echo "togglePlusMinus('solutionspan');\">";
    echo "{$strSolution} <span id='solutionspan'>[+]</span></a></h3>";
    echo "<textarea id='solution' name='solution' cols='100' rows='8' ";
    echo "style='display: none' onchange='kbSectionCollapse();'>{$sections['Solution']}";
    echo "</textarea>";

    echo "<h3><a href=\"javascript:void(0);\" onclick=\"$('workaround').toggle();";
    echo "togglePlusMinus('workaroundspan');\">";
    echo "{$strWorkaround} <span id='workaroundspan'>[+]</span></a></h3>";
    echo "<textarea id='workaround' name='workaround' cols='100' rows='8' ";
    echo "style='display: none' onchange='kbSectionCollapse();'>{$sections['Workaround']}";
    echo "</textarea>";

    echo "<h3><a href=\"javascript:void(0);\" onclick=\"$('status').toggle();";
    echo "togglePlusMinus('statusspan');\">";
    echo "{$strStatus} <span id='statusspan'>[+]</span></a></h3>";
    echo "<textarea id='status' name='status' cols='100' rows='8' ";
    echo "style='display: none' onchange='kbSectionCollapse();'>{$sections['Status']}";
    echo "</textarea>";

    echo "<h3><a href=\"javascript:void(0);\" onclick=\"$('additionalinfo').toggle();";
    echo "togglePlusMinus('additionalinfospan');\">";
    echo "{$strAdditionalInfo} <span id='additionalinfospan'>[+]</span></a></h3>";
    echo "<textarea id='additionalinfo' name='additionalinfo' cols='100' rows='8'  ";
    echo "style='display: none' onchange='kbSectionCollapse();'>{$sections['Additional Info']}";
    echo "</textarea>";

    echo "<h3><a href=\"javascript:void(0);\" onclick=\"$('references').toggle();";
    echo "togglePlusMinus('referencesspan');\">";
    echo "{$strReferences} <span id='referencesspan'>[+]</span></a></h3>";
    echo "<textarea id='references' name='references' cols='100' rows='8' ";
    echo "style='display: none' onchange='kbSectionCollapse();'>{$sections['References']}";
    echo "</textarea>";

    echo "<h3>{$strDisclaimer}</h3>";
    echo $CONFIG['kb_disclaimer_html'];
    echo "<p align='center'><input type='submit' name='submit' value='";
    if ($mode == 'edit')
    {
        echo $strSave;
    }
    else
    {
        echo $strAdd;
    }
    echo "' /></p></form></div>";
    echo "<script type='text/javascript'>\n//<![CDATA[\nkbSectionCollapse();\n//]]>\n</script>";
    include('htmlfooter.inc.php');
}


?>