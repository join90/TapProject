<html xmlns="http://www.w3.org/1999/xhtml"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Fast&Fruit</title>
	<link href="assets/css/style.css" rel="stylesheet" type="text/css" media="screen">
	<link href="assets/css/searchStyle.css" rel="stylesheet" type="text/css" media="screen">
	<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
	<script src="assets/js/js.cookie.js"></script>
	<script src="assets/js/jquery.js"></script>
	<script src="assets/js/main.js"></script>
	</head>
	<body>
		<?php
			session_start();

			require 'connect.php';

			if(isset($_GET['action'])){ //se è presente il parametro action, vuol dire che si tenta di fare il logout
				unset($_SESSION['iduser']); //chiude la sessione
				unset($_SESSION['idseller']);
				unset($_COOKIE['session']); //elimina il cookie che ricorda l'id dell'utente nel tempo
				setcookie("session","",time()-2000000,"/");	
				header("Location:index.php");
				exit();
			}

			if(isset($_COOKIE['session'])) //infatti se esiste questo cookie, la sessione viene ristabilita
				$_SESSION['iduser'] = $_COOKIE['session'];

			if(isset($_GET['selectcity'])){
				$city = $_GET['selectcity'];
			}
			else if(isset($_COOKIE['city'])){
				//setto il cookie city per ricordarci nelle pagine la città selezionata
				$city = $_COOKIE['city'];
			}
			else{
				$city = "catania"; //default
			}
			$_COOKIE['city'] = $city;
			setcookie('city',$city,time()+2000000,'/');
			

			if (isset($_COOKIE['strproduct'])) { //cancella il cookie che tiene traccia dell'ultima pagina visitata prima del login (serve a fare il redirect)
				unset($_COOKIE['strproduct']);
				setcookie("strproduct","",time()-2000000,"/");	
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
				<p id="listTitle">Cambia città</p>
				<select class="citySelectionLeftIndex" name="city" onchange="location = this.options[this.selectedIndex].value;">
						<?php
					 		for($i=0;$i<count($arrayCity);$i++){
								echo "<option value=\"index.php?selectcity=".$arrayCity[$i]."\">".ucfirst($arrayCity[$i])."</option>";
							}
						?>
					</select>
				<p id="listTitle">Prodotti per categorie</p>
				<ul class="cat">
					<li>
						<p>Frutta &#9660;</p><br>
						<ul class="productList" id="fruit">
					<?php 
						$queryFrutta = "SELECT DISTINCT p.categoria FROM prodotti p where p.tipo=\"frutta\";";						
						$resultFrutta = mysqli_query($conn,$queryFrutta) or die ("Error: ".mysqli_error($conn));
						if(mysqli_num_rows($resultFrutta) > 0){
							while ($arrayFrutta = mysqli_fetch_row($resultFrutta)) {
								echo '<li><a href="search.php?findproduct=&selectcity='.$city.'&categoria='.trim($arrayFrutta[0]).'">'.$arrayFrutta[0].'</a></li>';
							}
						}
					?>
						<!--
							<li>
								<a href="">Mele</a>
							</li>
						-->
						</ul>
					</li>
					<li>
						<p>Verdura &#9660;</p><br>
						<ul class="productList" id="vegetables">
					<?php 
						$queryVerdura = "SELECT DISTINCT p.categoria FROM prodotti p where p.tipo=\"verdura\";";						
						$resultVerdura = mysqli_query($conn,$queryVerdura) or die ("Error: ".mysqli_error($conn));
						if(mysqli_num_rows($resultVerdura) > 0){
							while ($arrayVerdura = mysqli_fetch_row($resultVerdura)) {
								echo '<li><a href="search.php?findproduct=&selectcity='.$city.'&categoria='.trim($arrayVerdura[0]).'">'.$arrayVerdura[0].'</a></li>';
							}
						}
					?>
						<!--
							<li>
								<a href="">Lattughe</a>
							</li>
						-->
						</ul>
					<li>
						<p>Frutta secca &#9660;</p><br>
						<ul class="productList" id="driedFruit">
					<?php 
						$queryFruttaSecca = "SELECT DISTINCT p.categoria FROM prodotti p where p.tipo=\"frutta secca\";";						
						$resultFruttaSecca = mysqli_query($conn,$queryFruttaSecca) or die ("Error: ".mysqli_error($conn));
						if(mysqli_num_rows($resultFruttaSecca) > 0){
							while ($arrayFruttaSecca = mysqli_fetch_row($resultFruttaSecca)) {
								echo '<li><a href="search.php?findproduct=&selectcity='.$city.'&categoria='.trim($arrayFruttaSecca[0]).'">'.$arrayFruttaSecca[0].'</a></li>';
							}
						}
					?>
						<!--
							<li>
								<a href="">Lattughe</a>
							</li>
						-->
						</ul>
					</li>
					<li>
						<p>Legumi &#9660;</p><br>
						<ul class="productList" id="legumes">
					<?php 
						$queryLegumi = "SELECT DISTINCT p.categoria FROM prodotti p where p.tipo=\"legumi\";";						
						$resultLegumi = mysqli_query($conn,$queryLegumi) or die ("Error: ".mysqli_error($conn));
						if(mysqli_num_rows($resultLegumi) > 0){
							while ($arrayLegumi = mysqli_fetch_row($resultLegumi)) {
								echo '<li><a href="search.php?findproduct=&selectcity='.$city.'&categoria='.trim($arrayLegumi[0]).'">'.$arrayLegumi[0].'</a></li>';
							}
						}
					?>
						<!--
							<li>
								<a href="">Lattughe</a>
							</li>
						-->
						</ul>
					</li>
					<li>
						<p>Spezie/aromi &#9660;</p><br>
						<ul class="productList" id="spices">
					<?php 
						$querySpezie = "SELECT DISTINCT p.categoria FROM prodotti p where p.tipo=\"spezie\";";						
						$resultSpezie = mysqli_query($conn,$querySpezie) or die ("Error: ".mysqli_error($conn));
						if(mysqli_num_rows($resultSpezie) > 0){
							while ($arraySpezie = mysqli_fetch_row($resultSpezie)) {
								echo '<li><a href="search.php?findproduct=&selectcity='.$city.'&categoria='.trim($arraySpezie[0]).'">'.$arraySpezie[0].'</a></li>';
							}
						}
					?>
						<!--
							<li>
								<a href="">Aglio</a>
							</li>
						-->
						</ul>
					</li>
					<li>
						<p>Altro &#9660;</p><br>
						<ul class="productList" id="other">
					<?php 
						$queryAltro = "SELECT DISTINCT p.categoria FROM prodotti p where p.tipo=\"altro\";";						
						$resultAltro = mysqli_query($conn,$queryAltro) or die ("Error: ".mysqli_error($conn));
						if(mysqli_num_rows($resultAltro) > 0){
							while ($arrayAltro = mysqli_fetch_row($resultAltro)) {
								echo '<li><a href="search.php?findproduct=&selectcity='.$city.'&categoria='.trim($arrayAltro[0]).'">'.$arrayAltro[0].'</a></li>';
							}
						}
					?>
						<!--
							<li>
								<a href="">cesti</a>
							</li>
						-->
						</ul>
					</li>
				</ul>
			</div>
			<div class="trend">
				<div class="trendingProduct">
					<?php 
						echo '<a href="search.php?findproduct=&selectcity='.$city.'&agricoltura=1"><img src="assets/images/bio.jpg"></a>'; 
					?>
					
					<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Prodotti Biologici</p>
				</div>
				<div class="trendingProduct">
					<?php 
						echo '<a href="search.php?findproduct=&selectcity='.$city.'&km0=1"><img src="assets/images/km0.jpg"></a>'; 
					?>
					<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Prodotti a km 0</p>
				</div>
				<div class="trendingProduct">
					<?php 
						echo '<a href="search.php?findproduct=&selectcity='.$city.'&domicilio=1"><img src="assets/images/domicilio.jpg"></a>'; 
					?>
					<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Consegne a domicilio</p>
				</div>
				<div class="trendingProduct">
					<?php 
						echo '<a href="search.php?findproduct=cesto&selectcity='.$city.'"><img src="assets/images/cesto.jpg"></a>'; 
					?>
					<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cesti della settimana</p>
				</div>
			</div>
			<div class="core">
				<div id="coreTitle">
					Prodotti in evidenza a
					<select class="citySelectionEvidence" name="city" onchange="location = this.options[this.selectedIndex].value;">
						<?php
					 		for($i=0;$i<count($arrayCity);$i++){
								echo "<option value=\"index.php?selectcity=".$arrayCity[$i]."\">".ucfirst($arrayCity[$i])."</option>";
							}
						?>
					</select>
				</div>
				<?php
					$query = "SELECT p.id, i.nomefile, p.titolo, p.promozione, p.prezzoVecchio, p.prezzo, p.quantUnita, p.marchio, p.provenienza, p.tipoAgricoltura, p.km0, n.nome 
					FROM prodotti p, negozi n, immagini i 
					WHERE p.negozio = n.id and p.id = i.prodotto and i.principale = 1 and p.presente = 1 and p.promozione= 1 and n.presente = 1 and n.citta like '".$city."' ORDER BY p.pzvenduti DESC;"; //and p.promozione= 1

					$resultp = mysqli_query($conn,$query) or die ("Error: ".mysqli_error($conn));

					if(mysqli_num_rows($resultp) > 0){
						while ($arrayProduct = mysqli_fetch_row($resultp)) {
							$valutazione=false;
							$checked=false;

							//valutazione media del prodotto
							$queryVal="SELECT AVG(r.valutazione) FROM recensioni r WHERE r.idprodotto=".$arrayProduct[0].";";
							$resultVal=mysqli_query($conn,$queryVal) or die ("Error: ".mysqli_error($conn));
							if(mysqli_num_rows($resultVal)>0){
								$valutaz = mysqli_fetch_row($resultVal);
								$valutazione=true;
							}
							if($valutaz[0]==NULL)
								$valutazione=false;

								echo 
					 			'<a href="product.php?id='.$arrayProduct[0].'">
									<div id="productContainer">
										<div id="imageContainer">
											<img src="prodotti/'.$arrayProduct[1].'">
										</div>
										<div id="infoContainer">
											<p id="title">'.$arrayProduct[2].'</p>';

											if($arrayProduct[3] != 0)
												echo '<p id="oldPrice">'.str_replace(".", ",", $arrayProduct[4]).'€</p>';

											echo '<p id="price">'.str_replace(".", ",", $arrayProduct[5]).'€</p>
									<p id="quantity">quantità:'.$arrayProduct[6].'</p>
									<p id="moreInfo">';

											if ($arrayProduct[7] != "")
												echo 'Marca: '.$arrayProduct[7].'</br>';
											if ($arrayProduct[8] != "") 
											echo 'Provenienza: '.$arrayProduct[8].'</br>';
										
											if ($arrayProduct[9] == 0)
												echo 'Tipo di agricoltura: normale</br>'; 
											elseif ($arrayProduct[9] == 1) 
												echo 'Tipo di agricoltura: biologica</br>';
											else
												echo 'Tipo di agricoltura: integrata</br>';

											if ($arrayProduct[10] == 1)
												echo 'Km0: SI</br>';
											else
												echo 'Km0: NO</br>';
									echo '						
									</p>';

									if($valutazione==true){
										echo '
									<div id="evaluation">Valutazione:</div>
									<div class="rating">
										<fieldset class="productRating" id="productRat'.$arrayProduct[0].'" name="productRat'.$arrayProduct[0].'">
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
									<p id="shopName">Negozio: '.$arrayProduct[11].'</p>
								</div>
							</div>
						</a>';
								
						}
					}
			?>
			<!-- singolo prodotto 
				<a href="">
					<div id="productContainer">
						<div id="imageContainer">
							<img src="">
						</div>
						<div id="infoContainer">
							<p id="title"></p>
							<p id="oldPrice"></p>
							<p id="price"></p>
							<p id="quantity"></p>
							<p id="moreInfo">
								Marca: xxx</br>
								Provenienza:</br>
								Tipo di agricoltura: normale</br>
								Tipo di agricoltura: biologica</br>
							</p>
							<p id="shopName">Negozio: </p>
						</div>
					</div>
				</a>
			-->
			</div>
		</div>
		<div class="footer">
			<p>Fast&Fruit. Copyright &#169; 2016. Tutti i diritti riservati. fastandfruit.it</p>	
		</div>
	</body>
</html>