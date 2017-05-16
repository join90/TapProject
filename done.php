<html xmlns="http://www.w3.org/1999/xhtml"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Fast&Fruit</title>
	<link href="assets/css/loginStyle.css" rel="stylesheet" type="text/css" media="screen">
	</head>
	<body>
		<?php 
			session_start();
		
			require 'connect.php';
			
			if(isset($_SESSION['iduser'])){ //utente loggato
				
				//else if($_SERVER['HTTP_REFERER']!="cart.php" || $_SERVER['HTTP_REFERER']!="pay.php")
				//	exit("Access denied");

				if(!isset($_GET['payed'])){
					$productidsqToExp=$_GET['productidsq'];
					$shopsubtotToExp=$_GET['shopsubtot'];
					if($productidsqToExp=="" || $shopsubtotToExp=="")
						exit("carrello vuoto");

					$negozio=1;
					//$pagato=0;
					$domicilio="0";
					$timestamp="NULL";
					$today=date("Y-m-d H:i:s");
					$ordersIdList="";
					$success=false;
					//se devo pagare online, conservo tutte le info del carrello in 2 cookie e reindirizzo alla pagina pay.php
					if(isset($_GET['payGoToShop']) || isset($_GET['payHome'])){
						setcookie("productidsq",$_GET['productidsq'], time()+2000000, "/");
						setcookie("shopsubtot",$_GET['shopsubtot'], time()+2000000, "/");
						if(isset($_GET['payHome'])){
							$domicilio="1";
							$negozio="0";
							//setcookie("domicilio","",time()+2000000, "/");
							//ricordiamo anche l'ora di consegna selezionata
							$date = date("Y-m-d");
							$time = " ".$_GET['hour'].":".$_GET['minute'].":00"; //" 12:22:00"
							$timestamp="'".$date.$time."'";
							//setcookie("oraconsegna",$timestamp,time()+2000000, "/");
							setcookie("shippingcost",$_GET['shippingcost'], time()+2000000, "/");
						}

						//header("Location:pay.php");
						//exit();//non esegue il codice successivo
					}
					else{
						$success=true;
					}

					
					

					$productidsq=explode("_",$productidsqToExp);
					$productID=array();
					$productQuantity=array();
					for($i=0;$i<count($productidsq)-1;$i++){
						$id_quantity=explode("-",$productidsq[$i]);
						$productID[$i]=$id_quantity[0];
						$productQuantity=$id_quantity[1];
					}
					$shopsubtot=explode("_",$shopsubtotToExp);
					$shopID=array();
					$shopSub=array();
					for($i=0;$i<count($shopsubtot)-1;$i++){
						$idshop_sub=explode("-",$shopsubtot[$i]);
						$shopID[$i]=$idshop_sub[0];
						$shopSub[$i]=$idshop_sub[1];
					}

					//controlliamo che tutti i negozi fanno spedizione a domicilio, nel caso in cui si richieda la consegna a domicilio
					if(isset($_GET['payHome'])){
						$noDomicilio=false;
						for($i=0;$i<count($shopID);$i++){	
							$queryDom="SELECT n.domicilio FROM negozi n WHERE n.id=".$shopID[$i].";";
							$resultDom = mysqli_query($conn,$queryDom) or die ("Error: ".mysqli_error($conn));
							if(mysqli_num_rows($resultDom) > 0){
								$dom = mysqli_fetch_row($resultDom);
								if($dom[0]==0)
									$noDomicilio=true;
							}
						}
						if($noDomicilio==true){
							unset($_COOKIE['productidsq']);
							unset($_COOKIE['shopsubtot']);
							unset($_COOKIE['shippingcost']);
							setcookie("productidsq","", time()-2000000, "/");
							setcookie("shopsubtot","", time()-2000000, "/");
							setcookie("shippingcost","", time()-2000000, "/");
							exit("Non tutti i negozi selezionati effettuano consegna a domicilio. Rivedi il tuo carrello o rinuncia al servizio a domicilio.");
						}
					}
					if(isset($_GET['payGoToShop']) || isset($_GET['payHome']))
						header("Location:pay.php");

					//adesso inseriamo nel db tanti ordini quanti sono i negozi coinvolti
					for($i=0;$i<count($shopID);$i++){
						$query = "INSERT INTO `dbfastandfruits`.`ordini` (`prezzoTot`, `dataOraConsegna`, `domicilio`, `pagato`, `pronto`, `successo`, `archiviato`, `cliente`, `negozio`, `dataOraOrdine`, `eliminato`, `modificato`) 
						VALUES ('".$shopSub[$i]."', ".$timestamp.", ".$domicilio.", 0, 0, 0, 0, '".$_SESSION['iduser']."', '".$shopID[$i]."', '".$today."', 0, 0);";
						$result = mysqli_query($conn,$query);
						if(!$result){
							echo "errore ".mysqli_error($conn);
						}
						$lastID=mysqli_insert_id($conn);
						
						$queryP = "SELECT pc.prodotto,pc.quantita,p.prezzo FROM prodotticarrello pc, prodotti p where p.negozio=".$shopID[$i]." and p.id=pc.prodotto;";				
						$resultP = mysqli_query($conn,$queryP) or die("Errore nella selezione dei parametri!");
				
						if(mysqli_num_rows($resultP) > 0){
							while ($arrayProduct = mysqli_fetch_row($resultP)) {
								$queryPPO = "INSERT INTO `dbfastandfruits`.`prodottiperordine` (`ordine`, `prodotto`, `quantita`, `prezzoQuantita`) 
								VALUES (".$lastID.", ".$arrayProduct[0].", ".$arrayProduct[1].", ".($arrayProduct[2]*$arrayProduct[1]).");";
								$resultPPO = mysqli_query($conn,$queryPPO);
								if(!$resultPPO){
									echo "errore ".mysqli_error($conn);
								}
								
								
									$queryDPC="DELETE FROM `dbfastandfruits`.`prodotticarrello` WHERE `prodotticarrello`.`cliente` = ".$_SESSION['iduser']." AND `prodotticarrello`.`prodotto` = ".$arrayProduct[0].";";
									$resultDPC = mysqli_query($conn,$queryDPC);
									if(!$resultDPC){
										echo "errore ".mysqli_error($conn);
									}
								
							}
						}
						$ordersIdList.=$lastID."_";
					}
					setcookie("ordersid",$ordersIdList, time()+2000000, "/");
				}
				else{
					if($_GET['payed']=="true"){
						if(isset($_GET['order'])){
							$query="UPDATE `dbfastandfruits`.`ordini` SET `pagato` = 1 WHERE `ordini`.`id` = '".$_GET['order']."';";
								$result = mysqli_query($conn,$query);
								if(!$result){
									echo "errore ".mysqli_error($conn);
								}
								else{
									$dataora=date("Y-m-d H:i:s");
									$queryTrans = "INSERT INTO `dbfastandfruits`.`transazioni` (`data`, `importo`, `esito`, `circuito`, `ordine`) 
									VALUES ('".$dataora."', ".$_GET['amount'].", 1, '".$_GET['circuit']."', ".$_GET['order'].");";
									$resultTrans = mysqli_query($conn,$queryTrans);
									if(!$resultTrans){
										echo "errore ".mysqli_error($conn);
									}
								}
						}
						else{
							$shopsToUpdate=explode("_",$_COOKIE['ordersid']);
					        for($i=0;$i<count($shopsToUpdate)-1;$i++){
					          $query="UPDATE `dbfastandfruits`.`ordini` SET `pagato` = 1 WHERE `ordini`.`id` = '".$shopsToUpdate[$i]."';";
								$result = mysqli_query($conn,$query);
								if(!$result){
									echo "errore ".mysqli_error($conn);
								}
								else{
									$shopsubtotToExp=$_COOKIE['shopsubtot'];
									$shopsubtot=explode("_",$shopsubtotToExp);
									$shopSub=array();
									for($j=0;$j<count($shopsubtot)-1;$j++){
										$idshop_sub=explode("-",$shopsubtot[$j]);
										$shopSub[$j]=$idshop_sub[1];
									}
									$dataora=date("Y-m-d H:i:s");
									$queryTrans = "INSERT INTO `dbfastandfruits`.`transazioni` (`data`, `importo`, `esito`, `circuito`, `ordine`) 
									VALUES ('".$dataora."', ".$shopSub[$i].", 1, '".$_GET['circuit']."', ".$shopsToUpdate[$i].");";
									$resultTrans = mysqli_query($conn,$queryTrans);
									if(!$resultTrans){
										echo "errore ".mysqli_error($conn);
									}
								}
					        }
					    }
				    }

					$success=true;
					unset($_COOKIE['oraconsegna']);
					unset($_COOKIE['productidsq']);
					unset($_COOKIE['shopsubtot']);
					unset($_COOKIE['shippingcost']);
					unset($_COOKIE['ordersid']);
					setcookie("oraconsegna","", time()-2000000, "/");
					setcookie("productidsq","", time()-2000000, "/");
					setcookie("shopsubtot","", time()-2000000, "/");
					setcookie("shippingcost","", time()-2000000, "/");
					setcookie("ordersid","", time()-2000000, "/");
				}
							
			}
			else{
				exit("access denied");
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
		<?php 
			if($success){
		?>
			<p id="resultTitle">Ordine confermato</p>
			<div id="success">Ordine andato a buon fine! <a href="index.php">Ritorna nella home page</a> oppure <a href="account.php?tipoOrdine=0">vai al tuo account</a></div>
		<?php 
			}
			else{
		?>
			<p id="resultTitle">Errore</p>
			<div id="success">Errore durante la registrazione dell'ordine.</a></div>
		<?php 		
			}
		?>
		</div>
		<div class="footer">
			<p>Fast&Fruit. Copyright &#169; 2016. Tutti i diritti riservati. fastandfruit.it</p>	
		</div>
	</body>
</html>