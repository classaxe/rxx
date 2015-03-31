<?php

//attachment.php

function get_attachments($table,$ID,$type='') {
  global $sortBy;
  $out = array();
  if ($ID=='') {
    return $out;
  }
  switch ($sortBy){
    case 'type':
      $sort = "`attachment`.`type`,`attachment`.`title` ASC";
    break;
    case 'type_d':
      $sort = "`attachment`.`type`,`attachment`.`title` DESC";
    break;
    case 'description':
      $sort = "`attachment`.`type`,`attachment`.`title` ASC";
    break;
    case 'description_d':
      $sort = "`attachment`.`type`,`attachment`.`title` DESC";
    break;
    default:
      $sort = "`attachment`.`type`,`attachment`.`title` ASC";
    break;
  }
  $sql =
     "SELECT\n"
    ."  *\n"
    ."FROM\n"
    ."  `attachment`\n"
    ."WHERE\n"
    .($type!='' ? "  `type` = \"".addslashes($type)."\" AND\n" : "")
    ."  `destinationTable` = \"".addslashes($table)."\" AND\n"
    ."  `destinationID` = ".addslashes($ID)."\n"
    .($sort ?
       "ORDER BY\n"
      ."  ".$sort
     : "")
    ;
  if (!$result = mysql_query($sql)) {
    z($sql);
  }
  if (!mysql_num_rows($result)){
    return $out;
  }
  for ($i=0; $i<mysql_num_rows($result); $i++) {
    $out[] = mysql_fetch_array($result,MYSQL_ASSOC);
  }
  return $out;
}

function count_attachments($table,$ID,$type='') {
  if ($ID=='') {
    return 0;
  }
  $sql =
     "SELECT\n"
    ."  COUNT(*) as `count`\n"
    ."FROM\n"
    ."  `attachment`\n"
    ."WHERE\n"
    .($type!='' ? "  `type` = \"".addslashes($type)."\" AND\n" : "")
    ."  `destinationTable` = \"".addslashes($table)."\" AND\n"
    ."  `destinationID` = ".addslashes($ID)."\n"
    ;

  $result = mysql_query($sql);
  $row = mysql_fetch_array($result,MYSQL_ASSOC);
  return $row['count'];
}?>