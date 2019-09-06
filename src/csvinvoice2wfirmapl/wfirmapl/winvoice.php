<?php
namespace CsvInvoice2wFirmapl\wFirmaPl;

use CsvInvoice2wFirmapl\wFirmaPl\wFirmaPlApi;
use \DOMDocument;

class WInvoice extends wFirmaPlApi {

	public function findOne($invoice_no){
		$jsonRequest['api']['invoices']['parameters']['conditions']['condition'] = array('field'=>'fullnumber','operator'=>'like','value'=>$invoice_no);				
		$invoice = $this->request($jsonRequest,'invoices/find');
		if(isset($invoice['invoices'][0]['invoice'])){
			return $invoice['invoices'][0]['invoice'];
		}		
		return false;

	}

	public function get($id){
		$jsonRequest = array();
		$invoice = $this->request($jsonRequest,'invoices/get/'.$id);
		if(isset($invoice['invoices'][0]['invoice'])){
			return $invoice['invoices'][0]['invoice'];
		}
		return false;
	}

	public function updateProducts($id,$products){
		$jsonRequest = array();		
		foreach($products as $p){
			$jsonRequest['api']['invoices']['invoice']['invoicecontents'][] = array('invoicecontent' => $p);
		}
		return $this->request($jsonRequest,'invoices/edit/'.$id);			
	}


	public function findOneProduct($code){
		$jsonRequest['api']['goods']['parameters']['conditions']['condition'] = array('field'=>'code','operator'=>'like','value'=>$code);				
		$good = $this->request($jsonRequest,'goods/find');		
		if(isset($good['goods'][0]['good'])){
			return $good['goods'][0]['good'];
		}		
		return false;
	}


	public function getProduct($id_product){			
		$jsonRequest = array();
		$good = $this->request($jsonRequest,'goods/get/'.$id_product);				
		if(isset($good['goods'][0]['good'])){
			return $good['goods'][0]['good'];
		}		
		return false;
	}
	
}
?>