<?php
namespace CsvInvoice2wFirmapl;
use Exception;

class Config{
	
	protected $endpoint;
	protected $api_key;
	protected $_ini_file;

	/**
	*	Construct
	*/
	public function __construct($ini_file){
		$this->_ini_file = $ini_file;
	}

	/**
	*	Try load current configuration
	*/
	public function load(){
		$ini_data = @parse_ini_file($this->_ini_file);
		if(!$ini_data){
			throw new Exception("Nie można załadować konfiguracji z pliku:{$this->_ini_file}", 1);			
		}			
		foreach($ini_data as $key=>$value){
			$this->{$key} = $value;
		}					
		return 1;
	}


	/**
	*	Get server variable
	*/
	public function getEndPoint(){
		return $this->endpoint;
	}

	public function save(){
		$ini_str = '';
		foreach($this as $key => $value){
			if($key=='_ini_file'){continue;}
			$ini_str .= "{$key} = \"{$value}\"\n\r";
		}
		file_put_contents($this->_ini_file,$ini_str);
	}
		
}
?>