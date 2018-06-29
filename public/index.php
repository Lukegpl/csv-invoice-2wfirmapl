<?php
use CsvInvoice2wFirmapl\Config;
use CsvInvoice2wFirmapl\CsvFile;
use CsvInvoice2wFirmapl\wFirmaPl\WInvoice;
//var_dump($result);

require_once(dirname(__FILE__).'/init.php');

	//Config object	
	session_start();
	
	$cfg = new Config(CONFIG_INI_FILE);
	$cfg->load();

  
  $wfirma_invoice = new WInvoice($cfg->user,$cfg->password);
  $wfirma_invoice_data = array();

  if(isset($_POST['invoice_id'])){
     $wfirma_invoice_data = $wfirma_invoice->findOne($_POST['invoice_id']);     
     $_SESSION['invoice_id'] = $wfirma_invoice_data['id'];
     $_SESSION['fullnumber'] = $wfirma_invoice_data['fullnumber'];
  }

	if(isset($_POST['cancelfile']) && isset($_SESSION['products_file'])){
		unlink($_SESSION['products_file']);
		unset($_SESSION['products_file']);
    unset($_SESSION['fullnumber']);
    unset($_SESSION['invoice_id']);
	}
  
  $csvfile = false;
	$processing_response = array();
	if(isset($_POST['processfile']) && isset($_SESSION['products_file'])) {
    $vat = $_POST['vat'];
		$csvfile = new CsvFile($_SESSION['products_file']);		
    //Wyczyszczenie pliku i przygotowanie danych do zaciągnięcia
    $products = array();
    $i=0; 
    foreach($csvfile->getRow() as $row){
      if($i==0){$i++;continue;}
        $good = $wfirma_invoice->findOneProduct($row[$cfg->col_code]);
        //print_r($good);
        if(!isset($good['id'])){
          $processing_response[] = "Nie znalezionio produktu: <b>".$row[$cfg->col_code]."</b>";
          continue;
        }
        $products[] = array('count' => $row[$cfg->col_qty],
                               'price' => str_replace(',','.',str_replace('EUR','',$row[$cfg->col_price])),
                               'vat' => "{$vat}",                               
                               'good' => array('id'=>$good['id'])
          );  
                    
    }
    
    $ret = $wfirma_invoice->updateProducts($_SESSION['invoice_id'],$products);  
    //print_r($ret);
    //print_r($wfirma_invoice->get($_SESSION['invoice_id']));
		unlink($_SESSION['products_file']);
		unset($_SESSION['products_file']);
    unset($_SESSION['fullnumber']);
    unset($_SESSION['invoice_id']);
    $csvfile = false;
	}

	if(isset($_FILES['products_file'])){
		$csvfile = new CsvFile($_FILES['products_file']['tmp_name']);	
		$file_name = tempnam(sys_get_temp_dir(),'api-uploader');
		file_put_contents($file_name, file_get_contents($_FILES['products_file']['tmp_name']));
		$_SESSION['products_file'] = $file_name;
	}

	if(isset($_SESSION['products_file'])){		
		$csvfile = new CsvFile($_SESSION['products_file']);	
	}
//echo '<pre>';
//var_dump(count($wfirma_invoice_data));
//print_r($wfirma_invoice_data);


?>
<!DOCTYPE html>
<html lang="pl">
<head>

  <!-- Basic Page Needs
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta charset="utf-8">
  <title>Wysłanie produktow</title>
  <meta name="description" content="">
  <meta name="author" content="">

  <!-- Mobile Specific Metas
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- FONT
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <link href="//fonts.googleapis.com/css?family=Raleway:400,300,600" rel="stylesheet" type="text/css">

  <!-- CSS
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <link rel="stylesheet" href="css/normalize.css">
  <link rel="stylesheet" href="css/skeleton.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.js" integrity="sha256-fNXJFIlca05BIO2Y5zh1xrShK3ME+/lYZ0j+ChxX2DA=" crossorigin="anonymous"></script>  
    
</head>
<body>

  <!-- Primary Page Layout
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <div class="container">
<?php if(count($wfirma_invoice_data)==0): ?>
    <form enctype="multipart/form-data" method="post">
    <div class="row">
      <div class="twleve columns" style="margin-top:30px;">     
         <h4>Aktualziacja faktury z csv do wFirma.pl</h4>     
         <h5>Podaj numer faktury z wFirma którą chesz z aktualizować.</h5>      
      </div>
    </div>
  <div class="row">
     <div class="one-half column">
        <label for="newprefix">Numer faktury wfirma.pl</label>
          <input class="u-full-width" name="invoice_id" type="text" placeholder="podaj ID" value="<?php echo isset($_SESSION['fullnumber'])?$_SESSION['fullnumber']:''; ?>">
      </div> 
  </div>   
  <div class="row">
      <div class="one-half column">
        <input class="button-primary" type="submit" value="Pobierz fakturę" name="get_invoice">         
      </div>  
  </div>  
</form>    
<?php elseif(count($wfirma_invoice_data)>0): ?> 
    <form enctype="multipart/form-data" method="post">
    <div class="row">
      <div class="twleve columns" style="margin-top:30px;">     
         <h4>Import faktury z csv do wFirma.pl</h4>     
         <h5>Import pozycji do faktury: <?php echo $wfirma_invoice_data['fullnumber'] ?></h5>
	       <h6>Wybierz plik z przygotowaną pozycjami do zaimportowania do wFirma.pl.</h6>		   
      </div>
    </div>
  <div class="row">
  	 <div class="one-half column">
     		<label for="newprefix">Plik (csv)</label>
          <input class="u-full-width" name="products_file" type="file" placeholder="załaduj plik">
      </div> 
  </div>   
  <div class="row">
  	  <div class="one-half column">         
     		<input class="button-primary" type="submit" value="Wyślij" name="save">     		
      </div>  
  </div>  
</form>
<?php 
  endif;
if(count($processing_response)>0): ?>
    <div class="row">    	
      <div class="twleve columns" style="margin-top:30px;"> 
              <h4>Import faktury z csv do wFirma.pl</h4>     
              <h6>Raport importu</h6>
           <table style="width:100%;">
        <tbody>
          <?php foreach($processing_response as $row): ?>
            <tr>
              <td ><?php echo $row ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      </div>
    </div>
<?php endif;?>
<?php if($csvfile && $csvfile->count()>0 && count($processing_response)==0): ?>
    <div class="row">
    	<form method="post">
      <div class="twleve columns" style="margin-top:30px;">         
      <h4>Aktualziacja faktury z csv do wFirma.pl</h4>    
      <h5>Import pozycji do faktury: <?php echo $_SESSION['fullnumber'] ?></h5>
      <h6>VAT:
        <select name="vat">
          <option>WDT</option>
          <option>23</option>
        </select>
      </h6>
      <h6>Weryfikacja wprowadzonego pliku</h6> 
      <table style="width:100%;">

      	<tbody>
      		<?php foreach($csvfile->getRow() as $row): ?>
      			<tr id="<?php echo $row[0] ?>">
      				<td ><?php echo $row[0] ?></td>
      				<td><?php echo $row[1] ?></td>
      				<td><?php echo $row[2] ?></td>
      				<td><?php echo $row[3] ?></td>
      				<td><?php echo $row[4] ?></td>
      				<td><?php echo $row[5] ?></td>
      				<td><?php echo $row[6] ?></td>
              <td><?php echo $row[7] ?></td>
              <td><?php echo $row[8] ?></td>
              <td><?php echo $row[9] ?></td>
              <td><?php echo $row[10] ?></td>
      			</tr>
      		<?php endforeach; ?>
      	</tbody>
      </table>
      </div>
        <div class="row">
	  	  <div class="one-half column">
	     		<input class="button-primary" type="submit" value="Zatwierdzam dane" name="processfile">  
	     		<input class="button-primary" type="submit" value="Usuwam plik" name="cancelfile">    		
	      </div>  
  	</div>  
  	</form>
     </div>
<?php endif; ?>     
</div>
<!-- End Document
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
</body>
</html>

