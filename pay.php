<html xmlns="http://www.w3.org/1999/xhtml"><head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Fast&Fruit</title>
  <link href="assets/css/loginStyle.css" rel="stylesheet" type="text/css" media="screen">
  </head>
  <body>
    <?php 
      session_start();
    
      require 'connect.php';
      
      if(!isset($_SESSION['iduser'])){
        exit("Access denied");
      }
      else
        $tot=0;

    $orderid="";
    $shippingCost=0;
		if(isset($_POST['amount'])){
			$tot = str_replace(",", ".", $_POST['amount']);

      if(isset($_POST['shippingCost']))
        $shippingcost=str_replace(",", ".", $_POST['shippingCost']);
      else
        $shippingCost=0;
      $orderid=$_POST['orderID'];
    }
    else{
        if(isset($_COOKIE['shopsubtot'])){
        	$shopsubtot=explode("_",$_COOKIE['shopsubtot']);
       
	        for($i=0;$i<count($shopsubtot)-1;$i++){
	          $idshop_sub=explode("-",$shopsubtot[$i]);
	          $tot+=$idshop_sub[1];
        	}
         }
        if(isset($_COOKIE['shippingcost']))
          $shippingCost=$_COOKIE['shippingcost'];
      }

    ?>
    <div class="main">
      <div class="header">
        <div class="logo">
          <a href="index.php"><img src="assets/images/logo.jpg"></a>
        </div>      
      </div>
      <div class="downBar">
        <p>Frutta e verdura della migliore qualità da più di 1000 negozi in tutta Italia. Accedi subito!</p>
      </div>
      <p id="resultTitle">Paga con paypal</p>
      <a href="done.php?amount=<?php echo $tot+$shippingCost; ?>&payed=true&circuit=paypal<?php if($orderid!='') echo '&order='.$orderid; ?>"><img src="assets/images/paypal.png" id="paypal"></a>
      <div id="amount">
        <?php
          echo "Totale: ".($tot+$shippingCost)."&euro;";
        ?>
      </div>



    </div>
    <div class="footer">
      <p>Fast&Fruit. Copyright &#169; 2016. Tutti i diritti riservati. fastandfruit.it</p>  
    </div>
  </body>
</html>