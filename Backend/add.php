<html xmlns="http://www.w3.org/1999/xhtml"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Fast&Fruit</title>
	<link href="assets/css/loginStyle.css" rel="stylesheet" type="text/css" media="screen">
	</head>
	<body>
		<?php 
			session_start();
		
			require 'connect.php';
			if(isset($_GET['feedbackrating']) && isset($_GET['feedbackText'])){
				$productID = $_GET['idproduct'];
				$rate=$_GET['feedbackrating'];
				$feedbackText=$_GET['feedbackText'];

				if(isset($_SESSION['iduser'])){ //l'utente è loggato
					//controlliamo se esiste un ordine per questo prodotto da parte di questo cliente
					$query = "SELECT o.negozio FROM prodotti p,ordini o, prodottiperordine ppo where ppo.prodotto=".$productID." and ppo.ordine=o.id and o.cliente=".$_SESSION['iduser']." and o.successo=1 and o.eliminato=0;"; 
					$result = mysqli_query($conn,$query) or die("Errore nella selezione dei parametri!");
				
					if(mysqli_num_rows($result) > 0){ //utente ha fatto l'acquisto
						//echo "L'utente ha fatto l'acquisto";
						$shopID = mysqli_fetch_row($result);
						//controlliamo adesso che l'utente non abbia già rilasciato una recensione per questo prodotto
						$queryEsiste = "SELECT r.idprodotto FROM recensioni r where r.idprodotto=".$productID." and r.idcliente=".$_SESSION['iduser'].";"; 
						$resultEsiste = mysqli_query($conn,$queryEsiste) or die("Errore nella selezione dei parametri!");
						if(mysqli_num_rows($resultEsiste) > 0){
							exit("Impossibile inserire il feedback. Ne esiste gia uno per questo prodotto");
						}
						else{
							//non esiste ancora un feedback di questo cliente per questo prodotto. Procediamo ad inserire
							$queryInsFeed = "INSERT INTO `dbfastandfruits`.`recensioni` (`idnegozio`, `idprodotto`, `idcliente`, `valutazione`, `commento`, `nmodifiche`, `rispostanegozio`, `presente`) 
								VALUES (".$shopID[0].", ".$productID.", ".$_SESSION['iduser'].", ".$rate.", '".$feedbackText."', 0, NULL, 1);";
							$resultInsFeed = mysqli_query($conn,$queryInsFeed);
							$type="feed";
							if(!$resultInsFeed){
								echo "errore ".mysqli_error($conn);
							}
						}
					}
					else{
						exit("impossibile lasciare il feedback. Non hai acquistato questo prodotto");
					}
				}
				else if(isset($_SESSION['idseller'])){
					exit("impossibile eseguire l'operazione");
				}
				else{
					header("Location:login.php");
					exit();
				}
			}
			else if(isset($_GET['idproduct']) && !isset($_GET['feedbackrating'])){ //aggiungo un prodotto..
				$type="cart";
				$productID = $_GET['idproduct'];
				$productQuantity = $_GET['quantity'];
				if(isset($_SESSION['iduser'])){ //l'utente è loggato

					$userID = $_SESSION['iduser'];
					//controllo se è presente l'utente nella tabella clienti **************************
					$query = "SELECT c.id FROM clienti c where c.id = '".$userID."';"; 
					$result = mysqli_query($conn,$query) or die("Errore nella selezione dei parametri!");
				
					if(mysqli_num_rows($result) == 1){ //utente presente nella tabella clienti
						$row = mysqli_fetch_row($result); //restituisce un array con l'id dell'utente trovato

						//bisogna controllare se quell'utente ha già questo prodotto nel carrello..
						$query = "SELECT * FROM prodotticarrello p where p.cliente ='".$userID."' AND p.prodotto='".$productID."';";
						$result = mysqli_query($conn,$query) or die("Errore nella selezione dei parametri!");
						if(mysqli_num_rows($result) == 1){ //se l'utente ha già questo prodotto nel carrello
							$row = mysqli_fetch_row($result);
							$oldQuantity=$row[2]; //la quantità è il terzo attributo

							$query="UPDATE `dbfastandfruits`.`prodotticarrello` SET `quantita` = '".($oldQuantity+$productQuantity)."' WHERE `prodotticarrello`.`cliente` = '".$userID."' AND `prodotticarrello`.`prodotto` = '".$productID."';";
							$result = mysqli_query($conn,$query);
							if($result){
								$type="cart";
							}
							else{
								echo "errore ".mysqli_error($conn);
							}
						}
						else{
							$query = "INSERT INTO `dbfastandfruits`.`prodotticarrello` (`cliente`, `prodotto`, `quantita`) VALUES ('".$userID."', '".$productID."', '".$productQuantity."');";
							$result = mysqli_query($conn,$query);
							if($result){
								$type="cart";
							}
							else{
								echo "errore ".mysqli_error($conn);
							}
						}
					}
					else{
						echo "Impossibile acquistare un prodotto con un account negoziante.";
					}
				}
				else if(isset($_SESSION['idseller'])){
					exit("impossibile aggiungere il prodotto nel carrello");
				}
				else{ //se l'utente non è loggato, settiamo il cookie
					//aggiorniamo il cookie "product"
					$finalCookieString="";
					$existed=false;
					if(isset($_COOKIE['product'])){
						$productCookie=$_COOKIE['product'];
						$productList=explode(",",$productCookie); //separo i vari ordini tramite la virgola
						for($i=0; $i<count($productList)-1; $i++){ //per ogni ordine separo l'id dalla quantità, tramite il trattino - . Il -1 si rivela necessario poichè la stringa finisce sempre con la , e non deve prendere il record che c'è a seguire (vuoto)
							$id_quantity=explode("-",$productList[$i]); 
							if($id_quantity[0]==$productID){ //se questo prodotto è già presente nei cookie, devo aggiornare la quantità
								$id_quantity[1]+=$productQuantity;
								$existed=true;
							}
							$finalCookieString.=$id_quantity[0]."-".$id_quantity[1].","; //cosi ricompongo il contenuto del vecchio cookie, eventualmente aggiornato
						}
						if(!($existed)){ //se il prodotto non esisteva, l'accodo al cookie
							$finalCookieString.=$productID."-".$productQuantity.",";
						}
					}
					else{ //non esiste il cookie "product", quindi creiamo il suo contenuto iniziale
						$finalCookieString.=$productID."-".$productQuantity.",";
					}
					setcookie("product",$finalCookieString, time()+2000000, "/");
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
				<p>Frutta e verdura della migliore qualità da più di 1000 negozi in tutta Italia.
					<?php
					if(!isset($_SESSION['iduser'])){
						echo "<a href=\"register.php \">Registrati subito!</a>";
					}
					?>
				</p>
			</div>
			<div id="message">
				<?php
					if($type=="cart"){ ?>
						<div>Prodotto aggiunto al carrello! <a href="javascript:history.back()">Continua con i tuoi acquisti</a> oppure <a href="cart.php">vai al carrello</a></div>
				<?php
					}
					else if($type=="feed"){ ?>
						<div>Feedback inviato con successo! <a href="javascript:history.back()">Torna indietro</a> oppure <a href="index.php">vai alla homepage</a></div>
				<?php 
					}
				?>
			</div>
		<!--
			<div id="message">
				<?php
					if($success==true){
						if($type=="cart"){ ?>
							<div>Prodotto aggiunto al carrello! <?php echo "<a href=\"product.php?id=".$productID."\">"; ?>Continua con i tuoi acquisti</a> oppure <a href="cart.php">vai al carrello</a></div>
					<?php
						}
					}
				?>
			</div>
		-->
		</div>
		<div class="footer">
			<p>Fast&Fruit. Copyright &#169; 2016. Tutti i diritti riservati. fastandfruit.it</p>	
		</div>
	</body>
</html>