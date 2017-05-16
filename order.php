<html xmlns="http://www.w3.org/1999/xhtml"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Fast&Fruit - </title>
	<link href="assets/css/OrderStyle.css" rel="stylesheet" type="text/css" media="screen">
	<link href="assets/css/searchStyle.css" rel="stylesheet" type="text/css" media="screen">
	<script src="assets/js/js.cookie.js"></script>
	<script src="assets/js/jquery.js"></script>
	<script src="assets/js/main.js"></script>
	</head>
	<body>
		<?php
			session_start();
			require 'connect.php';

			if(!isset($_SESSION['iduser']))
				exit("Access denied");

			if(isset($_COOKIE['session']))
				$_SESSION['iduser'] = $_COOKIE['session'];


			if (isset($_GET['elimina'])) { //bisogna eliminare il prodotto dall'ordine
				$query = "DELETE FROM prodottiperordine WHERE ordine = ".$_GET['id']." and prodotto= ".$_GET['idp'].";";
				$resultelim = mysqli_query($conn,$query);
				unset($_GET['id']); //elimino l'id dell'elemento che ho eliminato
				header("location: account.php?tipoOrdine=0"); 
			}

			if(isset($_GET['id'])){ //id dell'ordine
				$query = "SELECT p.titolo,p.promozione,p.prezzoVecchio,o.prezzoQuantita,o.quantita,p.marchio,p.provenienza,p.tipoAgricoltura,n.nome,i.nomefile,p.id,oo.pagato from prodottiperordine o, prodotti p, negozi n, immagini i, ordini oo where o.ordine = ".$_GET['id']." and o.prodotto = p.id and p.negozio = n.id and i.prodotto = p.id and i.principale = 1 and oo.id = o.ordine;";

				$resultp = mysqli_query($conn,$query);

			}

			$queryCitta = "SELECT DISTINCT citta FROM negozi n";
			$resultCitta = mysqli_query($conn, $queryCitta) or die("Errore nella selezione delle citta nella tabella 'negozi'");
			$arrayCity=array();

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
					if(isset($_SESSION['iduser']) || isset($_SESSION['idseller'])){
						echo "";
					}
					else{
						echo "<a href=\"registerUser.php \">Registrati subito!</a>";
					}
					?>
				</p>
			</div>
			<div class="leftMenu">
				<div class="ordine">
					<p class="listTitle">Ordini:</p>
					<div class="order">
						<a  href="account.php?tipoOrdine=0">Ordini in Corso</a>
						<a href="account.php?tipoOrdine=1">Storico Ordini</a>
					</div>
				</div>
				<div class="ordine">
					<p class="listTitle">Impostazioni Account:</p>
					<div class="order"><a  href="registerUser.php">Modifica dati</a></div>
					
				</div>
			</div>
			
			<?php
				if(isset($_GET['id']))
				{
					if (mysqli_num_rows($resultp) > 0) {
						echo '<p id="resultTitle">Prodotti Ordine</p>';
						
			?>
			<div class="core">
				<?php
					while ($arrayO = mysqli_fetch_row($resultp)) {
				
				?>
				<div id="productContainer">
					<div id="imageContainer">
						<img src=<?php
								echo "prodotti/".$arrayO[9];
							?>
						>
					</div>
					<div id="infoContainer">
						<div id="title"><a href=<?php echo '"product.php?id='.$arrayO[10].'">'.$arrayO[0]; ?></a></div>
						<p id="oldPrice"><?php 
							if($arrayO[1] == 1)
								echo str_replace(".", ",", $arrayO[2])."€";
						?>
						</p>
						<p id="price"><?php echo str_replace(".", ",", $arrayO[3]);?>€</p>
						<p id="quantity">quantità: <?php echo $arrayO[4];?></p>
						<p id="moreInfo">
							<?php  
								if($arrayO[5] != "")
									echo "Marca: ".$arrayO[5]."</br>";	
							?>						 
							Provenienza: <?php echo $arrayO[6];?></br>			
							Tipo di agricoltura: <?php  
								if($arrayO[7] == 0)
									echo "normale";
								elseif ($arrayO[7]==1)
									 echo "biologica";
							?>
							</br>						
						</p>
						<p id="shopName">Negozio: <?php echo $arrayO[8];?></p>
					</div>
					<?php
						if(!isset($_GET['elimina']) && $arrayO[11] == "0")
							if ($_GET['tipoOrdine'] == 0) 
								echo '<a id="elimina" href="order.php?elimina=true&id='.$_GET['id'].'&idp='.$arrayO[10].'"><div>Elimina</div></a>';			
						

						echo "</div>";
						
						}
					?>
					
			</div>
			<?php 
				
				}

				else  
					echo '<p id="resultTitle">Nessun Risultato</p>';
			}

			?>
		</div>	
		<div class="footer">
			<p>Fast&Fruit. Copyright &#169; 2016. Tutti i diritti riservati. fastandfruit.it</p>	
		</div>
	</body>
</html