<html xmlns="http://www.w3.org/1999/xhtml"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Fast&Fruit - ricerca</title>
	<link href="assets/css/style.css" rel="stylesheet" type="text/css" media="screen">
	<link href="assets/css/searchStyle.css" rel="stylesheet" type="text/css" media="screen">
	<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
	<script src="assets/js/js.cookie.js"></script>
	<script src="assets/js/jquery.js"></script>
	<script src="assets/js/main.js"></script>
	</head>
	<body>
		<?php
			error_reporting(E_ALL);
			session_start();
			require 'connect.php';


			if(isset($_COOKIE['session']))
				$_SESSION['iduser'] = $_COOKIE['session'];

			//parola chiave di ricerca
			$product = $_GET['findproduct'];
			$product = trim($product); //elimina gli spazi all'inizio della stringa
			$product = mysqli_real_escape_string($conn,$product);
			//setto il cookie city per ricordarci nelle pagine la città selezionata
			$city = $_GET['selectcity'];
			$city = trim($city);
			$city = mysqli_real_escape_string($conn,$city);
			$_COOKIE['city'] = $city;
			setcookie('city',$city,time()+2000000,'/');
			
					
			
			if (isset($_COOKIE['strproduct'])) {
				unset($_COOKIE['strproduct']);
				setcookie("strproduct","",time()-2000000,"/");	
			}

			$queryCitta = "SELECT DISTINCT citta FROM negozi n";
			$resultCitta = mysqli_query($conn, $queryCitta) or die("Errore nella selezione delle citta nella tabella 'negozi'");
			$arrayCity=array();
			//mysqli_close($conn);
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
				<p id="listTitle">Cambia città</p>
				<select class="citySelectionLeft" name="city" onchange="location = this.options[this.selectedIndex].value;">
						<?php
					 		for($i=0;$i<count($arrayCity);$i++){
								echo "<option value=\"search.php?findproduct=&selectcity=".$arrayCity[$i]."\">".ucfirst($arrayCity[$i])."</option>";
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
			<div class="filters">
				<p>Filtri di ricerca</p>
				<form method="get" action="search.php" name="filters">
					<?php 
						if(isset($_GET['findproduct']))
							echo "<input type=\"hidden\" value=\"".$_GET['findproduct']."\" name=\"findproduct\">";
						if(isset($_GET['selectcity']))
							echo "<input type=\"hidden\" value=\"".$_GET['selectcity']."\" name=\"selectcity\">";
						if(isset($_GET['categoria']))
							echo "<input type=\"hidden\" value=\"".$_GET['categoria']."\" name=\"categoria\">";
					
					?>
					<div>
						<select class="pezzatura" name="pezzatura">
							<option value="0" <?php if(!isset($_GET['pezzatura'])) echo "selected"; ?> disabled>Pezzatura</option>
		  					<option value="1" <?php if(isset($_GET['pezzatura']) && $_GET['pezzatura']==1) echo "selected"; ?>>Piccola</option>
							<option value="2" <?php if(isset($_GET['pezzatura']) && $_GET['pezzatura']==2) echo "selected"; ?>>Media</option>
							<option value="3" <?php if(isset($_GET['pezzatura']) && $_GET['pezzatura']==3) echo "selected"; ?>>Grande</option>
						</select>
					</div>
					<div>
						<select class="maturazione" name="maturazione">
							<option value="0" <?php if(!isset($_GET['maturazione'])) echo "selected"; ?> disabled>Maturazione</option>
		  					<option value="1" <?php if(isset($_GET['maturazione']) && $_GET['maturazione']==1) echo "selected"; ?>>Meno maturo</option>
							<option value="2" <?php if(isset($_GET['maturazione']) && $_GET['maturazione']==2) echo "selected"; ?>>Maturazione media</option>
							<option value="3" <?php if(isset($_GET['maturazione']) && $_GET['maturazione']==3) echo "selected"; ?>>Più maturo</option>
						</select>
					</div>
					<div>
						<select class="valutazione" name="valutazione">
							<option value="0" <?php if(!isset($_GET['valutazione'])) echo "selected"; ?> disabled>Valutazione</option>
		  					<option value="1" <?php if(isset($_GET['valutazione']) && $_GET['valutazione']==1) echo "selected"; ?>>Da 4 a 5</option>
							<option value="2" <?php if(isset($_GET['valutazione']) && $_GET['valutazione']==2) echo "selected"; ?>>Da 3 a 4</option>
							<option value="3" <?php if(isset($_GET['valutazione']) && $_GET['valutazione']==3) echo "selected"; ?>>Da 2 a 3</option>
						</select>
					</div>
					<div class="buttonFilter">
						<input type="checkbox" value="1" class="check" name="agricoltura" <?php if(isset($_GET['agricoltura'])) echo "checked"; ?>>Prodotto biologico
					</div>
					<div class="buttonFilter">
						<input type="checkbox" value="1" class="check" name="km0" <?php if(isset($_GET['km0'])) echo "checked"; ?>>Km 0
					</div>
					<div class="buttonFilter">
						<input type="checkbox" value="1" class="check" name="promo" <?php if(isset($_GET['promo'])) echo "checked"; ?>>In promozione
					</div>
					<div class="buttonFilter">
						<input type="checkbox" value="1" class="check" name="domicilio" <?php if(isset($_GET['domicilio'])) echo "checked"; ?>>Servizio a domicilio
					</div>
					
					<input value="Filtra" class="submit" type="submit">
				</form>
			</div>

			<div class="orderBy">
				<p class="resultTitle">Risultati</p>
				<?php 

					$urlGet=$_SERVER['QUERY_STRING'];
					$pos1=strpos($urlGet,"orderby"); //posizione iniziale
					if($pos1!==false){ //orderby c'è
						$pos2=strpos($urlGet,"&",$pos1);
						if($pos2!==false){ //& dopo orderby c'è
							$urlGet=substr($urlGet,0,$pos1-1).substr($urlGet,$pos2,strlen($urlGet));
						}
						else{
							$urlGet=substr($urlGet,0,$pos1-1);
						}
						//echo "<h1>".$urlGet."</h1>";
					}

				?>
				<select class="ordina" name="orderby" onchange="location = this.options[this.selectedIndex].value;">
					<option value=<?php 
	  								echo "\"search.php?".$urlGet."\"";
	  								if(!isset($_GET['orderby']))
	  									echo " selected";
	  								?>
									>Rilevanza</option>
	  				<option value=<?php 
	  								echo "\"search.php?".$urlGet."&orderby=pricelow\"";
	  								if(isset($_GET['orderby']) && $_GET['orderby']=="pricelow")
	  									echo " selected";
	  								?>
	  								>Prezzo: dal più basso</option>
					<option value=<?php 
									echo "\"search.php?".$urlGet."&orderby=pricehigh\""; 
									if(isset($_GET['orderby']) && $_GET['orderby']=="pricehigh")
	  									echo " selected";
									?> 
									>Prezzo: dal più alto</option>
					
				</select>
				<p class="orderByLabel">Ordina per</p>
			</div>
			<div class="core">
				<?php
					//qui mi setto tutte le variabili per fare la corretta query nel db
					$pezzatura="";
					$maturazione="";
					$valutazione="";
					$tipoAgricoltura="";
					$km0="";
					$promozione="";
					$domicilio="";
					$orderby="";
					$categoria="";

					if(isset($_GET['pezzatura']))
						$pezzatura=" and p.pezzatura=".($_GET['pezzatura']-1); //nel db si parte da 0, nel form da 1, quindi diminuiamo il valore di 1
					if(isset($_GET['maturazione']))
						$maturazione=" and p.maturazione=".($_GET['maturazione']-1);
					if(isset($_GET['agricoltura']))
						$tipoAgricoltura=" and p.tipoAgricoltura=".$_GET['agricoltura'];
					if(isset($_GET['km0']))
						$km0=" and p.km0=".$_GET['km0'];
					if(isset($_GET['promo']))
						$promozione=" and p.promozione=".$_GET['promo'];
					if(isset($_GET['domicilio']))
						$domicilio=" and n.domicilio=".$_GET['domicilio'];
					if(isset($_GET['categoria']))
						$categoria=" and p.categoria=\"".$_GET['categoria']."\"";
					if(isset($_GET['orderby'])){
						if($_GET['orderby']=="pricelow")
							$orderby=" ORDER BY p.prezzo ASC";
						else if($_GET['orderby']=="pricehigh")
							$orderby=" ORDER BY p.prezzo DESC";
					}
					else{
						$orderby=" ORDER BY p.pzvenduti DESC";
					}


					$query = "SELECT p.id, i.nomefile, p.titolo, p.promozione, p.prezzoVecchio, p.prezzo, p.quantUnita, p.marchio, p.provenienza, p.tipoAgricoltura, p.km0, n.nome FROM prodotti p, negozi n, immagini i where p.negozio = n.id and p.id = i.prodotto and i.principale = 1 and p.presente = 1 and n.presente = 1 and p.titolo like '%".$product."%' and n.citta like '".$city."'".$pezzatura.$maturazione.$tipoAgricoltura.$km0.$promozione.$domicilio.$categoria.$orderby.";";							
					$resultp = mysqli_query($conn,$query) or die ("Error: ".mysqli_error($conn));

					if(mysqli_num_rows($resultp) > 0){
						while ($arrayProduct = mysqli_fetch_row($resultp)) {
							$good=false;
							$valutazione=false;
							$checked=false;

							//valutazione media del prodotto
							$queryVal="SELECT AVG(r.valutazione) FROM recensioni r WHERE r.idprodotto=".$arrayProduct[0].";";
							$resultVal=mysqli_query($conn,$queryVal) or die ("Error: ".mysqli_error($conn));
							if(mysqli_num_rows($resultVal)>0){
								$valutaz = mysqli_fetch_row($resultVal);
								$valutazione=true;

								if(isset($_GET['valutazione'])){
									if($valutaz[0]>=4 && $_GET['valutazione']==1)
										$good=true;
									if($valutaz[0]>=3 && $valutaz[0]<4 && $_GET['valutazione']==2)
										$good=true;
									if($valutaz[0]>=2 && $valutaz[0]<3 && $_GET['valutazione']==3)
										$good=true;
								}
								else{
									$good=true;
								}
							}
							if($valutaz[0]==NULL)
								$valutazione=false;


							if($good==true){ //se il prodotto ha una valutazione che corrisponde alla richiesta
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
					}
			?>
			</div>
		</div>
		<div class="footer">
			<p>Fast&Fruit. Copyright &#169; 2016. Tutti i diritti riservati. fastandfruit.it</p>	
		</div>
	</body>
</html>