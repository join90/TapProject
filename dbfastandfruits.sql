-- phpMyAdmin SQL Dump
-- version 4.5.3.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Creato il: Mar 18, 2016 alle 09:35
-- Versione del server: 5.7.10
-- Versione PHP: 5.6.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbfastandfruits`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `clienti`
--

CREATE TABLE `clienti` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(45) NOT NULL DEFAULT '',
  `cognome` varchar(45) NOT NULL DEFAULT '',
  `comune` varchar(45) DEFAULT NULL,
  `viaPiazza` varchar(45) DEFAULT NULL,
  `ncivico` varchar(5) DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `email` varchar(45) NOT NULL DEFAULT '',
  `pass` varchar(45) NOT NULL DEFAULT '',
  `presente` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `clienti`
--

INSERT INTO `clienti` (`id`, `nome`, `cognome`, `comune`, `viaPiazza`, `ncivico`, `telefono`, `email`, `pass`, `presente`) VALUES
(1, 'Giovanni', 'Guerrieri', 'Monterosso Almo', 'via Giovanni Verga ', '61', '0932970064', 'giovygu90@hotmail.it', 'lamiapassword123', 1),
(2, 'Marco', 'Rosano', 'Adrano', 'via nonloso', '4', '333344445', 'marcorosano@email.it', 'lamiapassword123', 1),
(3, 'Luca', 'Giurato', 'enna', 'via tevere', '31', '333333334', 'lamiaemail@email.it', '123', 1),
(4, 'Giulia', 'Platania', 'Adrano', 'via napoli', '6', '333345455', 'giuliap@email.it', '123', 1),
(5, 'Sandro', 'Foti', 'catania', 'via milano', '88', '232423423', 'sandro@email.it', '123', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `immagini`
--

CREATE TABLE `immagini` (
  `id` int(10) UNSIGNED NOT NULL,
  `nomefile` varchar(45) NOT NULL DEFAULT '',
  `principale` tinyint(1) DEFAULT NULL,
  `prodotto` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `immagini`
--

INSERT INTO `immagini` (`id`, `nomefile`, `principale`, `prodotto`) VALUES
(2, 'bio.jpg', 1, 3),
(3, 'logo.jpg', 0, 2),
(4, 'km0.jpg', 1, 2),
(5, 'bio.jpg', 0, 2);

--
-- Trigger `immagini`
--
DELIMITER $$
CREATE TRIGGER `insert_img` BEFORE INSERT ON `immagini` FOR EACH ROW BEGIN

DECLARE x integer;
DECLARE main integer;
DECLARE msg VARCHAR(255);

SELECT COUNT(*) INTO x
FROM immagini
WHERE immagini.prodotto = new.prodotto;

SELECT COUNT(*) INTO main
FROM immagini
WHERE immagini.prodotto = new.prodotto
AND immagini.principale = 1;

IF(x >= 3) THEN
	set msg = "Non puoi inserire piu' di tre immagini...";
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = msg;
END IF;

IF((main > 0) && (new.principale = 1)) THEN
	set msg = "Esiste gia' un'immagine principale...";
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = msg;
END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `negozi`
--

CREATE TABLE `negozi` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(45) NOT NULL DEFAULT '',
  `citta` varchar(45) NOT NULL DEFAULT '',
  `viaPiazza` varchar(45) NOT NULL DEFAULT '',
  `ncivico` varchar(5) NOT NULL DEFAULT '',
  `telefono` varchar(45) NOT NULL DEFAULT '',
  `cellulare` varchar(45) DEFAULT NULL,
  `giorniSettimanaApertura` varchar(45) NOT NULL DEFAULT '',
  `orariApertura` varchar(45) NOT NULL DEFAULT '',
  `domicilio` tinyint(1) DEFAULT NULL,
  `costoDomicilio` float DEFAULT NULL,
  `imgProfilo` varchar(45) DEFAULT NULL,
  `imgCopertina` varchar(45) DEFAULT NULL,
  `valutazione` float NOT NULL DEFAULT '0',
  `email` varchar(45) NOT NULL DEFAULT '',
  `pass` varchar(45) NOT NULL DEFAULT '',
  `presente` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `negozi`
--

INSERT INTO `negozi` (`id`, `nome`, `citta`, `viaPiazza`, `ncivico`, `telefono`, `cellulare`, `giorniSettimanaApertura`, `orariApertura`, `domicilio`, `costoDomicilio`, `imgProfilo`, `imgCopertina`, `valutazione`, `email`, `pass`, `presente`) VALUES
(1, 'fast', 'ragusa', 'via lucifero', '6', '09322970064', '33367892239', 'lun-ven', '8-12 e 15-20', 0, 0, 'domicilio.jpg', 'immagineCopertina', 3.5, 'fast@email.it', 'lapassword123', 1),
(2, 'ortofrutta', 'ragusa', 'via lucifero', '5', '0953455656', '3335456565', 'lun-ven', '8-12 e 15-20', 1, 5.5, 'immagineNegozio', 'immagineCopertina', 5, 'ortofrutta@email.it', 'lapassword123', 1),
(3, 'TantaFrutta ', 'catania', 'via milano', '61', '234234234', '234424223', 'lun-ven', '8-20', 1, 5, NULL, 'logo.jpg', 4, 'tantafrutta@email.it', '123', 1),
(4, 'TantaFruttaEverdura', 'Catania', 'via curato', '6', '32423423', '24423423', 'lun-ven', '8-20', 0, NULL, 'logo.jpg', 'logo.jpg', 3, 'tantaFruttaeVerdura@email.it', '123', 1),
(5, 'PocaFruttaETantaVerdura', 'messina', 'via verga', '4', '3434234234', '2342342342', 'lun-ven', '8-19', 1, 4, 'logo.jpg', 'logo.jpg', 5, 'pocafruttaetantaverdura@email.it', '123', 1),
(6, 'PocaFruttaEpocaVerdura', 'messina', 'via Cifali', '4', '4234234', '2342342', 'lun-sab', '8-20', 1, 3, 'logo.jpg', 'logo.jpg', 2, 'pocafruttaepocoverdura@email.it', '123', 1),
(7, 'TantaFruttaEverdura', 'siracusa', 'via curato', '6', '32423423', '24423423', 'lun-ven', '8-20', 0, NULL, 'logo.jpg', 'logo.jpg', 3, 'tantaFruttaeVerdura@email.it', '123', 1);

--
-- Trigger `negozi`
--
DELIMITER $$
CREATE TRIGGER `upd_prodotti` AFTER UPDATE ON `negozi` FOR EACH ROW BEGIN
IF (OLD.presente <> NEW.presente) THEN
	UPDATE prodotti SET prodotti.presente = NEW.presente WHERE prodotti.negozio = NEW.id;
END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `ordini`
--

CREATE TABLE `ordini` (
  `id` int(10) UNSIGNED NOT NULL,
  `prezzoTot` float NOT NULL DEFAULT '0',
  `dataOraConsegna` datetime DEFAULT NULL,
  `domicilio` tinyint(1) DEFAULT NULL,
  `programmato` tinyint(1) DEFAULT NULL,
  `pagato` tinyint(1) DEFAULT NULL,
  `pronto` tinyint(1) DEFAULT NULL,
  `successo` tinyint(1) DEFAULT NULL,
  `archiviato` tinyint(1) DEFAULT NULL,
  `cliente` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `negozio` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `dataOraOrdine` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `eliminato` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `modificato` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `ordini`
--

INSERT INTO `ordini` (`id`, `prezzoTot`, `dataOraConsegna`, `domicilio`, `programmato`, `pagato`, `pronto`, `successo`, `archiviato`, `cliente`, `negozio`, `dataOraOrdine`, `eliminato`, `modificato`) VALUES
(1, 6.6, '2016-03-17 05:14:24', 1, 0, 0, 1, 1, 0, 1, 1, '2016-03-17 05:14:24', 1, 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `prodotti`
--

CREATE TABLE `prodotti` (
  `id` int(10) UNSIGNED NOT NULL,
  `titolo` varchar(45) NOT NULL DEFAULT '',
  `categoria` varchar(15) DEFAULT NULL,
  `marchio` varchar(15) DEFAULT NULL,
  `provenienza` varchar(20) DEFAULT NULL,
  `prezzo` float NOT NULL DEFAULT '0',
  `pezzatura` int(10) UNSIGNED DEFAULT NULL,
  `quantUnita` varchar(10) NOT NULL DEFAULT '0',
  `disponibilita` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `dataOraAggiornamento` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `maturazione` int(10) UNSIGNED DEFAULT NULL,
  `tipoAgricoltura` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `km0` tinyint(1) DEFAULT NULL,
  `promozione` tinyint(1) DEFAULT NULL,
  `prezzoVecchio` float DEFAULT NULL,
  `negozio` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `presente` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `descrizione` text,
  `tipo` varchar(45) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `prodotti`
--

INSERT INTO `prodotti` (`id`, `titolo`, `categoria`, `marchio`, `provenienza`, `prezzo`, `pezzatura`, `quantUnita`, `disponibilita`, `dataOraAggiornamento`, `maturazione`, `tipoAgricoltura`, `km0`, `promozione`, `prezzoVecchio`, `negozio`, `presente`, `descrizione`, `tipo`) VALUES
(2, 'pomodoro datterino', 'pomodoro', '', 'pachino', 1.29, 3, '1Kg', 3, '2000-03-12 00:00:00', 0, 0, 0, 0, 0, 1, 1, NULL, 'ortaggio'),
(3, 'mele golden', 'mele', 'opzionale', 'roma', 1.29, 2, '1Kg', 3, '2000-03-12 00:00:00', 0, 1, 0, 0, 0, 2, 1, NULL, '');

-- --------------------------------------------------------

--
-- Struttura della tabella `prodotticarrello`
--

CREATE TABLE `prodotticarrello` (
  `cliente` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `prodotto` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `quantita` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `PzVenduti` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `prodotticarrello`
--

INSERT INTO `prodotticarrello` (`cliente`, `prodotto`, `quantita`, `PzVenduti`) VALUES
(1, 2, 1, 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `prodottiperordine`
--

CREATE TABLE `prodottiperordine` (
  `ordine` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `prodotto` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `quantita` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `prezzoQuantita` float NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Trigger `prodottiperordine`
--
DELIMITER $$
CREATE TRIGGER `update_ordine` AFTER DELETE ON `prodottiperordine` FOR EACH ROW BEGIN 
DECLARE x integer;

SELECT prodottiperordine.ordine INTO x
FROM prodottiperordine
WHERE old.ordine = prodottiperordine.ordine;

if(x <> 0) THEN

UPDATE ordini SET ordini.prezzoTot = (ordini.prezzoTot-old.prezzoQuantita) WHERE ordini.id = old.ordine;

UPDATE ordini set ordini.modificato = 1 WHERE ordini.id = old.ordine;

ELSE
UPDATE ordini set ordini.eliminato = 1 WHERE ordini.id = old.ordine;
END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `recensioni`
--

CREATE TABLE `recensioni` (
  `idnegozio` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `idprodotto` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `idcliente` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `valutazione` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `commento` text,
  `nmodifiche` int(10) UNSIGNED DEFAULT NULL,
  `rispostanegozio` text,
  `presente` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `recensioni`
--

INSERT INTO `recensioni` (`idnegozio`, `idprodotto`, `idcliente`, `valutazione`, `commento`, `nmodifiche`, `rispostanegozio`, `presente`) VALUES
(1, 2, 1, 3, 'Il pomodoro era bucato, pero e buono', NULL, NULL, 1),
(1, 3, 2, 4, 'la mela e buona', NULL, NULL, 1);

--
-- Trigger `recensioni`
--
DELIMITER $$
CREATE TRIGGER `update_val_negozio` AFTER INSERT ON `recensioni` FOR EACH ROW BEGIN
DECLARE mean float;

SELECT AVG(r.valutazione) into mean
FROM recensioni r 
WHERE r.idnegozio = new.idnegozio;

UPDATE negozi SET negozi.valutazione = mean WHERE negozi.id = new.idnegozio;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `transazioni`
--

CREATE TABLE `transazioni` (
  `id` int(10) UNSIGNED NOT NULL,
  `data` date NOT NULL DEFAULT '0000-00-00',
  `importo` float NOT NULL DEFAULT '0',
  `esito` tinyint(1) DEFAULT NULL,
  `circuito` varchar(45) DEFAULT NULL,
  `ordine` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `clienti`
--
ALTER TABLE `clienti`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `immagini`
--
ALTER TABLE `immagini`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_Immagini_1` (`prodotto`) USING BTREE;

--
-- Indici per le tabelle `negozi`
--
ALTER TABLE `negozi`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `ordini`
--
ALTER TABLE `ordini`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_ordini_1` (`cliente`),
  ADD KEY `FK_ordini_2` (`negozio`);

--
-- Indici per le tabelle `prodotti`
--
ALTER TABLE `prodotti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_prodotti_1` (`negozio`);

--
-- Indici per le tabelle `prodotticarrello`
--
ALTER TABLE `prodotticarrello`
  ADD PRIMARY KEY (`cliente`,`prodotto`),
  ADD KEY `FK_ProdottiCarrello_2` (`prodotto`);

--
-- Indici per le tabelle `prodottiperordine`
--
ALTER TABLE `prodottiperordine`
  ADD PRIMARY KEY (`ordine`,`prodotto`),
  ADD KEY `FK_ProdottiPerOrdine_2` (`prodotto`) USING BTREE;

--
-- Indici per le tabelle `recensioni`
--
ALTER TABLE `recensioni`
  ADD PRIMARY KEY (`idnegozio`,`idprodotto`,`idcliente`),
  ADD KEY `FK_recensioni_2` (`idprodotto`),
  ADD KEY `FK_recensioni_3` (`idcliente`);

--
-- Indici per le tabelle `transazioni`
--
ALTER TABLE `transazioni`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_transazioni_1` (`ordine`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `clienti`
--
ALTER TABLE `clienti`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT per la tabella `immagini`
--
ALTER TABLE `immagini`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT per la tabella `negozi`
--
ALTER TABLE `negozi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT per la tabella `ordini`
--
ALTER TABLE `ordini`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT per la tabella `prodotti`
--
ALTER TABLE `prodotti`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT per la tabella `transazioni`
--
ALTER TABLE `transazioni`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `immagini`
--
ALTER TABLE `immagini`
  ADD CONSTRAINT `FK_immagini_1` FOREIGN KEY (`prodotto`) REFERENCES `prodotti` (`id`);

--
-- Limiti per la tabella `ordini`
--
ALTER TABLE `ordini`
  ADD CONSTRAINT `FK_ordini_1` FOREIGN KEY (`cliente`) REFERENCES `clienti` (`id`),
  ADD CONSTRAINT `FK_ordini_2` FOREIGN KEY (`negozio`) REFERENCES `negozi` (`id`);

--
-- Limiti per la tabella `prodotti`
--
ALTER TABLE `prodotti`
  ADD CONSTRAINT `FK_prodotti_1` FOREIGN KEY (`negozio`) REFERENCES `negozi` (`id`);

--
-- Limiti per la tabella `prodotticarrello`
--
ALTER TABLE `prodotticarrello`
  ADD CONSTRAINT `FK_ProdottiCarrello_1` FOREIGN KEY (`cliente`) REFERENCES `clienti` (`id`),
  ADD CONSTRAINT `FK_ProdottiCarrello_2` FOREIGN KEY (`prodotto`) REFERENCES `prodotti` (`id`);

--
-- Limiti per la tabella `prodottiperordine`
--
ALTER TABLE `prodottiperordine`
  ADD CONSTRAINT `FK_prodottiperordine_1` FOREIGN KEY (`ordine`) REFERENCES `ordini` (`id`),
  ADD CONSTRAINT `FK_prodottiperordine_2` FOREIGN KEY (`prodotto`) REFERENCES `prodotti` (`id`);

--
-- Limiti per la tabella `recensioni`
--
ALTER TABLE `recensioni`
  ADD CONSTRAINT `FK_recensioni_1` FOREIGN KEY (`idnegozio`) REFERENCES `negozi` (`id`),
  ADD CONSTRAINT `FK_recensioni_2` FOREIGN KEY (`idprodotto`) REFERENCES `prodotti` (`id`),
  ADD CONSTRAINT `FK_recensioni_3` FOREIGN KEY (`idcliente`) REFERENCES `clienti` (`id`);

--
-- Limiti per la tabella `transazioni`
--
ALTER TABLE `transazioni`
  ADD CONSTRAINT `FK_transazioni_1` FOREIGN KEY (`ordine`) REFERENCES `ordini` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
