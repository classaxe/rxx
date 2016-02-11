<?php
namespace Rxx;

/**
 * Class Poll
 * @package Rxx
 */
class Poll
{
    /**
     * @return string
     */
    public function draw()
    {
        $my_answer =    (int)Rxx::get_var('my_answer');
        $questionID =   (int)Rxx::get_var('questionID');
        $sql =      "SELECT `ID` FROM `poll_question` WHERE `active` = 1";
        $result =   \Rxx\Database::query($sql);
        $row =      \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
        if (!$row) {
            return
                 "<p><b>Sorry!</b><br>The poll function is offline.</p>\n"
                . "<p>Click <a href='../../classes?mode=poll_list'><b>here</b></a> for previous.</p>";
        }
        if (Rxx::get_var('submode')=="vote" && $my_answer && $questionID
        ) {
            $sql =    "UPDATE `poll_answer` SET `votes` = `votes` + 1 WHERE `ID` = ".$my_answer;
            @\Rxx\Database::query($sql);
            setcookie('cookie_rxx_poll', $questionID, time()+31536000, "/");    // One year expiry
            header("Location: ".Rxx::$system_url."/".Rxx::$system_mode);
        }
        if (isset($_COOKIE['cookie_rxx_poll']) && $_COOKIE['cookie_rxx_poll'] = $row['ID']) {
            return $this->drawResults();
        }
        return $this->drawForm();
    }

    /**
     * @return string
     */
    protected function drawForm()
    {
        global $poll;

        $sql =    "SELECT * FROM `poll_question` WHERE `active` = '1'";
        $result =    @\Rxx\Database::query($sql);

        if (!\Rxx\Database::numRows($result)) {
            return'';
        }
        $row =      \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
        $ID =       $row['ID'];
        $MM =       substr($row['date'], 5, 2);
        $YYYY =     substr($row['date'], 0, 4);
        $title =    $row['title'];
        $text =     $row['text'];
        $date =     Rxx::MM_to_MMM($MM)." ".$YYYY;

        $sql =      "SELECT * FROM `poll_answer` WHERE `questionID` = '$ID'";
        $result =   @\Rxx\Database::query($sql);
        $answers =  array();
        $total_votes = 0;
        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $row =          \Rxx\Database::fetchArray($result);
            $answers[$i] =  array($row['ID'], $row['text'], $row['votes']);
            $total_votes+=  (int)trim($row['votes']);
        }

        $html =
             "<form name=\"poll\" action=\"".Rxx::$system_url."/".Rxx::$system_mode."\" method=\"POST\">\n"
            ."<input type='hidden' name='questionID' value='".$ID."'>\n"
            ."<input type='hidden' name='poll' value='".$poll."'>\n"
            ."<input type='hidden' name='submode' value=''>\n"
            ."<table cellpadding='1' cellspacing='0' border='0'>\n"
            ."  <tr>\n"
            ."    <td><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."      <tr>\n"
            ."        <td><b>".$title."</b></td>\n"
            ."        <td align='right' valign='top' nowrap><i>".$date."</i></td>\n"
            ."      </tr>\n"
            ."    </table></td>\n"
            ."  </tr>"
            ."  <tr>\n"
            ."    <td>".$text."</td>\n"
            ."  </tr>"
            ."  <tr>\n"
            ."    <td align='center'><table cellpadding='0' cellspacing='0' border='0' width='70%'>\n";

        for ($i=0; $i<count($answers); $i++) {
            $html.=
                 "      <tr>\n"
                ."        <td nowrap>&nbsp; ".trim($answers[$i][1])."</td>\n"
                ."        <td align='right'>\n"
                ."<input type='radio' name='my_answer' value=\"".urlencode(trim($answers[$i][0]))."\""
                ." onclick=\"document.poll.vote.disabled=0\"></td>"
                ."      </tr>\n";
        }
        $html.=
             "    </table></td>"
            ."  </tr>"
            ."  <tr>\n"
            ."    <td align='center'>(Votes cast: ".$total_votes.")</td>\n"
            ."  </tr>"
            ."  <tr>\n"
            ."    <th>\n"
            ."<input type='submit' value='Vote' name='vote' id='vote' disabled"
            ." onclick='document.poll.submode.value=\"vote\";document.poll.submit()'></th>\n"
            ."  </tr>"
            ."</table>\n"
            ."</form>";
        return $html;
    }

    /**
     * @return string
     */
    public function drawList()
    {
        global $ID, $sortBy;
        switch (\Rxx\Rxx::$system_submode) {
            case "activate":
                $sql =    "UPDATE `poll_question` SET `active` = 0";
                @\Rxx\Database::query($sql);
                $sql =    "UPDATE `poll_question` SET `active` = 1 WHERE `ID` = ".(int)$ID;
                @\Rxx\Database::query($sql);
                break;
        }
        $sortBy_SQL =        "";
        if ($sortBy=="") {
            $sortBy = "date";
        }
        switch ($sortBy) {
            case "active":
                $sortBy_SQL =    "`active` DESC, `date` ASC";
                break;
            case "active_d":
                $sortBy_SQL =    "`active` ASC, `date` ASC";
                break;
            case "date":
                $sortBy_SQL =    "`date` ASC";
                break;
            case "date_d":
                $sortBy_SQL =    "`date` DESC";
                break;
            case "title":
                $sortBy_SQL =    "`title` ASC";
                break;
            case "title_d":
                $sortBy_SQL =    "`title` DESC";
                break;
            case "text":
                $sortBy_SQL =    "`text` ASC";
                break;
            case "text_d":
                $sortBy_SQL =    "`text` DESC";
                break;
            case "votes":
                $sortBy_SQL =    "`votes` ASC";
                break;
            case "votes_d":
                $sortBy_SQL =    "`votes` DESC";
                break;
        }

        $sql =
             "SELECT\n"
            ."  `poll_question`.*,\n"
            ."  SUM(`poll_answer`.`votes`) as `votes`\n"
            ."FROM\n"
            ."  `poll_question`,\n"
            ."  `poll_answer`\n"
            ."WHERE\n"
            ."  `poll_question`.`ID` = `poll_answer`.`questionID`\n"
            .(Rxx::isAdmin() ? "" : " AND `active` = '0'\n")
            ."GROUP BY `poll_question`.`ID`\n"
            .($sortBy_SQL ? " ORDER BY $sortBy_SQL" : "");
        $result =     @\Rxx\Database::query($sql);

        $html =
             "<form name='form' action=\"".Rxx::$system_url."/".Rxx::$system_mode."\" method='POST'>\n"
            ."<input type='hidden' name='mode' value='".Rxx::$system_mode."'>\n"
            ."<input type='hidden' name='submode' value=''>\n"
            ."<input type='hidden' name='ID' value=''>\n"
            ."<input type='hidden' name='sortBy' value='$sortBy'>\n"
            ."<h2>List Polls</h2>\n"
            ."<table cellpadding='2' cellspacing='1' border='0' bgcolor='#ffffff' class='downloadtable'>\n"
            ."  <thead>\n"
            ."  <tr>\n"
            ."    <th valign='bottom' class='downloadTableHeadings' title='Sort by Date' align='left'"
            ." onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\""
            ." onmousedown=\"column_over(this,2);\""
            ." onclick=\"document.form.sortBy.value='date"
            .($sortBy=="date" ? "_d" : "")
            ."';document.form.submit()\">"
            ."Date "
            .($sortBy=='date' ? "<img src='".\Rxx\Rxx::$base_path."assets/icon_sort_asc.gif' alt='A-Z'>" : "")
            .($sortBy=='date_d' ? "<img src='".\Rxx\Rxx::$base_path."assets/icon_sort_desc.gif' alt='Z-A'>" : "")
            ."</th>\n"
            ."    <th valign='bottom' class='downloadTableHeadings' title='Sort by Title' align='left'"
            ." onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\""
            ." onmousedown=\"column_over(this,2);\""
            ." onclick=\"document.form.sortBy.value='title"
            .($sortBy=="title" ? "_d" : "")
            ."';document.form.submit()\">"
            ."Title "
            .($sortBy=='title' ? "<img src='".\Rxx\Rxx::$base_path."assets/icon_sort_asc.gif' alt='A-Z'>" : "")
            .($sortBy=='title_d' ? "<img src='".\Rxx\Rxx::$base_path."assets/icon_sort_desc.gif' alt='Z-A'>" : "")
            ."</th>\n"
            ."    <th valign='bottom' class='downloadTableHeadings' title='Sort by Question' align='left'"
            ." onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\""
            ." onmousedown=\"column_over(this,2);\""
            ." onclick=\"document.form.sortBy.value='text"
            .($sortBy=="text" ? "_d" : "")
            ."';document.form.submit()\">"
            ."Question "
            .($sortBy=='text' ? "<img src='".\Rxx\Rxx::$base_path."assets/icon_sort_asc.gif' alt='A-Z'>" : "")
            .($sortBy=='text_d' ? "<img src='".\Rxx\Rxx::$base_path."assets/icon_sort_desc.gif' alt='Z-A'>" : "")
            ."</th>\n"
            ."    <th valign='bottom' class='downloadTableHeadings' title='Sort by Votes' align='left'"
            ." onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\""
            ." onmousedown=\"column_over(this,2);\""
            ." onclick=\"document.form.sortBy.value='votes"
            .($sortBy=="votes" ? "_d" : "")
            ."';document.form.submit()\">"
            ."Total Votes "
            .($sortBy=='votes' ? "<img src='".\Rxx\Rxx::$base_path."assets/icon_sort_asc.gif' alt='A-Z'>" : "")
            .($sortBy=='votes_d' ? "<img src='".\Rxx\Rxx::$base_path."assets/icon_sort_desc.gif' alt='Z-A'>" : "")
            ."</th>\n"
            ."    <th valign='bottom' class='downloadTableHeadings_nosort'>Answers</th>\n";
        if (Rxx::isAdmin()) {
            $html.=
                 "    <th valign='bottom' class='downloadTableHeadings' title='Sort by Active status' align='left'"
                ." onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\""
                ." onmousedown=\"column_over(this,2);\""
                ." onclick=\"document.form.sortBy.value='active"
                .($sortBy=="active" ? "_d" : "")
                ."';document.form.submit()\">"
                ."Active "
                .($sortBy=='active' ? "<img src='".\Rxx\Rxx::$base_path."assets/icon_sort_asc.gif' alt='A-Z'>" : "")
                .($sortBy=='active_d' ? "<img src='".\Rxx\Rxx::$base_path."assets/icon_sort_desc.gif' alt='Z-A'>" : "")
                ."</th>\n";
        }
        $html.=
             "  </tr>\n"
            ."  </thead>\n";
        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $row =    \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
            $date =    Rxx::MM_to_MMM(substr($row['date'], 5, 2))." ".substr($row['date'], 2, 2);
            $html.=
                 "  <tr class='rownormal'>"
                ."    <td valign='top'>"
                .(Rxx::isAdmin() ?
                    "<a href=\"javascript:poll_edit('".$row['ID']."')\" title=\"Edit this poll\"><b>".$date."</b></a>"
                 :
                    "<b>$date</b>"
                )."</td>"
                ."    <td valign='top'>".$row['title']."</td>"
                ."    <td valign='top'>".$row['text']."</td>"
                ."    <td valign='top'>".$row['votes']."</td>"
                ."    <td valign='top' style='padding: 0;'>"
                ."<table cellpadding='2' cellspacing='0' border='0' bgcolor='#ffffff'>";

            $sql =    "SELECT * FROM `poll_answer` WHERE `questionID` = '".$row['ID']."'";
            $result2 =     @\Rxx\Database::query($sql);
            for ($j=0; $j<\Rxx\Database::numRows($result2); $j++) {
                $row2 =    \Rxx\Database::fetchArray($result2, MYSQL_ASSOC);
                $html.=
                     "  <tr class='rownormal'>"
                    ."    <td valign='top' width='200' style='border-bottom: solid 1px;'>".$row2['text']."</td>"
                    ."    <td valign='top' width='20' style='border-bottom: solid 1px;'>".$row2['votes']."</td>"
                    ."  </tr>\n";
            }
            $html.=
                "</table></td>";
            if (Rxx::isAdmin()) {
                $html.=
                     "    <td valign='top'>"
                    ."<a href='javascript:document.form.submode.value=\"activate\";"
                    ."document.form.ID.value=\"".$row['ID']."\";document.form.submit();'>"
                    ."<img src='".\Rxx\Rxx::$base_path."assets/checkbox_".$row['active'].".gif' border='0'></a></td>";
            }
            $html.=
                "  </tr>\n";
        }
        $html.=
             "</table><br><br>"
            ."<span class='noprint'>\n"
            ."<input type='button' value='Print...' onclick='window.print();'"
            ." class='formbutton' style='width: 150px;' /> ";
        if (Rxx::isAdmin()) {
            $html.=
                 "<input type='button' class='formbutton' value='Add poll...' style='width: 150px'"
                ." onclick='poll_edit(\"\")'> ";
        }
        $html.=
            "</span>\n";

        return $html;

    }

    /**
     * @return string
     */
    protected function drawResults()
    {
        global $doThis, $vote, $poll_number, $title, $date, $question, $answers, $my_answer;
        $sql =    "SELECT * FROM `poll_question` WHERE `active` = '1'";
        $result =    @\Rxx\Database::query($sql);

        if (!\Rxx\Database::numRows($result)) {
            return'';
        }
        $row =    \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
        $ID =        $row['ID'];
        $MM =        substr($row['date'], 5, 2);
        $YYYY =        substr($row['date'], 0, 4);
        $title =    $row['title'];
        $text =        $row['text'];
        $date =        Rxx::MM_to_MMM($MM)." ".$YYYY;

        $html =
             "<table>\n"
            ."  <tr>\n"
            ."    <td colspan='3'>\n"
            ."    <table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
            ."      <tr>\n"
            ."        <td><b>".$title."</b></td>\n"
            ."        <td align='right' valign='top' nowrap><i>".$date."</i></td>\n"
            ."      </tr>\n"
            ."    </table></td>\n"
            ."  </tr>"
            ."  <tr>\n"
            ."    <td colspan='3'>".$text."</td>\n"
            ."  </tr>";

        $sql =
             "SELECT\n"
            ."  `poll_answer`.*\n"
            ."FROM\n"
            ."	`poll_answer`,\n"
            ."	`poll_question`\n"
            ."WHERE\n"
            ."  `poll_question`.`ID` = `poll_answer`.`questionID` AND\n"
            ."  `poll_question`.`active`=1";
        $result = @\Rxx\Database::query($sql);
        $rows =   array();
        $total_votes =  0;
        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $row =          \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
            $rows[] =       $row;
            $total_votes+=  $row['votes'];
        }
        for ($i=0; $i<count($rows); $i++) {
            $rows[$i]['percent'] = ($total_votes>0 ? round(100 * $rows[$i]['votes']/$total_votes) : 0);
            $html.=
                 "  <tr>\n"
                ."    <td><b>".$rows[$i]['text']."</b></td>\n"
                ."    <td>&nbsp;</td>\n"
                ."    <td>"
                .($rows[$i]['percent'] ?
                     "<img align='middle' border='1' bordercolor='#808080' src='".\Rxx\Rxx::$base_path."assets/ff0000.gif'"
                    ." height='".poll_column_height."'"
                    ." width='".((int)$rows[$i]['percent'] * poll_column_width/100)."'"
                    ." title='(".$rows[$i]['votes']." votes)'>"
                 :
                    ""
                 )
                 ." ("
                 .$rows[$i]['percent']
                 ."%)</td>\n"
                 ."  </tr>";
        }
        $html.=
             "  <tr>\n"
            ."    <td colspan='3' align='center'><b>Votes cast: $total_votes</b></td>\n"
            ."  </tr>"
            ."</table>";
        return $html;
    }

    /**
     * @return string
     */
    public function edit()
    {
        global $ID, $question, $YYYY, $MMM, $title;

        $sql =    "SELECT * FROM `poll_question` where `ID` = '$ID'";
        $result =     @Database::query($sql);
        if ($result) {
            $row =    Database::fetchArray($result, MYSQL_ASSOC);
            $ID =    $row['ID'];
            $MMM =    substr($row['date'], 5, 2);
            $YYYY =    substr($row['date'], 0, 4);
            $title =    $row['title'];
            $text =    $row['text'];
            \Rxx\Rxx::$system_submode =    "update";
        } else {
            \Rxx\Rxx::$system_submode =    "add";
        }

        $html =
             "<form name=\"form\" action=\"".Rxx::$system_url."/".Rxx::$system_mode."\" method=\"POST\">\n"
            ."<input type='hidden' name='ID' value='$ID'>\n"
            ."<input type='hidden' name='mode' value='".Rxx::$system_mode."'>\n"
            ."<input type='hidden' name='submode' value=''>\n"
            ."<input type='hidden' name='targetID' value=''>\n"
            ."<h1>Edit Poll</h1>\n"
            ."<table cellpadding='0' border='0' cellspacing='0' width='100%'>\n"
            ."  <tr>\n"
            ."    <td width='18'><img src='".Rxx::$base_path."assets/corner_top_left.gif' width='15' height='18'></td>\n"
            ."    <td width='100%' class='downloadTableHeadings_nosort'>Poll Details</td>\n"
            ."    <td width='18'><img src='".Rxx::$base_path."assets/corner_top_right.gif' width='15' height='18'></td>\n"
            ."  </tr>\n"
            ."</table>\n"
            ."<table width='100%' cellpadding='0' cellspacing='0' border='1' bordercolor='#c0c0c0' class='tableForm'>\n"
            ."  <tr class='rowForm'>\n"
            ."    <td align='left' width='100'>&nbsp;<b>Date</b></td>\n"
            ."    <td valign='top' nowrap>"
            ."<select name='MMM' class='formField'>\n";
        for ($i=1; $i<=12; $i++) {
            $html.=    "<option value='$i'".((int)$MMM==(int)$i ? " selected" : "").">".Rxx::MM_to_MMM($i)."</option>\n";
        }
        $html.=
             "</select>\n"
            ."<select name='YYYY' class='formField'>\n";
        for ($i=2005; $i<=2020; $i++) {
            $html.=    "<option value='$i'".((int)$YYYY==$i ? " selected" : "").">$i</option>\n";
        }
        $html.=
             "</select>\n"
            ."  </tr>\n"
            ."  <tr class='rowForm'>"
            ."    <td align='left'>&nbsp;<b>Title</b></td></th>\n"
            ."    <td valign='top'>"
            ."<input type='text' class='formField' style='width: 100%;' name='title' value=\"".$title."\" /></td>"
            ."  </tr>\n"
            ."  <tr class='rowForm'>"
            ."    <td valign='top' align='left'>&nbsp;<b>Question</b></td></th>\n"
            ."    <td valign='top'>"
            ."<textarea class='formField' rows='5' style='width: 100%;' name='text'>".$text."</textarea></td>"
            ."  </tr>\n"
            ."  <tr>\n"
            ."    <th valign='bottom' class='downloadTableHeadings_nosort' colspan='2'>Choices</th>\n"
            ."  </tr>\n"
            ."  <tr class='rowForm'>\n"
            ."    <td valign='top' colspan='2' align='center'>"
            ."<table cellpadding='2' cellspacing='1' border='0' bgcolor='#ffffff' class='downloadtable'>";
        $sql =    "SELECT * FROM `poll_answer` WHERE `questionID` = '$ID'";
        $result2 =     @\Rxx\Database::query($sql);
        for ($j=0; $j<\Rxx\Database::numRows($result2); $j++) {
            $row2 =    \Rxx\Database::fetchArray($result2, MYSQL_ASSOC);
            $html.=
                 "  <tr class='rownormal'>"
                ."    <td valign='top' width='200'>".$row2['text']."</td>"
                ."    <td valign='top' width='20'>".$row2['votes']."</td>"
                ."    <td valign='top' width='20'><a href='#'"
                ." onclick='"
                ."document.form.submode.value=\"answer_del\";"
                ."document.form.targetID.value=\"".$row2['ID']."\";"
                ."document.form.submit();return false'>Del</a></td>"
                ."  </tr>\n";
        }
        $html.=
             "</table></td>"
            ."  </tr>\n"
            ."              <tr class='rowForm'>\n"
            ."                <td colspan='4' align='center' style='height: 30px;'>\n"
            ."<input type='button' value='Print...' onclick='window.print()' class='formbutton' style='width: 60px;'> "
            ."<input type='button' value='Close' onclick='window.close()' class='formbutton' style='width: 60px;'> "
            ."<input type='submit' value='"
            .(\Rxx\Rxx::$system_submode=="update" ? "Save" : "Add")
            ."' class='formbutton' style='width: 60px;'>"
            ."</td>\n"
            ."              </tr>\n"
            ."</table>";
        return $html;
    }
}
