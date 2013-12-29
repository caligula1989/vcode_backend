<?php

class Products extends Controller {

   const PRODUCT_NOT_FOUND = 5;
   const PRODUCT_CODE_INVALID = 4;
   const PRODUCT_VEGAN = 1;
   const PRODUCT_NOT_VEGAN = 2;
   const PRODUCT_VEGANISM_NOT_KNOWN = 3;
  
  public function check(){
  
    $barcode = App::GetApp()->getRouter()->getId();
    $product = Product::FindByBarcode($barcode);
    $productDetails = array("product" => null, "alternatives" => array());
    $res = self::PRODUCT_NOT_FOUND;
    if($product){
      $res = $product->vegan;
      $productDetails['product']['name'] = $product->name;
      $productDetails['product']['vegan'] = $product->vegan;
      $productDetails['product']['barcode'] = $product->barcode;
      $productDetails['product']['company'] = $product->company;
      $productDetails['product']['isav_approved'] = $product->isavApproved;
      $productDetails['product']['barcode'] = $product->barcode;
      $productDetails['product']['id'] = $product->id;
      
      foreach($product->getAlternatives() as $alternative) {
		$prod = Product::FindById($alternative->alternativeId);
		$product = array();
		$product['name'] = $prod->name;
		$product['company'] = $prod->company;
		$product['isav_approved'] = $prod->isavApproved;
		$product['barcode'] = $prod->barcode;
		$product['vegan'] = $prod->vegan;
		$product['id'] = $prod->id;
		$productDetails['alternatives'][] = $product;
      }
    }
    $response = array();
    $response['res'] = $res;
    $response['error'] = "";
    $response['data'] = $productDetails;
    MiscHelper::logit($response);
    return json_encode($response);
  }
  
  public function report(){
    $response = array();
    
    $barcode = App::GetApp()->getRouter()->getParam('barcode');
    $name = App::GetApp()->getRouter()->getParam('name');
    $comment = App::GetApp()->getRouter()->getParam('comment');
    $vegan = App::GetApp()->getRouter()->getParam('vegan');

    $report = ProductReport::Factory();
    $report->barcode = $barcode;
    $report->vegan = $vegan;
    $report->name = $name;
    $report->comment = $comment;
    try {
      $report->save();
      $response['res'] = 1;
    } catch(ValidationException $e){
      $response['res'] = 0;
      $response['error'] = $e;
    }
    
    return json_encode($response);
  }
  
  public function listReports(){
    $this->reports = "";
    $reports = ProductReport::GetAll();
    foreach($reports as $report) {
        $this->reports .= '<tr id="product_row_'.$report->id.'">';
        $this->reports .= "<td>".$report->barcode."</td>";
        $this->reports .= "<td>".$report->name."</td>";
        $this->reports .= "<td>".$report->vegan."</td>";
        $this->reports .= "<td>".$report->comment."</td>";
        $this->reports .= '<td>
	  <button onclick="approveReport('.$report->id.')" class="btn btn-sm btn-success" type="button">Approve</button>
	  <button onclick="deleteReport('.$report->id.')" class="btn btn-sm btn-danger" type="button">Delete</button>
        </td>';
        $this->reports .= "</tr>";
    }
    $this->addJsFile("Public/js/reportsList.js");
    $this->generateView('View/listReports.phtml');
  
  }
  
  public function approveReport(){
    $reportId = App::GetApp()->getRouter()->getId();
    $report = ProductReport::FindById($reportId);
    
    // Check for existing product
    $product = Product::FindByBarcode($report->barcode);
    
    if(!$product){
      $product = Product::CreateFromReport($report);
    }
    
    $product->vegan = $report->vegan;
    $product->save();
    $report->delete();
  }
  
  public function deleteReport(){
    $reportId = App::GetApp()->getRouter()->getId();
    $report = ProductReport::FindById($reportId);
    $report->delete();
  }
}

?>