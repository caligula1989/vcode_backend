<?php

class Product extends Model {

  protected $table = "products";
  
  const ID = "id";
  const BARCODE = "barcode";
  const COMPANY = "company";
  const NAME = "name";
  const VEGAN = "vegan";
  const ISAV_APPROVED = "isavApproved";
  
  protected $fields = array(
      "id" => "isEmpty|isNumeric",
      "barcode" => "isAlphaNumeric",
      "company" => false,
      "name" => false,
      "vegan" => "isNumeric",
      "isavApproved" => "isNumeric"
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
      if (!empty($selection) && !empty($selectionArgs)) {
	  $results = $instance->_search($selection, $selectionArgs, 1);
	  if(isset($results[0])){
	    $instance->populateFromRow($results[0]);
	  }
      }
      return $instance;
  }
    
  public function save() {
    if (!$this->isValid()) {
		Throw new ValidationException($this->error['field'], $this->error['value'], $this->error['validationMethods'], get_class($this));
    }
    $fields = array(self::ID, self::BARCODE, self::COMPANY, self::NAME, self::VEGAN, self::ISAV_APPROVED);
    $values = array($this->id, $this->barcode, $this->company, $this->name, $this->vegan, $this->isavApproved);
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
      $this->isavApproved = $details[self::ISAV_APPROVED];
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
  
  public static function CreateFromReport($report){
  
    $instance = self::Factory();
    $instance->name = $report->name;
    $instance->isavApproved = Products::PRODUCT_VEGANISM_NOT_KNOWN;
    $instance->vegan = $report->vegan;
    $instance->company = $report->company;
    $instance->barcode = $report->barcode;
    $instance->name = $report->name;
    return $instance;
    
  }
  
  public function getAlternatives(){
    $alternatives = Alternative::GetByProduct($this->id);
    return $alternatives;
  }
  
}

?>