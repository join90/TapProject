<html xmlns="http://www.w3.org/1999/xhtml"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Fast&Fruit - negozio</title>
	<link href="assets/css/style.css" rel="stylesheet" type="text/css" media="screen">
	<link href="assets/css/productStyle.css" rel="stylesheet" type="text/css" media="screen">
	<link href="assets/css/shopStyle.css" rel="stylesheet" type="text/css" media="screen">
	<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
	<script src="assets/js/js.cookie.js"></script>
	<script src="assets/js/jquery.js"></script>
	<script src="assets/js/main.js"></script>
	<script src="assets/js/product.js"></script>
	</head>
	<body>

		<?php
		
			session_start();
			require 'connect.php';

			if(isset($_COOKIE['session']))
				$_SESSION['iduser'] = $_COOKIE['session'];

			//id del prodotto passato via get
			if(isset($_GET['id'])){
				//query info negozio
				$queryNegozio = "SELECT * from negozi n where n.id=".$_GET['id'].";";
				//query per le recensioni dei prodotti
				$queryRecensioni = "SELECT c.nome, c.cognome, r.valutazione, r.commento, r.rispostanegozio, p.titolo from clienti c, recensioni r, prodotti p where c.id = r.idcliente and p.id=r.idprodotto and r.idnegozio=".$_GET['id'].";";

				$resultNegozio = mysqli_query($conn,$queryNegozio) or die ("Error: ".mysqli_error($conn));
				$resultRecensioni = mysqli_query($conn,$queryRecensioni) or die ("Error: ".mysqli_error($conn));
				if(mysqli_num_rows($resultNegozio) > 0){
					$shopInfo = mysqli_fetch_row($resultNegozio); //restituisce un array con i prodotti dell'utente trovato	
				}	
			}

			if (isset($_COOKIE['strproduct'])) {
				unset($_COOKIE['strproduct']);
				setcookie("strproduct","",time()-2000000,"/");	
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
					if(!isset($_SESSION['iduser']) && !isset($_SESSION['idseller'])){
						echo "<a href=\"register.php \">Registrati subito!</a>";
					}
					?>
				</p>
			</div>
		
			<div id="productPage">
				<div id="shopPicturesContainer">
					<div id="shopMainPictureContainer">
					<?php echo'<img src="negozi/'.$shopInfo[11].'">'; ?>
					</div>
				</div>
				<div id="mainShopTitle">
					<p> <?php echo $shopInfo[1]; ?></p>
				</div>
				<div id="mainShopInfo">
					<p class="sectionName">Informazioni sul venditore</p>
					<div id="infoShopContainer">
						<ul class="mainSpecList">
					<?php 
						if($shopInfo[12]>0){
							echo '<li>Valutazione: <p id="shopVote">'.$shopInfo[12].'</p>
							
							 <fieldset class="mainShopRating">
	    <input type="radio" id="star5" name="shoprating" value="5" /><label class = "full" for="star5" title="Awesome - 5 stars"></label>
	    <input type="radio" id="star4half" name="shoprating" value="4 and a half" /><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>
	    <input type="radio" id="star4" name="shoprating" value="4" /><label class = "full" for="star4" title="Pretty good - 4 stars"></label>
	    <input type="radio" id="star3half" name="shoprating" value="3 and a half" /><label class="half" for="star3half" title="Meh - 3.5 stars"></label>
	    <input type="radio" id="star3" name="shoprating" value="3" /><label class = "full" for="star3" title="Meh - 3 stars"></label>
	    <input type="radio" id="star2half" name="shoprating" value="2 and a half" /><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>
	    <input type="radio" id="star2" name="shoprating" value="2" /><label class = "full" for="star2" title="Kinda bad - 2 stars"></label>
	    <input type="radio" id="star1half" name="shoprating" value="1 and a half" /><label class="half" for="star1half" title="Meh - 1.5 stars"></label>
	    <input type="radio" id="star1" name="shoprating" value="1" /><label class = "full" for="star1" title="Sucks big time - 1 star"></label>
	    <input type="radio" id="starhalf" name="shoprating" value="half" /><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>
	</fieldset>
							</li>';
						}
					?>
							<li>Città: 
								<?php echo $shopInfo[2]; ?>
							</li>
							<li>Indirizzo:
								<?php echo $shopInfo[3]." n. ".$shopInfo[4]; ?>
							</li>
						<?php 
							if($shopInfo[5]!="")
								echo '<li>Telefono: '.$shopInfo[5].'</li>';
							if($shopInfo[6]!="")
								echo '<li>Cellulare: '.$shopInfo[6].'</li>';
						?>
							<li>Giorni di apertura: 
								<?php echo $shopInfo[7]; ?>
							</li>
							<li>Orari apertura: 
								<?php echo $shopInfo[8]; ?>
							</li>
							<li>Consegna a domicilio: 
						<?php 
							if($shopInfo[9]==0)
								echo "NO"; 
							else{
								echo "SI";
								echo '</li> <li>Costo domicilio: '.str_replace(".", ",", $shopInfo[10]).'&euro;</li>';
							}
						?>
						</ul>
					</div>
				</div>
				<div id="coreTitle">Altri prodotti del negoziante</div>
				<div id="publishedOrdersContainer">
					<?php
						
							//prodotti del negoziante
							$queryProd = "SELECT p.id, i.nomefile, p.titolo, p.marchio, p.provenienza, p.prezzo, p.prezzoVecchio, p.quantUnita, p.tipoAgricoltura, p.km0 
							FROM prodotti p, negozi n, immagini i 
							where n.id=".$_GET['id']." and p.negozio = n.id and p.id = i.prodotto and i.principale = 1 and p.presente = 1 and n.presente = 1;";							
							$resultProd = mysqli_query($conn,$queryProd) or die ("Error: ".mysqli_error($conn));

							if(mysqli_num_rows($resultProd) > 0){
								while ($arrayProduct = mysqli_fetch_row($resultProd)) {
									$checked=false;
									//valutazione media del prodotto
									$queryVal="SELECT AVG(r.valutazione) FROM recensioni r WHERE r.idprodotto=".$arrayProduct[0].";";
									$resultVal=mysqli_query($conn,$queryVal) or die ("Error: ".mysqli_error($conn));
									if(mysqli_num_rows($resultVal)>0){
										$valutaz = mysqli_fetch_row($resultVal);
										if($valutaz[0]==NULL)
											$valutazione=false;
										else
											$valutazione=true;
									}


									echo 
					 			'<a href="product.php?id='.$arrayProduct[0].'">
									<div id="productContainer">
										<div id="imageContainer">
											<img src="prodotti/'.$arrayProduct[1].'">
										</div>
										<div id="infoContainer">
											<p id="shopTitle">'.$arrayProduct[2].'</p>';

											if($arrayProduct[6] != 0)
												echo '<p id="shopOldPrice">'.$arrayProduct[6].'€</p>';

											echo '<p id="shopPrice">'.str_replace(".", ",", $arrayProduct[5]).'€</p>
									<p id="shopQuantity">quantità:'.$arrayProduct[7].'</p>
									<p id="shopMoreInfo">';

											if ($arrayProduct[3] != "")
												echo 'Marca: '.$arrayProduct[3].'</br>';
											if ($arrayProduct[4] != "")  
												echo 'Provenienza: '.$arrayProduct[4].'</br>';
										
											if ($arrayProduct[8] == 0)
												echo 'Tipo di agricoltura: normale</br>';
											elseif ($arrayProduct[8] == 1) 
												echo 'Tipo di agricoltura: biologica</br>';
											else
												echo 'Tipo di agricoltura: integrata</br>';

											if ($arrayProduct[9] != "")
												echo 'Km0: SI</br>';
											else
												echo 'Km0: NO</br>';
									echo '						
									</p>';

									if($valutazione==true){
										echo '
									<div id="evaluation">Valutazione:</div>
									<div class="rating">
										<fieldset class="shopProductRating" id="productRat'.$arrayProduct[0].'" name="productRat'.$arrayProduct[0].'">
		    <input type="radio" id="star5" name="productrating'.$arrayProduct[0].'" value="5" ';
		    $checked=false;
		    if($valutaz[0]>4.75){
		    	echo 'checked="checked"';
		    	$checked=true;
		    }
		    echo ' /><label class = "full" for="star5" title="Awesome - 5 stars"></label>
		    <input type="radio" id="star4half" name="productrating'.$arrayProduct[0].'" value="4 and a half" '; 
		    if($valutaz[0]>4.25 && $checked==false){
		    	echo 'checked="checked"';
		    	$checked=true;
		    }
		    echo '  /><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>
		    <input type="radio" id="star4" name="productrating'.$arrayProduct[0].'" value="4" '; 
		    if($valutaz[0]>3.75 && $checked==false){
		    	echo 'checked="checked"';
		    	$checked=true;
		    }
		    echo ' /><label class = "full" for="star4" title="Pretty good - 4 stars"></label>
		    <input type="radio" id="star3half" name="productrating'.$arrayProduct[0].'" value="3 and a half" '; 
		    if($valutaz[0]>3.25 && $checked==false){
		    	echo 'checked="checked"';
		    	$checked=true;
		    }
		    echo ' /><label class="half" for="star3half" title="Meh - 3.5 stars"></label>
		    <input type="radio" id="star3" name="productrating'.$arrayProduct[0].'" value="3" '; 
		    if($valutaz[0]>2.75 && $checked==false){
		    	echo 'checked="checked"';
		    	$checked=true;
		    }
		    echo ' /><label class = "full" for="star3" title="Meh - 3 stars"></label>
		    <input type="radio" id="star2half" name="productrating'.$arrayProduct[0].'" value="2 and a half" '; 
		    if($valutaz[0]>2.25 && $checked==false){
		    	echo 'checked="checked"';
		    	$checked=true;
		    }
		    echo ' /><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>
		    <input type="radio" id="star2" name="productrating'.$arrayProduct[0].'" value="2" '; 
		    if($valutaz[0]>1.75 && $checked==false){
		    	echo 'checked="checked"';
		    	$checked=true;
		    }
		    echo ' /><label class = "full" for="star2" title="Kinda bad - 2 stars"></label>
		    <input type="radio" id="star1half" name="productrating'.$arrayProduct[0].'" value="1 and a half" '; 
		    if($valutaz[0]>1.25 && $checked==false){
		    	echo 'checked="checked"';
		    	$checked=true;
		    }
		    echo ' /><label class="half" for="star1half" title="Meh - 1.5 stars"></label>
		    <input type="radio" id="star1" name="productrating'.$arrayProduct[0].'" value="1" '; 
		    if($valutaz[0]>0.75 && $checked==false){
		    	echo 'checked="checked"';
		    	$checked=true;
		    }
		    echo ' /><label class = "full" for="star1" title="Sucks big time - 1 star"></label>
		    <input type="radio" id="starhalf" name="productrating'.$arrayProduct[0].'" value="half" '; 
		    if($valutaz[0]>0.25 && $checked==false){
		    	echo 'checked="checked"';
		    	$checked=true;
		    }
		    echo ' /><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>
		</fieldset>
									</div>';
								}
								echo'
								</div>
							</div>
						</a>';
								
							}
						}
			?>

				
				</div>

				<div id="shopFeedback">
					<p class="sectionName">Commenti</p>
					<?php
						if(mysqli_num_rows($resultRecensioni) > 0){
							while ($arrayR = mysqli_fetch_row($resultRecensioni)) {
								echo '<div class="shopFeedbackContainer">
								<div id="shopStars">'.$arrayR[2].' stelle</div>
								<p id="shopAuthor">'.$arrayR[0].' '.$arrayR[1].'</p>
								<p id="shopProduct">'.$arrayR[5].'</p>
								<p id="shopFeedbackText">'.$arrayR[3].'</p>';
								if($arrayR[4]!=NULL){
									echo '<p id="shopReply">Risposta del negoziante</p>
									<p id="shopShopReply">'.$arrayR[4].'</p>';
								}
								echo '</div>';
							}
						}

					?>

					
				</div>

				
			</div>
		</div>

		<div class="footer">
			<p>Fast&Fruit. Copyright &#169; 2016. Tutti i diritti riservati. fastandfruit.it</p>	
		</div>
	</body>
</html>