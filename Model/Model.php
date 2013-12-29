<?php

class Model {

    protected $new = true;
    protected $table;
    protected $indexFields = array("id");
    protected $error;

    protected $fields;
    protected $values;

    // Magic methods
    function __get($field){
        if(isset($this->fields[$field])){
            return isset($this->values[$field]) ? $this->values[$field] : "";
        }
    }
    function __set($field, $value){
        if(isset($this->fields[$field])){
            $this->values[$field] = $value;
        }
    }

    /**
     * Use the validations array set in each model to verify data consistency
     * @returns bool
     */
    public function isValid() {
        foreach ($this->fields as $field => $validationMethods) {
            if(!$validationMethods){
                continue;
            }
            $value = $this->$field;
            // Check if we have multiple methods
            if (strpos($validationMethods, "|") != 0) {
                $methods = explode("|", $validationMethods);
                $pass = false;
                // We have an OR condition. It's enough that one succeeds
                foreach ($methods as $method) {
                    if (ValidationHelper::$method($value)) {
						$pass = true;
                        break;
                    }
                }
                if (!$pass) { //Both have failed, so bugger
                    $this->error = array("field" => $field, "value" => $value, "validationMethods" => $validationMethods);
                    return false;
                }
            } else {
                if (!ValidationHelper::$validationMethods($value)) {
                    $this->error = array("field" => $field, "value" => $value, "validationMethods" => $validationMethods);
                    return false;
                }
            }
        }
        return true;
    }

    public static function ManualFind($table, $arguments) {
        $stmt = App::GetApp()->db->prepare("SELECT * FROM " . $table . " " . $arguments);
        $stmt->execute();
        $result = $stmt->get_result();
        $results = array();
        while ($res = $result->fetch_assoc()) {
            $results[] = $res;
        }
        $stmt->close();
        return $results;
    }

    /**
    * returns array
    */
    protected function _search($selection, $selectionArguments, $limit = null) {
        $results = array();
        $where = "";
        if (!empty($selection)) {
            $where = " WHERE " . self::buildSelection($selection, $selectionArguments);
        }
        $query = "SELECT * FROM `" . $this->table . "`" . $where;
        $query = (!is_null($limit)) ? $query . " LIMIT " . $limit : $query;
        $stmt = App::GetApp()->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($res = $result->fetch_assoc()) {
            $results[] = $res;
        }
        $stmt->close();
        return $results;
    }

    protected function _find($id) {
      $res = $this->_search(array("id"), array($id), 1);
      return isset($res[0]) ? $res[0] : null;
    }

    protected function _save($fields, $values) {
        if ($this->new && $result = $this->insert($fields, $values)) {
            $this->new = false;
            return $result;
        }
        return $this->update($fields, $values);
    }

    protected function _delete($selection, $selectionArguments) {
        if (empty($selection) || empty($selectionArguments)) {
            return false;
        }
        // Set selection data
        $where = self::buildSelection($selection, $selectionArguments);
        $query = "DELETE from " . $this->table . " WHERE " . $where;
        $stmt = App::GetApp()->db->prepare($query);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    private function getIndexValues() {
        $indexValues = array();
        foreach ($this->indexFields as $fieldName) {
            $indexValues[] = $this->$fieldName;
        }
        return $indexValues;
    }

    private function update($fields, $values) {
        // Set selection data
        $query = "UPDATE " . $this->table . " SET " . self::buildUpdate($fields, $values) . " WHERE " . self::buildSelection($this->indexFields, $this->getIndexValues());
        logit($query);
        $stmt = App::GetApp()->db->prepare($query);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    private function insert($fields, $values) {
        $query = "INSERT INTO `" . $this->table . "` " . $this->buildInsert($fields, $values);
        logit($query);
        $stmt = App::GetApp()->db->prepare($query);
        $stmt->execute();
        $this->id = $stmt->insert_id;
        return $stmt->affected_rows;
    }

    protected static function InsertMany($table, $fields, $rows) {
        if (empty($rows)) {
            return false;
        }
        $query = "INSERT INTO `" . $table . "` " . self::buildInsertMultiple($fields, $rows);
        $stmt = App::GetApp()->db->prepare($query);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    protected static function buildInsertMultiple($fields, $rows) {
        $fieldString = "";
        $valueString = "";

        $c = sizeof($fields);
        $rs = sizeof($rows);

        for ($i = 0; $i < $c; $i++) { // Create insert fields
            $fieldString .= "`" . $fields[$i] . "`";
            $fieldString .= ($c == ($i + 1)) ? "" : ",";
        }

        for ($i = 0; $i < $rs; $i++) { // For each row

            $vs = sizeof($rows[$i]);
            $valueString .= " (";
            for ($k = 0; $k < $vs; $k++) { // For each value in the row
                $value = $rows[$i][$k];
                if (is_array($value)) {
                    $value = json_encode($rows[$i][$k]);
                }
                $valueString .= "'" . $value . "'";
                $valueString .= ($vs == ($k + 1)) ? "" : ",";
            }
            $valueString .= ") ";
            $valueString .= ($rs == ($i + 1)) ? "" : ",";

        }


        return "(" . $fieldString . ") VALUES " . $valueString . "";
    }

    protected static function buildInsert($fields, $values) {
        $fieldString = "";
        $valueString = "";
        $c = sizeof($fields);
        if ($c != sizeof($values)) {
            Throw new Exception("Values and fields differ in size!");
        }
        for ($i = 0; $i < $c; $i++) {
            $fieldString .= "`" . $fields[$i] . "`";
            $fieldString .= ($c == ($i + 1)) ? "" : ",";
            $value = $values[$i];
            if (is_array($value)) {
                $value = json_encode($values[$i]);
            }
            $valueString .= "'" . mysqli_real_escape_string(App::GetApp()->db,$value) . "'";
            $valueString .= ($c == ($i + 1)) ? "" : ",";
        }
        return "(" . $fieldString . ") VALUES (" . $valueString . ")";
    }

    protected static function buildUpdate($fields, $values) {
        $update = "";
        $c = sizeof($fields);
        if ($c != sizeof($values)) {
            Throw new Exception("Values and fields differ in size!");
        }

        for ($i = 0; $i < $c; $i++) {
            $update .= "`" . $fields[$i] . "` = '" . mysqli_real_escape_string(App::GetApp()->db,$values[$i]) . "'";
            $update .= ($c == ($i + 1)) ? " " : " , ";
        }
        return $update;
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

    protected static function buildSearch($needle, $haystack) {
        $str = " " . $needle . " IN (";
        $str .= implode(",", $haystack);
        $str .= ") ";
        return $str;
    }

    public function getError() {
        return $this->error;
    }
}

?>