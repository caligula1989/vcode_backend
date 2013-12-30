<?php

class ProductReport extends Model {

  protected $table = "product_reports";
  
  const ID = "id";
  const BARCODE = "barcode";
  const NAME = "name";
  const VEGAN = "vegan";
  const COMMENT = "comment";
  const COMPANY = "company";
  
  protected $fields = array(
      "id" => false,
      "barcode" => false,
      "name" => false,
      "vegan" => false,
      "comment" => false,
      "company" => false
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
  
  public static function GetAll(){
    $res = Model::ManualFind("product_reports", "");
    if(!is_array($res)){
      return array();
    }
    $instances = array();
    foreach($res as $report) {
	$instance = self::Factory();
	$instance->populateFromRow($report);
	$instances[] = $instance;
    }
    
    return $instances;
        
  }
  
  public static function Search($selection = array(), $selectionArgs = array()) {
      $instance = self::Factory();
      if (!empty($selection) && !empty($selectionArgs)) {
	  $results = $instance->_search($selection, $selectionArgs, 1);
	  $instance->populateFromRow($results[0]);
      }
      return $instance;
  }
    
  public function save() {
    if (!$this->isValid()) {
	Throw new ValidationException($this->error['field'], $this->error['value'], $this->error['validationMethods'], get_class($this));
    }
    $fields = array(self::ID, self::BARCODE, self::NAME, self::VEGAN, self::COMMENT, self::COMPANY);
    $values = array($this->id, $this->barcode, $this->name, $this->vegan, $this->comment, $this->company);
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
      $this->id = $details[self::ID];
      $this->barcode = $details[self::BARCODE];
      $this->name = $details[self::NAME];
      $this->vegan = $details[self::VEGAN];
      $this->comment = $details[self::COMMENT];
      $this->company = $details[self::COMPANY];
  }
  
  public static function FindByBarcode($barcode){
    $selection = array(self::BARCODE);
    $args = array($barcode);
    $instance = self::Search($selection,$args);
    if($instance->id){
      return $instance;
    }
  }
  
  public function getAlternatives(){
    $alternatives = Alternative::GetByProduct($this->id);
    return $alternatives;
  }
  
}

?>