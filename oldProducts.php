<html xmlns="http://www.w3.org/1999/xhtml"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Fast&Fruit - prodotto</title>
	<link href="assets/css/style.css" rel="stylesheet" type="text/css" media="screen">
	<link href="assets/css/controlpanelStyle.css" rel="stylesheet" type="text/css" media="screen">
	<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
	<script src="assets/js/js.cookie.js"></script>
	<script src="assets/js/jquery.js"></script>
	<script src="assets/js/main.js"></script>
	<script src="assets/js/controlpanel.js"></script>
	</head>
	<body>
		<div class="main">
			<div class="header">
				<div class="logo">
					<img src="assets/images/logo.jpg">
				</div>
				<div class="CPcenter">
					
					<div id="CPshopName">
						
					</div>
				</div>

				<div class="CPright">
					<a href="insertProducts.php"><p class="newInsertionButton"><br>Vendi un nuovo prodotto</p></a>
					<p>Oppure</p>
					<a href="oldProducts.php"><p class="newInsertionButton"><br>Rimettine uno in vendita</p></a>
				</div>
			</div>
			
			<div id="CPpage">

				<div id="coreTitle">Storico ordini</div>
				<div id="publishedOrdersContainer">
					<?php
						session_start();
						require 'connect.php';
						//se l'utente è loggato
						if (isset($_SESSION['idseller'])){ //SELLERRR!!!
							//prodotti del negoziante che non sono più in vendita
							$query = "SELECT i.nomefile, p.id, p.titolo, p.marchio, p.provenienza, p.prezzo, p.prezzoVecchio, p.quantUnita, p.tipoAgricoltura, p.km0 
							FROM prodotti p, negozi n, immagini i 
							where n.id=".$_SESSION['idseller']." and p.negozio = n.id and p.id = i.prodotto and i.principale = 1 and p.presente = 0 and n.presente = 1;";							
							$resultp = mysqli_query($conn,$query) or die ("Error: ".mysqli_error($conn));

							if(mysqli_num_rows($resultp) > 0){
								while ($arrayProduct = mysqli_fetch_row($resultp)) {
									echo '

										<div id="productContainer">
											<div id="imageContainer">
												<img src="prodotti/'.$arrayProduct[0].'">
											</div>
											<div id="infoContainer">
												<a href="product.php?id='.$arrayProduct[1].'"><p id="title">'.$arrayProduct[2].'</p></a>';
												
												if($arrayProduct[6] != 0)
													echo '<p id="oldPrice">'.str_replace(".", ",", $arrayProduct[6]).'€</p>';

												echo '<p id="price">'.str_replace(".", ",", $arrayProduct[5]).'€</p>
													<p id="quantity">quantità:'.$arrayProduct[7].'</p>
													<p id="moreInfo">';

												if ($arrayProduct[3] != "")
													echo 'Marca: '.$arrayProduct[3].'</br>';
												if($arrayProduct[4] != "") 
													echo 'Provenienza: '.$arrayProduct[4].'</br>';											
												if ($arrayProduct[8] == 0)
													echo 'Tipo di agricoltura: normale</br>'; 
												elseif ($arrayProduct[8] == 1) 
													echo 'Tipo di agricoltura: biologica</br>';
												else
													echo 'Tipo di agricoltura: integrata</br>';
												if ($arrayProduct[9] == 1)
													echo 'Km0: SI</br>';
												else
													echo 'Km0: NO</br>';
									echo '</p>
										<div id="sellerActions">
													<form method="get" action="insertProducts.php" name="resellProduct">
														<input type="hidden" value="'.$arrayProduct[1].'" name="oldProduct">
														<input type="submit" value="Rimetti in vendita" class="resale">
													</form>
												</div>
											</div>
										</div>';
									}
								}
								else{
									echo "nessun prodotto disponibile";
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