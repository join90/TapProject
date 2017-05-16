<html xmlns="http://www.w3.org/1999/xhtml"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Fast&Fruit - prodotto</title>
	<link href="assets/css/style.css" rel="stylesheet" type="text/css" media="screen">
	<link href="assets/css/productStyle.css" rel="stylesheet" type="text/css" media="screen">
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
				
				//join prodotto e negozio
				$queryPN = "SELECT p.negozio, p.titolo, p.prezzo, p.quantUnita, p.disponibilita, n.domicilio, p.marchio, p.provenienza, p.pezzatura, p.maturazione, p.tipoAgricoltura, p.km0, n.imgProfilo, n.nome, n.valutazione, n.citta, n.viaPiazza, n.ncivico, n.telefono, n.giorniSettimanaApertura, n.orariApertura, n.costoDomicilio, p.descrizione, p.pzvenduti FROM prodotti p, negozi n where p.id = ".$_GET['id']." and p.negozio = n.id;";
			
				//query per le immagini corrispondenti al prodotto
				$queryIS = "SELECT i.nomefile, i.principale FROM immagini i where i.prodotto = ".$_GET['id'].";";

				//query per le recensioni dei prodotti
				$queryR = "SELECT c.nome, c.cognome, r.valutazione, r.commento, r.rispostanegozio from clienti c, recensioni r, prodotti p where p.id = ".$_GET['id']." and p.id = r.idprodotto and c.id = r.idcliente and r.presente = 1;";

				//query media valutazione prodotto
				$queryMvP = "SELECT AVG(r.valutazione) as media from recensioni r where r.idprodotto = ".$_GET['id'].";";

				$resultPN = mysqli_query($conn,$queryPN) or die ("Error: ".mysqli_error($conn));
				$resultIS =  mysqli_query($conn,$queryIS) or die ("Error: ".mysqli_error($conn)); //secondary image
				$resultR = mysqli_query($conn,$queryR) or die ("Error: ".mysqli_error($conn));
				$resultMvP = mysqli_query($conn,$queryMvP) or die ("Error: ".mysqli_error($conn));
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
					if(!isset($_SESSION['iduser']) && !isset($_SESSION['idseller']) ){
						echo "<a href=\"register.php \">Registrati subito!</a>";
					}
					?>
				</p>
			</div>
		
			<div id="productPage">
				<div id="picturesContainer">
					<div id="mainPictureContainer">
						<?php
						 	$imgMain; $imgSeconds; $count=0;
							while ($arrayIS = mysqli_fetch_row($resultIS)) {
								if($arrayIS[1] == 1){
									$imgMain = $arrayIS[0]; //mi conservo l'immagine principale
									echo '<img src="prodotti/'.$imgMain.'">'; //fetch main image	
								}
								else{
									$imgSeconds[$count] = $arrayIS[0];
									$count++;									
								}
							}
							 
						?>	
					</div>

					<div class="miniPictureContainer">
						<img src=
							<?php
								echo '"prodotti/'.$imgMain.'"';
							?>
						>
					</div>

					<?php
						if(isset($imgSeconds)){
							for ($i = 0;  $i<count($imgSeconds); $i++) { //fetch secondary image
							echo "<div class=\"miniPictureContainer\">
							<img src=\"prodotti/".$imgSeconds[$i]." \"></div>";
						}
					}
					?>

				</div>
				<div id="title">
					<p><?php 
						$arrayP = mysqli_fetch_row($resultPN);
						echo $arrayP[1];
					?>
					</p>
				</div>
				<div id="sell">
					<p id="price">
						<?php
							echo str_replace(".", ",", $arrayP[2])."&euro;";
						?>

					</p>
					<p id="quant">Quantità: 

							<?php
								echo $arrayP[3];

							?>
					</p>
					<form method="get" action="add.php" name="addProduct">
						<input <?php echo "value=\"".$_GET['id']."\" ";	?> id="prodid" type="hidden" name="idproduct">
						<input value="1" id="selectedQuantity" type="text" name="quantity">
						<input value="Aggiungi al carrello" id="cartButton" type="submit">
						
					</form>
						<p id="disponib">Disponibilità: <span>
						
					<?php
						if($arrayP[4] == 1)
							echo "bassa";
						elseif ($arrayP[4] == 2) 
							echo "media";
						else
							echo "alta";
						
					?>

					</span></p>
					<p id="disponib">Domicilio: 
							<?php
								if($arrayP[5] == 0)
									echo "NO";
								else
									echo "SI";
							?>

					</p>
					<p id="instructions">Dopo aver aggiunto il prodotto nel carrello puoi decidere se andarlo a ritirare in negozio 
						o usufruire della consagna a domicilio (se disponibile), pagare online o in contanti.</p>
				</div>
				<div id="specifications">
					<p class="sectionName">Caratteristiche del prodotto</p>
					<ul class="specList">
						<?php
							if($arrayP[6] != "")
								echo '<li>Marca: '.ucfirst($arrayP[6]).'</li>';
						?>
						
						<li>Provenienza: 
						<?php
							if($arrayP[7] != "")
								echo ucfirst($arrayP[7]);
							else
								echo "-";
						?>
						</li>
						<li>Pezzatura: 
						<?php
							if($arrayP[8] == 1)
								echo "piccola";
							elseif ($arrayP[8] == 2) {
								echo "media";
							}
							else
								echo "grande";

						?>

						</li>
						<li>Maturazione: 
						<?php
							if($arrayP[9] == 1)
								echo "meno maturo";
							elseif ($arrayP[9] == 2) {
								echo "media";
							}
							else
								echo "più maturo";

						?>

						</li>
						<li>Biologico: 
						<?php
							if($arrayP[10] == 1)
								echo "SI";
							else
								echo "NO";

						?>

						</li>
						<li>Km 0: 
						<?php
							if($arrayP[11] == 0)
								echo "NO";
							else
								echo "SI";
						?>

						</li>
						
						<li>Quantità venduta: 
						<?php echo $arrayP[23]; ?>
						</li>

						<?php 
							$arrayMvP = mysqli_fetch_row($resultMvP);
								if($arrayMvP[0]>0){
						?>
			
						<li>Valutazione: 
						<?php 
							
							$val = explode("0", $arrayMvP[0]);
							echo "<p id=\"productVote\">".$val[0]."</p>"; //paragrafo invisibile
						?>
							<fieldset class="productRating">
	    <input type="radio" id="star5" name="productrating" value="5" /><label class = "full" for="star5" title="Awesome - 5 stars"></label>
	    <input type="radio" id="star4half" name="productrating" value="4 and a half" /><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>
	    <input type="radio" id="star4" name="productrating" value="4" /><label class = "full" for="star4" title="Pretty good - 4 stars"></label>
	    <input type="radio" id="star3half" name="productrating" value="3 and a half" /><label class="half" for="star3half" title="Meh - 3.5 stars"></label>
	    <input type="radio" id="star3" name="productrating" value="3" /><label class = "full" for="star3" title="Meh - 3 stars"></label>
	    <input type="radio" id="star2half" name="productrating" value="2 and a half" /><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>
	    <input type="radio" id="star2" name="productrating" value="2" /><label class = "full" for="star2" title="Kinda bad - 2 stars"></label>
	    <input type="radio" id="star1half" name="productrating" value="1 and a half" /><label class="half" for="star1half" title="Meh - 1.5 stars"></label>
	    <input type="radio" id="star1" name="productrating" value="1" /><label class = "full" for="star1" title="Sucks big time - 1 star"></label>
	    <input type="radio" id="starhalf" name="productrating" value="half" /><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>
	</fieldset>


						</li>
					<?php 
						}
					?>
					</ul>
				</div>
				<div id="shopInfo">
					<p class="sectionName">Informazioni sul venditore</p>
					<div id="imgShopContainer">
						<?php
							echo '<img src="negozi/'.$arrayP[12].'">';
						?>						
					</div>
					<div id="infoShopContainer">
						<ul class="specList">
							<li>Negozio: 
								<?php
									echo '<a href="shop.php?id='.$arrayP[0].'">'.ucfirst($arrayP[13]).'</a>';
								?>

							</li>
						<?php 
							if($arrayP[14]!=0){
						?>
							<li>Valutazione: 
							<?php
								echo "<p id=\"shopVote\">".$arrayP[14]."</p>"; //paragrafo invisibile
							?>
							 <fieldset class="shopRating">
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
							</li>
						<?php 
							}
						?>
							<li>Città: 
							<?php
								echo ucfirst($arrayP[15]);
							?>

							</li>
							<li>Indirizzo: 
							<?php
								echo $arrayP[16]." ".$arrayP[17];
							?>

							</li>
							<?php 
							if($arrayP[18]!=NULL)
								echo '<li>Telefono: '.$arrayP[18].'</li>';
							?>

							</li>
							<li>Giorni di apertura: 
							<?php
								echo $arrayP[19];
							?>

							</li>
							<li>Orari apertura: 
							<?php
								echo $arrayP[20];
							?>

							</li>

							<?php

								if($arrayP[5] != 0)
									echo '<li>Costo domicilio: '.$arrayP[21].'&euro;</li>';
							?>
							
							<li>Consegna a domicilio: 
							<?php
								if($arrayP[5] == 0)
									echo "NO";
								else
									echo "SI";
							?>

							</li>
						</ul>
					</div>
				</div>
				<div id="description">
					<p class="sectionName">Descrizione del prodotto</p>
					<p id="descriptionText">
						<?php
							echo $arrayP[22];
						?>

					</p>
				</div>

				<div id="feedback">
					<p class="sectionName">Commenti</p>
					<?php
						while ($arrayR = mysqli_fetch_row($resultR)) {
							echo '<div class="feedbackContainer">
							<div id="stars">'.$arrayR[2].' stelle</div>
							<p id="author">'.$arrayR[0].' '.$arrayR[1].'</p>
							<p id="feedbackText">'.$arrayR[3].'</p>';
							if($arrayR[4]!=NULL){
								echo '<p id="reply">Risposta del negoziante</p>
								<p id="shopReply">'.$arrayR[4].'</p>';
							}
							echo '</div>';
						}

					?>

					<div id="insertComment">Inserisci commento</div>
					<form method="get" action="add.php" name="feedback" id="feedbackSend">	
						<!--div per inserire un feedback, di default nascosto-->
						<div id="feedbackInsertionContainer">
							<div id="starsSelector">
								
									<div class="rating">
									    <input type="radio" class="feedRate" name="feedbackrating" value="0" checked /><span id="hide"></span>
									    <input type="radio" class="feedRate" name="feedbackrating" value="1" /><span></span>
									    <input type="radio" class="feedRate" name="feedbackrating" value="2" /><span></span>
									    <input type="radio" class="feedRate" name="feedbackrating" value="3" /><span></span>
									    <input type="radio" class="feedRate" name="feedbackrating" value="4" /><span></span>
									    <input type="radio" class="feedRate" name="feedbackrating" value="5" /><span></span>
									</div>
								
							</div>
							<textarea id="userComment" name="feedbackText" maxlength="500">Inserisci un commento sul prodotto (max 500 caratteri)</textarea>
							<input type="hidden" value=<?php echo '"'.$_GET['id'].'"'; ?> name="idproduct">
							<input value="Invia" class="feedbackSubmit" type="submit">
						</div>
				</form>
				</div>
				
			</div>
		</div>

		<div class="footer">
			<p>Fast&Fruit. Copyright &#169; 2016. Tutti i diritti riservati. fastandfruit.it</p>	
		</div>
	</body>
</html>