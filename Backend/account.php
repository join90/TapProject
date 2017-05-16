<html xmlns="http://www.w3.org/1999/xhtml"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Fast&Fruit - Account </title>
	<link href="assets/css/AccountStyle.css" rel="stylesheet" type="text/css" media="screen">
	<link href="assets/css/searchStyle.css" rel="stylesheet" type="text/css" media="screen">
	<script src="assets/js/js.cookie.js"></script>
	<script src="assets/js/jquery.js"></script>
	<script src="assets/js/main.js"></script>
	</head>
	<body>
		<?php
			session_start();
			require 'connect.php';
			if(isset($_COOKIE['session']))
				$_SESSION['iduser'] = $_COOKIE['session'];

			if(!isset($_SESSION['iduser']))
				exit("Access denied");

			if(isset($_GET['elimina'])){ //elimina ordine
				
				$query = 'DELETE FROM prodottiperordine WHERE prodottiperordine.ordine='.$_GET['id'].';';
				mysqli_query($conn,$query);

				unset($_GET['id']);
				header("Location: account.php?tipoOrdine=0");
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
					if(!isset($_SESSION['iduser']) && !isset($_SESSION['idseller'])){
						echo "<a href=\"register.php \">Registrati subito!</a>";
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
				require 'connect.php';
				if (isset($_GET['tipoOrdine']) && isset($_SESSION['iduser'])) {
					$query = "SELECT i.id, i.prezzoTot, i.dataOraOrdine, i.pagato, i.archiviato, i.dataOraConsegna, n.nome, i.domicilio, n.costoDomicilio from Ordini i, negozi n where i.cliente = ".$_SESSION['iduser']." and i.negozio = n.id and i.archiviato = ".$_GET['tipoOrdine']." and i.eliminato=0 ORDER BY i.dataOraOrdine DESC;";
					
					$resultO = mysqli_query($conn,$query);
					
					if (mysqli_num_rows($resultO) > 0)
						echo '<p id="resultTitle">Lista Ordini</p>';
					else
						echo '<p id="resultTitle">Nessun Risultato</p>';

				?>

			<div class="core">
					
				<?php
					while ($arrayO = mysqli_fetch_row($resultO)) {
					
				?>
			
				<div id="orderContainer">
					<a href= <?php
							echo "order.php?id=".$arrayO[0]."&tipoOrdine=".$_GET['tipoOrdine']; //mi porto 1)l'id dell'ordine per visualizzare i prodotti che ne fanno parte 2)il tipo di ordine, per sapere se i prodotti possono essere eliminati da quell'ordine
						?>
					>
						<div id="infoContainer">
							<div id="title">Ordine N° <?php echo $arrayO[0];?></div>
							<div id="tot">
								<p id="pricet">Totale:</p>
								<p id="price"><?php echo str_replace(".", ",", $arrayO[1]);?>€</p>
							</div>
							<div id="moreInfo">
								Data/Ora ordine: <?php echo $arrayO[2]; ?></br>
								Pagato: <?php 
									if($arrayO[3] == 0)
										echo "NO";
									else
										echo "SI";
								?></br>
								<?php
									if ($arrayO[3] == 1) 
										echo 'Data/Ora consegna: '.$arrayO[5].'</br>';
									
								?>
								Negozio: <?php echo $arrayO[6]; ?>
							</div>
						</div>
					</a>
					<?php 
						if (($arrayO[3] == 0) && ($arrayO[4] == 0)){ //se non è pagato e non è archiviato
							echo '<form action="pay.php" method="post">
       								<input type="hidden" name="amount" value="'.$arrayO[1].'" />
       								<input type="hidden" name="orderID" value="'.$arrayO[0].'" />';
       						if($arrayO[7]==1)
       							echo'<input value="'.$arrayO[8].'" type="hidden" name="shippingCost">';
       						
       						echo '<input type="submit" id="paga" value="Paga"/>
   								</form>';

							echo '<a href="account.php?elimina=true&id='.$arrayO[0].'"><div id="elimina">Elimina</div></a>';	
						}
						echo "</div>";
					
				}
		
				} 
			
			?>			
					 
		</div>	
		<div class="footer">
			<p>Fast&Fruit. Copyright &#169; 2016. Tutti i diritti riservati. fastandfruit.it</p>	
		</div>
	</body>
</html