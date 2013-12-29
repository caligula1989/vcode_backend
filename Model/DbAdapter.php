<?php

class DbAdapter {

  public static function FindById($table,$id){
    $db = App::GetApp()->db;
    $stmt = $db->prepare("SELECT * FROM `".$table."` WHERE id = :id LIMIT 1");
    $stmt->execute(array(":id" => $id));
    $row = $stmt->fetch();
    return $row;
  }
  
  public function Find($table, $selection, $selectionArguments, $limit = null) {
        $results = array();
        $where = "";
        if (!empty($selection)) {
            $where = " WHERE " . self::buildSelection($selection, $selectionArguments);
        }
        $query = "SELECT * FROM `" . $table . "`" . $where;
        $query = (!is_null($limit)) ? $query . " LIMIT " . $limit : $query;
        $stmt = App::GetApp()->db->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
  }
  
  protected static function buildSelection($selection, $selectionArguments) {
      $where = "";
      $c = sizeof($selection);
      if ($c != sizeof($selectionArguments)) {
	  Throw new Exception("Selection and argument differ in size!");
      }

      for ($i = 0; $i < $c; $i++) {
	  $where .= "`" . $selection[$i] . "`";
	  if (is_array($selectionArguments[$i])) {
	      $where .= " IN (" . implode(",", $selectionArguments[$i]) . ")";
	  } else {
	      $where .= " = '" . $selectionArguments[$i] . "'";
	  }
	  $where .= ($c == ($i + 1)) ? " " : " AND ";
      }
      return $where;
  }

}

?>