<?php

class Alternative extends Model {

  protected $table = "alternatives";
  
  const PRODUCT_ID = "productId";
  const ALTERNATIVE_ID = "alternativeId";
  
  protected $fields = array(
      "productId" => false,
      "alternativeId" => false
  );
  
  public static function Factory($fields = array(), $values = array()) {
      return new self();
  }
  
  public static function FindById($id) {
      $instance = self::Factory();
      $results = $instance->_find($id);
      if (!is_null($results)) {
	  $instance->populateFromRow($results);
      }
      return $instance;
  }
  
  public static function Search($selection = array(), $selectionArgs = array()) {
      $instance = self::Factory();
      if (empty($selection) || empty($selectionArgs)) {
	return array();
      }
      $results = $instance->_search($selection,$selectionArgs);
      $instances = array();
      foreach($results as $row) {
          $instance = self::Factory();
          $instance->populateFromRow($row);
          $instances[] = $instance;
      }
      
      return $instances;
  }
    
  public function save() {
    if (!$this->isValid()) {
	Throw new ValidationException($this->error['field'], $this->error['value'], $this->error['validationMethods'], get_class($this));
    }
    $fields = array(self::PRODUCT_ID, self::ALTERNATIVE_ID);
    $values = array($this->productId, $this->alternativeId);
    $this->_save($fields, $values);
  }

  public function delete() {
      $selection = array(self::ID);
      $args = array($this->id);
      return $this->_delete($selection, $args);
  }

  private function populateFromRow($details) {
      if (is_null($details) || isset($details[0])) {
	  return false;
      }
      $this->new = false;
      $this->productId = $details[self::PRODUCT_ID];
      $this->alternativeId = $details[self::ALTERNATIVE_ID];
  }
  
  public static function GetByProduct($productId){
    $selection = array(self::PRODUCT_ID);
    $args = array($productId);
    $alternatives = self::Search($selection,$args);
    return $alternatives;
  }
}

?>