<?php
// *******************************************
// * FILE HEADER:                            *
// *******************************************
// * Project:   RXX Polling System           *
// * Filename:  poll.php                     *
// *                                         *
// * Created:   13/12/2004 (MF)              *
// * Revised:   04/01/2005 (MF)              *
// * Email:     martin@pivotdc.com           *
// *******************************************


// ************************************
// * Configuration settings           *
// ************************************
// Put the two text files in a directory NOT accessible directly to the outside world via a web browser
// but make it world readable and writable (CHMOD 777)



function doNow() {
  global $poll_number;
  global $doThis;
  $sql =      "SELECT `ID` FROM `poll_question` WHERE `active` = 1";
  $result =   @mysql_query($sql);
  $row =      mysql_fetch_array($result,MYSQL_ASSOC);
  $poll_number =	$row['ID'];

  switch($doThis) {
    case "show_results":
      return show_results();
    break;
    case "show_poll":
      return show_poll();
    break;
    default:
      return     "<p><b>Sorry!</b><br>The poll function is offline.</p>\n"
                ."<p>Click <a href='./?mode=poll_list'><b>here</b></a> for previous.</p>";
    break;
  }
}

/*
// ************************************
// * Check cookies are enabled:       *
// ************************************
if (!isset($cookie_rxx_poll)) {
  if (isset($poll)) {
    $question .=	"<p align='center'><i>Your browser must accept cookies in order to participate in this poll</i></p>";
    $answers =	array();
  }
  else {
    setcookie('cookie_rxx_poll',0,time()+31536000,"/");	// One year expiry
    header("Location: $script?poll=show");
  }
}
*/




// ************************************
// * Determine action:                *
// ************************************
if (isset($poll_number)) {
  if ($cookie_rxx_poll != $poll_number) {
    if (isset($submode) && $submode=="vote") {
      cast_vote($my_answer);
      setcookie('cookie_rxx_poll',$poll_number,time()+31536000,"/");	// One year expiry
      header("Location: $script?poll=show&done");
    }
    else {
      $doThis = "show_poll";      
    }
  }
  else {
    $doThis = "show_results";
  }
}


// ************************************
// * show_poll()                      *
// ************************************
function show_poll() {
  global $submode, $poll, $my_answer;
  $out =	array();

  $sql =	"SELECT * FROM `poll_question` WHERE `active` = '1'";
  $result =	@mysql_query($sql);

  if (mysql_num_rows($result)) {
    $row =	mysql_fetch_array($result,MYSQL_ASSOC);
    $ID =	$row['ID'];
    $MM =	substr($row['date'],5,2);
    $YYYY =	substr($row['date'],0,4);
    $title =	$row['title'];
    $text =	$row['text'];
    $date =	MM_to_MMM($MM)." ".$YYYY;

    $sql =	"SELECT * FROM `poll_answer` WHERE `questionID` = '$ID'";
    $result =	@mysql_query($sql);
    $answers =	array();
    for ($i=0; $i<mysql_num_rows($result); $i++) {
      $row =	mysql_fetch_array($result);
      $answers[$i] =	array($row['ID'], $row['text'], $row['votes']);
    }




    $out[] =	"<form name=\"poll\" action=\"".getenv("SCRIPT_NAME")."\" method=\"POST\">\n";
    $out[] =	"<input type='hidden' name='questionID' value='$ID'>\n";
    $out[] =	"<input type='hidden' name='poll' value='$poll'>\n";
    $out[] =	"<input type='hidden' name='submode' value=''>\n";
    $out[] =	"<table cellpadding='1' cellspacing='0' border='0'>\n";
    $out[] =	"  <tr>\n";
    $out[] =	"    <td><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n";
    $out[] =	"      <tr>\n";
    $out[] =	"        <td><b>$title</b></td>\n";
    $out[] =	"        <td align='right' valign='top' nowrap><i>$date</i></td>\n";
    $out[] =	"      </tr>\n";
    $out[] =	"    </table></td>\n";
    $out[] =	"  </tr>";
    $out[] =	"  <tr>\n";
    $out[] =	"    <td>$text</td>\n";
    $out[] =	"  </tr>";
    $out[] =	"  <tr>\n";
    $out[] =	"    <td align='center'><table cellpadding='0' cellspacing='0' border='0' width='70%'>\n";
    
    $total_votes = 0;
    for ($i=0; $i<count($answers); $i++) {
      $total_votes+=		(int)trim($answers[$i][2]);

      $out[] =	"      <tr>\n";
      $out[] =	"        <td nowrap>&nbsp; ".trim($answers[$i][1])."</td>\n";
      $out[] =	"        <td align='right'><INPUT type='radio' name='my_answer' value=\"".urlencode(trim($answers[$i][0]))."\"";
      $out[] =	" onclick=\"document.poll.vote.disabled=0;\"></td>";
      $out[] =	"      </tr>\n";
    }
    $out[] =	"    </table></td>";
    $out[] =	"  </tr>";
    
    $out[] =	"  <tr>\n";
    $out[] =	"    <td align='center'>(Votes cast: $total_votes)</td>\n";
    $out[] =	"  </tr>";
  
    $out[] =	"  <tr>\n";
    $out[] =	"    <th><br><input type='submit' value='Vote' name='vote' disabled onclick='document.poll.submode.value=\"vote\";document.poll.submit()'></th>\n";
    $out[] =	"  </tr>";
    $out[] =	"</table>";
    $out[] =	"</form>";
  }
  return implode($out,"");
}



// ************************************
// * show_results()                   *
// ************************************
function show_results() {
  global $doThis,$vote,$poll_number,$title,$date,$question,$answers,$my_answer;
  $out =	array();
  $out[] =	"<table>\n";
  $out[] =	"  <tr>\n";
  $out[] =	"    <td colspan='2'><table cellpadding='0' cellspacing='0' border='0' width='100%'><tr><td><b>$title</b></td><td align='right' valign='top' nowrap><i>$date</i></td></tr></table></th>\n";
  $out[] =	"  </tr>";
  $out[] =	"  <tr>\n";
  $out[] =	"    <td colspan='2'>$question</td>\n";
  $out[] =	"  </tr>";
  $sql =     "SELECT\n"
            ."  `poll_answer`.*\n"
            ."FROM\n"
            ."	`poll_answer`,\n"
            ."	`poll_question`\n"
            ."WHERE\n"
            ."  `poll_question`.`ID` = `poll_answer`.`questionID` AND\n"
            ."  `poll_question`.`active`=1";
  $result = @mysql_query($sql);
  $rows =   array();
  $total_votes =  0;
  for ($i=0; $i<mysql_num_rows($result); $i++) {
    $row =      mysql_fetch_array($result,MYSQL_ASSOC);
    $rows[] =   $row;
    $total_votes+=    $row['votes'];
  }
  for ($i=0; $i<count($rows); $i++) {
    $rows[$i]['percent'] = ($total_votes>0 ? round(100 * $rows[$i]['votes']/$total_votes) : 0);
    $out[] =	"  <tr>\n";
    $out[] =	"    <td><b>".$rows[$i]['text']."</b></td>\n";
    $out[] =	"    <td>".($rows[$i]['percent'] ? "<img align='middle' border='1' bordercolor='#808080' src='".BASE_PATH."assets/ff0000.gif' height='".poll_column_height."' width='".((int)$rows[$i]['percent']*poll_column_width/100)."' title='(".$rows[$i]['votes']." votes)'>" : "")." (".$rows[$i]['percent']."%)</td>\n";
    $out[] =	"  </tr>";
  }
  $out[] =	"  <tr>\n";
  $out[] =	"    <td colspan='2' align='center'><b>Votes cast: $total_votes</b></td>\n";
  $out[] =	"  </tr>";
  $out[] =	"</table>";
  return implode($out,"");
}



// ************************************
// * poll_edit()                      *
// ************************************
function poll_edit() {
  global $script, $mode, $submode, $ID, $question, $YYYY, $MMM, $title;

  $out = array();

  $sql =	"SELECT * FROM `poll_question` where `ID` = '$ID'";
  $result = 	@mysql_query($sql);
  if ($result) {
    $row =	mysql_fetch_array($result,MYSQL_ASSOC);
    $ID =	$row['ID'];
    $MMM =	substr($row['date'],5,2);
    $YYYY =	substr($row['date'],0,4);
    $title =	$row['title'];
    $text =	$row['text'];
    $submode =	"update";
  }
  else {
    $submode =	"add";
  }

  $out[] =	"<form name='form' action='".system_URL."' method='POST'>\n";
  $out[] =	"<input type='hidden' name='ID' value='$ID'>\n";
  $out[] =	"<input type='hidden' name='mode' value='$mode'>\n";
  $out[] =	"<input type='hidden' name='submode' value=''>\n";
  $out[] =	"<input type='hidden' name='targetID' value=''>\n";
  $out[] =	"<h1>Edit Poll</h1>\n";
  $out[] =	"            <table cellpadding='0' border='0' cellspacing='0' width='100%'>\n";
  $out[] =	"              <tr>\n";
  $out[] =	"                <td width='18'><img src='".BASE_PATH."assets/corner_top_left.gif' width='15' height='18'></td>\n";
  $out[] =	"                <td width='100%' class='downloadTableHeadings_nosort'>Poll Details</td>\n";
  $out[] =	"                <td width='18'><img src='".BASE_PATH."assets/corner_top_right.gif' width='15' height='18'></td>\n";
  $out[] =	"              </tr>\n";
  $out[] =	"            </table>\n";
  $out[] =	"<table width='100%' cellpadding='0' cellspacing='0' border='1' bordercolor='#c0c0c0' class='tableForm'>\n";
  $out[] =	"  <tr class='rowForm'>\n";
  $out[] =	"    <td align='left' width='100'>&nbsp;<b>Date</b></td>\n";
  $out[] =	"    <td valign='top' nowrap>";
  $out[] =	"<select name='MMM' class='formField'>\n";
  for ($i=1; $i<=12; $i++) {
    $out[] =	"<option value='$i'".((int)$MMM==(int)$i ? " selected" : "").">".MM_to_MMM($i)."</option>\n";
  }
  $out[] =	"</select>\n";
  $out[] =	"<select name='YYYY' class='formField'>\n";
  for ($i=2005; $i<=2010; $i++) {
    $out[] =	"<option value='$i'".((int)$YYYY==$i ? " selected" : "").">$i</option>\n";
  }
  $out[] =	"</select>\n";
  $out[] =	"  </tr>\n";
  $out[] =	"  <tr class='rowForm'>";
  $out[] =	"    <td align='left'>&nbsp;<b>Title</b></td></th>\n";
  $out[] =	"    <td valign='top'><input type='text' class='formField' style='width: 100%;' name='title' value=\"$title\"></td>";
  $out[] =	"  </tr>\n";
  $out[] =	"  <tr class='rowForm'>";
  $out[] =	"    <td valign='top' align='left'>&nbsp;<b>Question</b></td></th>\n";
  $out[] =	"    <td valign='top'><textarea class='formField' rows='5' style='width: 100%;' name='text'>$text</textarea></td>";
  $out[] =	"  </tr>\n";
  $out[] =	"  <tr>\n";
  $out[] =	"    <th valign='bottom' class='downloadTableHeadings_nosort' colspan='2'>Choices</th>\n";
  $out[] =	"  </tr>\n";
  $out[] =	"  <tr class='rowForm'>\n";
  $out[] =	"    <td valign='top' colspan='2' align='center'><table cellpadding='2' cellspacing='1' border='0' bgcolor='#ffffff' class='downloadtable'>";

  $sql =	"SELECT * FROM `poll_answer` WHERE `questionID` = '$ID'";
  $result2 = 	@mysql_query($sql);
  for ($j=0; $j<mysql_num_rows($result2); $j++) {
    $row2 =	mysql_fetch_array($result2,MYSQL_ASSOC);
    $out[] =	"  <tr class='rownormal'>";
    $out[] =	"    <td valign='top' width='200'>".$row2['text']."</td>";
    $out[] =	"    <td valign='top' width='20'>".$row2['votes']."</td>";
    $out[] =	"    <td valign='top' width='20'><a href='javascript:document.form.submode.value=\"answer_del\";document.form.targetID.value=\"".$row2['ID']."\";document.form.submit()'>Del</a></td>";
    $out[] =	"  </tr>\n";
  }
  $out[] =	"</table></td>";
  $out[] =	"  </tr>\n";
  $out[] =	"              <tr class='rowForm'>\n";
  $out[] =	"                <td colspan='4' align='center' style='height: 30px;'>\n";
  $out[] =	"<input type='button' value='Print...' onclick='window.print()' class='formbutton' style='width: 60px;'> ";
  $out[] =	"<input type='button' value='Close' onclick='window.close()' class='formbutton' style='width: 60px;'> ";
  $out[] =	"<input type='submit' value='".($submode=="update" ? "Save" : "Add")."' class='formbutton' style='width: 60px;'>";
  $out[] =	"</td>\n";
  $out[] =	"              </tr>\n";
  $out[] =	"</table>";
 
  return implode($out,"");

}


// ************************************
// * poll_list()                      *
// ************************************
function poll_list() {
  global $mode, $submode, $ID, $sortBy, $script, $mode;
  $out = array();
  switch ($submode) {
    case "activate":
      $sql =	"UPDATE `poll_question` SET `active` = 0";
      @mysql_query($sql);
      $sql =	"UPDATE `poll_question` SET `active` = 1 WHERE `ID` = '$ID'";
      @mysql_query($sql);
    break;
  }

  $sortBy_SQL =		"";

  if ($sortBy=="") {
    $sortBy = "date";
  }

  switch ($sortBy) {
    case "active":	$sortBy_SQL =	"`active` DESC, `date` ASC";	break;
    case "active_d":	$sortBy_SQL =	"`active` ASC, `date` ASC";	break;
    case "date":	$sortBy_SQL =	"`date` ASC";			break;
    case "date_d":	$sortBy_SQL =	"`date` DESC";			break;
    case "title":	$sortBy_SQL =	"`title` ASC";			break;
    case "title_d":	$sortBy_SQL =	"`title` DESC";			break;
    case "text":	$sortBy_SQL =	"`text` ASC";			break;
    case "text_d":	$sortBy_SQL =	"`text` DESC";			break;
    case "votes":	$sortBy_SQL =	"`votes` ASC";			break;
    case "votes_d":	$sortBy_SQL =	"`votes` DESC";			break;
  }

  $sql =	 "SELECT\n"
		."  `poll_question`.*,\n"
		."  SUM(`poll_answer`.`votes`) as `votes`\n"
		."FROM\n"
		."  `poll_question`,\n"
		."  `poll_answer`\n"
		."WHERE\n"
		."  `poll_question`.`ID` = `poll_answer`.`questionID`\n"
		.(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ? "" : " AND `active` = '0'\n")
		."GROUP BY `poll_question`.`ID`\n"
		.($sortBy_SQL ? " ORDER BY $sortBy_SQL" : "");
  $result = 	@mysql_query($sql);

  $out[] =	"<form name='form' action='".system_URL."' method='POST'>\n";
  $out[] =	"<input type='hidden' name='mode' value='$mode'>\n";
  $out[] =	"<input type='hidden' name='submode' value=''>\n";
  $out[] =	"<input type='hidden' name='ID' value=''>\n";
  $out[] =	"<input type='hidden' name='sortBy' value='$sortBy'>\n";
  $out[] =	"<h2>List Polls</h2>\n";
  $out[] =	"<table cellpadding='2' cellspacing='1' border='0' bgcolor='#ffffff' class='downloadtable'>\n";
  $out[] =	"  <thead>\n";
  $out[] =	"  <tr>\n";
  $out[] =	"    <th valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" class='downloadTableHeadings' title='Sort by Date' onclick=\"document.form.sortBy.value='date".($sortBy=="date" ? "_d" : "")."';document.form.submit()\" align='left'>Date ".($sortBy=='date' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='date_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n";
  $out[] =	"    <th valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" class='downloadTableHeadings' title='Sort by Title' onclick=\"document.form.sortBy.value='title".($sortBy=="title" ? "_d" : "")."';document.form.submit()\" align='left'>Title ".($sortBy=='title' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='title_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n";
  $out[] =	"    <th valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" class='downloadTableHeadings' title='Sort by Question'  onclick=\"document.form.sortBy.value='text".($sortBy=="text" ? "_d" : "")."';document.form.submit()\" align='left'>Question ".($sortBy=='text' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='text_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n";
  $out[] =	"    <th valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" class='downloadTableHeadings' title='Sort by Votes'  onclick=\"document.form.sortBy.value='votes".($sortBy=="votes" ? "_d" : "")."';document.form.submit()\" align='left'>Total Votes ".($sortBy=='votes' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='votes_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n";
  $out[] =	"    <th valign='bottom' class='downloadTableHeadings_nosort'>Answers</th>\n";
  if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
    $out[] =	"    <th valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" class='downloadTableHeadings' title='Sort by Active status' onclick=\"document.form.sortBy.value='active".($sortBy=="active" ? "_d" : "")."';document.form.submit()\" align='left'>Active ".($sortBy=='active' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='active_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n";
  }
  $out[] =	"  </tr>\n";
  $out[] =	"  </thead>\n";

  for ($i=0; $i<mysql_num_rows($result); $i++) {
    $row =	mysql_fetch_array($result,MYSQL_ASSOC);
    $date =	MM_to_MMM(substr($row['date'],5,2))." ".substr($row['date'],2,2);
    $out[] =	"  <tr class='rownormal'>";
    $out[] =	"    <td valign='top'>".(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ? "<a href=\"javascript:poll_edit('".$row['ID']."')\" title=\"Edit this poll\"><b>$date</b></a>" : "<b>$date</b>")."</td>";
    $out[] =	"    <td valign='top'>".$row['title']."</td>";
    $out[] =	"    <td valign='top'>".$row['text']."</td>";
    $out[] =	"    <td valign='top'>".$row['votes']."</td>";
    $out[] =	"    <td valign='top' style='padding: 0;'><table cellpadding='2' cellspacing='0' border='0' bgcolor='#ffffff'>";

    $sql =	"SELECT * FROM `poll_answer` WHERE `questionID` = '".$row['ID']."'";
    $result2 = 	@mysql_query($sql);
    for ($j=0; $j<mysql_num_rows($result2); $j++) {
      $row2 =	mysql_fetch_array($result2,MYSQL_ASSOC);
      $out[] =	"  <tr class='rownormal'>";
      $out[] =	"    <td valign='top' width='200' style='border-bottom: solid 1px;'>".$row2['text']."</td>";
      $out[] =	"    <td valign='top' width='20' style='border-bottom: solid 1px;'>".$row2['votes']."</td>";
      $out[] =	"  </tr>\n";
    }
    $out[] =	"</table></td>";
    if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
      $out[] =	"    <td valign='top'><a href='javascript:document.form.submode.value=\"activate\";document.form.ID.value=\"".$row['ID']."\";document.form.submit();'><img src='".BASE_PATH."assets/checkbox_".$row['active'].".gif' border='0'></a></td>";
    }
    $out[] =	"  </tr>\n";
  }
  $out[] =	"</table><br><br>";
  $out[] =	"<span class='noprint'>\n";
  $out[] =	"<input type='button' value='Print...' onclick='window.print();' class='formbutton' style='width: 150px;'> ";
  if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
    $out[] =	"<input type='button' class='formbutton' value='Add poll...' style='width: 150px' onclick='poll_edit(\"\")'> ";
  }
  $out[] =	"</span>\n";

  return implode($out,"");

}

?>
