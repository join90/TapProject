<html xmlns="http://www.w3.org/1999/xhtml"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Fast&Fruit</title>
	<link href="assets/css/loginStyle.css" rel="stylesheet" type="text/css" media="screen">
	</head>
	<body>
		<?php 
			session_start();
		
			require 'connect.php';
			
			if(isset($_SESSION['idseller'])){
				if(isset($_GET['delete'])){ //prodotto da "eliminare"
					$productID=$_GET['productToModify'];
					$query="UPDATE `dbfastandfruits`.`prodotti` SET `presente` = 0 WHERE `prodotti`.`id` = '".$productID."';";
					$result = mysqli_query($conn,$query);
					if(!$result){
						echo "errore ".mysqli_error($conn);
					}
					else{
						exit("Prodotto eliminato correttamente. <a href=\"controlpanel.php\">Torna al pannello di controllo</a>");

					}
				}
				if(isset($_GET['newPrice'])){ //aggiornamento prezzo
					$newPrice=$_GET['newPrice'];
					$newPrice=str_replace(',','.',$newPrice);
					$productID=$_GET['productid'];
					if($_GET['newPrice']<$_GET['old']){ //prezzo ridotto
						$query="UPDATE `dbfastandfruits`.`prodotti` SET `prezzo` =\"".$newPrice."\", `prezzoVecchio` = ".$_GET['old'].", `promozione` = 1 WHERE `prodotti`.`id` = '".$_GET['productid']."';";
					}
					else{
						$query="UPDATE `dbfastandfruits`.`prodotti` SET `prezzo` = \"".$newPrice."\" WHERE `prodotti`.`id` = '".$_GET['productid']."';";
					}
					$result = mysqli_query($conn,$query);
					if(!$result){
						echo "errore ".mysqli_error($conn);
					}
					else{
						exit("Prezzo aggiornato correttamente. <a href=\"controlpanel.php\">Torna al pannello di controllo</a>");

					}
				}
				else if(isset($_GET['feedback'])){ //invio risposta al feedback
					$query="UPDATE `dbfastandfruits`.`recensioni` SET `rispostanegozio` = '".$_GET['replyFeedback']."' WHERE `recensioni`.`idnegozio` = '".$_GET['idshop']."' and `recensioni`.`idprodotto` = '".$_GET['idproduct']."' and `recensioni`.`idcliente` = '".$_GET['idclient']."';";
					$result = mysqli_query($conn,$query);
					if(!$result){
						echo "errore ".mysqli_error($conn);
					}
					else{
						exit("risposta inviata correttamente. <a href=\"controlpanel.php\">Torna al pannello di controllo</a>");
					}
				}
			}

		?>
		<div class="main">
			<div class="header">
				<div class="logo">
					<img src="assets/images/logo.jpg">
				</div>			
			</div>
			<div class="downBar">
				<p>Frutta e verdura della migliore qualità da più di 1000 negozi in tutta Italia.</p>
			</div>

			<p id="resultTitle">Cambia prezzo</p>
			<form method="get" action="" name="Login">
				<div id="login">
					<div class="log">
					<?php 
						if(isset($_SESSION['idseller'])){
							if(isset($_GET['sale'])){ //in offerta
								$productID=$_GET['productToModify'];

								$query = "SELECT p.prezzo FROM prodotti p WHERE p.id=".$productID.";";					
								$result = mysqli_query($conn,$query) or die ("Error: ".mysqli_error($conn));
								if(mysqli_num_rows($result) > 0){
									$prezzo=mysqli_fetch_row($result);
				}

							}
						}

					echo '<input type="hidden" name="productid" value="'.$productID.'"></input>
							<input type="hidden" name="old" value="'.$prezzo[0].'"></input>
						<div class="titlePrice">Vecchio prezzo</div>
						<input type="text" name="oldPrice" value="'.$prezzo[0].'" disabled></input>';
				?>
					</div>	
					<div class="log">
						<div class="titlePrice">Nuovo prezzo</div>
						<input type="text" name="newPrice"></input>
					</div>
					<!--<div id="radios">
						<div class="radio">
        					<div class="titler">client</div> 
        					<input type="radio" name="scelta" value="clienti"/>
        				</div>
        				<div class="radio">
        					<div class="titler">seller</div> 
        					<input type="radio" name="scelta" value="negozi"/>
        				</div>
        			</div>-->
        			<div id="sendlogin">
						<input name="" type="submit" value="Modifica"/>
					</div>
				</div>
			</form>
		</div>
		<div class="footer">
			<p>Fast&Fruit. Copyright &#169; 2016. Tutti i diritti riservati. fastandfruit.it</p>	
		</div>
	</body>
</html>