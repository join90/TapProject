<html xmlns="http://www.w3.org/1999/xhtml"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<!--<meta http-equiv="refresh" content="60" url="controlPanel.php"> -->
	<title>Fast&Fruit - pannello di controllo</title>
	<link href="assets/css/style.css" rel="stylesheet" type="text/css" media="screen">
	<link href="assets/css/controlpanelStyle.css" rel="stylesheet" type="text/css" media="screen">
	<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
	<script src="assets/js/js.cookie.js"></script>
	<script src="assets/js/jquery.js"></script>
	<script src="assets/js/main.js"></script>
	<script src="assets/js/controlpanel.js"></script>
	</head>
	<body>
		<?php
			session_start();

			require 'connect.php';

			if(isset($_GET['citta']))
				echo "citta: ".$_GET['citta']."</br>";

			if(isset($_POST['changepicture'])){ //cambia l'immagine del negozio

				$to = "negozi/".$_FILES['picture']['name'];
				$allowed_types = array("image/jpeg","image/png");
				
				if(in_array($_FILES['picture']['type'], $allowed_types)){
					move_uploaded_file($_FILES['picture']['tmp_name'], $to);
					$queryImgShop = "UPDATE negozi set imgProfilo = '".$_FILES['picture']['name']."' where id = ".$_SESSION['idseller'].";";
					$resultImg = mysqli_query($conn,$queryImgShop) or die ("Errore di mysql: ".mysqli_error($conn));
				
				}
				else
					exit("Errore nel formato dei file! Caricare i file solo con estensione .jpeg o .png <a href=\"controlpanel.php\">Torna indietro</a>");
				
			}

			if (isset($_SESSION['idseller'])){ //SELLERRR!!!
				$query = "SELECT n.nome FROM negozi n WHERE n.id=".$_SESSION['idseller'].";";					
				$result = mysqli_query($conn,$query) or die ("Error: ".mysqli_error($conn));
				if(mysqli_num_rows($result) > 0){
					$nome=mysqli_fetch_row($result);
				}

				if(isset($_GET['completed'])){
					$orderID=$_GET['completed'];
					$query="UPDATE `dbfastandfruits`.`ordini` SET `successo` = 1, `archiviato` = 1 WHERE `ordini`.`id` = '".$orderID."';";
					$result = mysqli_query($conn,$query);
					if(!$result){
						echo "errore ".mysqli_error($conn);
					}
				}
				else if(isset($_GET['notcompleted'])){
					$orderID=$_GET['notcompleted'];
					$query="UPDATE `dbfastandfruits`.`ordini` SET `successo` = -1, `archiviato` = 1 WHERE `ordini`.`id` = '".$orderID."';";
					$result = mysqli_query($conn,$query);
					if(!$result){
						echo "errore ".mysqli_error($conn);
					}
				}
			}
			else{
				exit("accesso negato");
			}

		?>
		<div class="main">
			<div class="header">
				<div class="logo">
					<a href="index.php"><img src="assets/images/logo.jpg"></a>
				</div>
				<div class="CPcenter">
					<div id="CPimgShopContainer">
						<?php
							$queryImgShop = "SELECT imgProfilo from negozi where id = ".$_SESSION['idseller'].";";
							$resultImg = mysqli_query($conn,$queryImgShop) or die("Errore with mysql query: ".mysql_error($conn));
							$val = mysqli_fetch_row($resultImg);
							echo '<img src="negozi/'.$val[0].'">';	
						?>
						
					</div>
					<div id="CPshopName">
						<?php echo $nome[0]; ?>
					</div>
					<div id="CPoptions">
						<form method="post" action="" name="changePicture" enctype="multipart/form-data">
							<input type="file" id="fileButton" name="picture"></input>
							<input type="submit" value="Carica nuova immagine" id="pictureButton" name="changepicture">
						</form>
						<a href="registerseller.php">Modifica informazioni negozio</a>
					</div>
				</div>

				<div class="CPright">
					<a href="insertProducts.php"><p class="newInsertionButton"><br>Vendi un nuovo prodotto</p></a>
					<p>Oppure</p>
					<a href="oldProducts.php"><p class="newInsertionButton"><br>Rimettine uno in vendita</p></a>
				</div>
			</div>
			
			<div id="CPpage">
				<div id="coreTitle">Ordini del giorno</div>
				<div id="orders">
					<!-- legenda -->
					<div class="legend">
						<div class="orderRow">
							<div class="client">
								<p>Nome acquirente</p>
							</div>
							<div class="dateTime">
								<p>data e ora dell'ordine</p>
							</div>
							<div class="ship">
								<p>Spedizione a domicilio?</p>
							</div>
							<div class="payed">
								<p>Pagato?</p>
							</div>
							<div class="ready">
								<p>Ordine pronto?</p>
							</div>

							<div class="succeded">
								<p>Ordine completato?</p>
							</div>
							<div class="productRow">
								<div class="productTitle">
									<p>Nome del prodotto</p>
								</div>
								<div class="brand">
									<p>Marca</p>
								</div>
								<div class="origin">
									<p>Origine</p>
								</div>
								<div class="price">
									<p>Prezzo</p>
								</div>
								<div class="quantity">
									<p>Quantità</p>
								</div>
								<div class="priceTot">
									<p>Totale</p>
								</div>
								<div class="maturation">
									<p>Grado di maturazione</p>
								</div>
								<div class="agriType">
									<p>Tipo di agricoltura</p>
								</div>
								<div class="km0">
									<p>prodotto a Km0?</p>
								</div>
							</div>
						</div>
					</div>
					<!-- fine legenda -->
					<?php 
						if (isset($_SESSION['idseller'])){
							$queryOrdini = "SELECT o.id, c.nome, c.cognome, o.dataOraOrdine, o.domicilio, o.pagato, o.modificato, o.eliminato FROM ordini o, clienti c WHERE o.negozio=".$_SESSION['idseller']." and o.successo=0 and o.cliente=c.id;";				
							$resultOrdini = mysqli_query($conn,$queryOrdini) or die ("Error: ".mysqli_error($conn));
							if(mysqli_num_rows($resultOrdini) > 0){
								while($infoOrdine=mysqli_fetch_row($resultOrdini)){
									echo '<div class="superContainer"><div class="orderRow" id="'.$infoOrdine[0].'">
										<div class="client">
											<p>'.$infoOrdine[1].' '.$infoOrdine[2];
											if($infoOrdine[7]==1)
												echo '<br><b>ATTENZIONE:ORDINE ELIMINATO</b>';
											else if($infoOrdine[6]==1)
												echo '<br><b>ATTENZIONE:ORDINE MODIFICATO</b>';	
									echo'</p>
										</div>
										<div class="dateTime">
											<p>'.$infoOrdine[3].'</p>
										</div>
										<div class="ship">';
										if($infoOrdine[4]==1)
											echo '<p>SI</p>';
										else
											echo '<p>NO</p>';

									echo '</div>
										<div class="payed">';
										if($infoOrdine[5]==1)
											echo '<p>SI</p>';
										else
											echo '<p>NO</p>';
									echo '</div>
										<div class="ready">';
										if($infoOrdine[7]==0)//se non è eliminato
											echo '<p><img src="assets/images/ready.png"></p>';
									echo '	</div>
										<div class="succeded">
											<p>';
												if($infoOrdine[7]==0)//se non è eliminato
													echo '<a href="controlpanel.php?completed='.$infoOrdine[0].'"><img src="assets/images/completed.png"></a>';
										echo'	<a href="controlpanel.php?notcompleted='.$infoOrdine[0].'"><img src="assets/images/notcompleted.png"></a>
											</p>
										</div>';

										$queryProdotti = "SELECT p.titolo, p.marchio, p.provenienza, p.prezzo, ppo.quantita, ppo.prezzoQuantita, p.maturazione, p.tipoAgricoltura, p.km0 FROM prodotti p, prodottiperordine ppo WHERE ppo.prodotto=p.id and ppo.ordine=".$infoOrdine[0].";";					
										$resultProdotti = mysqli_query($conn,$queryProdotti) or die ("Error: ".mysqli_error($conn));
										if(mysqli_num_rows($resultProdotti) > 0){
											while($infoProdotti=mysqli_fetch_row($resultProdotti)){
												echo '
												<div class="productRow">
													<div class="productTitle">
														<p>'.$infoProdotti[0].'</p>
													</div>
													<div class="brand">
														<p>'.$infoProdotti[1].'</p>
													</div>
													<div class="origin">
														<p>'.$infoProdotti[2].'</p>
													</div>
													<div class="price">
														<p>'.$infoProdotti[3].'&euro;</p>
													</div>
													<div class="quantity">
														<p>'.$infoProdotti[4].'</p>
													</div>
													<div class="priceTot">
														<p>'.$infoProdotti[5].'&euro;</p>
													</div>
													<div class="maturation">';
													if($infoProdotti[6]==0)
														echo '<p>meno maturo</p>';
													else if($infoProdotti[6]==1)
														echo '<p>normale</p>';
													else
														echo '<p>più maturo';
												echo '</div>
													<div class="agriType">';
													if($infoProdotti[7]==0)
														echo '<p>normale</p>';
													else if($infoProdotti[7]==1)
														echo '<p>biologica</p>';
												echo '</div>
													<div class="km0">';
													if($infoProdotti[7]==0)
														echo '<p>NO</p>';
													else
														echo '<p>SI</p>';
												echo '</div>
												</div>';
											}
										}
									echo '</div></div>';
								}
							}
						}


					?>
				</div>

				<div id="coreTitle">Prodotti in vendita</div>
				<div id="publishedOrdersContainer">
				<?php
					if (isset($_SESSION['idseller'])){ //SELLERRR!!!
							//prodotti del negoziante che non sono più in vendita
							$queryInVendita = "SELECT i.nomefile, p.id, p.titolo, p.marchio, p.provenienza, p.prezzo, p.prezzoVecchio, p.quantUnita, p.tipoAgricoltura, p.km0 
							FROM prodotti p, negozi n, immagini i 
							where n.id=".$_SESSION['idseller']." and p.negozio = n.id and p.id = i.prodotto and i.principale = 1 and p.presente = 1 and n.presente = 1;";							
							$resultInVendita = mysqli_query($conn,$queryInVendita) or die ("Error: ".mysqli_error($conn));

							if(mysqli_num_rows($resultInVendita) > 0){
								while ($arrayProductV = mysqli_fetch_row($resultInVendita)) {
									echo '

										<div id="productContainer">
											<div id="imageContainer">
												<img src="prodotti/'.$arrayProductV[0].'">
											</div>
											<div id="infoContainer">
												<a href="product.php?id='.$arrayProductV[1].'"><p id="title">'.$arrayProductV[2].'</p></a>';
												
												if($arrayProductV[6] != 0)
													echo '<p id="oldPrice">'.str_replace(".", ",", $arrayProductV[6]).'€</p>';

												echo '<p id="price">'.str_replace(".", ",", $arrayProductV[5]).'€</p>
													<p id="quantity">quantità:'.$arrayProductV[7].'</p>
													<p id="moreInfo">';

												if ($arrayProductV[3] != "")
													echo 'Marca: '.$arrayProductV[3].'</br>';
												if($arrayProductV[4] != "") 
													echo 'Provenienza: '.$arrayProductV[4].'</br>';											
												if ($arrayProductV[8] == 0)
													echo 'Tipo di agricoltura: normale</br>'; 
												elseif ($arrayProductV[8] == 1) 
													echo 'Tipo di agricoltura: biologica</br>';
												else
													echo 'Tipo di agricoltura: integrata</br>';
												if ($arrayProductV[9] == 1)
													echo 'Km0: SI</br>';
												else
													echo 'Km0: NO</br>';
									echo '</p>
							<div id="sellerActions">
								<form method="get" action="optionProducts.php" name="optionProduct">
									<input type="hidden" value="'.$arrayProductV[1].'" name="productToModify">
									<input type="submit" value="Metti in offerta" class="sale" name="sale">
									<input type="submit" value="Elimina" class="delete" name="delete">
								</form>
							</div>
						</div></div>';
					}
				}
			}
			?>
					

					
				
				</div>

				<div id="coreTitle">Ultimi feedback</div>
				<div id="feedbackContainer">
					<div class="feedbackRow" id="feedbackLegend">
						<div id="stars">Stelle</div>
						<p id="author">Autore</p>
						<p id="feedbackText">Commento</p>
						<p id="reply">Rispondi</p>
					</div>
				<?php 
					$queryFeedback = "SELECT p.id, r.valutazione, c.nome, c.cognome, c.id, r.commento FROM recensioni r, prodotti p, negozi n, clienti c where n.id=".$_SESSION['idseller']." and r.idnegozio=n.id and r.idprodotto=p.id and c.id=r.idcliente and r.rispostanegozio is NULL;";							
					$resultFeedback = mysqli_query($conn,$queryFeedback) or die ("Error: ".mysqli_error($conn));

							if(mysqli_num_rows($resultFeedback) > 0){
								while ($arrayFeedback = mysqli_fetch_row($resultFeedback)) {
									echo '
									<div class="feedbackRow">
										<div id="stars">'.$arrayFeedback[1].' stelle</div>
										<p id="author">'.$arrayFeedback[2].' '.$arrayFeedback[3].'</p>
										<p id="feedbackText">'.$arrayFeedback[5].'</p>
										
											<form id="replyFeed" method="get" action="optionProducts.php" name="replyFeed">
												<input type="hidden" value="'.$arrayFeedback[0].'" name="idproduct">
												<input type="hidden" value="'.$_SESSION['idseller'].'" name="idshop">
												<input type="hidden" value="'.$arrayFeedback[4].'" name="idclient">
												
												<input id="replyText" autocomplete="off" type="text" name="replyFeedback" value="(max 200 caratteri)">
												<input type="submit" value="Invia" id=feedbackReplyButton" name="feedback">
											</form>
										
									</div>';
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