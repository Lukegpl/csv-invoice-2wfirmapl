<?php
namespace CsvInvoice2wFirmapl\wFirmaPl;

class wFirmaPlApi {

protected $login;
protected $password;
protected $api_endpoint = 'https://api2.wfirma.pl/';


public function __construct($login,$password){
	$this->login = $login;
	$this->password = $password;
}
	
public function request($jsonRequest,$method,$debug = false){
	if($debug){
		print_r (json_encode($jsonRequest));
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $this->api_endpoint.$method.'&inputFormat=json&outputFormat=json');
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($jsonRequest));
	curl_setopt($ch, CURLOPT_USERPWD, $this->login . ':' . $this->password);
	$result = curl_exec($ch);
	return json_decode($result,true) ;
}
	
}
?>