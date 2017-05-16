<html xmlns="http://www.w3.org/1999/xhtml"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Fast&Fruit - carrello</title>
	<link href="assets/css/style.css" rel="stylesheet" type="text/css" media="screen">
	<link href="assets/css/cartStyle.css" rel="stylesheet" type="text/css" media="screen">
	<script src="assets/js/js.cookie.js"></script>
	<script src="assets/js/jquery.js"></script>
	<script src="assets/js/main.js"></script> 
	<!-- <script src="assets/js/ajax.js"></script> -->
	</head>
	<body>

		<?php
			error_reporting(E_ALL);
			session_start();
			require 'connect.php';

			if(isset($_COOKIE['session'])){
				$_SESSION['iduser'] = $_COOKIE['session'];
			}

			if(!isset($_SESSION['iduser'])){ //l'utente NON è loggato.
				if(isset($_SESSION['idseller'])){
					exit("Access denied");
				}
				else{
					header("Location:login.php");
					$_COOKIE['strproduct'] = $_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING']; //il famoso cookie che ricorda qual è stata l'ultima pagina visitata prima del login
					setcookie('strproduct',$_COOKIE['strproduct'],time()+2000000,'/');
				}
			}
			else{
				if(isset($_COOKIE['strproduct'])){
					unset($_COOKIE['strproduct']);
					setcookie("strproduct","",time()-2000000, "/");
				}
				$userID = $_SESSION['iduser'];
				//controllo se è presente l'utente nella tabella clienti **************************
				$query = "SELECT c.id FROM clienti c where c.id = '".$userID."';"; 
				$result = mysqli_query($conn,$query) or die("Errore nella selezione dei parametri!");
				
				if(mysqli_num_rows($result) == 1){ //utente presente nella tabella clienti
					if(isset($_COOKIE['product'])){ //se esiste il cookie, dobbiamo riversare il suo contenuto nel database

						$productCookie=$_COOKIE['product'];
						$productList=explode(",",$productCookie); //separo i vari ordini tramite la virgola
						for($i=0; $i<count($productList)-1; $i++){ //per ogni ordine separo l'id dalla quantità, tramite il trattino - . Il -1 si rivela necessario poichè la stringa finisce sempre con la , e non deve prendere il record che c'è a seguire (vuoto)
							$id_quantity=explode("-",$productList[$i]);
							//echo $id_quantity[0]." ".$id_quantity[1];	
							//bisogna ora controllare se questi prodotti sono già presenti nel carrello del database dell'utente
							$query = "SELECT * FROM prodotticarrello p where p.cliente ='".$userID."' AND p.prodotto='".$id_quantity[0]."';";
							$result = mysqli_query($conn,$query) or die("Errore nella selezione dei parametri!");
							if(mysqli_num_rows($result) == 1){ //se l'utente ha già questo prodotto nel carrello del db
								$row = mysqli_fetch_row($result);
								$oldQuantity=$row[2]; //la quantità è il terzo attributo

								$query="UPDATE `dbfastandfruits`.`prodotticarrello` SET `quantita` = '".($oldQuantity+$id_quantity[1])."' WHERE `prodotticarrello`.`cliente` = '".$userID."' AND `prodotticarrello`.`prodotto` = '".$id_quantity[0]."';";
								$result = mysqli_query($conn,$query);
								if(!$result){
									echo "errore ".mysqli_error($conn);
								}
							}
							else{
								$query = "INSERT INTO `dbfastandfruits`.`prodotticarrello` (`cliente`, `prodotto`, `quantita`) VALUES ('".$userID."', '".$id_quantity[0]."', '".$id_quantity[1]."');";
								$result = mysqli_query($conn,$query);
								if(!$result){
									echo "errore ".mysqli_error($conn);
								}
							}
						}
						//dopo aver riversato tutto, si elimina il cookie
						unset($_COOKIE['prodotto']);
						setcookie("product","", time()-2000000, "/");
					}
				}
				else{
					echo "Impossibile accedere al carrello con un account negoziante.";
				}
			}
			$toDelete=false;
			if(isset($_POST['aggiorna'])){ //cliccato "aggiorna"
				$newQuantity=$_POST['changequantity'];
				$newProductID=$_POST['cartproductid'];
				$newUserID = $_SESSION['iduser'];
				if($newQuantity==0)
					$toDelete=true;
				$query="UPDATE `dbfastandfruits`.`prodotticarrello` SET `quantita` = '".$newQuantity."' WHERE `prodotticarrello`.`cliente` = '".$newUserID."' AND `prodotticarrello`.`prodotto` = '".$newProductID."';";
				$result = mysqli_query($conn,$query);
				if(!$result){
					echo "errore ".mysqli_error($conn);
				}
			}
			if(isset($_POST['elimina']) || $toDelete==true){
				$newProductID=$_POST['cartproductid'];
				$newUserID = $_SESSION['iduser'];
				$query="DELETE FROM `dbfastandfruits`.`prodotticarrello` WHERE `prodotticarrello`.`cliente` = ".$newUserID." AND `prodotticarrello`.`prodotto` = ".$newProductID.";";
				$result = mysqli_query($conn,$query);
				if(!$result){
					echo "errore ".mysqli_error($conn);
				}
			}
			

			$queryCitta = "SELECT DISTINCT citta FROM negozi n";
			$resultCitta = mysqli_query($conn, $queryCitta) or die("Errore nella selezione delle citta nella tabella 'negozi'");
		?>

		<div class="main">
			<div class="header">
				<div class="logo">
					<a href="index.php"><img src="assets/images/logo.jpg"></a>
				</div>
				<div class="search">
					<form method="get" action="search.php" name="cerca">
						<input value="Cerca il tuo prodotto.." id="userSearch" autocomplete="off" type="text" name="findproduct">
					 	<select class="citySelection" name="selectcity">
					 	<?php
					 		while($citta=mysqli_fetch_row($resultCitta)){
					 			$arrayCity[]=$citta[0];
								echo "<option value=\"".$citta[0]."\">".ucfirst($citta[0])."</option>";
							}
						?>
						</select> 
						<input value ="" class="submit" type="submit">
					</form>
				</div>

				<div class="right">
				<?php 
					if(isset($_SESSION['idseller']))
						echo '<a href=""><img src="assets/images/cart.png"></a>';
					else
						echo '<a href="cart.php"><img src="assets/images/cart.png"></a>';
				?>
					<p id="cartNumber">

						<?php
							//se l'utente è loggato
							if (isset($_SESSION['iduser'])) {
								$account="account.php?tipoOrdine=0";
								$query = "SELECT count(*) as numprodotti FROM prodotticarrello p where p.cliente = ".$_SESSION['iduser'].";"; //numero prodotti nel carrello					
								$result = mysqli_query($conn,$query) or die("Errore nella selezione dei parametri!");
			
								if(mysqli_num_rows($result) == 1){
									$row = mysqli_fetch_row($result); //restituisce un array con i prodotti dell'utente trovato
									echo $row[0];	
								}		
							}
							else if(isset($_SESSION['idseller'])){
								$account="controlPanel.php";
								echo "0";
							}
							else if(isset($_COOKIE['product'])){
								$productCookie=$_COOKIE['product'];
								$productList=explode(",",$productCookie); //separo i vari ordini tramite la virgola
								
								echo count($productList);
								$account="login.php";
							}
							else{
								echo "0"; //nessun prodotto nel carrello
								$account="login.php";
							}	
						?>
					</p>
				<?php echo '<a href="'.$account.'"><img src="assets/images/profile2.png"></a>'; ?>

					<a href= <?php
							if(isset($_SESSION['iduser'])) //se l'utente è loggato...
								echo "account.php?tipoOrdine=0"; //...il tasto verde reindirizza nel proprio account
							else if(isset($_SESSION['idseller']))
								echo "controlPanel.php";
							else{
								echo "login.php"; //altrimenti reindirizza al link di login
								$_COOKIE['strproduct'] = $_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING']; //il famoso cookie che ricorda qual è stata l'ultima pagina visitata prima del login
								setcookie('strproduct',$_COOKIE['strproduct'],time()+2000000,'/'); 
							}
						?>
						    ><p id="login">
						<?php
							if(isset($_SESSION['iduser']) || isset($_SESSION['idseller'])) //se l'utente è loggato il stato verde si chiamerà "account"
								echo "Account";	
							else
								echo "Accedi";

						?>

					</p></a>

					<!-- tastino sotto quello verde. O logout o registrati -->
					<a href= <?php
							if(isset($_SESSION['iduser']) || isset($_SESSION['idseller']))
								echo "index.php?action=logout"; 
							else
								echo "registerUser.php";
							?>

					><p id="register">
								<?php
								
								if((isset($_SESSION['iduser']) || isset($_SESSION['idseller'])))
									echo "logout";
								else
									echo "o registrati";
							?>
					</p></a>
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
			
			<div id="mainCart">
				<div id="coreTitle">Carrello</div>
				<div id="cartLegend">
					<p class="cartProductTitle">Titolo</p>
					<p class="cartQuantity">Quantità</p>
					<p class="cartProductCost">Costo unitario</p>
					<p class="cartProductTotCost">Costo totale</p>
				</div>
				<?php 
					$productList=array();
					$ProdQuantity=array();
					$prodIdQuantString="";
					$shopIdSubtot="";
					$subtot=0;
					$shippingCost=0;
					$Nnegozi=0;
					$count;
					$shid=array();
					$shSub=array();
					$prshco;
					$query = "SELECT p.titolo, pc.quantita, pc.prodotto, p.prezzo, i.nomefile FROM prodotticarrello pc, prodotti p, immagini i where pc.cliente = '".$userID."' and p.id = pc.prodotto and i.principale = 1 and p.presente = 1 and i.prodotto=pc.prodotto;";
					$resultp = mysqli_query($conn,$query) or die ("Error: ".mysqli_error($conn));

					if(mysqli_num_rows($resultp) > 0){
						while ($arrayProduct = mysqli_fetch_row($resultp)) {
							$subtot+=($arrayProduct[3]*$arrayProduct[1]); //aggiorno il totale
							$productList[]=$arrayProduct[2]; //lista dei prodotti da passare a done.php
							$ProdQuantity[]=$arrayProduct[1]; //quantità dei prodotti da passare a done.php
							echo '
				<div class="cartOrder">
					<div class="cartProductImage">
						<img src="prodotti/'.$arrayProduct[4].'">
					</div>
					<p class="cartProductTitle">'.$arrayProduct[0].'</p>
					<p class="cartQuantity">'.$arrayProduct[1].'</p>
					<form method="post" action="cart.php" class="form">
					<div class="cartQuantInput">
						<p>Cambia quantità</p>
						<input value="1" type="text" id="cartQuantityInput" name="changequantity">
						<input value="'.$arrayProduct[2].'" type="hidden" id="cartProductId" name="cartproductid">
					</div>
					<div class="cartOrderButtonsContainer">
						<input value="Aggiorna" class="cartOrderButtons" id="updateButton" type="submit" name="aggiorna">
						<input value="Elimina" class="cartOrderButtons" id="deleteButton" type="submit" name="elimina">
					</div>
					</form>
					<p class="cartProductCost">'.$arrayProduct[3].'&euro;</p>
					<p class="cartProductTotCost">'.($arrayProduct[3]*$arrayProduct[1]).'&euro;</p>
				</div>';

							$query="SELECT n.id, n.costoDomicilio FROM negozi n WHERE n.id=(SELECT p.negozio FROM prodotti p WHERE p.id='".$arrayProduct[2]."');";
							$result = mysqli_query($conn,$query) or die ("Error: ".mysqli_error($conn));
							if(mysqli_num_rows($result) > 0){
								$row = mysqli_fetch_row($result);
								$exist=false;
								$count=0;
								
								for($i=0;$i<count($shid); $i++){
									if($row[0]==$shid[$i]){
										$exist=true;
										$count=$i;
									}
								}
								if($exist==false){ //se non ci sono altri prodotti di un negozio già contato, si accodano i valori
									$shid[]=$row[0];
									$prshco[]=$row[1];

									$shSub[]=($arrayProduct[3]*$arrayProduct[1]); //costo del prodotto X
								}
								else{
									$shSub[$count]+=($arrayProduct[3]*$arrayProduct[1]); //aggiorno il totale di QUEL negozio
								}

								
							}
						}
						for($i=0; $i<count($prshco); $i++){ //sommiamo i costi di spedizione e contiamo i negozi coinvolti
							$shippingCost+=$prshco[$i];
							$Nnegozi++;
						}
						for($i=0;$i<count($productList);$i++){ //creiamo la stringa con id ordini e quantità da passare a done.php
							$prodIdQuantString.=$productList[$i]."-".$ProdQuantity[$i]."_";
						}
						for($i=0;$i<count($shid); $i++){
							$shopIdSubtot.=$shid[$i]."-".$shSub[$i]."_";
						}
					}
				?>

				<div id="checkoutContainer">
					<p id="checkout"><?php echo $subtot; ?>&euro;</p>
					<p id="checkoutTitle">Totale: </p>
				</div>
				<form method="get" action="done.php" class="form">
					<input value=<?php echo '"'.$prodIdQuantString.'"'; ?> class="cartFinishButtons" type="hidden" name="productidsq">
					<input value=<?php echo '"'.$shopIdSubtot.'"'; ?> class="cartFinishButtons" type="hidden" name="shopsubtot">
					<input value=<?php echo '"'.$shippingCost.'"'; ?> class="cartFinishButtons" type="hidden" name="shippingcost">
					<div id="lowBar">
						<input value="Ritira i prodotti in negozio" class="cartFinishButtons" type="submit" name="goToShop">
					</div>
					<div id="lowBar">
						<input value="Paga online e ritira i prodotti in negozio" class="cartFinishButtons" type="submit" name="payGoToShop">
					</div>
					<div id="lowBar">
						<input value="Paga online e consegna i prodotti a domicilio" class="cartFinishButtons" type="submit" name="payHome" onclick="checkTime()" id="payHome">
					</div>
					<div id="shippingCostContainer">
						<p id="shippingCost">
							<?php 
								echo $shippingCost."&euro; da ".$Nnegozi;
								if($Nnegozi>1)
									echo " negozi";
								else
									echo " negozio";
							?>
						</p>
						<p id="shippingCostTitle">Costo consegna a domicilio: </p>
					</div>
					<div id="shippingTime">
						<select name="hour" class="hour" id="selectHour">
							<option value="0" selected>ore</option>
	  						<option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option>
	  						<option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option>
	  						<option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option>
	  						<option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option>
	  						<option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option>
	  						<option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option>
						</select>
						<select name="minute"class="minute" id="selectMinute">
							<option value="0" selected>min</option>
	  						<option value="00">00</option><option value="15">15</option><option value="30">30</option><option value="45">45</option>
						</select>
						<p>Scegli l'orario per la spedizione:</p> 
					</div>
				</form>

			</div>
		</div>
		<div class="footer">
			<p>Fast&Fruit. Copyright &#169; 2016. Tutti i diritti riservati. fastandfruit.it</p>	
		</div>
	</body>
</html>