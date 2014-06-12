-- MySQL dump 10.13  Distrib 5.5.29, for FreeBSD9.1 (amd64)
--
-- Host: localhost    Database: development
-- ------------------------------------------------------
-- Server version	5.5.29-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `core_group`
--

DROP TABLE IF EXISTS `core_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `description` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `core_user_ID_change` int(11) NOT NULL,
  `core_user_ID_create` int(11) NOT NULL,
  `core_user_ID_delete` int(11) NOT NULL,
  `datetime_change` datetime NOT NULL,
  `datetime_create` datetime NOT NULL,
  `datetime_delete` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_group`
--

LOCK TABLES `core_group` WRITE;
/*!40000 ALTER TABLE `core_group` DISABLE KEYS */;
INSERT INTO `core_group` VALUES (1,'Administratoren','Administratoren Gruppe',1,0,1,1,0,'2011-05-26 00:00:00','2011-05-26 00:00:00','0000-00-00 00:00:00'),(2,'MyGruppe','Sis is eine Beschreibung',1,0,1,1,0,'2012-03-18 14:41:28','2012-03-02 20:11:27','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `core_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_intl_country`
--

DROP TABLE IF EXISTS `core_intl_country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_intl_country` (
  `id` varchar(2) NOT NULL,
  `iso3` varchar(3) NOT NULL,
  `ioc` varchar(3) NOT NULL,
  `tld` varchar(6) NOT NULL,
  `core_intl_currency_ID` varchar(3) NOT NULL,
  `calling_code` varchar(7) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_intl_country`
--

LOCK TABLES `core_intl_country` WRITE;
/*!40000 ALTER TABLE `core_intl_country` DISABLE KEYS */;
INSERT INTO `core_intl_country` VALUES ('AC','ASC','','.ac','','+247'),('AD','AND','AND','.ad','EUR','+376'),('AE','ARE','UAE','.ae','AED','+971'),('AF','AFG','AFG','.af','AFN','+93'),('AG','ATG','ANT','.ag','XCD','+1268'),('AI','AIA','','.ai','XCD','+1264'),('AL','ALB','ALB','.al','ALL','+355'),('AM','ARM','ARM','.am','AMD','+374'),('AN','ANT','AHO','.an','ANG','+599'),('AO','AGO','ANG','.ao','AOA','+244'),('AQ','ATA','','.aq','','+672'),('AR','ARG','ARG','.ar','ARS','+54'),('AS','ASM','','.as','USD','+1684'),('AT','AUT','AUT','.at','EUR','+43'),('AU','AUS','AUS','.au','AUD','+61'),('AW','ABW','ARU','.aw','ANG','+297'),('AX','ALA','','.ax','EUR','+35818'),('AZ','AZE','AZE','.az','AZN','+994'),('BA','BIH','BIH','.ba','BAM','+387'),('BB','BRB','BAR','.bb','BBD','+1246'),('BD','BGD','BAN','.bd','BDT','+880'),('BE','BEL','BEL','.be','EUR','+32'),('BF','BFA','BUR','.bf','XOF','+226'),('BG','BGR','BUL','.bg','BGN','+359'),('BH','BHR','BRN','.bh','BHD','+973'),('BI','BDI','BDI','.bi','BIF','+257'),('BJ','BEN','BEN','.bj','XOF','+229'),('BM','BMU','BER','.bm','BMD','+1441'),('BN','BRN','BRU','.bn','BND','+673'),('BO','BOL','BOL','.bo','BOB','+591'),('BR','BRA','BRA','.br','BRL','+55'),('BS','BHS','BAH','.bs','BSD','+1242'),('BT','BTN','BHU','.bt','BTN','+975'),('BV','BVT','','.bv','NOK',''),('BW','BWA','BOT','.bw','BWP','+267'),('BY','BLR','BLR','.by','BYR','+375'),('BZ','BLZ','BLZ','.bz','BZD','+51'),('CA','CAN','CAN','.ca','CAD','+1NXX'),('CC','CCK','','.cc','AUD',''),('CD','COD','COD','.cd','CDF','+243'),('CF','CAF','CAF','.cf','XAF','+236'),('CG','COG','CGO','.cg','XAF','+242'),('CH','CHE','SUI','.ch','CHF','+41'),('CI','CIV','CIV','.ci','XOF','+225'),('CK','COK','COK','.ck','NZD','+682'),('CL','CHL','CHI','.cl','CLP','+56'),('CM','CMR','CMR','.cm','XAF','+237'),('CN','CHN','CHN','.cn','CNY','+86'),('CO','COL','COL','.co','COP','+57'),('CR','CRI','CRC','.cr','CRC','+56'),('CS','SCG','YUG','.cs','','+381'),('CU','CUB','CUB','.cu','CUP','+53'),('CV','CPV','CPV','.cv','CVE','+238'),('CX','CXR','','.cx','AUD',''),('CY','CYP','CYP','.cy','CYP','+357'),('CZ','CZE','CZE','.cz','CZK','+420'),('DE','DEU','GER','.de','EUR','+49'),('DG','DGA','','','','+246'),('DJ','DJI','DJI','dj','DJF','+253'),('DK','DNK','DEN','.dk','DKK','+45'),('DM','DMA','','.dm','XCD','+1767'),('DO','DOM','DOM','.do','DOP','+1809'),('DZ','DZA','ALG','.dz','DZD','+213'),('EC','ECU','ECU','.ec','USD','+593'),('EE','EST','EST','.ee','EEK','+372'),('EG','EGY','EGY','.eg','EGP','+20'),('EH','ESH','','.eh','MAD',''),('ER','ERI','ERI','.er','ERN','+291'),('ES','ESP','ESP','.es','EUR','+34'),('ET','ETH','ETH','.et','ETB','+251'),('EU','-/-','-/-','.eu','','+3883'),('FI','FIN','FIN','.fi','EUR','+358'),('FJ','FJI','FJI','.fj','FJD','+679'),('FK','FLK','','.fk','FLP','+500'),('FM','FSM','FSM','.fm','USD','+691'),('FO','FRO','FRO','.fo','DKK','+298'),('FR','FRA','FRA','.fr','EUR','+33'),('GA','GAB','GAB','.ga','XAF','+241'),('GB','GBR','GBR','.gb','GBP','+44'),('GD','GRD','GRN','.gd','XCD','+1473'),('GE','GEO','GEO','.ge','GEL','+995'),('GF','GUF','','.gf','EUR','+594'),('GG','GGY','','.gg','GGP','+44'),('GH','GHA','GHA','.gh','GHC','+233'),('GI','GIB','','.gi','GIP','+350'),('GL','GRL','','.gl','DKK','+299'),('GM','GMB','GAM','.gm','GMD','+220'),('GN','GIN','GUI','.gn','GNF','+224'),('GP','GLP','','.gp','EUR','+590'),('GQ','GNQ','GEQ','.gq','XAF','+240'),('GR','GRC','GRE','.gr','EUR','+30'),('GS','SGS','','','GBP',''),('GT','GTM','GUA','.gt','GTQ','+52'),('GU','GUM','GUM','.gu','USD','+1671'),('GW','GNB','GBS','.gw','XOF','+245'),('GY','GUY','GUY','.gy','GYD','+592'),('HK','HKG','HKG','.hk','HNL','+852'),('HM','HMD','','.hm','AUD',''),('HN','HND','HON','.hn','HNL','+54'),('HR','HRV','CRO','.hr','HRK','+385'),('HT','HTI','HAI','.ht','USD','+59'),('HU','HUN','HUN','.hu','HUF','+36'),('IC','','','','',''),('ID','IDN','INA','.id','INR','+62'),('IE','IRL','IRL','.ie','EUR','+353'),('IL','ISR','ISR','.il','ILS','+972'),('IM','IMN','','.im','IMP','+44'),('IN','IND','IND','.in','ISK','+91'),('IO','IOT','','.io','USD',''),('IQ','IRQ','IRQ','.iq','IDR','+964'),('IR','IRN','IRI','.ir','IRR','+98'),('IS','ISL','ISL','.is','HUF','+354'),('IT','ITA','ITA','.it','EUR','+39'),('JE','JEY','','.je','JEP','+44'),('JM','JAM','JAM','.jm','JMD','+1876'),('JO','JOR','JOR','.jo','JOD','+962'),('JP','JPN','JPN','.jp','JPY','+81'),('KE','KEN','KEN','.ke','KES','+254'),('KG','KGZ','KGZ','.kg','KGS','+996'),('KH','KHM','CAM','.kh','KHR','+855'),('KI','KIR','','.ki','AUD','+686'),('KM','','','.km','KMF','+269'),('KN','KNA','SKN','.kn','XCD','+1869'),('KP','PRK','PRK','.kp','KPW','+850'),('KR','KOR','KOR','.kr','KRW','+82'),('KW','KWT','KUW','.kw','KWD','+965'),('KY','CYM','CAY','.ky','KYD','+1345'),('KZ','KAZ','KAZ','.kz','KZT','+7'),('LA','LAO','LAO','.la','LAK','+856'),('LB','LBN','LIB','.lb','LBP','+961'),('LC','LCA','LCA','.lc','XCD','+1758'),('LI','LIE','LIE','.li','CHF','+423'),('LK','LKA','SRI','.lk','LKR','+94'),('LR','LBR','LBR','.lr','LRD','+231'),('LS','LSO','LES','.ls','LSL','+266'),('LT','LTU','LTU','.lt','LTL','+370'),('LU','LUX','LUX','.lu','EUR','+352'),('LV','LVA','LAT','.lv','LVL','+371'),('LY','LBY','LBA','.ly','LYD','+218'),('MA','MAR','MAR','.ma','MAD','+211'),('MC','MCO','MON','.mc','EUR','+377'),('MD','MDA','MDA','.md','MDL','+373'),('ME','MNE','MNE','.me','','+382'),('MG','MDG','MAD','.mg','MGA','+261'),('MH','MHL','','.mh','USD','+692'),('MK','MKD','MKD','.mk','MKD','+389'),('ML','MLI','MLI','.ml','XOF','+223'),('MM','MMR','MYA','.mm','MMK','+95'),('MN','MNG','MGL','.mn','MNT','+976'),('MO','MAC','','.mo','MOP','+853'),('MP','MNP','','.mp','USD','+1670'),('MQ','MTQ','','.mq','EUR','+596'),('MR','MRT','MTN','.mr','MRO','+222'),('MS','MSR','','.ms','XCD','+1664'),('MT','MLT','MLT','.mt','EUR','+356'),('MU','MUS','MRI','.mu','MUR','+230'),('MV','MDV','MDV','.mv','MVR','+960'),('MW','MWI','MAW','.mw','MWK','+265'),('MX','MEX','MEX','.mx','MXN','+52'),('MY','MYS','MAS','.my','MYR','+60'),('MZ','MOZ','MOZ','.mz','MZM','+258'),('NA','NAM','NAM','.na','ZAR','+264'),('NC','NCL','','.nc','XPF','+687'),('NE','NER','NIG','.ne','XOF','+227'),('NF','NFK','','.nf','AUD','+6723'),('NG','NGA','NGR','.ng','NGN','+234'),('NI','NIC','NCA','.ni','NIO','+55'),('NL','NLD','NED','.nl','EUR','+31'),('NO','NOR','NOR','.no','NOK','+47'),('NP','NPL','NEP','.np','NPR','+977'),('NR','NRU','NRU','.nr','AUD','+674'),('NT','NTZ','','.nt','',''),('NU','NIU','','.nu','NZD','+683'),('NZ','NZL','NZL','.nz','NZD','+64'),('OM','OMN','OMA','.om','OMR','+968'),('PA','PAN','PAN','.pa','USD','+57'),('PE','PER','PER','.pe','PEN','+51'),('PF','PYF','','.pf','XPF','+689'),('PG','PNG','','.pg','PGK','+675'),('PH','PHL','PHI','.ph','PHP','+63'),('PK','PAK','PAK','.pk','PKR','+92'),('PL','POL','POL','.pl','PLN','+48'),('PM','SPM','','.pm','EUR','+508'),('PN','PCN','','.pn','NZD','+649'),('PR','PRI','PUR','.pr','USD','+1939'),('PS','PSE','PLE','.ps','','+970'),('PT','PRT','POR','.pt','EUR','+351'),('PW','PLW','PLW','.pw','USD','+680'),('PY','PRY','PAR','.py','PYG','+595'),('QA','QAT','QAT','.qa','QAR','+974'),('RE','REU','','.re','EUR','+262'),('RO','ROU','ROM','.ro','RON','+40'),('RU','RUS','RUS','.ru','RUB','+7'),('RW','RWA','RWA','.rw','RWF','+250'),('SA','SAU','KSA','.sa','SAR','+966'),('SB','SLB','SOL','.sb','SBD','+677'),('SC','SYC','SEY','.sc','SCR','+248'),('SD','SDN','SUD','.sd','SDD','+249'),('SE','SWE','SWE','.se','SEK','+46'),('SG','SGP','SIN','.sg','SGD','+65'),('SH','SHN','','.sh','SHP','+290'),('SI','SVN','SLO','.si','SIT','+386'),('SJ','SJM','','.sj','NOK',''),('SK','SVK','SVK','.sk','SKK','+421'),('SL','SLE','SLE','.sl','SLL','+232'),('SM','SMR','SMR','.sm','EUR','+378'),('SN','SEN','SEN','.sn','XOF','+221'),('SO','SOM','SOM','.so','SOS','+252'),('SR','SUR','SUR','.sr','SRD','+597'),('ST','STP','STP','.st','STD','+239'),('SU','SUN','URS','.su','',''),('SV','SLV','ESA','.sv','SVC','+53'),('SY','SYR','SYR','.sy','SYP','+963'),('SZ','SWZ','SWZ','.sz','SZL','+268'),('TA','TAA','','','','+290'),('TC','TCA','','.tc','USD','+1649'),('TD','TCD','CHA','.td','XAF','+235'),('TF','ATF','','.tf','EUR',''),('TG','TGO','TOG','.tg','XOF','+228'),('TH','THA','THA','.th','THB','+66'),('TJ','TJK','TJK','.tj','RUB','+992'),('TK','TKL','','.tk','NZD','+690'),('TL','TLS','','.tl','IDR','+670'),('TM','TKM','TKM','.tm','TMM','+993'),('TN','TUN','TUN','.tn','TND','+216'),('TO','TON','TGA','.to','TOP','+676'),('TR','TUR','TUR','.tr','TRY','+90'),('TT','TTO','TRI','.tt','TTD','+1868'),('TV','TUV','','.tv','TVD','+688'),('TW','TWN','TPE','.tw','TWD','+886'),('TZ','TZA','TAN','.tz','TZS','+255'),('UA','UKR','UKR','.ua','UAH','+380'),('UG','UGA','UGA','.ug','UGX','+256'),('US','USA','USA','.us','USD','+1'),('UY','URY','URU','.uy','UYU','+598'),('UZ','UZB','UZB','.uz','UZS','+998'),('VA','VAT','','.va','EUR','+3906'),('VC','VCT','VIN','.vc','XCD','+1784'),('VE','VEN','VEN','.ve','VEB','+58'),('VG','VGB','ISB','.vg','USD','+1284'),('VI','VIR','','.vi','USD','+1340'),('VN','VNM','VIE','.vn','VND','+84'),('VU','VUT','VAN','.vu','VUV','+678'),('WF','WLF','','.wf','XPF','+681'),('WS','WSM','SAM','.ws','WST',''),('YE','YEM','YEM','.ye','YER','+967'),('YT','MYT','','.yt','EUR','+269'),('ZA','ZAF','RSA','.za','ZAR','+27'),('ZM','ZMB','ZAM','.zm','ZMK','+260'),('ZW','ZWE','ZIM','.zw','ZWD','+263');
/*!40000 ALTER TABLE `core_intl_country` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_intl_country_names`
--

DROP TABLE IF EXISTS `core_intl_country_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_intl_country_names` (
  `core_intl_country_ID` varchar(2) NOT NULL,
  `country_name_language` varchar(2) NOT NULL,
  `country_name` varchar(127) NOT NULL,
  PRIMARY KEY (`core_intl_country_ID`,`country_name_language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_intl_country_names`
--

LOCK TABLES `core_intl_country_names` WRITE;
/*!40000 ALTER TABLE `core_intl_country_names` DISABLE KEYS */;
INSERT INTO `core_intl_country_names` VALUES ('AC','de','Ascension'),('AC','en','Ascension Island'),('AC','es','Isla de la Ascensión'),('AC','fr','Île de l’Ascension'),('AC','it','Isola di Ascensione'),('AD','de','Andorra'),('AD','en','Andorra'),('AD','es','Andorra'),('AD','fr','Andorre'),('AD','it','Andorra'),('AE','de','Vereinigte Arabische Emirate'),('AE','en','United Arab Emirates'),('AE','es','Emiratos Árabes Unidos'),('AE','fr','Émirats arabes unis'),('AE','it','Emirati Arabi Uniti'),('AF','de','Afghanistan'),('AF','en','Afghanistan'),('AF','es','Afganistán'),('AF','fr','Afghanistan'),('AF','it','Afghanistan'),('AG','de','Antigua und Barbuda'),('AG','en','Antigua and Barbuda'),('AG','es','Antigua y Barbuda'),('AG','fr','Antigua-et-Barbuda'),('AG','it','Antigua e Barbuda'),('AI','de','Anguilla'),('AI','en','Anguilla'),('AI','es','Anguila'),('AI','fr','Anguilla'),('AI','it','Anguilla'),('AL','de','Albanien'),('AL','en','Albania'),('AL','es','Albania'),('AL','fr','Albanie'),('AL','it','Albania'),('AM','de','Armenien'),('AM','en','Armenia'),('AM','es','Armenia'),('AM','fr','Arménie'),('AM','it','Armenia'),('AN','de','Niederländische Antillen'),('AN','en','Netherlands Antilles'),('AN','es','Antillas Neerlandesas'),('AN','fr','Antilles néerlandaises'),('AN','it','Antille Olandesi'),('AO','de','Angola'),('AO','en','Angola'),('AO','es','Angola'),('AO','fr','Angola'),('AO','it','Angola'),('AQ','de','Antarktis'),('AQ','en','Antarctica'),('AQ','es','Antártida'),('AQ','fr','Antarctique'),('AQ','it','Antartide'),('AR','de','Argentinien'),('AR','en','Argentina'),('AR','es','Argentina'),('AR','fr','Argentine'),('AR','it','Argentina'),('AS','de','Amerikanisch-Samoa'),('AS','en','American Samoa'),('AS','es','Samoa Americana'),('AS','fr','Samoa américaines'),('AS','it','Samoa Americane'),('AT','de','Österreich'),('AT','en','Austria'),('AT','es','Austria'),('AT','fr','Autriche'),('AT','it','Austria'),('AU','de','Australien'),('AU','en','Australia'),('AU','es','Australia'),('AU','fr','Australie'),('AU','it','Australia'),('AW','de','Aruba'),('AW','en','Aruba'),('AW','es','Aruba'),('AW','fr','Aruba'),('AW','it','Aruba'),('AX','de','Alandinseln'),('AX','en','Åland Islands'),('AX','es','Islas Åland'),('AX','fr','Îles Åland'),('AX','it','Isole Aland'),('AZ','de','Aserbaidschan'),('AZ','en','Azerbaijan'),('AZ','es','Azerbaiyán'),('AZ','fr','Azerbaïdjan'),('AZ','it','Azerbaigian'),('BA','de','Bosnien und Herzegowina'),('BA','en','Bosnia and Herzegovina'),('BA','es','Bosnia-Herzegovina'),('BA','fr','Bosnie-Herzégovine'),('BA','it','Bosnia Erzegovina'),('BB','de','Barbados'),('BB','en','Barbados'),('BB','es','Barbados'),('BB','fr','Barbade'),('BB','it','Barbados'),('BD','de','Bangladesch'),('BD','en','Bangladesh'),('BD','es','Bangladesh'),('BD','fr','Bangladesh'),('BD','it','Bangladesh'),('BE','de','Belgien'),('BE','en','Belgium'),('BE','es','Bélgica'),('BE','fr','Belgique'),('BE','it','Belgio'),('BF','de','Burkina Faso'),('BF','en','Burkina Faso'),('BF','es','Burkina Faso'),('BF','fr','Burkina Faso'),('BF','it','Burkina Faso'),('BG','de','Bulgarien'),('BG','en','Bulgaria'),('BG','es','Bulgaria'),('BG','fr','Bulgarie'),('BG','it','Bulgaria'),('BH','de','Bahrain'),('BH','en','Bahrain'),('BH','es','Bahréin'),('BH','fr','Bahreïn'),('BH','it','Bahrein'),('BI','de','Burundi'),('BI','en','Burundi'),('BI','es','Burundi'),('BI','fr','Burundi'),('BI','it','Burundi'),('BJ','de','Benin'),('BJ','en','Benin'),('BJ','es','Benín'),('BJ','fr','Bénin'),('BJ','it','Benin'),('BL','de','St. Barthélemy'),('BL','en','Saint Barthélemy'),('BL','es','San Bartolomé'),('BL','fr','Saint-Barthélémy'),('BL','it','San Bartolomeo'),('BM','de','Bermuda'),('BM','en','Bermuda'),('BM','es','Bermudas'),('BM','fr','Bermudes'),('BM','it','Bermuda'),('BN','de','Brunei Darussalam'),('BN','en','Brunei'),('BN','es','Brunéi'),('BN','fr','Brunéi Darussalam'),('BN','it','Brunei'),('BO','de','Bolivien'),('BO','en','Bolivia'),('BO','es','Bolivia'),('BO','fr','Bolivie'),('BO','it','Bolivia'),('BQ','de','Karibische Niederlande'),('BQ','en','Caribbean Netherlands'),('BQ','es','Caribe neerlandés'),('BQ','fr','Pays-Bas caribéens'),('BQ','it','Caraibi Olandesi'),('BR','de','Brasilien'),('BR','en','Brazil'),('BR','es','Brasil'),('BR','fr','Brésil'),('BR','it','Brasile'),('BS','de','Bahamas'),('BS','en','Bahamas'),('BS','es','Bahamas'),('BS','fr','Bahamas'),('BS','it','Bahamas'),('BT','de','Bhutan'),('BT','en','Bhutan'),('BT','es','Bután'),('BT','fr','Bhoutan'),('BT','it','Bhutan'),('BV','de','Bouvetinsel'),('BV','en','Bouvet Island'),('BV','es','Isla Bouvet'),('BV','fr','Île Bouvet'),('BV','it','Isola Bouvet'),('BW','de','Botsuana'),('BW','en','Botswana'),('BW','es','Botsuana'),('BW','fr','Botswana'),('BW','it','Botswana'),('BY','de','Belarus'),('BY','en','Belarus'),('BY','es','Bielorrusia'),('BY','fr','Bélarus'),('BY','it','Bielorussia'),('BZ','de','Belize'),('BZ','en','Belize'),('BZ','es','Belice'),('BZ','fr','Belize'),('BZ','it','Belize'),('CA','de','Kanada'),('CA','en','Canada'),('CA','es','Canadá'),('CA','fr','Canada'),('CA','it','Canada'),('CC','de','Kokosinseln'),('CC','en','Cocos [Keeling] Islands'),('CC','es','Islas Cocos'),('CC','fr','Îles Cocos [Keeling]'),('CC','it','Isole Cocos'),('CD','de','Kongo [Demokratische Republik]'),('CD','en','Congo [DRC]'),('CD','es','República Democrática del Congo'),('CD','fr','République démocratique du Congo'),('CD','it','Congo - RDC'),('CF','de','Zentralafrikanische Republik'),('CF','en','Central African Republic'),('CF','es','República Centroafricana'),('CF','fr','République centrafricaine'),('CF','it','Repubblica Centrafricana'),('CG','de','Kongo [Republik]'),('CG','en','Congo [Republic]'),('CG','es','Congo [República]'),('CG','fr','République du Congo'),('CG','it','Repubblica del Congo'),('CH','de','Schweiz'),('CH','en','Switzerland'),('CH','es','Suiza'),('CH','fr','Suisse'),('CH','it','Svizzera'),('CI','de','Elfenbeinküste'),('CI','en','Ivory Coast'),('CI','es','Costa de Marfil'),('CI','fr','Côte d’Ivoire'),('CI','it','Costa d\'Avorio'),('CK','de','Cookinseln'),('CK','en','Cook Islands'),('CK','es','Islas Cook'),('CK','fr','Îles Cook'),('CK','it','Isole Cook'),('CL','de','Chile'),('CL','en','Chile'),('CL','es','Chile'),('CL','fr','Chili'),('CL','it','Cile'),('CM','de','Kamerun'),('CM','en','Cameroon'),('CM','es','Camerún'),('CM','fr','Cameroun'),('CM','it','Camerun'),('CN','de','China'),('CN','en','China'),('CN','es','China'),('CN','fr','Chine'),('CN','it','Cina'),('CO','de','Kolumbien'),('CO','en','Colombia'),('CO','es','Colombia'),('CO','fr','Colombie'),('CO','it','Colombia'),('CP','de','Clipperton-Insel'),('CP','en','Clipperton Island'),('CP','es','Isla Clipperton'),('CP','fr','Île Clipperton'),('CP','it','Isola di Clipperton'),('CR','de','Costa Rica'),('CR','en','Costa Rica'),('CR','es','Costa Rica'),('CR','fr','Costa Rica'),('CR','it','Costa Rica'),('CU','de','Kuba'),('CU','en','Cuba'),('CU','es','Cuba'),('CU','fr','Cuba'),('CU','it','Cuba'),('CV','de','Kap Verde'),('CV','en','Cape Verde'),('CV','es','Cabo Verde'),('CV','fr','Cap-Vert'),('CV','it','Capo Verde'),('CW','de','Curaçao'),('CW','en','Curaçao'),('CW','es','Curazao'),('CW','fr','Curaçao'),('CW','it','Curaçao'),('CX','de','Weihnachtsinsel'),('CX','en','Christmas Island'),('CX','es','Isla Christmas'),('CX','fr','Île Christmas'),('CX','it','Isola di Christmas'),('CY','de','Zypern'),('CY','en','Cyprus'),('CY','es','Chipre'),('CY','fr','Chypre'),('CY','it','Cipro'),('CZ','de','Tschechische Republik'),('CZ','en','Czech Republic'),('CZ','es','República Checa'),('CZ','fr','République tchèque'),('CZ','it','Repubblica Ceca'),('DE','de','Deutschland'),('DE','en','Germany'),('DE','es','Alemania'),('DE','fr','Allemagne'),('DE','it','Germania'),('DG','de','Diego Garcia'),('DG','en','Diego Garcia'),('DG','es','Diego García'),('DG','fr','Diego Garcia'),('DG','it','Diego Garcia'),('DJ','de','Dschibuti'),('DJ','en','Djibouti'),('DJ','es','Yibuti'),('DJ','fr','Djibouti'),('DJ','it','Gibuti'),('DK','de','Dänemark'),('DK','en','Denmark'),('DK','es','Dinamarca'),('DK','fr','Danemark'),('DK','it','Danimarca'),('DM','de','Dominica'),('DM','en','Dominica'),('DM','es','Dominica'),('DM','fr','Dominique'),('DM','it','Dominica'),('DO','de','Dominikanische Republik'),('DO','en','Dominican Republic'),('DO','es','República Dominicana'),('DO','fr','République dominicaine'),('DO','it','Repubblica Dominicana'),('DZ','de','Algerien'),('DZ','en','Algeria'),('DZ','es','Argelia'),('DZ','fr','Algérie'),('DZ','it','Algeria'),('EA','de','Ceuta und Melilla'),('EA','en','Ceuta and Melilla'),('EA','es','Ceuta y Melilla'),('EA','fr','Ceuta et Melilla'),('EA','it','Ceuta e Melilla'),('EC','de','Ecuador'),('EC','en','Ecuador'),('EC','es','Ecuador'),('EC','fr','Équateur'),('EC','it','Ecuador'),('EE','de','Estland'),('EE','en','Estonia'),('EE','es','Estonia'),('EE','fr','Estonie'),('EE','it','Estonia'),('EG','de','Ägypten'),('EG','en','Egypt'),('EG','es','Egipto'),('EG','fr','Égypte'),('EG','it','Egitto'),('EH','de','Westsahara'),('EH','en','Western Sahara'),('EH','es','Sáhara Occidental'),('EH','fr','Sahara occidental'),('EH','it','Sahara Occidentale'),('ER','de','Eritrea'),('ER','en','Eritrea'),('ER','es','Eritrea'),('ER','fr','Érythrée'),('ER','it','Eritrea'),('ES','de','Spanien'),('ES','en','Spain'),('ES','es','España'),('ES','fr','Espagne'),('ES','it','Spagna'),('ET','de','Äthiopien'),('ET','en','Ethiopia'),('ET','es','Etiopía'),('ET','fr','Éthiopie'),('ET','it','Etiopia'),('EU','de','Europäische Union'),('EU','en','European Union'),('EU','es','Unión Europea'),('EU','fr','Union européenne'),('EU','it','Unione Europea'),('FI','de','Finnland'),('FI','en','Finland'),('FI','es','Finlandia'),('FI','fr','Finlande'),('FI','it','Finlandia'),('FJ','de','Fidschi'),('FJ','en','Fiji'),('FJ','es','Fiyi'),('FJ','fr','Fidji'),('FJ','it','Figi'),('FK','de','Falklandinseln'),('FK','en','Falkland Islands [Islas Malvinas]'),('FK','es','Islas Malvinas [Islas Falkland]'),('FK','fr','Îles Malouines'),('FK','it','Isole Falkland [isole Malvine]'),('FM','de','Mikronesien'),('FM','en','Micronesia'),('FM','es','Micronesia'),('FM','fr','États fédérés de Micronésie'),('FM','it','Micronesia'),('FO','de','Färöer'),('FO','en','Faroe Islands'),('FO','es','Islas Feroe'),('FO','fr','Îles Féroé'),('FO','it','Isole Faroe'),('FR','de','Frankreich'),('FR','en','France'),('FR','es','Francia'),('FR','fr','France'),('FR','it','Francia'),('GA','de','Gabun'),('GA','en','Gabon'),('GA','es','Gabón'),('GA','fr','Gabon'),('GA','it','Gabon'),('GB','de','Vereinigtes Königreich'),('GB','en','United Kingdom'),('GB','es','Reino Unido'),('GB','fr','Royaume-Uni'),('GB','it','Regno Unito'),('GD','de','Grenada'),('GD','en','Grenada'),('GD','es','Granada'),('GD','fr','Grenade'),('GD','it','Grenada'),('GE','de','Georgien'),('GE','en','Georgia'),('GE','es','Georgia'),('GE','fr','Géorgie'),('GE','it','Georgia'),('GF','de','Französisch-Guayana'),('GF','en','French Guiana'),('GF','es','Guayana Francesa'),('GF','fr','Guyane française'),('GF','it','Guiana Francese'),('GG','de','Guernsey'),('GG','en','Guernsey'),('GG','es','Guernsey'),('GG','fr','Guernesey'),('GG','it','Guernsey'),('GH','de','Ghana'),('GH','en','Ghana'),('GH','es','Ghana'),('GH','fr','Ghana'),('GH','it','Ghana'),('GI','de','Gibraltar'),('GI','en','Gibraltar'),('GI','es','Gibraltar'),('GI','fr','Gibraltar'),('GI','it','Gibilterra'),('GL','de','Grönland'),('GL','en','Greenland'),('GL','es','Groenlandia'),('GL','fr','Groenland'),('GL','it','Groenlandia'),('GM','de','Gambia'),('GM','en','Gambia'),('GM','es','Gambia'),('GM','fr','Gambie'),('GM','it','Gambia'),('GN','de','Guinea'),('GN','en','Guinea'),('GN','es','Guinea'),('GN','fr','Guinée'),('GN','it','Guinea'),('GP','de','Guadeloupe'),('GP','en','Guadeloupe'),('GP','es','Guadalupe'),('GP','fr','Guadeloupe'),('GP','it','Guadalupa'),('GQ','de','Äquatorialguinea'),('GQ','en','Equatorial Guinea'),('GQ','es','Guinea Ecuatorial'),('GQ','fr','Guinée équatoriale'),('GQ','it','Guinea Equatoriale'),('GR','de','Griechenland'),('GR','en','Greece'),('GR','es','Grecia'),('GR','fr','Grèce'),('GR','it','Grecia'),('GS','de','Südgeorgien und die Südlichen Sandwichinseln'),('GS','en','South Georgia and the South Sandwich Islands'),('GS','es','Islas Georgia del Sur y Sandwich del Sur'),('GS','fr','Géorgie du Sud et les Îles Sandwich du Sud'),('GS','it','Georgia del Sud e Isole Sandwich del Sud'),('GT','de','Guatemala'),('GT','en','Guatemala'),('GT','es','Guatemala'),('GT','fr','Guatemala'),('GT','it','Guatemala'),('GU','de','Guam'),('GU','en','Guam'),('GU','es','Guam'),('GU','fr','Guam'),('GU','it','Guam'),('GW','de','Guinea-Bissau'),('GW','en','Guinea-Bissau'),('GW','es','Guinea-Bissau'),('GW','fr','Guinée-Bissau'),('GW','it','Guinea-Bissau'),('GY','de','Guyana'),('GY','en','Guyana'),('GY','es','Guyana'),('GY','fr','Guyana'),('GY','it','Guyana'),('HK','de','Hongkong'),('HK','en','Hong Kong SAR China'),('HK','es','Hong Kong'),('HK','fr','R.A.S. chinoise de Hong Kong'),('HK','it','RAS di Hong Kong'),('HM','de','Heard- und McDonald-Inseln'),('HM','en','Heard Island and McDonald Islands'),('HM','es','Islas Heard y McDonald'),('HM','fr','Îles Heard et MacDonald'),('HM','it','Isole Heard ed Isole McDonald'),('HN','de','Honduras'),('HN','en','Honduras'),('HN','es','Honduras'),('HN','fr','Honduras'),('HN','it','Honduras'),('HR','de','Kroatien'),('HR','en','Croatia'),('HR','es','Croacia'),('HR','fr','Croatie'),('HR','it','Croazia'),('HT','de','Haiti'),('HT','en','Haiti'),('HT','es','Haití'),('HT','fr','Haïti'),('HT','it','Haiti'),('HU','de','Ungarn'),('HU','en','Hungary'),('HU','es','Hungría'),('HU','fr','Hongrie'),('HU','it','Ungheria'),('IC','de','Kanarische Inseln'),('IC','en','Canary Islands'),('IC','es','Islas Canarias'),('IC','fr','Îles Canaries'),('IC','it','Isole Canarie'),('ID','de','Indonesien'),('ID','en','Indonesia'),('ID','es','Indonesia'),('ID','fr','Indonésie'),('ID','it','Indonesia'),('IE','de','Irland'),('IE','en','Ireland'),('IE','es','Irlanda'),('IE','fr','Irlande'),('IE','it','Irlanda'),('IL','de','Israel'),('IL','en','Israel'),('IL','es','Israel'),('IL','fr','Israël'),('IL','it','Israele'),('IM','de','Isle of Man'),('IM','en','Isle of Man'),('IM','es','Isla de Man'),('IM','fr','Île de Man'),('IM','it','Isola di Man'),('IN','de','Indien'),('IN','en','India'),('IN','es','India'),('IN','fr','Inde'),('IN','it','India'),('IO','de','Britisches Territorium im Indischen Ozean'),('IO','en','British Indian Ocean Territory'),('IO','es','Territorio Británico del Océano Índico'),('IO','fr','Territoire britannique de l\'océan Indien'),('IO','it','Territorio Britannico dell’Oceano Indiano'),('IQ','de','Irak'),('IQ','en','Iraq'),('IQ','es','Iraq'),('IQ','fr','Irak'),('IQ','it','Iraq'),('IR','de','Iran'),('IR','en','Iran'),('IR','es','Irán'),('IR','fr','Iran'),('IR','it','Iran'),('IS','de','Island'),('IS','en','Iceland'),('IS','es','Islandia'),('IS','fr','Islande'),('IS','it','Islanda'),('IT','de','Italien'),('IT','en','Italy'),('IT','es','Italia'),('IT','fr','Italie'),('IT','it','Italia'),('JE','de','Jersey'),('JE','en','Jersey'),('JE','es','Jersey'),('JE','fr','Jersey'),('JE','it','Jersey'),('JM','de','Jamaika'),('JM','en','Jamaica'),('JM','es','Jamaica'),('JM','fr','Jamaïque'),('JM','it','Giamaica'),('JO','de','Jordanien'),('JO','en','Jordan'),('JO','es','Jordania'),('JO','fr','Jordanie'),('JO','it','Giordania'),('JP','de','Japan'),('JP','en','Japan'),('JP','es','Japón'),('JP','fr','Japon'),('JP','it','Giappone'),('KE','de','Kenia'),('KE','en','Kenya'),('KE','es','Kenia'),('KE','fr','Kenya'),('KE','it','Kenya'),('KG','de','Kirgisistan'),('KG','en','Kyrgyzstan'),('KG','es','Kirguistán'),('KG','fr','Kirghizistan'),('KG','it','Kirghizistan'),('KH','de','Kambodscha'),('KH','en','Cambodia'),('KH','es','Camboya'),('KH','fr','Cambodge'),('KH','it','Cambogia'),('KI','de','Kiribati'),('KI','en','Kiribati'),('KI','es','Kiribati'),('KI','fr','Kiribati'),('KI','it','Kiribati'),('KM','de','Komoren'),('KM','en','Comoros'),('KM','es','Comoras'),('KM','fr','Comores'),('KM','it','Comore'),('KN','de','St. Kitts und Nevis'),('KN','en','Saint Kitts and Nevis'),('KN','es','San Cristóbal y Nieves'),('KN','fr','Saint-Kitts-et-Nevis'),('KN','it','Saint Kitts e Nevis'),('KP','de','Demokratische Volksrepublik Korea'),('KP','en','North Korea'),('KP','es','Corea del Norte'),('KP','fr','Corée du Nord'),('KP','it','Corea del Nord'),('KR','de','Republik Korea'),('KR','en','South Korea'),('KR','es','Corea del Sur'),('KR','fr','Corée du Sud'),('KR','it','Corea del Sud'),('KW','de','Kuwait'),('KW','en','Kuwait'),('KW','es','Kuwait'),('KW','fr','Koweït'),('KW','it','Kuwait'),('KY','de','Kaimaninseln'),('KY','en','Cayman Islands'),('KY','es','Islas Caimán'),('KY','fr','Îles Caïmans'),('KY','it','Isole Cayman'),('KZ','de','Kasachstan'),('KZ','en','Kazakhstan'),('KZ','es','Kazajistán'),('KZ','fr','Kazakhstan'),('KZ','it','Kazakistan'),('LA','de','Laos'),('LA','en','Laos'),('LA','es','Laos'),('LA','fr','Laos'),('LA','it','Laos'),('LB','de','Libanon'),('LB','en','Lebanon'),('LB','es','Líbano'),('LB','fr','Liban'),('LB','it','Libano'),('LC','de','St. Lucia'),('LC','en','Saint Lucia'),('LC','es','Santa Lucía'),('LC','fr','Sainte-Lucie'),('LC','it','Saint Lucia'),('LI','de','Liechtenstein'),('LI','en','Liechtenstein'),('LI','es','Liechtenstein'),('LI','fr','Liechtenstein'),('LI','it','Liechtenstein'),('LK','de','Sri Lanka'),('LK','en','Sri Lanka'),('LK','es','Sri Lanka'),('LK','fr','Sri Lanka'),('LK','it','Sri Lanka'),('LR','de','Liberia'),('LR','en','Liberia'),('LR','es','Liberia'),('LR','fr','Libéria'),('LR','it','Liberia'),('LS','de','Lesotho'),('LS','en','Lesotho'),('LS','es','Lesoto'),('LS','fr','Lesotho'),('LS','it','Lesotho'),('LT','de','Litauen'),('LT','en','Lithuania'),('LT','es','Lituania'),('LT','fr','Lituanie'),('LT','it','Lituania'),('LU','de','Luxemburg'),('LU','en','Luxembourg'),('LU','es','Luxemburgo'),('LU','fr','Luxembourg'),('LU','it','Lussemburgo'),('LV','de','Lettland'),('LV','en','Latvia'),('LV','es','Letonia'),('LV','fr','Lettonie'),('LV','it','Lettonia'),('LY','de','Libyen'),('LY','en','Libya'),('LY','es','Libia'),('LY','fr','Libye'),('LY','it','Libia'),('MA','de','Marokko'),('MA','en','Morocco'),('MA','es','Marruecos'),('MA','fr','Maroc'),('MA','it','Marocco'),('MC','de','Monaco'),('MC','en','Monaco'),('MC','es','Mónaco'),('MC','fr','Monaco'),('MC','it','Monaco'),('MD','de','Republik Moldau'),('MD','en','Moldova'),('MD','es','Moldavia'),('MD','fr','Moldavie'),('MD','it','Moldavia'),('ME','de','Montenegro'),('ME','en','Montenegro'),('ME','es','Montenegro'),('ME','fr','Monténégro'),('ME','it','Montenegro'),('MF','de','St. Martin'),('MF','en','Saint Martin'),('MF','es','San Martín'),('MF','fr','Saint-Martin [partie française]'),('MF','it','Saint Martin'),('MG','de','Madagaskar'),('MG','en','Madagascar'),('MG','es','Madagascar'),('MG','fr','Madagascar'),('MG','it','Madagascar'),('MH','de','Marshallinseln'),('MH','en','Marshall Islands'),('MH','es','Islas Marshall'),('MH','fr','Îles Marshall'),('MH','it','Isole Marshall'),('MK','de','Mazedonien'),('MK','en','Macedonia'),('MK','es','Macedonia'),('MK','fr','Macédoine'),('MK','it','Repubblica di Macedonia'),('ML','de','Mali'),('ML','en','Mali'),('ML','es','Mali'),('ML','fr','Mali'),('ML','it','Mali'),('MM','de','Myanmar'),('MM','en','Myanmar [Burma]'),('MM','es','Myanmar [Birmania]'),('MM','fr','Myanmar'),('MM','it','Myanmar'),('MN','de','Mongolei'),('MN','en','Mongolia'),('MN','es','Mongolia'),('MN','fr','Mongolie'),('MN','it','Mongolia'),('MO','de','Macao'),('MO','en','Macau SAR China'),('MO','es','Macao'),('MO','fr','R.A.S. chinoise de Macao'),('MO','it','RAS di Macao'),('MP','de','Nördliche Marianen'),('MP','en','Northern Mariana Islands'),('MP','es','Islas Marianas del Norte'),('MP','fr','Îles Mariannes du Nord'),('MP','it','Isole Marianne Settentrionali'),('MQ','de','Martinique'),('MQ','en','Martinique'),('MQ','es','Martinica'),('MQ','fr','Martinique'),('MQ','it','Martinica'),('MR','de','Mauretanien'),('MR','en','Mauritania'),('MR','es','Mauritania'),('MR','fr','Mauritanie'),('MR','it','Mauritania'),('MS','de','Montserrat'),('MS','en','Montserrat'),('MS','es','Montserrat'),('MS','fr','Montserrat'),('MS','it','Montserrat'),('MT','de','Malta'),('MT','en','Malta'),('MT','es','Malta'),('MT','fr','Malte'),('MT','it','Malta'),('MU','de','Mauritius'),('MU','en','Mauritius'),('MU','es','Mauricio'),('MU','fr','Maurice'),('MU','it','Mauritius'),('MV','de','Malediven'),('MV','en','Maldives'),('MV','es','Maldivas'),('MV','fr','Maldives'),('MV','it','Maldive'),('MW','de','Malawi'),('MW','en','Malawi'),('MW','es','Malaui'),('MW','fr','Malawi'),('MW','it','Malawi'),('MX','de','Mexiko'),('MX','en','Mexico'),('MX','es','México'),('MX','fr','Mexique'),('MX','it','Messico'),('MY','de','Malaysia'),('MY','en','Malaysia'),('MY','es','Malasia'),('MY','fr','Malaisie'),('MY','it','Malesia'),('MZ','de','Mosambik'),('MZ','en','Mozambique'),('MZ','es','Mozambique'),('MZ','fr','Mozambique'),('MZ','it','Mozambico'),('NA','de','Namibia'),('NA','en','Namibia'),('NA','es','Namibia'),('NA','fr','Namibie'),('NA','it','Namibia'),('NC','de','Neukaledonien'),('NC','en','New Caledonia'),('NC','es','Nueva Caledonia'),('NC','fr','Nouvelle-Calédonie'),('NC','it','Nuova Caledonia'),('NE','de','Niger'),('NE','en','Niger'),('NE','es','Níger'),('NE','fr','Niger'),('NE','it','Niger'),('NF','de','Norfolkinsel'),('NF','en','Norfolk Island'),('NF','es','Isla Norfolk'),('NF','fr','Île Norfolk'),('NF','it','Isola Norfolk'),('NG','de','Nigeria'),('NG','en','Nigeria'),('NG','es','Nigeria'),('NG','fr','Nigéria'),('NG','it','Nigeria'),('NI','de','Nicaragua'),('NI','en','Nicaragua'),('NI','es','Nicaragua'),('NI','fr','Nicaragua'),('NI','it','Nicaragua'),('NL','de','Niederlande'),('NL','en','Netherlands'),('NL','es','Países Bajos'),('NL','fr','Pays-Bas'),('NL','it','Paesi Bassi'),('NO','de','Norwegen'),('NO','en','Norway'),('NO','es','Noruega'),('NO','fr','Norvège'),('NO','it','Norvegia'),('NP','de','Nepal'),('NP','en','Nepal'),('NP','es','Nepal'),('NP','fr','Népal'),('NP','it','Nepal'),('NR','de','Nauru'),('NR','en','Nauru'),('NR','es','Nauru'),('NR','fr','Nauru'),('NR','it','Nauru'),('NU','de','Niue'),('NU','en','Niue'),('NU','es','Isla Niue'),('NU','fr','Niue'),('NU','it','Niue'),('NZ','de','Neuseeland'),('NZ','en','New Zealand'),('NZ','es','Nueva Zelanda'),('NZ','fr','Nouvelle-Zélande'),('NZ','it','Nuova Zelanda'),('OM','de','Oman'),('OM','en','Oman'),('OM','es','Omán'),('OM','fr','Oman'),('OM','it','Oman'),('PA','de','Panama'),('PA','en','Panama'),('PA','es','Panamá'),('PA','fr','Panama'),('PA','it','Panama'),('PE','de','Peru'),('PE','en','Peru'),('PE','es','Perú'),('PE','fr','Pérou'),('PE','it','Perù'),('PF','de','Französisch-Polynesien'),('PF','en','French Polynesia'),('PF','es','Polinesia Francesa'),('PF','fr','Polynésie française'),('PF','it','Polinesia Francese'),('PG','de','Papua-Neuguinea'),('PG','en','Papua New Guinea'),('PG','es','Papúa Nueva Guinea'),('PG','fr','Papouasie-Nouvelle-Guinée'),('PG','it','Papua Nuova Guinea'),('PH','de','Philippinen'),('PH','en','Philippines'),('PH','es','Filipinas'),('PH','fr','Philippines'),('PH','it','Filippine'),('PK','de','Pakistan'),('PK','en','Pakistan'),('PK','es','Pakistán'),('PK','fr','Pakistan'),('PK','it','Pakistan'),('PL','de','Polen'),('PL','en','Poland'),('PL','es','Polonia'),('PL','fr','Pologne'),('PL','it','Polonia'),('PM','de','St. Pierre und Miquelon'),('PM','en','Saint Pierre and Miquelon'),('PM','es','San Pedro y Miquelón'),('PM','fr','Saint-Pierre-et-Miquelon'),('PM','it','Saint Pierre e Miquelon'),('PN','de','Pitcairninseln'),('PN','en','Pitcairn Islands'),('PN','es','Islas Pitcairn'),('PN','fr','Pitcairn'),('PN','it','Pitcairn'),('PR','de','Puerto Rico'),('PR','en','Puerto Rico'),('PR','es','Puerto Rico'),('PR','fr','Porto Rico'),('PR','it','Portorico'),('PS','de','Palästinensische Autonomiegebiete'),('PS','en','Palestinian Territories'),('PS','es','Territorios Palestinos'),('PS','fr','Territoire palestinien'),('PS','it','Territori palestinesi'),('PT','de','Portugal'),('PT','en','Portugal'),('PT','es','Portugal'),('PT','fr','Portugal'),('PT','it','Portogallo'),('PW','de','Palau'),('PW','en','Palau'),('PW','es','Palau'),('PW','fr','Palaos'),('PW','it','Palau'),('PY','de','Paraguay'),('PY','en','Paraguay'),('PY','es','Paraguay'),('PY','fr','Paraguay'),('PY','it','Paraguay'),('QA','de','Katar'),('QA','en','Qatar'),('QA','es','Qatar'),('QA','fr','Qatar'),('QA','it','Qatar'),('QO','de','Äußeres Ozeanien'),('QO','en','Outlying Oceania'),('QO','es','Territorios alejados de Oceanía'),('QO','fr','régions éloignées de l’Océanie'),('QO','it','Oceania lontana'),('RE','de','Réunion'),('RE','en','Réunion'),('RE','es','Reunión'),('RE','fr','Réunion'),('RE','it','Réunion'),('RO','de','Rumänien'),('RO','en','Romania'),('RO','es','Rumanía'),('RO','fr','Roumanie'),('RO','it','Romania'),('RS','de','Serbien'),('RS','en','Serbia'),('RS','es','Serbia'),('RS','fr','Serbie'),('RS','it','Serbia'),('RU','de','Russische Föderation'),('RU','en','Russia'),('RU','es','Rusia'),('RU','fr','Russie'),('RU','it','Federazione Russa'),('RW','de','Ruanda'),('RW','en','Rwanda'),('RW','es','Ruanda'),('RW','fr','Rwanda'),('RW','it','Ruanda'),('SA','de','Saudi-Arabien'),('SA','en','Saudi Arabia'),('SA','es','Arabia Saudí'),('SA','fr','Arabie saoudite'),('SA','it','Arabia Saudita'),('SB','de','Salomonen'),('SB','en','Solomon Islands'),('SB','es','Islas Salomón'),('SB','fr','Îles Salomon'),('SB','it','Isole Solomon'),('SC','de','Seychellen'),('SC','en','Seychelles'),('SC','es','Seychelles'),('SC','fr','Seychelles'),('SC','it','Seychelles'),('SD','de','Sudan'),('SD','en','Sudan'),('SD','es','Sudán'),('SD','fr','Soudan'),('SD','it','Sudan'),('SE','de','Schweden'),('SE','en','Sweden'),('SE','es','Suecia'),('SE','fr','Suède'),('SE','it','Svezia'),('SG','de','Singapur'),('SG','en','Singapore'),('SG','es','Singapur'),('SG','fr','Singapour'),('SG','it','Singapore'),('SH','de','St. Helena'),('SH','en','Saint Helena'),('SH','es','Santa Elena'),('SH','fr','Sainte-Hélène'),('SH','it','Sant’Elena'),('SI','de','Slowenien'),('SI','en','Slovenia'),('SI','es','Eslovenia'),('SI','fr','Slovénie'),('SI','it','Slovenia'),('SJ','de','Svalbard und Jan Mayen'),('SJ','en','Svalbard and Jan Mayen'),('SJ','es','Svalbard y Jan Mayen'),('SJ','fr','Svalbard et Île Jan Mayen'),('SJ','it','Svalbard e Jan Mayen'),('SK','de','Slowakei'),('SK','en','Slovakia'),('SK','es','Eslovaquia'),('SK','fr','Slovaquie'),('SK','it','Slovacchia'),('SL','de','Sierra Leone'),('SL','en','Sierra Leone'),('SL','es','Sierra Leona'),('SL','fr','Sierra Leone'),('SL','it','Sierra Leone'),('SM','de','San Marino'),('SM','en','San Marino'),('SM','es','San Marino'),('SM','fr','Saint-Marin'),('SM','it','San Marino'),('SN','de','Senegal'),('SN','en','Senegal'),('SN','es','Senegal'),('SN','fr','Sénégal'),('SN','it','Senegal'),('SO','de','Somalia'),('SO','en','Somalia'),('SO','es','Somalia'),('SO','fr','Somalie'),('SO','it','Somalia'),('SR','de','Suriname'),('SR','en','Suriname'),('SR','es','Surinam'),('SR','fr','Suriname'),('SR','it','Suriname'),('SS','de','Südsudan'),('SS','en','South Sudan'),('SS','es','Sudán del Sur'),('SS','fr','Soudan du Sud'),('SS','it','Sudan del Sud'),('ST','de','São Tomé und Príncipe'),('ST','en','São Tomé and Príncipe'),('ST','es','Santo Tomé y Príncipe'),('ST','fr','Sao Tomé-et-Príncipe'),('ST','it','Sao Tomé e Príncipe'),('SV','de','El Salvador'),('SV','en','El Salvador'),('SV','es','El Salvador'),('SV','fr','El Salvador'),('SV','it','El Salvador'),('SX','de','Sint Maarten'),('SX','en','Sint Maarten'),('SX','es','Sint Maarten'),('SX','fr','Saint-Martin [partie néerlandaise]'),('SX','it','Sint Maarten'),('SY','de','Syrien'),('SY','en','Syria'),('SY','es','Siria'),('SY','fr','Syrie'),('SY','it','Siria'),('SZ','de','Swasiland'),('SZ','en','Swaziland'),('SZ','es','Suazilandia'),('SZ','fr','Swaziland'),('SZ','it','Swaziland'),('TA','de','Tristan da Cunha'),('TA','en','Tristan da Cunha'),('TA','es','Tristán da Cunha'),('TA','fr','Tristan da Cunha'),('TA','it','Tristan da Cunha'),('TC','de','Turks- und Caicosinseln'),('TC','en','Turks and Caicos Islands'),('TC','es','Islas Turcas y Caicos'),('TC','fr','Îles Turks et Caïques'),('TC','it','Isole Turks e Caicos'),('TD','de','Tschad'),('TD','en','Chad'),('TD','es','Chad'),('TD','fr','Tchad'),('TD','it','Ciad'),('TF','de','Französische Süd- und Antarktisgebiete'),('TF','en','French Southern Territories'),('TF','es','Territorios Australes Franceses'),('TF','fr','Terres australes françaises'),('TF','it','Territori australi francesi'),('TG','de','Togo'),('TG','en','Togo'),('TG','es','Togo'),('TG','fr','Togo'),('TG','it','Togo'),('TH','de','Thailand'),('TH','en','Thailand'),('TH','es','Tailandia'),('TH','fr','Thaïlande'),('TH','it','Tailandia'),('TJ','de','Tadschikistan'),('TJ','en','Tajikistan'),('TJ','es','Tayikistán'),('TJ','fr','Tadjikistan'),('TJ','it','Tagikistan'),('TK','de','Tokelau'),('TK','en','Tokelau'),('TK','es','Tokelau'),('TK','fr','Tokelau'),('TK','it','Tokelau'),('TL','de','Osttimor'),('TL','en','East Timor'),('TL','es','Timor Oriental'),('TL','fr','Timor oriental'),('TL','it','Timor Est'),('TM','de','Turkmenistan'),('TM','en','Turkmenistan'),('TM','es','Turkmenistán'),('TM','fr','Turkménistan'),('TM','it','Turkmenistan'),('TN','de','Tunesien'),('TN','en','Tunisia'),('TN','es','Túnez'),('TN','fr','Tunisie'),('TN','it','Tunisia'),('TO','de','Tonga'),('TO','en','Tonga'),('TO','es','Tonga'),('TO','fr','Tonga'),('TO','it','Tonga'),('TR','de','Türkei'),('TR','en','Turkey'),('TR','es','Turquía'),('TR','fr','Turquie'),('TR','it','Turchia'),('TT','de','Trinidad und Tobago'),('TT','en','Trinidad and Tobago'),('TT','es','Trinidad y Tobago'),('TT','fr','Trinité-et-Tobago'),('TT','it','Trinidad e Tobago'),('TV','de','Tuvalu'),('TV','en','Tuvalu'),('TV','es','Tuvalu'),('TV','fr','Tuvalu'),('TV','it','Tuvalu'),('TW','de','Taiwan'),('TW','en','Taiwan'),('TW','es','Taiwán'),('TW','fr','Taïwan'),('TW','it','Taiwan'),('TZ','de','Tansania'),('TZ','en','Tanzania'),('TZ','es','Tanzania'),('TZ','fr','Tanzanie'),('TZ','it','Tanzania'),('UA','de','Ukraine'),('UA','en','Ukraine'),('UA','es','Ucrania'),('UA','fr','Ukraine'),('UA','it','Ucraina'),('UG','de','Uganda'),('UG','en','Uganda'),('UG','es','Uganda'),('UG','fr','Ouganda'),('UG','it','Uganda'),('UM','de','Amerikanisch-Ozeanien'),('UM','en','U.S. Outlying Islands'),('UM','es','Islas menores alejadas de los Estados Unidos'),('UM','fr','Îles éloignées des États-Unis'),('UM','it','Isole periferiche agli USA'),('US','de','Vereinigte Staaten'),('US','en','United States'),('US','es','Estados Unidos'),('US','fr','États-Unis'),('US','it','Stati Uniti'),('UY','de','Uruguay'),('UY','en','Uruguay'),('UY','es','Uruguay'),('UY','fr','Uruguay'),('UY','it','Uruguay'),('UZ','de','Usbekistan'),('UZ','en','Uzbekistan'),('UZ','es','Uzbekistán'),('UZ','fr','Ouzbékistan'),('UZ','it','Uzbekistan'),('VA','de','Vatikanstadt'),('VA','en','Vatican City'),('VA','es','Ciudad del Vaticano'),('VA','fr','État de la Cité du Vatican'),('VA','it','Città del Vaticano'),('VC','de','St. Vincent und die Grenadinen'),('VC','en','Saint Vincent and the Grenadines'),('VC','es','San Vicente y las Granadinas'),('VC','fr','Saint-Vincent-et-les Grenadines'),('VC','it','Saint Vincent e Grenadines'),('VE','de','Venezuela'),('VE','en','Venezuela'),('VE','es','Venezuela'),('VE','fr','Venezuela'),('VE','it','Venezuela'),('VG','de','Britische Jungferninseln'),('VG','en','British Virgin Islands'),('VG','es','Islas Vírgenes Británicas'),('VG','fr','Îles Vierges britanniques'),('VG','it','Isole Vergini Britanniche'),('VI','de','Amerikanische Jungferninseln'),('VI','en','U.S. Virgin Islands'),('VI','es','Islas Vírgenes de los Estados Unidos'),('VI','fr','Îles Vierges des États-Unis'),('VI','it','Isole Vergini Americane'),('VN','de','Vietnam'),('VN','en','Vietnam'),('VN','es','Vietnam'),('VN','fr','Viêt Nam'),('VN','it','Vietnam'),('VU','de','Vanuatu'),('VU','en','Vanuatu'),('VU','es','Vanuatu'),('VU','fr','Vanuatu'),('VU','it','Vanuatu'),('WF','de','Wallis und Futuna'),('WF','en','Wallis and Futuna'),('WF','es','Wallis y Futuna'),('WF','fr','Wallis-et-Futuna'),('WF','it','Wallis e Futuna'),('WS','de','Samoa'),('WS','en','Samoa'),('WS','es','Samoa'),('WS','fr','Samoa'),('WS','it','Samoa'),('YE','de','Jemen'),('YE','en','Yemen'),('YE','es','Yemen'),('YE','fr','Yémen'),('YE','it','Yemen'),('YT','de','Mayotte'),('YT','en','Mayotte'),('YT','es','Mayotte'),('YT','fr','Mayotte'),('YT','it','Mayotte'),('ZA','de','Südafrika'),('ZA','en','South Africa'),('ZA','es','Sudáfrica'),('ZA','fr','Afrique du Sud'),('ZA','it','Sudafrica'),('ZM','de','Sambia'),('ZM','en','Zambia'),('ZM','es','Zambia'),('ZM','fr','Zambie'),('ZM','it','Zambia'),('ZW','de','Simbabwe'),('ZW','en','Zimbabwe'),('ZW','es','Zimbabue'),('ZW','fr','Zimbabwe'),('ZW','it','Zimbabwe'),('ZZ','de','Unbekannte Region'),('ZZ','en','Unknown Region'),('ZZ','es','Región desconocida'),('ZZ','fr','région indéterminée'),('ZZ','it','Regione non valida o sconosciuta');
/*!40000 ALTER TABLE `core_intl_country_names` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_intl_currency`
--

DROP TABLE IF EXISTS `core_intl_currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_intl_currency` (
  `id` varchar(3) NOT NULL,
  `currency_symbol` varchar(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_intl_currency`
--

LOCK TABLES `core_intl_currency` WRITE;
/*!40000 ALTER TABLE `core_intl_currency` DISABLE KEYS */;
INSERT INTO `core_intl_currency` VALUES ('ADP',''),('AED',''),('AFN',''),('ALL',''),('AMD',''),('ANG',''),('AOA',''),('ARA',''),('ARS',''),('ATS','öS'),('AUD','AU'),('AWG',''),('AZN',''),('BAM',''),('BBD',''),('BDT',''),('BEC',''),('BEF',''),('BEL',''),('BGN',''),('BHD',''),('BIF',''),('BMD',''),('BND',''),('BOB',''),('BOP',''),('BOV',''),('BRL','R$'),('BRZ',''),('BSD',''),('BTN',''),('BUK',''),('BWP',''),('BYR',''),('BZD',''),('CAD','CA'),('CDF',''),('CHE',''),('CHF',''),('CHW',''),('CLF',''),('CLP',''),('CNY','CN'),('COP',''),('CRC',''),('CSD',''),('CSK',''),('CUC',''),('CUP',''),('CVE',''),('CYP',''),('CZK',''),('DDM',''),('DEM',''),('DJF',''),('DKK',''),('DOP',''),('DZD',''),('ECS',''),('ECV',''),('EEK',''),('EGP',''),('ERN',''),('ESA',''),('ESB',''),('ESP',''),('ETB',''),('EUR','€'),('FIM',''),('FJD',''),('FKP',''),('FRF',''),('GBP','£'),('GEK',''),('GEL',''),('GHS',''),('GIP',''),('GMD',''),('GNF',''),('GNS',''),('GQE',''),('GRD',''),('GTQ',''),('GWE',''),('GWP',''),('GYD',''),('HKD','HK'),('HNL',''),('HRD',''),('HRK',''),('HTG',''),('HUF',''),('IDR',''),('IEP',''),('ILP',''),('ILS','₪'),('INR','₹'),('IQD',''),('IRR',''),('ISK',''),('ITL',''),('JMD',''),('JOD',''),('JPY','¥'),('KES',''),('KGS',''),('KHR',''),('KMF',''),('KPW',''),('KRW','₩'),('KWD',''),('KYD',''),('KZT',''),('LAK',''),('LBP',''),('LKR',''),('LRD',''),('LSL',''),('LTL',''),('LTT',''),('LUC',''),('LUF',''),('LUL',''),('LVL',''),('LVR',''),('LYD',''),('MAD',''),('MAF',''),('MDL',''),('MGA',''),('MGF',''),('MKD',''),('MLF',''),('MMK',''),('MNT',''),('MOP',''),('MRO',''),('MTL',''),('MTP',''),('MUR',''),('MVR',''),('MWK',''),('MXN','MX'),('MXP',''),('MXV',''),('MYR',''),('MZE',''),('MZN',''),('NAD',''),('NGN',''),('NIO',''),('NLG',''),('NOK',''),('NPR',''),('NZD','NZ'),('OMR',''),('PAB',''),('PEI',''),('PEN',''),('PES',''),('PGK',''),('PHP',''),('PKR',''),('PLN',''),('PTE',''),('PYG',''),('QAR',''),('RHD',''),('RON',''),('RSD',''),('RUB',''),('RWF',''),('SAR',''),('SBD',''),('SCR',''),('SDG',''),('SEK',''),('SGD',''),('SHP',''),('SIT',''),('SKK',''),('SLL',''),('SOS',''),('SRD',''),('SRG',''),('SSP',''),('STD',''),('SUR',''),('SVC',''),('SYP',''),('SZL',''),('THB','฿'),('TJR',''),('TJS',''),('TMT',''),('TND',''),('TOP',''),('TPE',''),('TRY',''),('TTD',''),('TWD','NT'),('TZS',''),('UAH',''),('UAK',''),('UGX',''),('USD','$'),('USN',''),('USS',''),('UYU',''),('UZS',''),('VEB',''),('VEF',''),('VND','₫'),('VUV',''),('WST',''),('XAF','FC'),('XAG',''),('XAU',''),('XBA',''),('XBB',''),('XBC',''),('XBD',''),('XCD','EC'),('XDR',''),('XEU',''),('XFO',''),('XFU',''),('XOF','CF'),('XPD',''),('XPF','CF'),('XPT',''),('XRE',''),('XTS',''),('XXX',''),('YDD',''),('YER',''),('YUN',''),('ZAL',''),('ZAR',''),('ZMK',''),('ZWL',''),('ZWR','');
/*!40000 ALTER TABLE `core_intl_currency` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_intl_currency_names`
--

DROP TABLE IF EXISTS `core_intl_currency_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_intl_currency_names` (
  `core_intl_currency_ID` varchar(3) NOT NULL,
  `currency_name_language` varchar(2) NOT NULL,
  `currency_name` varchar(127) NOT NULL,
  PRIMARY KEY (`core_intl_currency_ID`,`currency_name_language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_intl_currency_names`
--

LOCK TABLES `core_intl_currency_names` WRITE;
/*!40000 ALTER TABLE `core_intl_currency_names` DISABLE KEYS */;
INSERT INTO `core_intl_currency_names` VALUES ('ADP','de','Andorranische Pesete'),('ADP','en','Andorran Peseta'),('ADP','es','peseta andorrana'),('ADP','fr','peseta andorrane'),('ADP','it',''),('AED','de','VAE-Dirham'),('AED','en','United Arab Emirates Dirham'),('AED','es','dírham de los Emiratos Árabes Unidos'),('AED','fr','dirham des Émirats arabes unis'),('AED','it','Dirham degli Emirati Arabi Uniti'),('AFA','es',''),('AFA','it',''),('AFN','de','Afghanische Afghani'),('AFN','en','Afghan Afghani'),('AFN','es','afgani afgano'),('AFN','fr','afghani afghan'),('AFN','it','Afghani'),('ALL','de','Albanische Lek'),('ALL','en','Albanian Lek'),('ALL','es','lek albanés'),('ALL','fr','lek albanais'),('ALL','it','Lek Albanese'),('AMD','de','Armenische Dram'),('AMD','en','Armenian Dram'),('AMD','es','dram armenio'),('AMD','fr','dram arménien'),('AMD','it','Dram Armeno'),('ANG','de','Niederländische-Antillen-Gulden'),('ANG','en','Netherlands Antillean Guilder'),('ANG','es','florín de las Antillas Neerlandesas'),('ANG','fr','florin antillais'),('ANG','it','Fiorino delle Antille Olandesi'),('AOA','de','Angolanischer Kwanza'),('AOA','en','Angolan Kwanza'),('AOA','es','kwanza angoleño'),('AOA','fr','kwanza angolais'),('AOA','it','Kwanza Angolano'),('AOK','es',''),('AOK','it',''),('AON','es',''),('AON','it',''),('AOR','es',''),('AOR','it',''),('ARA','de','Argentinischer Austral'),('ARA','en','Argentine Austral'),('ARA','es','austral argentino'),('ARA','fr','austral argentin'),('ARA','it',''),('ARM','en','Argentine Peso (1881-1970)'),('ARP','it',''),('ARS','de','Argentinischer Peso'),('ARS','en','Argentine Peso'),('ARS','es','peso argentino'),('ARS','fr','peso argentin'),('ARS','it','Peso Argentino'),('ATS','de','Österreichischer Schilling'),('ATS','en','Austrian Schilling'),('ATS','es','chelín austriaco'),('ATS','fr','schilling autrichien'),('ATS','it',''),('AUD','de','Australischer Dollar'),('AUD','en','Australian Dollar'),('AUD','es','dólar australiano'),('AUD','fr','dollar australien'),('AUD','it','Dollaro Australiano'),('AWG','de','Aruba-Florin'),('AWG','en','Aruban Florin'),('AWG','es','florín de Aruba'),('AWG','fr','florin arubais'),('AWG','it','Fiorino di Aruba'),('AZM','es',''),('AZM','it',''),('AZN','de','Aserbaidschan-Manat'),('AZN','en','Azerbaijani Manat'),('AZN','es','manat azerí'),('AZN','fr','manat azéri'),('AZN','it','Manat Azero'),('BAD','es','dinar bosnio'),('BAD','fr','dinar bosniaque'),('BAD','it',''),('BAM','de','Bosnien und Herzegowina Konvertierbare Mark'),('BAM','en','Bosnia-Herzegovina Convertible Mark'),('BAM','es','marco convertible de Bosnia-Herzegovina'),('BAM','fr','mark convertible bosniaque'),('BAM','it','Marco Conv. Bosnia-Erzegovina'),('BBD','de','Barbados-Dollar'),('BBD','en','Barbadian Dollar'),('BBD','es','dólar de Barbados'),('BBD','fr','dollar barbadien'),('BBD','it','Dollaro di Barbados'),('BDT','de','Bangladesch-Taka'),('BDT','en','Bangladeshi Taka'),('BDT','es','taka de Bangladesh'),('BDT','fr','taka bangladeshi'),('BDT','it','Taka Bangladese'),('BEC','de','Belgischer Franc (konvertibel)'),('BEC','en','Belgian Franc (convertible)'),('BEC','es','franco belga (convertible)'),('BEC','fr','franc belge (convertible)'),('BEC','it',''),('BEF','de','Belgischer Franc'),('BEF','en','Belgian Franc'),('BEF','es','franco belga'),('BEF','fr','franc belge'),('BEF','it',''),('BEL','de','Belgischer Finanz-Franc'),('BEL','en','Belgian Franc (financial)'),('BEL','es','franco belga (financiero)'),('BEL','fr','franc belge (financier)'),('BEL','it',''),('BGL','en','Bulgarian Hard Lev'),('BGL','es','lev fuerte búlgaro'),('BGL','it',''),('BGM','en','Bulgarian Socialist Lev'),('BGN','de','Bulgarische Lew'),('BGN','en','Bulgarian Lev'),('BGN','es','leva búlgara'),('BGN','fr','nouveau lev bulgare'),('BGN','it','Nuovo Lev Bulgaro'),('BGO','en','Bulgarian Lev (1879-1952)'),('BHD','de','Bahrain-Dinar'),('BHD','en','Bahraini Dinar'),('BHD','es','dinar bahreiní'),('BHD','fr','dinar bahreïni'),('BHD','it','Dinaro del Bahraini'),('BIF','de','Burundi-Franc'),('BIF','en','Burundian Franc'),('BIF','es','franco de Burundi'),('BIF','fr','franc burundais'),('BIF','it','Franco del Burundi'),('BMD','de','Bermuda-Dollar'),('BMD','en','Bermudan Dollar'),('BMD','es','dólar de Bermudas'),('BMD','fr','dollar bermudien'),('BMD','it','Dollaro delle Bermuda'),('BND','de','Brunei-Dollar'),('BND','en','Brunei Dollar'),('BND','es','dólar de Brunéi'),('BND','fr','dollar brunéien'),('BND','it','Dollaro del Brunei'),('BOB','de','Bolivanischer Boliviano'),('BOB','en','Bolivian Boliviano'),('BOB','es','boliviano'),('BOB','fr','boliviano bolivien'),('BOB','it','Boliviano'),('BOL','en','Bolivian Boliviano (1863-1963)'),('BOP','de','Bolivianischer Peso'),('BOP','en','Bolivian Peso'),('BOP','es','peso boliviano'),('BOP','fr','peso bolivien'),('BOP','it',''),('BOV','de','Boliviansiche Mvdol'),('BOV','en','Bolivian Mvdol'),('BOV','es','MVDOL boliviano'),('BOV','fr','mvdol bolivien'),('BOV','it',''),('BRB','it',''),('BRC','es','cruzado brasileño'),('BRC','it',''),('BRE','it',''),('BRL','de','Brasilianischer Real'),('BRL','en','Brazilian Real'),('BRL','es','real brasileño'),('BRL','fr','réal brésilien'),('BRL','it','Real Brasiliano'),('BRN','es','nuevo cruzado brasileño'),('BRN','fr','nouveau cruzado'),('BRN','it',''),('BRR','es','cruceiro brasileño'),('BRR','fr','cruzeiro'),('BRR','it',''),('BRZ','de',''),('BSD','de','Bahama-Dollar'),('BSD','en','Bahamian Dollar'),('BSD','es','dólar de las Bahamas'),('BSD','fr','dollar bahaméen'),('BSD','it','Dollaro delle Bahamas'),('BTN','de','Bhutan-Ngultrum'),('BTN','en','Bhutanese Ngultrum'),('BTN','es','ngultrum butanés'),('BTN','fr','ngultrum bouthanais'),('BTN','it','Ngultrum Butanese'),('BUK','de','Birmanischer Kyat'),('BUK','en','Burmese Kyat'),('BUK','es','kyat birmano'),('BUK','fr','kyat birman'),('BUK','it',''),('BWP','de','Botswanische Pula'),('BWP','en','Botswanan Pula'),('BWP','es','pula botsuano'),('BWP','fr','pula botswanais'),('BWP','it','Pula del Botswana'),('BYB','it',''),('BYR','de','Belarus-Rubel'),('BYR','en','Belarusian Ruble'),('BYR','es','rublo bielorruso'),('BYR','fr','rouble biélorusse'),('BYR','it','Rublo Bielorussia'),('BZD','de','Belize-Dollar'),('BZD','en','Belize Dollar'),('BZD','es','dólar de Belice'),('BZD','fr','dollar bélizéen'),('BZD','it','Dollaro del Belize'),('CAD','de','Kanadischer Dollar'),('CAD','en','Canadian Dollar'),('CAD','es','dólar canadiense'),('CAD','fr','dollar canadien'),('CAD','it','Dollaro Canadese'),('CDF','de','Kongo-Franc'),('CDF','en','Congolese Franc'),('CDF','es','franco congoleño'),('CDF','fr','franc congolais'),('CDF','it','Franco Congolese'),('CHE','de','WIR-Euro'),('CHE','en','WIR Euro'),('CHE','es','euro WIR'),('CHE','fr','euro WIR'),('CHF','de','Schweizer Franken'),('CHF','en','Swiss Franc'),('CHF','es','franco suizo'),('CHF','fr','franc suisse'),('CHF','it','Franco Svizzero'),('CHW','de','WIR Franken'),('CHW','en','WIR Franc'),('CHW','es','franco WIR'),('CHW','fr','franc WIR'),('CLE','en','Chilean Escudo'),('CLF','de','Chilenische Unidades de Fomento'),('CLF','en','Chilean Unit of Account (UF)'),('CLF','es','unidad de fomento chilena'),('CLF','fr','unité d’investissement chilienne'),('CLF','it',''),('CLP','de','Chilenischer Peso'),('CLP','en','Chilean Peso'),('CLP','es','peso chileno'),('CLP','fr','peso chilien'),('CLP','it','Peso Cileno'),('CNX','en','Chinese People’s Bank Dollar'),('CNY','de','Renminbi Yuan'),('CNY','en','Chinese Yuan'),('CNY','es','yuan chino'),('CNY','fr','yuan renminbi chinois'),('CNY','it','Renmimbi Cinese'),('COP','de','Kolumbianischer Peso'),('COP','en','Colombian Peso'),('COP','es','peso colombiano'),('COP','fr','peso colombien'),('COP','it','Peso Colombiano'),('COU','en','Colombian Real Value Unit'),('COU','es','unidad de valor real colombiana'),('COU','fr','unité de valeur réelle colombienne'),('CRC','de','Costa-Rica-Colón'),('CRC','en','Costa Rican Colón'),('CRC','es','colón costarricense'),('CRC','fr','colón costaricain'),('CRC','it','Colón Costaricano'),('CSD','de','Serbischer Dinar (2002-2006)'),('CSD','en','Serbian Dinar (2002-2006)'),('CSD','es','antiguo dinar serbio'),('CSD','fr','dinar serbo-monténégrin'),('CSD','it',''),('CSK','de','Tschechoslowakische Krone'),('CSK','en','Czechoslovak Hard Koruna'),('CSK','es','corona fuerte checoslovaca'),('CSK','fr','couronne forte tchécoslovaque'),('CSK','it',''),('CUC','de','Kubanischer Peso (konvertibel)'),('CUC','en','Cuban Convertible Peso'),('CUC','es','peso cubano convertible'),('CUC','fr','peso cubain convertible'),('CUC','it','Peso Cubano Convertibile'),('CUP','de','Kubanischer Peso'),('CUP','en','Cuban Peso'),('CUP','es','peso cubano'),('CUP','fr','peso cubain'),('CUP','it','Peso Cubano'),('CVE','de','Kap-Verde-Escudo'),('CVE','en','Cape Verdean Escudo'),('CVE','es','escudo de Cabo Verde'),('CVE','fr','escudo capverdien'),('CVE','it','Escudo del Capo Verde'),('CYP','de','Zypern-Pfund'),('CYP','en','Cypriot Pound'),('CYP','es','libra chipriota'),('CYP','fr','livre chypriote'),('CYP','it',''),('CZK','de','Tschechische Krone'),('CZK','en','Czech Republic Koruna'),('CZK','es','corona checa'),('CZK','fr','couronne tchèque'),('CZK','it','Corona Ceca'),('DDM','de','Mark der DDR'),('DDM','en','East German Mark'),('DDM','es','ostmark de Alemania del Este'),('DDM','fr','mark est-allemand'),('DDM','it',''),('DEM','de','Deutsche Mark'),('DEM','en','German Mark'),('DEM','es','marco alemán'),('DEM','fr','mark allemand'),('DEM','it',''),('DJF','de','Dschibuti-Franc'),('DJF','en','Djiboutian Franc'),('DJF','es','franco de Yibuti'),('DJF','fr','franc djiboutien'),('DJF','it','Franco Gibutiano'),('DKK','de','Dänische Krone'),('DKK','en','Danish Krone'),('DKK','es','corona danesa'),('DKK','fr','couronne danoise'),('DKK','it','Corona Danese'),('DOP','de','Dominikanischer Peso'),('DOP','en','Dominican Peso'),('DOP','es','peso dominicano'),('DOP','fr','peso dominicain'),('DOP','it','Peso Dominicano'),('DZD','de','Algerischer Dinar'),('DZD','en','Algerian Dinar'),('DZD','es','dinar argelino'),('DZD','fr','dinar algérien'),('DZD','it','Dinaro Algerino'),('ECS','de','Ecuadorianischer Sucre'),('ECS','en','Ecuadorian Sucre'),('ECS','es','sucre ecuatoriano'),('ECS','fr','sucre équatorien'),('ECS','it',''),('ECV','de','Verrechnungseinheit für Ecuador'),('ECV','en','Ecuadorian Unit of Constant Value'),('ECV','es','unidad de valor constante (UVC) ecuatoriana'),('ECV','fr','unité de valeur constante équatoriale (UVC)'),('ECV','it',''),('EEK','de','Estnische Krone'),('EEK','en','Estonian Kroon'),('EEK','es','corona estonia'),('EEK','fr','couronne estonienne'),('EEK','it',''),('EGP','de','Ägyptisches Pfund'),('EGP','en','Egyptian Pound'),('EGP','es','libra egipcia'),('EGP','fr','livre égyptienne'),('EGP','it','Sterlina Egiziana'),('ERN','de','Eritreische Nakfa'),('ERN','en','Eritrean Nakfa'),('ERN','es','nakfa eritreo'),('ERN','fr','nafka érythréen'),('ERN','it','Nakfa Eritreo'),('ESA','de','Spanische Peseta (A-Konten)'),('ESA','en','Spanish Peseta (A account)'),('ESA','es','peseta española (cuenta A)'),('ESA','fr','peseta espagnole (compte A)'),('ESA','it',''),('ESB','de','Spanische Peseta (konvertibel)'),('ESB','en','Spanish Peseta (convertible account)'),('ESB','es','peseta española (cuenta convertible)'),('ESB','fr','peseta espagnole (compte convertible)'),('ESB','it',''),('ESP','de','Spanische Peseta'),('ESP','en','Spanish Peseta'),('ESP','es','peseta española'),('ESP','fr','peseta espagnole'),('ESP','it',''),('ETB','de','Äthiopische Birr'),('ETB','en','Ethiopian Birr'),('ETB','es','birr etíope'),('ETB','fr','birr éthiopien'),('ETB','it','Birr Etiopico'),('EUR','de','Euro'),('EUR','en','Euro'),('EUR','es','euro'),('EUR','fr','euro'),('EUR','it','Euro'),('FIM','de','Finnische Mark'),('FIM','en','Finnish Markka'),('FIM','es','marco finlandés'),('FIM','fr','mark finlandais'),('FIM','it',''),('FJD','de','Fidschi-Dollar'),('FJD','en','Fijian Dollar'),('FJD','es','dólar de las Islas Fiyi'),('FJD','fr','dollar fidjien'),('FJD','it','Dollaro delle Figi'),('FKP','de','Falkland-Pfund'),('FKP','en','Falkland Islands Pound'),('FKP','es','libra de las Islas Malvinas'),('FKP','fr','livre des Falkland'),('FKP','it','Sterlina delle Falkland'),('FRF','de','Französischer Franc'),('FRF','en','French Franc'),('FRF','es','franco francés'),('FRF','fr','franc français'),('FRF','it',''),('GBP','de','Britisches Pfund Sterling'),('GBP','en','British Pound Sterling'),('GBP','es','libra esterlina británica'),('GBP','fr','livre sterling'),('GBP','it','Sterlina Inglese'),('GEK','de','Georgischer Kupon Larit'),('GEK','en','Georgian Kupon Larit'),('GEK','es',''),('GEK','fr','coupon de lari géorgien'),('GEK','it',''),('GEL','de','Georgischer Lari'),('GEL','en','Georgian Lari'),('GEL','es','lari georgiano'),('GEL','fr','lari géorgien'),('GEL','it','Lari Georgiano'),('GHC','es',''),('GHC','fr','cédi'),('GHC','it',''),('GHS','de','Ghanaischer Cedi'),('GHS','en','Ghanaian Cedi'),('GHS','es','cedi ghanés'),('GHS','fr','cédi ghanéen'),('GHS','it','Cedi ghanese'),('GIP','de','Gibraltar-Pfund'),('GIP','en','Gibraltar Pound'),('GIP','es','libra de Gibraltar'),('GIP','fr','livre de Gibraltar'),('GIP','it','Sterlina di Gibilterra'),('GMD','de','Gambia-Dalasi'),('GMD','en','Gambian Dalasi'),('GMD','es','dalasi gambiano'),('GMD','fr','dalasi gambien'),('GMD','it','Dalasi del Gambia'),('GNF','de','Guinea-Franc'),('GNF','en','Guinean Franc'),('GNF','es','franco guineano'),('GNF','fr','franc guinéen'),('GNF','it','Franco della Guinea'),('GNS','de','Guineischer Syli'),('GNS','en','Guinean Syli'),('GNS','es',''),('GNS','fr','syli guinéen'),('GNS','it',''),('GQE','de','Äquatorialguinea-Ekwele'),('GQE','en','Equatorial Guinean Ekwele'),('GQE','es','ekuele de Guinea Ecuatorial'),('GQE','fr','ekwélé équatoguinéen'),('GQE','it',''),('GRD','de','Griechische Drachme'),('GRD','en','Greek Drachma'),('GRD','es','dracma griego'),('GRD','fr','drachme grecque'),('GRD','it',''),('GTQ','de','Guatemaltekischer Quetzal'),('GTQ','en','Guatemalan Quetzal'),('GTQ','es','quetzal guatemalteco'),('GTQ','fr','quetzal guatémaltèque'),('GTQ','it','Quetzal Guatemalteco'),('GWE','de','Portugiesisch Guinea Escudo'),('GWE','en','Portuguese Guinea Escudo'),('GWE','es',''),('GWE','fr','escudo de Guinée portugaise'),('GWE','it',''),('GWP','de','Guinea-Bissau Peso'),('GWP','en','Guinea-Bissau Peso'),('GWP','es',''),('GWP','fr','peso bissau-guinéen'),('GWP','it',''),('GYD','de','Guyana-Dollar'),('GYD','en','Guyanaese Dollar'),('GYD','es','dólar guyanés'),('GYD','fr','dollar du Guyana'),('GYD','it','Dollaro della Guyana'),('HKD','de','Hongkong-Dollar'),('HKD','en','Hong Kong Dollar'),('HKD','es','dólar de Hong Kong'),('HKD','fr','dollar de Hong Kong'),('HKD','it','Dollaro di Hong Kong'),('HNL','de','Honduras-Lempira'),('HNL','en','Honduran Lempira'),('HNL','es','lempira hondureño'),('HNL','fr','lempira hondurien'),('HNL','it','Lempira Hoduregno'),('HRD','de','Kroatischer Dinar'),('HRD','en','Croatian Dinar'),('HRD','es','dinar croata'),('HRD','fr','dinar croate'),('HRD','it',''),('HRK','de','Kroatischer Kuna'),('HRK','en','Croatian Kuna'),('HRK','es','kuna croata'),('HRK','fr','kuna croate'),('HRK','it','Kuna Croata'),('HTG','de','Haitianische Gourde'),('HTG','en','Haitian Gourde'),('HTG','es','gourde haitiano'),('HTG','fr','gourde haïtienne'),('HTG','it','Gourde Haitiano'),('HUF','de','Ungarische Forint'),('HUF','en','Hungarian Forint'),('HUF','es','florín húngaro'),('HUF','fr','forint hongrois'),('HUF','it','Fiorino Ungherese'),('IDR','de','Indonesische Rupiah'),('IDR','en','Indonesian Rupiah'),('IDR','es','rupia indonesia'),('IDR','fr','roupie indonésienne'),('IDR','it','Rupia Indonesiana'),('IEP','de','Irisches Pfund'),('IEP','en','Irish Pound'),('IEP','es','libra irlandesa'),('IEP','fr','livre irlandaise'),('IEP','it',''),('ILP','de','Israelisches Pfund'),('ILP','en','Israeli Pound'),('ILP','es','libra israelí'),('ILP','fr','livre israélienne'),('ILP','it',''),('ILS','de','Israelische Neuer Schekel'),('ILS','en','Israeli New Sheqel'),('ILS','es','nuevo sheqel israelí'),('ILS','fr','nouveau shekel israélien'),('ILS','it','Nuovo Shequel Israeliano'),('INR','de','Indische Rupie'),('INR','en','Indian Rupee'),('INR','es','rupia india'),('INR','fr','roupie indienne'),('INR','it','Rupia Indiana'),('IQD','de','Irakischer Dinar'),('IQD','en','Iraqi Dinar'),('IQD','es','dinar iraquí'),('IQD','fr','dinar irakien'),('IQD','it','Dinaro Iracheno'),('IRR','de','Iranische Rial'),('IRR','en','Iranian Rial'),('IRR','es','rial iraní'),('IRR','fr','rial iranien'),('IRR','it','Rial Iraniano'),('ISK','de','Isländische Krone'),('ISK','en','Icelandic Króna'),('ISK','es','corona islandesa'),('ISK','fr','couronne islandaise'),('ISK','it','Corona Islandese'),('ITL','de','Italienische Lira'),('ITL','en','Italian Lira'),('ITL','es','lira italiana'),('ITL','fr','lire italienne'),('ITL','it',''),('JMD','de','Jamaika-Dollar'),('JMD','en','Jamaican Dollar'),('JMD','es','dólar de Jamaica'),('JMD','fr','dollar jamaïcain'),('JMD','it','Dollaro Giamaicano'),('JOD','de','Jordanischer Dinar'),('JOD','en','Jordanian Dinar'),('JOD','es','dinar jordano'),('JOD','fr','dinar jordanien'),('JOD','it','Dinaro Giordano'),('JPY','de','Japanische Yen'),('JPY','en','Japanese Yen'),('JPY','es','yen japonés'),('JPY','fr','yen japonais'),('JPY','it','Yen Giapponese'),('KES','de','Kenia-Schilling'),('KES','en','Kenyan Shilling'),('KES','es','chelín keniata'),('KES','fr','shilling kényan'),('KES','it','Scellino Keniota'),('KGS','de','Kirgisischer Som'),('KGS','en','Kyrgystani Som'),('KGS','es','som kirguís'),('KGS','fr','som kirghize'),('KGS','it','Som Kirghiso'),('KHR','de','Kambodschanischer Riel'),('KHR','en','Cambodian Riel'),('KHR','es','riel camboyano'),('KHR','fr','riel cambodgien'),('KHR','it','Riel Cambogiano'),('KMF','de','Komoren-Franc'),('KMF','en','Comorian Franc'),('KMF','es','franco comorense'),('KMF','fr','franc comorien'),('KMF','it','Franco Comoriano'),('KPW','de','Nordkoreanischer Won'),('KPW','en','North Korean Won'),('KPW','es','won norcoreano'),('KPW','fr','won nord-coréen'),('KPW','it','Won Nordcoreano'),('KRW','de','Südkoreanischer Won'),('KRW','en','South Korean Won'),('KRW','es','won surcoreano'),('KRW','fr','won sud-coréen'),('KRW','it','Won Sudcoreano'),('KWD','de','Kuwait-Dinar'),('KWD','en','Kuwaiti Dinar'),('KWD','es','dinar kuwaití'),('KWD','fr','dinar koweïtien'),('KWD','it','Dinaro Kuwaitiano'),('KYD','de','Kaiman-Dollar'),('KYD','en','Cayman Islands Dollar'),('KYD','es','dólar de las Islas Caimán'),('KYD','fr','dollar des îles Caïmanes'),('KYD','it','Dollaro delle Isole Cayman'),('KZT','de','Kasachischer Tenge'),('KZT','en','Kazakhstani Tenge'),('KZT','es','tenge kazako'),('KZT','fr','tenge kazakh'),('KZT','it','Tenge Kazaco'),('LAK','de','Laotischer Kip'),('LAK','en','Laotian Kip'),('LAK','es','kip laosiano'),('LAK','fr','kip loatien'),('LAK','it','Kip Laotiano'),('LBP','de','Libanesisches Pfund'),('LBP','en','Lebanese Pound'),('LBP','es','libra libanesa'),('LBP','fr','livre libanaise'),('LBP','it','Sterlina Libanese'),('LKR','de','Sri-Lanka-Rupie'),('LKR','en','Sri Lankan Rupee'),('LKR','es','rupia de Sri Lanka'),('LKR','fr','roupie srilankaise'),('LKR','it','Rupia di Sri Lanka'),('LRD','de','Liberianischer Dollar'),('LRD','en','Liberian Dollar'),('LRD','es','dólar liberiano'),('LRD','fr','dollar libérien'),('LRD','it','Dollaro Liberiano'),('LSL','de','Loti'),('LSL','en','Lesotho Loti'),('LSL','es',''),('LSL','fr','loti lesothan'),('LSL','it',''),('LTL','de','Litauischer Litas'),('LTL','en','Lithuanian Litas'),('LTL','es','litas lituano'),('LTL','fr','litas lituanien'),('LTL','it','Lita Lituana'),('LTT','de','Litauischer Talonas'),('LTT','en','Lithuanian Talonas'),('LTT','es','talonas lituano'),('LTT','fr','talonas lituanien'),('LTT','it',''),('LUC','de','Luxemburgischer Franc (konvertibel)'),('LUC','en','Luxembourgian Convertible Franc'),('LUC','es','franco convertible luxemburgués'),('LUC','fr','franc convertible luxembourgeois'),('LUC','it',''),('LUF','de','Luxemburgischer Franc'),('LUF','en','Luxembourgian Franc'),('LUF','es','franco luxemburgués'),('LUF','fr','franc luxembourgeois'),('LUF','it',''),('LUL','de','Luxemburgischer Finanz-Franc'),('LUL','en','Luxembourg Financial Franc'),('LUL','es','franco financiero luxemburgués'),('LUL','fr','franc financier luxembourgeois'),('LUL','it',''),('LVL','de','Lettischer Lats'),('LVL','en','Latvian Lats'),('LVL','es','lats letón'),('LVL','fr','lats letton'),('LVL','it','Lat Lettone'),('LVR','de','Lettischer Rubel'),('LVR','en','Latvian Ruble'),('LVR','es','rublo letón'),('LVR','fr','rouble letton'),('LVR','it',''),('LYD','de','Libyscher Dinar'),('LYD','en','Libyan Dinar'),('LYD','es','dinar libio'),('LYD','fr','dinar lybien'),('LYD','it','Dinaro Libico'),('MAD','de','Marokkanischer Dirham'),('MAD','en','Moroccan Dirham'),('MAD','es','dirham marroquí'),('MAD','fr','dirham marocain'),('MAD','it','Dirham Marocchino'),('MAF','de','Marokkanischer Franc'),('MAF','en','Moroccan Franc'),('MAF','es','franco marroquí'),('MAF','fr','franc marocain'),('MAF','it',''),('MCF','en','Monegasque Franc'),('MDC','en','Moldovan Cupon'),('MDL','de','Moldau-Leu'),('MDL','en','Moldovan Leu'),('MDL','es','leu moldavo'),('MDL','fr','leu moldave'),('MDL','it','Leu Moldavo'),('MGA','de','Madagaskar-Ariary'),('MGA','en','Malagasy Ariary'),('MGA','es','ariary malgache'),('MGA','fr','ariary malgache'),('MGA','it','Ariary Malgascio'),('MGF','de','Madagaskar-Franc'),('MGF','en','Malagasy Franc'),('MGF','es',''),('MGF','fr','franc malgache'),('MGF','it',''),('MKD','de','Mazedonischer Denar'),('MKD','en','Macedonian Denar'),('MKD','es','dinar macedonio'),('MKD','fr','denar macédonien'),('MKD','it','Dinaro Macedone'),('MLF','de','Malischer Franc'),('MLF','en','Malian Franc'),('MLF','es',''),('MLF','fr','franc malien'),('MLF','it',''),('MMK','de','Myanmarischer Kyat'),('MMK','en','Myanma Kyat'),('MMK','es','kyat de Myanmar'),('MMK','fr','kyat myanmarais'),('MMK','it','Kyat di Myanmar'),('MNT','de','Mongolischer Tögrög'),('MNT','en','Mongolian Tugrik'),('MNT','es','tugrik mongol'),('MNT','fr','tugrik mongol'),('MNT','it','Tugrik Mongolo'),('MOP','de','Macao-Pataca'),('MOP','en','Macanese Pataca'),('MOP','es','pataca de Macao'),('MOP','fr','pataca macanaise'),('MOP','it','Pataca di Macao'),('MRO','de','Mauretanischer Ouguiya'),('MRO','en','Mauritanian Ouguiya'),('MRO','es','ouguiya mauritano'),('MRO','fr','ouguiya mauritanien'),('MRO','it','Ouguiya della Mauritania'),('MTL','de','Maltesische Lira'),('MTL','en','Maltese Lira'),('MTL','es','lira maltesa'),('MTL','fr','lire maltaise'),('MTL','it',''),('MTP','de','Maltesisches Pfund'),('MTP','en','Maltese Pound'),('MTP','es','libra maltesa'),('MTP','fr','livre maltaise'),('MTP','it',''),('MUR','de','Mauritius-Rupie'),('MUR','en','Mauritian Rupee'),('MUR','es','rupia mauriciana'),('MUR','fr','roupie mauricienne'),('MUR','it','Rupia Mauriziana'),('MVP','en','Maldivian Rupee'),('MVR','de','Malediven-Rupie'),('MVR','en','Maldivian Rufiyaa'),('MVR','es','rufiyaa de Maldivas'),('MVR','fr','rufiyaa maldivien'),('MVR','it',''),('MWK','de','Malawi-Kwacha'),('MWK','en','Malawian Kwacha'),('MWK','es','kwacha de Malawi'),('MWK','fr','kwacha malawite'),('MWK','it','Kwacha Malawiano'),('MXN','de','Mexikanischer Peso'),('MXN','en','Mexican Peso'),('MXN','es','peso mexicano'),('MXN','fr','peso mexicain'),('MXN','it','Peso Messicano'),('MXP','de','Mexikanischer Silber-Peso (1861-1992)'),('MXP','en','Mexican Silver Peso (1861-1992)'),('MXP','es','peso de plata mexicano (1861-1992)'),('MXP','fr','peso d’argent mexicain (1861–1992)'),('MXP','it',''),('MXV','de','Mexicanischer Unidad de Inversion (UDI)'),('MXV','en','Mexican Investment Unit'),('MXV','es','unidad de inversión (UDI) mexicana'),('MXV','fr','unité de conversion mexicaine (UDI)'),('MXV','it',''),('MYR','de','Malaysischer Ringgit'),('MYR','en','Malaysian Ringgit'),('MYR','es','ringgit malasio'),('MYR','fr','ringgit malais'),('MYR','it','Ringgit della Malesia'),('MZE','de','Mosambikanischer Escudo'),('MZE','en','Mozambican Escudo'),('MZE','es','escudo mozambiqueño'),('MZE','fr','escudo mozambicain'),('MZE','it',''),('MZM','es',''),('MZM','fr','métical'),('MZN','de','Mosambikanischer Metical'),('MZN','en','Mozambican Metical'),('MZN','es','metical mozambiqueño'),('MZN','fr','metical mozambicain'),('MZN','it','Metical mozambicano'),('NAD','de','Namibia-Dollar'),('NAD','en','Namibian Dollar'),('NAD','es','dólar de Namibia'),('NAD','fr','dollar namibien'),('NAD','it','Dollaro Namibiano'),('NGN','de','Nigerianischer Naira'),('NGN','en','Nigerian Naira'),('NGN','es','naira nigeriano'),('NGN','fr','naira nigérian'),('NGN','it','Naira Nigeriana'),('NIC','fr','cordoba'),('NIC','it','Cordoba Nicaraguense'),('NIO','de','Nicaragua-Cordoba'),('NIO','en','Nicaraguan Córdoba'),('NIO','es','córdoba nicaragüense'),('NIO','fr','córdoba oro nicaraguayen'),('NIO','it','Córdoba oro nicaraguense'),('NLG','de','Niederländischer Gulden'),('NLG','en','Dutch Guilder'),('NLG','es','florín neerlandés'),('NLG','fr','florin néerlandais'),('NLG','it',''),('NOK','de','Norwegische Krone'),('NOK','en','Norwegian Krone'),('NOK','es','corona noruega'),('NOK','fr','couronne norvégienne'),('NOK','it','Corona Norvegese'),('NPR','de','Nepalesische Rupie'),('NPR','en','Nepalese Rupee'),('NPR','es','rupia nepalesa'),('NPR','fr','roupie népalaise'),('NPR','it','Rupia Nepalese'),('NZD','de','Neuseeland-Dollar'),('NZD','en','New Zealand Dollar'),('NZD','es','dólar neozelandés'),('NZD','fr','dollar néo-zélandais'),('NZD','it','Dollaro Neozelandese'),('OMR','de','Omanischer Rial'),('OMR','en','Omani Rial'),('OMR','es','rial omaní'),('OMR','fr','rial omanais'),('OMR','it','Rial Omanita'),('PAB','de','Panamaischer Balboa'),('PAB','en','Panamanian Balboa'),('PAB','es','balboa panameño'),('PAB','fr','balboa panaméen'),('PAB','it','Balboa di Panama'),('PEI','de','Peruanischer Inti'),('PEI','en','Peruvian Inti'),('PEI','es','inti peruano'),('PEI','fr','inti péruvien'),('PEI','it',''),('PEN','de','Peruanischer Neuer Sol'),('PEN','en','Peruvian Nuevo Sol'),('PEN','es','nuevo sol peruano'),('PEN','fr','nouveau sol péruvien'),('PEN','it','Sol Nuevo Peruviano'),('PES','de','Peruanischer Sol (1863-1965)'),('PES','en','Peruvian Sol (1863-1965)'),('PES','es','sol peruano'),('PES','fr','sol péruvien'),('PES','it',''),('PGK','de','Papua-Neuguineischer Kina'),('PGK','en','Papua New Guinean Kina'),('PGK','es','kina de Papúa Nueva Guinea'),('PGK','fr','kina papouan-néo-guinéen'),('PGK','it','Kina della Papua Nuova Guinea'),('PHP','de','Philippinischer Peso'),('PHP','en','Philippine Peso'),('PHP','es','peso filipino'),('PHP','fr','peso philippin'),('PHP','it','Peso delle Filippine'),('PKR','de','Pakistanische Rupie'),('PKR','en','Pakistani Rupee'),('PKR','es','rupia pakistaní'),('PKR','fr','roupie pakistanaise'),('PKR','it','Rupia del Pakistan'),('PLN','de','Polnischer Złoty'),('PLN','en','Polish Zloty'),('PLN','es','zloty polaco'),('PLN','fr','zloty polonais'),('PLN','it','Zloty Polacco'),('PLZ','it',''),('PTE','de','Portugiesischer Escudo'),('PTE','en','Portuguese Escudo'),('PTE','es','escudo portugués'),('PTE','fr','escudo portugais'),('PTE','it',''),('PYG','de','Paraguayischer Guaraní'),('PYG','en','Paraguayan Guarani'),('PYG','es','guaraní paraguayo'),('PYG','fr','guaraní paraguayen'),('PYG','it','Guarani del Paraguay'),('QAR','de','Katar-Riyal'),('QAR','en','Qatari Rial'),('QAR','es','riyal de Qatar'),('QAR','fr','rial qatari'),('QAR','it','Rial del Qatar'),('RHD','de','Rhodesischer Dollar'),('RHD','en','Rhodesian Dollar'),('RHD','es',''),('RHD','fr','dollar rhodésien'),('RHD','it',''),('ROL','es','antiguo leu rumano'),('ROL','fr','ancien leu roumain'),('ROL','it',''),('RON','de','Rumänischer Leu'),('RON','en','Romanian Leu'),('RON','es','leu rumano'),('RON','fr','leu roumain'),('RON','it','Leu Rumeno'),('RSD','de','Serbischer Dinar'),('RSD','en','Serbian Dinar'),('RSD','es','dinar serbio'),('RSD','fr','dinar serbe'),('RSD','it','Dinaro Serbo'),('RUB','de','Russischer Rubel'),('RUB','en','Russian Ruble'),('RUB','es','rublo ruso'),('RUB','fr','rouble russe'),('RUB','it','Rublo Russo'),('RUR','it',''),('RWF','de','Ruanda-Franc'),('RWF','en','Rwandan Franc'),('RWF','es','franco ruandés'),('RWF','fr','franc rwandais'),('RWF','it','Franco Ruandese'),('SAR','de','Saudi-Rial'),('SAR','en','Saudi Riyal'),('SAR','es','riyal saudí'),('SAR','fr','rial saoudien'),('SAR','it','Ryal Saudita'),('SBD','de','Salomonen-Dollar'),('SBD','en','Solomon Islands Dollar'),('SBD','es','dólar de las Islas Salomón'),('SBD','fr','dollar des îles Salomon'),('SBD','it','Dollaro delle Isole Solomon'),('SCR','de','Seychellen-Rupie'),('SCR','en','Seychellois Rupee'),('SCR','es','rupia de Seychelles'),('SCR','fr','roupie des Seychelles'),('SCR','it','Rupia delle Seychelles'),('SDD','es','dinar sudanés'),('SDD','fr','dinar soudanais'),('SDD','it',''),('SDG','de','Sudanesisches Pfund'),('SDG','en','Sudanese Pound'),('SDG','es','libra sudanesa'),('SDG','fr','livre soudanaise'),('SDG','it','Sterlina Sudanese'),('SDP','es','libra sudanesa antigua'),('SEK','de','Schwedische Krone'),('SEK','en','Swedish Krona'),('SEK','es','corona sueca'),('SEK','fr','couronne suédoise'),('SEK','it','Corona Svedese'),('SGD','de','Singapur-Dollar'),('SGD','en','Singapore Dollar'),('SGD','es','dólar singapurense'),('SGD','fr','dollar de Singapour'),('SGD','it','Dollaro di Singapore'),('SHP','de','St. Helena-Pfund'),('SHP','en','Saint Helena Pound'),('SHP','es','libra de Santa Elena'),('SHP','fr','livre de Sainte-Hélène'),('SHP','it','Sterlina di Sant’Elena'),('SIT','de','Slowenischer Tolar'),('SIT','en','Slovenian Tolar'),('SIT','es','tólar esloveno'),('SIT','fr','tolar slovène'),('SIT','it',''),('SKK','de','Slowakische Krone'),('SKK','en','Slovak Koruna'),('SKK','es','corona eslovaca'),('SKK','fr','couronne slovaque'),('SKK','it',''),('SLL','de','Sierra-leonischer Leone'),('SLL','en','Sierra Leonean Leone'),('SLL','es','leone de Sierra Leona'),('SLL','fr','leone sierra-léonais'),('SLL','it','Leone della Sierra Leone'),('SOS','de','Somalia-Schilling'),('SOS','en','Somali Shilling'),('SOS','es','chelín somalí'),('SOS','fr','shilling somalien'),('SOS','it','Scellino Somalo'),('SRD','de','Surinamischer Dollar'),('SRD','en','Surinamese Dollar'),('SRD','es','dólar surinamés'),('SRD','fr','dollar surinamais'),('SRD','it','Dollaro Surinamese'),('SRG','de','Suriname Gulden'),('SRG','en','Surinamese Guilder'),('SRG','es',''),('SRG','fr','florin surinamais'),('SRG','it',''),('SSP','de','Südsudanesisches Pfund'),('SSP','en','South Sudanese Pound'),('SSP','es','libra sursudanesa'),('SSP','fr','livre sud-soudanaise'),('SSP','it','Sterlina sudsudanese'),('STD','de','São-toméischer Dobra'),('STD','en','São Tomé and Príncipe Dobra'),('STD','es','dobra de Santo Tomé y Príncipe'),('STD','fr','dobra santoméen'),('STD','it','Dobra di Sao Tomé e Principe'),('SUR','de','Sowjetischer Rubel'),('SUR','en','Soviet Rouble'),('SUR','es','rublo soviético'),('SUR','fr','rouble soviétique'),('SUR','it',''),('SVC','de','El Salvador Colon'),('SVC','en','Salvadoran Colón'),('SVC','es','colón salvadoreño'),('SVC','fr','colón salvadorien'),('SVC','it',''),('SYP','de','Syrisches Pfund'),('SYP','en','Syrian Pound'),('SYP','es','libra siria'),('SYP','fr','livre syrienne'),('SYP','it','Sterlina Siriana'),('SZL','de','Swasiländischer Lilangeni'),('SZL','en','Swazi Lilangeni'),('SZL','es','lilangeni suazi'),('SZL','fr','lilangeni swazi'),('SZL','it','Lilangeni dello Swaziland'),('THB','de','Thailändischer Baht'),('THB','en','Thai Baht'),('THB','es','baht tailandés'),('THB','fr','baht thaïlandais'),('THB','it','Baht Tailandese'),('TJR','de','Tadschikistan Rubel'),('TJR','en','Tajikistani Ruble'),('TJR','es',''),('TJR','fr','rouble tadjik'),('TJR','it',''),('TJS','de','Tadschikistan-Somoni'),('TJS','en','Tajikistani Somoni'),('TJS','es','somoni tayiko'),('TJS','fr','somoni tadjik'),('TJS','it','Somoni del Tajikistan'),('TMM','fr','manat turkmène'),('TMM','it',''),('TMT','de','Turkmenistan-Manat'),('TMT','en','Turkmenistani Manat'),('TMT','es','manat turcomano'),('TMT','fr','nouveau manat turkmène'),('TMT','it','Manat Turkmeno'),('TND','de','Tunesischer Dinar'),('TND','en','Tunisian Dinar'),('TND','es','dinar tunecino'),('TND','fr','dinar tunisien'),('TND','it','Dinaro Tunisino'),('TOP','de','Tongaischer Paʻanga'),('TOP','en','Tongan Paʻanga'),('TOP','es','paʻanga tongano'),('TOP','fr','pa’anga tongan'),('TOP','it','Paʻanga di Tonga'),('TPE','de','Timor-Escudo'),('TPE','en','Timorese Escudo'),('TPE','es',''),('TPE','fr','escudo timorais'),('TPE','it',''),('TRL','fr','livre turque (1844-2005)'),('TRL','it','Lira Turca'),('TRY','de','Türkische Lira'),('TRY','en','Turkish Lira'),('TRY','es','lira turca'),('TRY','fr','livre turque'),('TRY','it','Nuova Lira Turca'),('TTD','de','Trinidad und Tobago-Dollar'),('TTD','en','Trinidad and Tobago Dollar'),('TTD','es','dólar de Trinidad y Tobago'),('TTD','fr','dollar de Trinité-et-Tobago'),('TTD','it','Dollaro di Trinidad e Tobago'),('TWD','de','Neuer Taiwan-Dollar'),('TWD','en','New Taiwan Dollar'),('TWD','es','nuevo dólar taiwanés'),('TWD','fr','nouveau dollar taïwanais'),('TWD','it','Nuovo dollaro taiwanese'),('TZS','de','Tansania-Schilling'),('TZS','en','Tanzanian Shilling'),('TZS','es','chelín tanzano'),('TZS','fr','shilling tanzanien'),('TZS','it','Scellino della Tanzania'),('UAH','de','Ukrainische Hrywnja'),('UAH','en','Ukrainian Hryvnia'),('UAH','es','grivna ucraniana'),('UAH','fr','hryvnia ukrainienne'),('UAH','it','Grivnia Ucraina'),('UAK','de','Ukrainischer Karbovanetz'),('UAK','en','Ukrainian Karbovanets'),('UAK','es','karbovanet ucraniano'),('UAK','fr','karbovanetz'),('UAK','it',''),('UGS','es',''),('UGS','it',''),('UGX','de','Uganda-Schilling'),('UGX','en','Ugandan Shilling'),('UGX','es','chelín ugandés'),('UGX','fr','shilling ougandais'),('UGX','it','Scellino Ugandese'),('USD','de','US-Dollar'),('USD','en','US Dollar'),('USD','es','dólar estadounidense'),('USD','fr','dollar des États-Unis'),('USD','it','Dollaro Statunitense'),('USN','de','US Dollar (Nächster Tag)'),('USN','en','US Dollar (Next day)'),('USN','es','dólar estadounidense (día siguiente)'),('USN','fr','dollar des Etats-Unis (jour suivant)'),('USN','it',''),('USS','de','US Dollar (Gleicher Tag)'),('USS','en','US Dollar (Same day)'),('USS','es','dólar estadounidense (mismo día)'),('USS','fr','dollar des Etats-Unis (jour même)'),('USS','it',''),('UYI','en','Uruguayan Peso (Indexed Units)'),('UYI','es','peso uruguayo en unidades indexadas'),('UYI','fr','peso uruguayen (unités indexées)'),('UYI','it',''),('UYP','it',''),('UYU','de','Uruguayischer Peso'),('UYU','en','Uruguayan Peso'),('UYU','es','peso uruguayo'),('UYU','fr','peso uruguayen'),('UYU','it','Peso Uruguaiano'),('UZS','de','Usbekistan-Sum'),('UZS','en','Uzbekistan Som'),('UZS','es','sum uzbeko'),('UZS','fr','sum ouzbek'),('UZS','it','Sum dell’Uzbekistan'),('VEB','de','Venezolanischer Bolívar (1871-2008)'),('VEB','en','Venezuelan Bolívar (1871-2008)'),('VEB','es','bolívar venezolano (1871-2008)'),('VEB','fr','bolivar'),('VEB','it','Bolivar Venezuelano'),('VEF','de','Venezolanischer Bolívar'),('VEF','en','Venezuelan Bolívar'),('VEF','es','bolívar venezolano'),('VEF','fr','bolivar fuerte vénézuélien'),('VEF','it','Bolívar venezuelano forte'),('VND','de','Vietnamesischer Dong'),('VND','en','Vietnamese Dong'),('VND','es','dong vietnamita'),('VND','fr','dông vietnamien'),('VND','it','Dong Vietnamita'),('VUV','de','Vanuatu-Vatu'),('VUV','en','Vanuatu Vatu'),('VUV','es','vatu vanuatuense'),('VUV','fr','vatu vanuatuan'),('VUV','it','Vatu di Vanuatu'),('WST','de','Samoanischer Tala'),('WST','en','Samoan Tala'),('WST','es','tala samoano'),('WST','fr','tala samoan'),('WST','it','Tala della Samoa Occidentale'),('XAF','de','CFA-Franc (BEAC)'),('XAF','en','CFA Franc BEAC'),('XAF','es','franco CFA BEAC'),('XAF','fr','franc CFA (BEAC)'),('XAF','it','Franco CFA BEAC'),('XAG','de','Unze Silber'),('XAG','en','Silver'),('XAG','es','plata'),('XAG','fr','argent'),('XAG','it',''),('XAU','de','Unze Gold'),('XAU','en','Gold'),('XAU','es','oro'),('XAU','fr','or'),('XAU','it',''),('XBA','de','Europäische Rechnungseinheit'),('XBA','en','European Composite Unit'),('XBA','es','unidad compuesta europea'),('XBA','fr','unité européenne composée'),('XBA','it',''),('XBB','de','Europäische Währungseinheit (XBB)'),('XBB','en','European Monetary Unit'),('XBB','es','unidad monetaria europea'),('XBB','fr','unité monétaire européenne'),('XBB','it',''),('XBC','de','Europäische Rechnungseinheit (XBC)'),('XBC','en','European Unit of Account (XBC)'),('XBC','es','unidad de cuenta europea (XBC)'),('XBC','fr','unité de compte européenne (XBC)'),('XBC','it',''),('XBD','de','Europäische Rechnungseinheit (XBD)'),('XBD','en','European Unit of Account (XBD)'),('XBD','es','unidad de cuenta europea (XBD)'),('XBD','fr','unité de compte européenne (XBD)'),('XBD','it',''),('XCD','de','Ostkaribischer Dollar'),('XCD','en','East Caribbean Dollar'),('XCD','es','dólar del Caribe Oriental'),('XCD','fr','dollar des Caraïbes orientales'),('XCD','it','Dollaro dei Caraibi Orientali'),('XDR','de','Sonderziehungsrechte'),('XDR','en','Special Drawing Rights'),('XDR','es',''),('XDR','fr','droit de tirage spécial'),('XDR','it',''),('XEU','de','Europäische Währungseinheit (XEU)'),('XEU','en','European Currency Unit'),('XEU','es','unidad de moneda europea'),('XEU','fr','unité de compte européenne (ECU)'),('XFO','de','Französischer Gold-Franc'),('XFO','en','French Gold Franc'),('XFO','es','franco oro francés'),('XFO','fr','franc or'),('XFO','it',''),('XFU','de','Französischer UIC-Franc'),('XFU','en','French UIC-Franc'),('XFU','es','franco UIC francés'),('XFU','fr','franc UIC'),('XFU','it',''),('XOF','de','CFA-Franc (BCEAO)'),('XOF','en','CFA Franc BCEAO'),('XOF','es','franco CFA BCEAO'),('XOF','fr','franc CFA (BCEAO)'),('XOF','it','Franco CFA BCEAO'),('XPD','de','Unze Palladium'),('XPD','en','Palladium'),('XPD','es','paladio'),('XPD','fr','palladium'),('XPD','it',''),('XPF','de','CFP-Franc'),('XPF','en','CFP Franc'),('XPF','es','franco CFP'),('XPF','fr','franc CFP'),('XPF','it','Franco CFP'),('XPT','de','Unze Platin'),('XPT','en','Platinum'),('XPT','es','platino'),('XPT','fr','platine'),('XPT','it',''),('XRE','de','RINET Funds'),('XRE','en','RINET Funds'),('XRE','es',''),('XRE','fr','type de fonds RINET'),('XRE','it',''),('XSU','en','Sucre'),('XTS','de','Testwährung'),('XTS','en','Testing Currency Code'),('XTS','es',''),('XTS','fr','(devise de test)'),('XTS','it',''),('XUA','en','ADB Unit of Account'),('XXX','de','Unbekannte Währung'),('XXX','en','Unknown Currency'),('XXX','es','divisa desconocida'),('XXX','fr','devise inconnue ou non valide'),('XXX','it','Nessuna valuta'),('YDD','de','Jemen-Dinar'),('YDD','en','Yemeni Dinar'),('YDD','es',''),('YDD','fr','dinar du Yémen'),('YDD','it',''),('YER','de','Jemen-Rial'),('YER','en','Yemeni Rial'),('YER','es','rial yemení'),('YER','fr','rial yéménite'),('YER','it','Rial dello Yemen'),('YUD','es',''),('YUD','fr','nouveau dinar yougoslave'),('YUD','it',''),('YUM','es',''),('YUM','fr','dinar yougoslave Noviy'),('YUM','it',''),('YUN','de','Jugoslawischer Dinar (konvertibel)'),('YUN','es','dinar convertible yugoslavo'),('YUN','fr','dinar yougoslave convertible'),('YUN','it',''),('ZAL','de','Südafrikanischer Rand (Finanz)'),('ZAL','en','South African Rand (financial)'),('ZAL','es',''),('ZAL','fr','rand sud-africain (financier)'),('ZAL','it',''),('ZAR','de','Südafrikanischer Rand'),('ZAR','en','South African Rand'),('ZAR','es','rand sudafricano'),('ZAR','fr','rand sud-africain'),('ZAR','it','Rand Sudafricano'),('ZMK','de','Kwacha'),('ZMK','en','Zambian Kwacha'),('ZMK','es','kwacha zambiano'),('ZMK','fr','kwacha zambien'),('ZMK','it','Kwacha dello Zambia'),('ZRN','es',''),('ZRN','fr','nouveau zaïre zaïrien'),('ZRN','it',''),('ZRZ','es',''),('ZRZ','fr','zaïre zaïrois'),('ZRZ','it',''),('ZWD','es',''),('ZWD','fr','dollar zimbabwéen'),('ZWD','it',''),('ZWL','de','Simbabwe-Dollar (2009)'),('ZWL','en','Zimbabwean Dollar (2009)'),('ZWL','es',''),('ZWL','fr','dollar zimbabwéen (2009)'),('ZWL','it',''),('ZWR','de','Simbabwe-Dollar (2008)'),('ZWR','en','Zimbabwean Dollar (2008)'),('ZWR','fr','dollar zimbabwéen (2008)');
/*!40000 ALTER TABLE `core_intl_currency_names` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_intl_language`
--

DROP TABLE IF EXISTS `core_intl_language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_intl_language` (
  `id` varchar(2) NOT NULL,
  `core_intl_country_ID` varchar(2) NOT NULL,
  PRIMARY KEY (`id`,`core_intl_country_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_intl_language`
--

LOCK TABLES `core_intl_language` WRITE;
/*!40000 ALTER TABLE `core_intl_language` DISABLE KEYS */;
INSERT INTO `core_intl_language` VALUES ('aa',''),('aa','DJ'),('aa','ER'),('aa','ET'),('af',''),('af','NA'),('af','ZA'),('ak',''),('ak','GH'),('am',''),('am','ET'),('ar',''),('ar','AE'),('ar','BH'),('ar','DJ'),('ar','DZ'),('ar','EG'),('ar','EH'),('ar','ER'),('ar','IL'),('ar','IQ'),('ar','JO'),('ar','KM'),('ar','KW'),('ar','LB'),('ar','LY'),('ar','MA'),('ar','MR'),('ar','OM'),('ar','PS'),('ar','QA'),('ar','SA'),('ar','SD'),('ar','SO'),('ar','SY'),('ar','TD'),('ar','TN'),('ar','YE'),('as',''),('as','IN'),('az',''),('be',''),('be','BY'),('bg',''),('bg','BG'),('bm',''),('bm','ML'),('bn',''),('bn','BD'),('bn','IN'),('bo',''),('bo','CN'),('bo','IN'),('br',''),('br','FR'),('bs',''),('ca',''),('ca','AD'),('ca','ES'),('cs',''),('cs','CZ'),('cy',''),('cy','GB'),('da',''),('da','DK'),('de',''),('de','AT'),('de','BE'),('de','CH'),('de','DE'),('de','LI'),('de','LU'),('dz',''),('dz','BT'),('ee',''),('ee','GH'),('ee','TG'),('el',''),('el','CY'),('el','GR'),('en',''),('en','AG'),('en','AS'),('en','AU'),('en','BB'),('en','BE'),('en','BM'),('en','BS'),('en','BW'),('en','BZ'),('en','CA'),('en','CM'),('en','DM'),('en','FJ'),('en','FM'),('en','GB'),('en','GD'),('en','GG'),('en','GH'),('en','GI'),('en','GM'),('en','GU'),('en','GY'),('en','HK'),('en','IE'),('en','IM'),('en','IN'),('en','JE'),('en','JM'),('en','KE'),('en','KI'),('en','KN'),('en','KY'),('en','LC'),('en','LR'),('en','LS'),('en','MG'),('en','MH'),('en','MP'),('en','MT'),('en','MU'),('en','MW'),('en','NA'),('en','NG'),('en','NZ'),('en','PG'),('en','PH'),('en','PK'),('en','PR'),('en','PW'),('en','SB'),('en','SC'),('en','SG'),('en','SL'),('en','SS'),('en','SZ'),('en','TC'),('en','TO'),('en','TT'),('en','TZ'),('en','UG'),('en','UM'),('en','US'),('en','VC'),('en','VG'),('en','VI'),('en','VU'),('en','WS'),('en','ZA'),('en','ZM'),('en','ZW'),('eo',''),('es',''),('es','AR'),('es','BO'),('es','CL'),('es','CO'),('es','CR'),('es','CU'),('es','DO'),('es','EA'),('es','EC'),('es','ES'),('es','GQ'),('es','GT'),('es','HN'),('es','IC'),('es','MX'),('es','NI'),('es','PA'),('es','PE'),('es','PH'),('es','PR'),('es','PY'),('es','SV'),('es','US'),('es','UY'),('es','VE'),('et',''),('et','EE'),('eu',''),('eu','ES'),('fa',''),('fa','AF'),('fa','IR'),('ff',''),('ff','SN'),('fi',''),('fi','FI'),('fo',''),('fo','FO'),('fr',''),('fr','BE'),('fr','BF'),('fr','BI'),('fr','BJ'),('fr','BL'),('fr','CA'),('fr','CD'),('fr','CF'),('fr','CG'),('fr','CH'),('fr','CI'),('fr','CM'),('fr','DJ'),('fr','DZ'),('fr','FR'),('fr','GA'),('fr','GF'),('fr','GN'),('fr','GP'),('fr','GQ'),('fr','HT'),('fr','KM'),('fr','LU'),('fr','MA'),('fr','MC'),('fr','MF'),('fr','MG'),('fr','ML'),('fr','MQ'),('fr','MR'),('fr','MU'),('fr','NC'),('fr','NE'),('fr','PF'),('fr','RE'),('fr','RW'),('fr','SC'),('fr','SN'),('fr','SY'),('fr','TD'),('fr','TG'),('fr','TN'),('fr','VU'),('fr','YT'),('ga',''),('ga','IE'),('gd',''),('gd','GB'),('gl',''),('gl','ES'),('gu',''),('gu','IN'),('gv',''),('gv','GB'),('ha',''),('he',''),('he','IL'),('hi',''),('hi','IN'),('hr',''),('hr','BA'),('hr','HR'),('hu',''),('hu','HU'),('hy',''),('hy','AM'),('ia',''),('id',''),('id','ID'),('ig',''),('ig','NG'),('ii',''),('ii','CN'),('is',''),('is','IS'),('it',''),('it','CH'),('it','IT'),('it','SM'),('ja',''),('ja','JP'),('ka',''),('ka','GE'),('ki',''),('ki','KE'),('kk',''),('kl',''),('kl','GL'),('km',''),('km','KH'),('kn',''),('kn','IN'),('ko',''),('ko','KP'),('ko','KR'),('ks',''),('kw',''),('kw','GB'),('ky',''),('ky','KG'),('lg',''),('lg','UG'),('ln',''),('ln','AO'),('ln','CD'),('ln','CF'),('ln','CG'),('lo',''),('lo','LA'),('lt',''),('lt','LT'),('lu',''),('lu','CD'),('lv',''),('lv','LV'),('mg',''),('mg','MG'),('mk',''),('mk','MK'),('ml',''),('ml','IN'),('mr',''),('mr','IN'),('ms',''),('ms','BN'),('ms','MY'),('ms','SG'),('mt',''),('mt','MT'),('my',''),('my','MM'),('nb',''),('nb','NO'),('nd',''),('nd','ZW'),('ne',''),('ne','IN'),('ne','NP'),('nl',''),('nl','AW'),('nl','BE'),('nl','CW'),('nl','NL'),('nl','SR'),('nl','SX'),('nn',''),('nn','NO'),('nr',''),('nr','ZA'),('om',''),('om','ET'),('om','KE'),('or',''),('or','IN'),('os',''),('os','GE'),('os','RU'),('pa',''),('pl',''),('pl','PL'),('ps',''),('ps','AF'),('pt',''),('pt','AO'),('pt','BR'),('pt','CV'),('pt','GW'),('pt','MO'),('pt','MZ'),('pt','PT'),('pt','ST'),('pt','TL'),('rm',''),('rm','CH'),('rn',''),('rn','BI'),('ro',''),('ro','MD'),('ro','RO'),('ru',''),('ru','BY'),('ru','KG'),('ru','KZ'),('ru','MD'),('ru','RU'),('ru','UA'),('rw',''),('rw','RW'),('se',''),('se','FI'),('se','NO'),('sg',''),('sg','CF'),('si',''),('si','LK'),('sk',''),('sk','SK'),('sl',''),('sl','SI'),('sn',''),('sn','ZW'),('so',''),('so','DJ'),('so','ET'),('so','KE'),('so','SO'),('sq',''),('sq','AL'),('sq','MK'),('sr',''),('ss',''),('ss','SZ'),('ss','ZA'),('st',''),('st','LS'),('st','ZA'),('sv',''),('sv','AX'),('sv','FI'),('sv','SE'),('sw',''),('sw','KE'),('sw','TZ'),('sw','UG'),('ta',''),('ta','IN'),('ta','LK'),('ta','MY'),('ta','SG'),('te',''),('te','IN'),('tg',''),('th',''),('th','TH'),('ti',''),('ti','ER'),('ti','ET'),('tn',''),('tn','BW'),('tn','ZA'),('to',''),('to','TO'),('tr',''),('tr','CY'),('tr','TR'),('ts',''),('ts','ZA'),('uk',''),('uk','UA'),('ur',''),('ur','IN'),('ur','PK'),('uz',''),('ve',''),('ve','ZA'),('vi',''),('vi','VN'),('vo',''),('xh',''),('xh','ZA'),('yo',''),('yo','NG'),('zh',''),('zu',''),('zu','ZA');
/*!40000 ALTER TABLE `core_intl_language` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_intl_language_names`
--

DROP TABLE IF EXISTS `core_intl_language_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_intl_language_names` (
  `core_intl_language_ID` varchar(2) NOT NULL,
  `language_name_language` varchar(2) NOT NULL,
  `language_name` varchar(127) NOT NULL,
  PRIMARY KEY (`core_intl_language_ID`,`language_name_language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_intl_language_names`
--

LOCK TABLES `core_intl_language_names` WRITE;
/*!40000 ALTER TABLE `core_intl_language_names` DISABLE KEYS */;
INSERT INTO `core_intl_language_names` VALUES ('aa','de','Afar'),('aa','en','Afar'),('aa','es','afar'),('aa','fr','afar'),('aa','it','afar'),('ab','de','Abchasisch'),('ab','en','Abkhazian'),('ab','es','abjasio'),('ab','fr','abkhaze'),('ab','it','abkhazian'),('ae','de','Avestisch'),('ae','en','Avestan'),('ae','es','avéstico'),('ae','fr','avestique'),('ae','it','avestan'),('af','de','Afrikaans'),('af','en','Afrikaans'),('af','es','afrikaans'),('af','fr','afrikaans'),('af','it','afrikaans'),('ak','de','Akan'),('ak','en','Akan'),('ak','es','akan'),('ak','fr','akan'),('ak','it','akan'),('am','de','Amharisch'),('am','en','Amharic'),('am','es','amárico'),('am','fr','amharique'),('am','it','amarico'),('an','de','Aragonesisch'),('an','en','Aragonese'),('an','es','aragonés'),('an','fr','aragonais'),('an','it','aragonese'),('ar','de','Arabisch'),('ar','en','Arabic'),('ar','es','árabe'),('ar','fr','arabe'),('ar','it','arabo'),('as','de','Assamesisch'),('as','en','Assamese'),('as','es','asamés'),('as','fr','assamais'),('as','it','assamese'),('av','de','Awarisch'),('av','en','Avaric'),('av','es','avar'),('av','fr','avar'),('av','it','avaro'),('ay','de','Aymara'),('ay','en','Aymara'),('ay','es','aimara'),('ay','fr','aymara'),('ay','it','aymara'),('az','de','Aserbaidschanisch'),('az','en','Azerbaijani'),('az','es','azerí'),('az','fr','azéri'),('az','it','azerbaigiano'),('ba','de','Baschkirisch'),('ba','en','Bashkir'),('ba','es','bashkir'),('ba','fr','bachkir'),('ba','it','baschiro'),('be','de','Weißrussisch'),('be','en','Belarusian'),('be','es','bielorruso'),('be','fr','biélorusse'),('be','it','bielorusso'),('bg','de','Bulgarisch'),('bg','en','Bulgarian'),('bg','es','búlgaro'),('bg','fr','bulgare'),('bg','it','bulgaro'),('bh','de','Biharisch'),('bh','en','Bihari'),('bh','es','bihari'),('bh','fr','bihari'),('bh','it','bihari'),('bi','de','Bislama'),('bi','en','Bislama'),('bi','es','bislama'),('bi','fr','bichelamar'),('bi','it','bislama'),('bm','de','Bambara-Sprache'),('bm','en','Bambara'),('bm','es','bambara'),('bm','fr','bambara'),('bm','it','bambara'),('bn','de','Bengalisch'),('bn','en','Bengali'),('bn','es','bengalí'),('bn','fr','bengali'),('bn','it','bengalese'),('bo','de','Tibetisch'),('bo','en','Tibetan'),('bo','es','tibetano'),('bo','fr','tibétain'),('bo','it','tibetano'),('br','de','Bretonisch'),('br','en','Breton'),('br','es','bretón'),('br','fr','breton'),('br','it','bretone'),('bs','de','Bosnisch'),('bs','en','Bosnian'),('bs','es','bosnio'),('bs','fr','bosniaque'),('bs','it','bosniaco'),('ca','de','Katalanisch'),('ca','en','Catalan'),('ca','es','catalán'),('ca','fr','catalan'),('ca','it','catalano'),('ce','de','Tschetschenisch'),('ce','en','Chechen'),('ce','es','checheno'),('ce','fr','tchétchène'),('ce','it','ceceno'),('ch','de','Chamorro-Sprache'),('ch','en','Chamorro'),('ch','es','chamorro'),('ch','fr','chamorro'),('ch','it','chamorro'),('co','de','Korsisch'),('co','en','Corsican'),('co','es','corso'),('co','fr','corse'),('co','it','corso'),('cr','de','Cree'),('cr','en','Cree'),('cr','es','cree'),('cr','fr','cree'),('cr','it','cree'),('cs','de','Tschechisch'),('cs','en','Czech'),('cs','es','checo'),('cs','fr','tchèque'),('cs','it','ceco'),('cu','de','Kirchenslawisch'),('cu','en','Church Slavic'),('cu','es','eslavo eclesiástico'),('cu','fr','slavon d’église'),('cu','it','slavo della Chiesa'),('cv','de','Tschuwaschisch'),('cv','en','Chuvash'),('cv','es','chuvash'),('cv','fr','tchouvache'),('cv','it','chuvash'),('cy','de','Walisisch'),('cy','en','Welsh'),('cy','es','galés'),('cy','fr','gallois'),('cy','it','gallese'),('da','de','Dänisch'),('da','en','Danish'),('da','es','danés'),('da','fr','danois'),('da','it','danese'),('de','de','Deutsch'),('de','en','German'),('de','es','alemán'),('de','fr','allemand'),('de','it','tedesco'),('dv','de','Maledivisch'),('dv','en','Divehi'),('dv','es','divehi'),('dv','fr','maldivien'),('dv','it','divehi'),('dz','de','Bhutanisch'),('dz','en','Dzongkha'),('dz','es','dzongkha'),('dz','fr','dzongkha'),('dz','it','dzongkha'),('ee','de','Ewe-Sprache'),('ee','en','Ewe'),('ee','es','ewe'),('ee','fr','éwé'),('ee','it','ewe'),('el','de','Griechisch'),('el','en','Greek'),('el','es','griego'),('el','fr','grec'),('el','it','greco'),('en','de','Englisch'),('en','en','English'),('en','es','inglés'),('en','fr','anglais'),('en','it','inglese'),('eo','de','Esperanto'),('eo','en','Esperanto'),('eo','es','esperanto'),('eo','fr','espéranto'),('eo','it','esperanto'),('es','de','Spanisch'),('es','en','Spanish'),('es','es','español'),('es','fr','espagnol'),('es','it','spagnolo'),('et','de','Estnisch'),('et','en','Estonian'),('et','es','estonio'),('et','fr','estonien'),('et','it','estone'),('eu','de','Baskisch'),('eu','en','Basque'),('eu','es','vasco'),('eu','fr','basque'),('eu','it','basco'),('fa','de','Persisch'),('fa','en','Persian'),('fa','es','persa'),('fa','fr','persan'),('fa','it','persiano'),('ff','de','Ful'),('ff','en','Fulah'),('ff','es','fula'),('ff','fr','peul'),('ff','it','fulah'),('fi','de','Finnisch'),('fi','en','Finnish'),('fi','es','finés'),('fi','fr','finnois'),('fi','it','finlandese'),('fj','de','Fidschianisch'),('fj','en','Fijian'),('fj','es','fidjiano'),('fj','fr','fidjien'),('fj','it','figiano'),('fo','de','Färöisch'),('fo','en','Faroese'),('fo','es','feroés'),('fo','fr','féroïen'),('fo','it','faroese'),('fr','de','Französisch'),('fr','en','French'),('fr','es','francés'),('fr','fr','français'),('fr','it','francese'),('fy','de','Westfriesisch'),('fy','en','Western Frisian'),('fy','es','frisón occidental'),('fy','fr','frison'),('fy','it','frisone occidentale'),('ga','de','Irisch'),('ga','en','Irish'),('ga','es','irlandés'),('ga','fr','irlandais'),('ga','it','irlandese'),('gd','de','Schottisches Gälisch'),('gd','en','Scottish Gaelic'),('gd','es','gaélico escocés'),('gd','fr','gaélique écossais'),('gd','it','gaelico scozzese'),('gl','de','Galizisch'),('gl','en','Galician'),('gl','es','gallego'),('gl','fr','galicien'),('gl','it','galiziano'),('gn','de','Guarani'),('gn','en','Guarani'),('gn','es','guaraní'),('gn','fr','guarani'),('gn','it','guarana'),('gu','de','Gujarati'),('gu','en','Gujarati'),('gu','es','gujarati'),('gu','fr','goudjarâtî'),('gu','it','gujarati'),('gv','de','Manx'),('gv','en','Manx'),('gv','es','gaélico manés'),('gv','fr','manx'),('gv','it','manx'),('ha','de','Hausa'),('ha','en','Hausa'),('ha','es','hausa'),('ha','fr','haoussa'),('ha','it','haussa'),('he','de','Hebräisch'),('he','en','Hebrew'),('he','es','hebreo'),('he','fr','hébreu'),('he','it','ebraico'),('hi','de','Hindi'),('hi','en','Hindi'),('hi','es','hindi'),('hi','fr','hindi'),('hi','it','hindi'),('ho','de','Hiri-Motu'),('ho','en','Hiri Motu'),('ho','es','hiri motu'),('ho','fr','hiri motu'),('ho','it','hiri motu'),('hr','de','Kroatisch'),('hr','en','Croatian'),('hr','es','croata'),('hr','fr','croate'),('hr','it','croato'),('ht','de','Haitianisch'),('ht','en','Haitian'),('ht','es','haitiano'),('ht','fr','haïtien'),('ht','it','haitiano'),('hu','de','Ungarisch'),('hu','en','Hungarian'),('hu','es','húngaro'),('hu','fr','hongrois'),('hu','it','ungherese'),('hy','de','Armenisch'),('hy','en','Armenian'),('hy','es','armenio'),('hy','fr','arménien'),('hy','it','armeno'),('hz','de','Herero-Sprache'),('hz','en','Herero'),('hz','es','herero'),('hz','fr','héréro'),('hz','it','herero'),('ia','de','Interlingua'),('ia','en','Interlingua'),('ia','es','interlingua'),('ia','fr','interlingua'),('ia','it','interlingua'),('id','de','Indonesisch'),('id','en','Indonesian'),('id','es','indonesio'),('id','fr','indonésien'),('id','it','indonesiano'),('ie','de','Interlingue'),('ie','en','Interlingue'),('ie','es','interlingue'),('ie','fr','interlingue'),('ie','it','interlingue'),('ig','de','Igbo-Sprache'),('ig','en','Igbo'),('ig','es','igbo'),('ig','fr','igbo'),('ig','it','igbo'),('ii','de','Sichuan Yi'),('ii','en','Sichuan Yi'),('ii','es','sichuan yi'),('ii','fr','yi de Sichuan'),('ii','it','sichuan yi'),('ik','de','Inupiak'),('ik','en','Inupiaq'),('ik','es','inupiaq'),('ik','fr','inupiaq'),('ik','it','inupiak'),('io','de','Ido-Sprache'),('io','en','Ido'),('io','es','ido'),('io','fr','ido'),('io','it','ido'),('is','de','Isländisch'),('is','en','Icelandic'),('is','es','islandés'),('is','fr','islandais'),('is','it','islandese'),('it','de','Italienisch'),('it','en','Italian'),('it','es','italiano'),('it','fr','italien'),('it','it','italiano'),('iu','de','Inukitut'),('iu','en','Inuktitut'),('iu','es','inuktitut'),('iu','fr','inuktitut'),('iu','it','inuktitut'),('ja','de','Japanisch'),('ja','en','Japanese'),('ja','es','japonés'),('ja','fr','japonais'),('ja','it','giapponese'),('jv','de','Javanisch'),('jv','en','Javanese'),('jv','es','javanés'),('jv','fr','javanais'),('jv','it','giavanese'),('ka','de','Georgisch'),('ka','en','Georgian'),('ka','es','georgiano'),('ka','fr','géorgien'),('ka','it','georgiano'),('kg','de','Kongolesisch'),('kg','en','Kongo'),('kg','es','kongo'),('kg','fr','kongo'),('kg','it','kongo'),('ki','de','Kikuyu-Sprache'),('ki','en','Kikuyu'),('ki','es','kikuyu'),('ki','fr','kikuyu'),('ki','it','kikuyu'),('kj','de','Kwanyama'),('kj','en','Kuanyama'),('kj','es','kuanyama'),('kj','fr','kuanyama'),('kj','it','kuanyama'),('kk','de','Kasachisch'),('kk','en','Kazakh'),('kk','es','kazajo'),('kk','fr','kazakh'),('kk','it','kazako'),('kl','de','Grönländisch'),('kl','en','Kalaallisut'),('kl','es','groenlandés'),('kl','fr','groenlandais'),('kl','it','kalaallisut'),('km','de','Kambodschanisch'),('km','en','Khmer'),('km','es','jemer'),('km','fr','khmer'),('km','it','khmer'),('kn','de','Kannada'),('kn','en','Kannada'),('kn','es','canarés'),('kn','fr','kannada'),('kn','it','kannada'),('ko','de','Koreanisch'),('ko','en','Korean'),('ko','es','coreano'),('ko','fr','coréen'),('ko','it','coreano'),('kr','de','Kanuri-Sprache'),('kr','en','Kanuri'),('kr','es','kanuri'),('kr','fr','kanouri'),('kr','it','kanuri'),('ks','de','Kaschmirisch'),('ks','en','Kashmiri'),('ks','es','cachemiro'),('ks','fr','kâshmîrî'),('ks','it','kashmiri'),('ku','de','Kurdisch'),('ku','en','Kurdish'),('ku','es','kurdo'),('ku','fr','kurde'),('ku','it','curdo'),('kv','de','Komi-Sprache'),('kv','en','Komi'),('kv','es','komi'),('kv','fr','komi'),('kv','it','komi'),('kw','de','Kornisch'),('kw','en','Cornish'),('kw','es','córnico'),('kw','fr','cornique'),('kw','it','cornico'),('ky','de','Kirgisisch'),('ky','en','Kirghiz'),('ky','es','kirghiz'),('ky','fr','kirghize'),('ky','it','kirghiso'),('la','de','Latein'),('la','en','Latin'),('la','es','latín'),('la','fr','latin'),('la','it','latino'),('lb','de','Luxemburgisch'),('lb','en','Luxembourgish'),('lb','es','luxemburgués'),('lb','fr','luxembourgeois'),('lb','it','lussemburghese'),('lg','de','Ganda-Sprache'),('lg','en','Ganda'),('lg','es','ganda'),('lg','fr','ganda'),('lg','it','ganda'),('li','de','Limburgisch'),('li','en','Limburgish'),('li','es','limburgués'),('li','fr','limbourgeois'),('li','it','limburgese'),('ln','de','Lingala'),('ln','en','Lingala'),('ln','es','lingala'),('ln','fr','lingala'),('ln','it','lingala'),('lo','de','Laotisch'),('lo','en','Lao'),('lo','es','laosiano'),('lo','fr','lao'),('lo','it','lao'),('lt','de','Litauisch'),('lt','en','Lithuanian'),('lt','es','lituano'),('lt','fr','lituanien'),('lt','it','lituano'),('lu','de','Luba-Katanga'),('lu','en','Luba-Katanga'),('lu','es','luba-katanga'),('lu','fr','luba-katanga'),('lu','it','luba-katanga'),('lv','de','Lettisch'),('lv','en','Latvian'),('lv','es','letón'),('lv','fr','letton'),('lv','it','lettone'),('mg','de','Malagassi-Sprache'),('mg','en','Malagasy'),('mg','es','malgache'),('mg','fr','malgache'),('mg','it','malgascio'),('mh','de','Marschallesisch'),('mh','en','Marshallese'),('mh','es','marshalés'),('mh','fr','marshall'),('mh','it','marshallese'),('mi','de','Maori'),('mi','en','Maori'),('mi','es','maorí'),('mi','fr','maori'),('mi','it','maori'),('mk','de','Mazedonisch'),('mk','en','Macedonian'),('mk','es','macedonio'),('mk','fr','macédonien'),('mk','it','macedone'),('ml','de','Malayalam'),('ml','en','Malayalam'),('ml','es','malayalam'),('ml','fr','malayalam'),('ml','it','malayalam'),('mn','de','Mongolisch'),('mn','en','Mongolian'),('mn','es','mongol'),('mn','fr','mongol'),('mn','it','mongolo'),('mo','de','Moldauisch'),('mo','en','Moldavian'),('mo','es','moldavo'),('mo','fr','moldave'),('mo','it','moldavo'),('mr','de','Marathi'),('mr','en','Marathi'),('mr','es','marathi'),('mr','fr','marathe'),('mr','it','marathi'),('ms','de','Malaiisch'),('ms','en','Malay'),('ms','es','malayo'),('ms','fr','malais'),('ms','it','malese'),('mt','de','Maltesisch'),('mt','en','Maltese'),('mt','es','maltés'),('mt','fr','maltais'),('mt','it','maltese'),('my','de','Birmanisch'),('my','en','Burmese'),('my','es','birmano'),('my','fr','birman'),('my','it','birmano'),('na','de','Nauruisch'),('na','en','Nauru'),('na','es','nauruano'),('na','fr','nauruan'),('na','it','nauru'),('nb','de','Norwegisch Bokmål'),('nb','en','Norwegian Bokmål'),('nb','es','bokmal noruego'),('nb','fr','norvégien bokmål'),('nb','it','norvegese bokmal'),('nd','de','Nord-Ndebele-Sprache'),('nd','en','North Ndebele'),('nd','es','ndebele septentrional'),('nd','fr','ndébélé du Nord'),('nd','it','ndebele del nord'),('ne','de','Nepalesisch'),('ne','en','Nepali'),('ne','es','nepalí'),('ne','fr','népalais'),('ne','it','nepalese'),('ng','de','Ndonga'),('ng','en','Ndonga'),('ng','es','ndonga'),('ng','fr','ndonga'),('ng','it','ndonga'),('nl','de','Niederländisch'),('nl','en','Dutch'),('nl','es','neerlandés'),('nl','fr','néerlandais'),('nl','it','olandese'),('nn','de','Norwegisch Nynorsk'),('nn','en','Norwegian Nynorsk'),('nn','es','nynorsk noruego'),('nn','fr','norvégien nynorsk'),('nn','it','norvegese nynorsk'),('no','de','Norwegisch'),('no','en','Norwegian'),('no','es','noruego'),('no','fr','norvégien'),('no','it','norvegese'),('nr','de','Süd-Ndebele-Sprache'),('nr','en','South Ndebele'),('nr','es','ndebele meridional'),('nr','fr','ndébélé du Sud'),('nr','it','ndebele del sud'),('nv','de','Navajo'),('nv','en','Navajo'),('nv','es','navajo'),('nv','fr','navaho'),('nv','it','navajo'),('ny','de','Nyanja-Sprache'),('ny','en','Nyanja'),('ny','es','nyanja'),('ny','fr','nyanja'),('ny','it','nyanja'),('oc','de','Okzitanisch'),('oc','en','Occitan'),('oc','es','occitano'),('oc','fr','occitan'),('oc','it','occitano'),('oj','de','Ojibwa-Sprache'),('oj','en','Ojibwa'),('oj','es','ojibwa'),('oj','fr','ojibwa'),('oj','it','ojibwa'),('om','de','Oromo'),('om','en','Oromo'),('om','es','oromo'),('om','fr','oromo'),('om','it','oromo'),('or','de','Orija'),('or','en','Oriya'),('or','es','oriya'),('or','fr','oriya'),('or','it','oriya'),('os','de','Ossetisch'),('os','en','Ossetic'),('os','es','osético'),('os','fr','ossète'),('os','it','ossetico'),('pa','de','Pandschabisch'),('pa','en','Punjabi'),('pa','es','punjabí'),('pa','fr','pendjabi'),('pa','it','punjabi'),('pi','de','Pali'),('pi','en','Pali'),('pi','es','pali'),('pi','fr','pali'),('pi','it','pali'),('pl','de','Polnisch'),('pl','en','Polish'),('pl','es','polaco'),('pl','fr','polonais'),('pl','it','polacco'),('ps','de','Paschtu'),('ps','en','Pashto'),('ps','es','pastún'),('ps','fr','pachto'),('ps','it','pashto'),('pt','de','Portugiesisch'),('pt','en','Portuguese'),('pt','es','portugués'),('pt','fr','portugais'),('pt','it','portoghese'),('qu','de','Quechua'),('qu','en','Quechua'),('qu','es','quechua'),('qu','fr','quechua'),('qu','it','quechua'),('rm','de','Rätoromanisch'),('rm','en','Romansh'),('rm','es','retorrománico'),('rm','fr','romanche'),('rm','it','romancio'),('rn','de','Rundi-Sprache'),('rn','en','Rundi'),('rn','es','kiroundi'),('rn','fr','roundi'),('rn','it','rundi'),('ro','de','Rumänisch'),('ro','en','Romanian'),('ro','es','rumano'),('ro','fr','roumain'),('ro','it','rumeno'),('ru','de','Russisch'),('ru','en','Russian'),('ru','es','ruso'),('ru','fr','russe'),('ru','it','russo'),('rw','de','Ruandisch'),('rw','en','Kinyarwanda'),('rw','es','kinyarwanda'),('rw','fr','rwanda'),('rw','it','kinyarwanda'),('sa','de','Sanskrit'),('sa','en','Sanskrit'),('sa','es','sánscrito'),('sa','fr','sanskrit'),('sa','it','sanscrito'),('sc','de','Sardisch'),('sc','en','Sardinian'),('sc','es','sardo'),('sc','fr','sarde'),('sc','it','sardo'),('sd','de','Sindhi'),('sd','en','Sindhi'),('sd','es','sindhi'),('sd','fr','sindhî'),('sd','it','sindhi'),('se','de','Nord-Samisch'),('se','en','Northern Sami'),('se','es','sami septentrional'),('se','fr','sami du Nord'),('se','it','sami del nord'),('sg','de','Sango'),('sg','en','Sango'),('sg','es','sango'),('sg','fr','sangho'),('sg','it','sango'),('sh','de','Serbo-Kroatisch'),('sh','en','Serbo-Croatian'),('sh','es','serbocroata'),('sh','fr','serbo-croate'),('sh','it','serbo-croato'),('si','de','Singhalesisch'),('si','en','Sinhala'),('si','es','cingalés'),('si','fr','cinghalais'),('si','it','singalese'),('sk','de','Slowakisch'),('sk','en','Slovak'),('sk','es','eslovaco'),('sk','fr','slovaque'),('sk','it','slovacco'),('sl','de','Slowenisch'),('sl','en','Slovenian'),('sl','es','esloveno'),('sl','fr','slovène'),('sl','it','sloveno'),('sm','de','Samoanisch'),('sm','en','Samoan'),('sm','es','samoano'),('sm','fr','samoan'),('sm','it','samoano'),('sn','de','Shona'),('sn','en','Shona'),('sn','es','shona'),('sn','fr','shona'),('sn','it','shona'),('so','de','Somali'),('so','en','Somali'),('so','es','somalí'),('so','fr','somali'),('so','it','somalo'),('sq','de','Albanisch'),('sq','en','Albanian'),('sq','es','albanés'),('sq','fr','albanais'),('sq','it','albanese'),('sr','de','Serbisch'),('sr','en','Serbian'),('sr','es','serbio'),('sr','fr','serbe'),('sr','it','serbo'),('ss','de','Swazi'),('ss','en','Swati'),('ss','es','siswati'),('ss','fr','swati'),('ss','it','swati'),('st','de','Süd-Sotho-Sprache'),('st','en','Southern Sotho'),('st','es','sesotho meridional'),('st','fr','sesotho'),('st','it','sotho del sud'),('su','de','Sundanesisch'),('su','en','Sundanese'),('su','es','sundanés'),('su','fr','soundanais'),('su','it','sundanese'),('sv','de','Schwedisch'),('sv','en','Swedish'),('sv','es','sueco'),('sv','fr','suédois'),('sv','it','svedese'),('sw','de','Suaheli'),('sw','en','Swahili'),('sw','es','swahili'),('sw','fr','swahili'),('sw','it','swahili'),('ta','de','Tamilisch'),('ta','en','Tamil'),('ta','es','tamil'),('ta','fr','tamoul'),('ta','it','tamil'),('te','de','Telugu'),('te','en','Telugu'),('te','es','telugu'),('te','fr','télougou'),('te','it','telugu'),('tg','de','Tadschikisch'),('tg','en','Tajik'),('tg','es','tayiko'),('tg','fr','tadjik'),('tg','it','tagicco'),('th','de','Thailändisch'),('th','en','Thai'),('th','es','tailandés'),('th','fr','thaï'),('th','it','thai'),('ti','de','Tigrinja'),('ti','en','Tigrinya'),('ti','es','tigriña'),('ti','fr','tigrigna'),('ti','it','tigrinya'),('tk','de','Turkmenisch'),('tk','en','Turkmen'),('tk','es','turcomano'),('tk','fr','turkmène'),('tk','it','turcomanno'),('tl','de','Tagalog'),('tl','en','Tagalog'),('tl','es','tagalo'),('tl','fr','tagalog'),('tl','it','tagalog'),('tn','de','Tswana-Sprache'),('tn','en','Tswana'),('tn','es','setchwana'),('tn','fr','tswana'),('tn','it','tswana'),('to','de','Tongaisch'),('to','en','Tongan'),('to','es','tongano'),('to','fr','tongien'),('to','it','tongano'),('tr','de','Türkisch'),('tr','en','Turkish'),('tr','es','turco'),('tr','fr','turc'),('tr','it','turco'),('ts','de','Tsonga'),('ts','en','Tsonga'),('ts','es','tsonga'),('ts','fr','tsonga'),('ts','it','tsonga'),('tt','de','Tatarisch'),('tt','en','Tatar'),('tt','es','tártaro'),('tt','fr','tatar'),('tt','it','tatarico'),('tw','de','Twi'),('tw','en','Twi'),('tw','es','twi'),('tw','fr','twi'),('tw','it','ci'),('ty','de','Tahitisch'),('ty','en','Tahitian'),('ty','es','tahitiano'),('ty','fr','tahitien'),('ty','it','taitiano'),('ug','de','Uigurisch'),('ug','en','Uighur'),('ug','es','uigur'),('ug','fr','ouïghour'),('ug','it','uigurico'),('uk','de','Ukrainisch'),('uk','en','Ukrainian'),('uk','es','ucraniano'),('uk','fr','ukrainien'),('uk','it','ucraino'),('ur','de','Urdu'),('ur','en','Urdu'),('ur','es','urdu'),('ur','fr','ourdou'),('ur','it','urdu'),('uz','de','Usbekisch'),('uz','en','Uzbek'),('uz','es','uzbeko'),('uz','fr','ouzbek'),('uz','it','usbeco'),('ve','de','Venda-Sprache'),('ve','en','Venda'),('ve','es','venda'),('ve','fr','venda'),('ve','it','venda'),('vi','de','Vietnamesisch'),('vi','en','Vietnamese'),('vi','es','vietnamita'),('vi','fr','vietnamien'),('vi','it','vietnamita'),('vo','de','Volapük'),('vo','en','Volapük'),('vo','es','volapük'),('vo','fr','volapuk'),('vo','it','volapük'),('wa','de','Wallonisch'),('wa','en','Walloon'),('wa','es','valón'),('wa','fr','wallon'),('wa','it','vallone'),('wo','de','Wolof'),('wo','en','Wolof'),('wo','es','uolof'),('wo','fr','wolof'),('wo','it','volof'),('xh','de','Xhosa'),('xh','en','Xhosa'),('xh','es','xhosa'),('xh','fr','xhosa'),('xh','it','xosa'),('yi','de','Jiddisch'),('yi','en','Yiddish'),('yi','es','yídish'),('yi','fr','yiddish'),('yi','it','yiddish'),('yo','de','Yoruba'),('yo','en','Yoruba'),('yo','es','yoruba'),('yo','fr','yoruba'),('yo','it','yoruba'),('za','de','Zhuang'),('za','en','Zhuang'),('za','es','zhuang'),('za','fr','zhuang'),('za','it','zhuang'),('zh','de','Chinesisch'),('zh','en','Chinese'),('zh','es','chino'),('zh','fr','chinois'),('zh','it','cinese'),('zu','de','Zulu'),('zu','en','Zulu'),('zu','es','zulú'),('zu','fr','zoulou'),('zu','it','zulu');
/*!40000 ALTER TABLE `core_intl_language_names` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_plugins`
--

DROP TABLE IF EXISTS `core_plugins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_plugins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `sort_order` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_plugins`
--

LOCK TABLES `core_plugins` WRITE;
/*!40000 ALTER TABLE `core_plugins` DISABLE KEYS */;
INSERT INTO `core_plugins` VALUES (1,'baseLayout',1),(4,'payroll',2);
/*!40000 ALTER TABLE `core_plugins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_registry`
--

DROP TABLE IF EXISTS `core_registry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_registry` (
  `path` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `value` varchar(512) NOT NULL,
  PRIMARY KEY (`path`,`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_registry`
--

LOCK TABLES `core_registry` WRITE;
/*!40000 ALTER TABLE `core_registry` DISABLE KEYS */;
INSERT INTO `core_registry` VALUES ('GLOBAL/SETTINGS/CORE','dateformat_long',0,'%e. %B %Y'),('GLOBAL/SETTINGS/CORE','dateformat_medium',0,'%d.%m.%Y'),('GLOBAL/SETTINGS/CORE','dateformat_short',0,'%d.%m.%y'),('GLOBAL/SETTINGS/CORE','datetimeformat_hm',0,'%d.%m.%Y %H:%M'),('GLOBAL/SETTINGS/CORE','datetimeformat_hms',0,'%d.%m.%Y %H:%M:%s'),('GLOBAL/SETTINGS/CORE','numberformat_dec_point',0,'.'),('GLOBAL/SETTINGS/CORE','numberformat_thousands_sep',0,'\''),('GLOBAL/SETTINGS/CORE','timeformat_hm',0,'%H:%M'),('GLOBAL/SETTINGS/CORE','timeformat_hms',0,'%H:%M:%s'),('GLOBAL/SETTINGS/CORE/payroll','pensioner_relief',0,'16800.00'),('GROUPS/2/ModulePermission/baseLayout','configEnabled',0,'0'),('GROUPS/2/ModulePermission/baseLayout','groupAdd',0,'0'),('GROUPS/2/ModulePermission/baseLayout','groupDelete',0,'0'),('GROUPS/2/ModulePermission/baseLayout','groupDetailDisplay',0,'0'),('GROUPS/2/ModulePermission/baseLayout','groupEdit',0,'0'),('GROUPS/2/ModulePermission/baseLayout','groupListDisplay',0,'1'),('GROUPS/2/ModulePermission/baseLayout','userAdd',0,'0'),('GROUPS/2/ModulePermission/baseLayout','userAdminEnabled',0,'1'),('GROUPS/2/ModulePermission/baseLayout','userDelete',0,'0'),('GROUPS/2/ModulePermission/baseLayout','userDetailDisplay',0,'0'),('GROUPS/2/ModulePermission/baseLayout','userEdit',0,'0'),('GROUPS/2/ModulePermission/baseLayout','userListDisplay',0,'1'),('USERS/1/ExtendedSecurity','bFri',0,'1'),('USERS/1/ExtendedSecurity','bMon',0,'1'),('USERS/1/ExtendedSecurity','bSat',0,'0'),('USERS/1/ExtendedSecurity','bSun',0,'0'),('USERS/1/ExtendedSecurity','bThu',0,'0'),('USERS/1/ExtendedSecurity','bTue',0,'0'),('USERS/1/ExtendedSecurity','bWed',0,'1'),('USERS/1/ExtendedSecurity','ipInclude1',0,'192.168.1.205'),('USERS/1/ExtendedSecurity','ipInclude2',0,''),('USERS/1/ExtendedSecurity','ipInclude3',0,''),('USERS/1/ExtendedSecurity','ipInclude4',0,''),('USERS/1/ExtendedSecurity','ipInclude5',0,''),('USERS/1/ExtendedSecurity','ipRestriction',0,'0'),('USERS/1/ExtendedSecurity','subnetInclude1',0,'255.255.255.0'),('USERS/1/ExtendedSecurity','subnetInclude2',0,''),('USERS/1/ExtendedSecurity','subnetInclude3',0,''),('USERS/1/ExtendedSecurity','subnetInclude4',0,''),('USERS/1/ExtendedSecurity','subnetInclude5',0,''),('USERS/1/ExtendedSecurity','timeFrom',0,'07:30'),('USERS/1/ExtendedSecurity','timeRestriction',0,'0'),('USERS/1/ExtendedSecurity','timeUntil',0,'17:30'),('USERS/1/SETTINGS','acc_fin_fiscalyear_ID',0,'1'),('USERS/1/SETTINGS','acc_fin_fiscalyear_name',0,'2012'),('USERS/1/SETTINGS/acc','acc_fin_fiscalyear_ID',0,'1'),('USERS/1/SETTINGS/acc','acc_fin_fiscalyear_name',0,'2012'),('USERS/1/SETTINGS/payroll','calcOvSettings',0,'a:3:{s:18:\"quickFilterEnabled\";b:0;s:12:\"columnsWidth\";a:8:{i:0;d:80;i:1;d:127;i:2;d:113;i:3;d:53;i:4;d:151;i:5;d:119;i:6;d:110;i:7;d:111;}s:4:\"sort\";a:1:{i:0;a:2:{s:8:\"columnId\";s:8:\"Lastname\";s:7:\"sortAsc\";b:1;}}}'),('USERS/1/SETTINGS/payroll','cmpcSettings',0,'a:3:{s:18:\"quickFilterEnabled\";b:0;s:12:\"columnsWidth\";a:5:{i:0;d:85;i:1;d:120;i:2;d:160;i:3;d:60;i:4;d:174;}s:4:\"sort\";a:1:{i:0;a:2:{s:8:\"columnId\";s:17:\"company_shortname\";s:7:\"sortAsc\";b:1;}}}'),('USERS/1/SETTINGS/payroll','finalizeEqualDates',0,'1'),('USERS/1/SETTINGS/payroll','inscSettings',0,'a:3:{s:18:\"quickFilterEnabled\";b:0;s:12:\"columnsWidth\";a:12:{i:0;d:95;i:1;d:106;i:2;d:116;i:3;d:102;i:4;d:70;i:5;d:75;i:6;d:73;i:7;d:55;i:8;d:89;i:9;d:84;i:10;d:95;i:11;d:84;}s:4:\"sort\";a:1:{i:0;a:2:{s:8:\"columnId\";s:13:\"InsuranceCode\";s:7:\"sortAsc\";b:1;}}}'),('USERS/1/SETTINGS/payroll','loacSettings',0,'a:3:{s:18:\"quickFilterEnabled\";b:0;s:12:\"columnsWidth\";a:3:{i:0;d:85;i:1;d:366;i:2;d:135;}s:4:\"sort\";a:1:{i:0;a:2:{s:8:\"columnId\";s:2:\"id\";s:7:\"sortAsc\";b:1;}}}'),('USERS/1/SETTINGS/payroll','psoCurrentEmplForm',0,'2'),('USERS/1/SETTINGS/payroll','psoDbFilter',0,''),('USERS/1/SETTINGS/payroll','psoSettings',0,'a:3:{s:18:\"quickFilterEnabled\";b:0;s:12:\"columnsWidth\";a:7:{i:0;d:80;i:1;d:137;i:2;d:122;i:3;d:203;i:4;d:69;i:5;d:157;i:6;d:53;}s:4:\"sort\";a:1:{i:0;a:2:{s:8:\"columnId\";s:8:\"Lastname\";s:7:\"sortAsc\";b:1;}}}'),('USERS/1/SETTINGS/payroll','syacSettings',0,'a:3:{s:18:\"quickFilterEnabled\";b:0;s:12:\"columnsWidth\";a:2:{i:0;d:90;i:1;d:300;}s:4:\"sort\";a:1:{i:0;a:2:{s:8:\"columnId\";s:18:\"payroll_account_ID\";s:7:\"sortAsc\";b:1;}}}'),('USERS/4/ExtendedSecurity','bFri',0,'1'),('USERS/4/ExtendedSecurity','bMon',0,'1'),('USERS/4/ExtendedSecurity','bSat',0,'0'),('USERS/4/ExtendedSecurity','bSun',0,'0'),('USERS/4/ExtendedSecurity','bThu',0,'1'),('USERS/4/ExtendedSecurity','bTue',0,'1'),('USERS/4/ExtendedSecurity','bWed',0,'1'),('USERS/4/ExtendedSecurity','ipInclude1',0,'213.188.228.86'),('USERS/4/ExtendedSecurity','ipInclude2',0,''),('USERS/4/ExtendedSecurity','ipInclude3',0,''),('USERS/4/ExtendedSecurity','ipInclude4',0,''),('USERS/4/ExtendedSecurity','ipInclude5',0,''),('USERS/4/ExtendedSecurity','ipRestriction',0,'0'),('USERS/4/ExtendedSecurity','subnetInclude1',0,'255.255.255.0'),('USERS/4/ExtendedSecurity','subnetInclude2',0,''),('USERS/4/ExtendedSecurity','subnetInclude3',0,''),('USERS/4/ExtendedSecurity','subnetInclude4',0,''),('USERS/4/ExtendedSecurity','subnetInclude5',0,''),('USERS/4/ExtendedSecurity','timeFrom',0,'06:30'),('USERS/4/ExtendedSecurity','timeRestriction',0,'0'),('USERS/4/ExtendedSecurity','timeUntil',0,'17:30'),('USERS/4/SETTINGS','acc_fin_fiscalyear_ID',0,'1'),('USERS/4/SETTINGS','acc_fin_fiscalyear_name',0,'2012'),('USERS/4/SETTINGS/acc','acc_fin_fiscalyear_ID',0,'1'),('USERS/4/SETTINGS/acc','acc_fin_fiscalyear_name',0,'2012'),('USERS/4/SETTINGS/payroll','calcOvSettings',0,'a:3:{s:18:\"quickFilterEnabled\";b:0;s:12:\"columnsWidth\";a:8:{i:0;d:100;i:1;d:80;i:2;d:80;i:3;d:40;i:4;d:163;i:5;d:80;i:6;d:80;i:7;d:80;}s:4:\"sort\";a:0:{}}'),('USERS/4/SETTINGS/payroll','cmpcSettings',0,'a:3:{s:18:\"quickFilterEnabled\";b:0;s:12:\"columnsWidth\";a:5:{i:0;d:60;i:1;d:173;i:2;d:184;i:3;d:60;i:4;d:60;}s:4:\"sort\";a:0:{}}'),('USERS/4/SETTINGS/payroll','finalizeEqualDates',0,'0'),('USERS/4/SETTINGS/payroll','inscSettings',0,'a:3:{s:18:\"quickFilterEnabled\";b:0;s:12:\"columnsWidth\";a:12:{i:0;d:93;i:1;d:202;i:2;d:93;i:3;d:104;i:4;d:89;i:5;d:70;i:6;d:70;i:7;d:55;i:8;d:70;i:9;d:70;i:10;d:70;i:11;d:70;}s:4:\"sort\";a:0:{}}'),('USERS/4/SETTINGS/payroll','loacSettings',0,'a:4:{s:18:\"quickFilterEnabled\";b:1;s:17:\"quickFilterValues\";a:0:{}s:12:\"columnsWidth\";a:3:{i:0;d:107;i:1;d:286;i:2;d:80;}s:4:\"sort\";a:0:{}}'),('USERS/4/SETTINGS/payroll','psoCurrentEmplForm',0,'6'),('USERS/4/SETTINGS/payroll','psoDbFilter',0,''),('USERS/4/SETTINGS/payroll','psoSettings',0,'a:4:{s:18:\"quickFilterEnabled\";b:1;s:17:\"quickFilterValues\";a:0:{}s:12:\"columnsWidth\";a:7:{i:0;d:60;i:1;d:138;i:2;d:144;i:3;d:150;i:4;d:71;i:5;d:180;i:6;d:40;}s:4:\"sort\";a:1:{i:0;a:2:{s:8:\"columnId\";s:14:\"EmployeeNumber\";s:7:\"sortAsc\";b:1;}}}');
/*!40000 ALTER TABLE `core_registry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_user`
--

DROP TABLE IF EXISTS `core_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(25) NOT NULL,
  `pwd` varchar(50) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `plugin_settings` text NOT NULL,
  `language` varchar(6) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `timeout_minutes` smallint(5) unsigned NOT NULL DEFAULT '30',
  `allow_pwd_change` tinyint(1) NOT NULL DEFAULT '1',
  `force_pwd_change` tinyint(1) NOT NULL DEFAULT '0',
  `pwd_change_period_days` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `datetime_pwd_change` datetime NOT NULL,
  `datetime_change` datetime NOT NULL,
  `core_user_ID_pwd_change` int(11) NOT NULL,
  `core_user_ID_change` int(11) NOT NULL,
  `datetime_create` datetime NOT NULL,
  `core_user_ID_create` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `core_user_ID_delete` int(11) NOT NULL,
  `datetime_delete` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_user`
--

LOCK TABLES `core_user` WRITE;
/*!40000 ALTER TABLE `core_user` DISABLE KEYS */;
INSERT INTO `core_user` VALUES (1,'dwm','*611AA6ACB6F28026B141446E6C85F36423226BB3','Daniel Müller','daniel.mueller@pesaris.com','','de',1,0,90,1,0,0,'0000-00-00 00:00:00','2011-07-28 07:52:27',0,1,'0000-00-00 00:00:00',0,0,0,'0000-00-00 00:00:00'),(4,'p.moulin','*3491839C4810A0EB87F3CE7ABBDE7A80B0325089','Pascal Moulin','pascal.moulin@pesaris.com','','de',1,0,30,1,0,0,'0000-00-00 00:00:00','2012-12-12 09:33:33',0,1,'2012-12-12 09:33:33',1,0,0,'0000-00-00 00:00:00');
/*!40000 ALTER TABLE `core_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_user_group`
--

DROP TABLE IF EXISTS `core_user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_user_group` (
  `core_user_ID` int(11) NOT NULL,
  `core_group_ID` int(11) NOT NULL,
  PRIMARY KEY (`core_user_ID`,`core_group_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_user_group`
--

LOCK TABLES `core_user_group` WRITE;
/*!40000 ALTER TABLE `core_user_group` DISABLE KEYS */;
INSERT INTO `core_user_group` VALUES (1,1),(4,1);
/*!40000 ALTER TABLE `core_user_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_account`
--

DROP TABLE IF EXISTS `payroll_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_account` (
  `id` varchar(5) CHARACTER SET latin1 NOT NULL,
  `payroll_year_ID` smallint(6) NOT NULL,
  `processing_order` smallint(6) NOT NULL DEFAULT '0',
  `sign` tinyint(1) NOT NULL,
  `print_account` tinyint(4) NOT NULL,
  `var_fields` tinyint(4) NOT NULL,
  `input_assignment` tinyint(4) NOT NULL,
  `output_assignment` tinyint(4) NOT NULL,
  `having_limits` tinyint(1) NOT NULL DEFAULT '0',
  `having_calculation` tinyint(1) NOT NULL DEFAULT '0',
  `having_rounding` tinyint(1) NOT NULL DEFAULT '0',
  `payroll_formula_ID` smallint(6) NOT NULL,
  `surcharge` decimal(12,5) NOT NULL,
  `factor` decimal(12,5) NOT NULL,
  `quantity` decimal(12,5) NOT NULL,
  `rate` decimal(12,5) NOT NULL,
  `amount` decimal(12,5) NOT NULL,
  `round_param` decimal(6,4) NOT NULL,
  `limits_aux_account_ID` varchar(5) CHARACTER SET latin1 NOT NULL,
  `limits_calc_mode` tinyint(4) NOT NULL,
  `max_limit` decimal(8,2) NOT NULL,
  `min_limit` decimal(8,2) NOT NULL,
  `deduction` decimal(8,2) NOT NULL,
  `quantity_conversion` decimal(9,4) NOT NULL DEFAULT '1.0000',
  `quantity_decimal` tinyint(4) NOT NULL DEFAULT '10',
  `quantity_print` tinyint(1) NOT NULL DEFAULT '1',
  `rate_conversion` decimal(9,4) NOT NULL DEFAULT '1.0000',
  `rate_decimal` tinyint(4) NOT NULL DEFAULT '10',
  `rate_print` tinyint(1) NOT NULL DEFAULT '1',
  `amount_conversion` decimal(9,4) NOT NULL DEFAULT '1.0000',
  `amount_decimal` tinyint(4) NOT NULL DEFAULT '10',
  `amount_print` tinyint(1) NOT NULL DEFAULT '1',
  `mandatory` tinyint(1) NOT NULL DEFAULT '1',
  `carry_over` tinyint(4) NOT NULL DEFAULT '0',
  `insertion_rules` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`payroll_year_ID`),
  KEY `fk_payroll_account_payroll_year1_idx` (`payroll_year_ID`),
  CONSTRAINT `fk_payroll_account_payroll_year1_idx` FOREIGN KEY (`payroll_year_ID`) REFERENCES `payroll_year` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_account`
--

LOCK TABLES `payroll_account` WRITE;
/*!40000 ALTER TABLE `payroll_account` DISABLE KEYS */;
INSERT INTO `payroll_account` VALUES ('1000',2011,1,0,1,4,0,0,0,0,0,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1001',2011,1,0,1,4,0,0,0,0,0,1,0.00000,0.00000,0.00000,0.00000,0.00000,0.0000,'0',0,0.00,0.00,0.00,1.0000,10,1,1.0000,10,1,1.0000,10,1,0,0,0),('1005',2011,1,0,1,3,3,5,0,1,1,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1007',2011,1,0,1,3,3,5,0,1,1,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1010',2011,1,0,1,1,0,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1015',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1016',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1017',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1018',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1021',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1030',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1031',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1032',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1050',2011,1,0,1,4,0,0,0,0,0,1,0.00000,0.00000,0.00000,0.00000,0.00000,0.0000,'0',0,0.00,0.00,0.00,1.0000,10,1,1.0000,10,1,1.0000,10,1,0,0,0),('1055',2011,1,0,1,3,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1061',2011,2,0,3,1,0,5,0,1,1,3,0.00000,1.25000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,10,1,1.0000,10,1,1.0000,10,1,1,0,0),('1065',2011,1,0,1,3,3,5,0,1,1,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1071',2011,1,0,3,3,0,5,0,1,1,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,10,1,1.0000,10,1,1.0000,10,1,0,0,0),('1072',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1073',2011,1,0,1,3,0,5,0,1,1,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,10,1,1.0000,10,1,1.0000,10,1,0,0,0),('1074',2011,1,0,1,3,0,5,0,1,1,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,10,1,1.0000,10,1,1.0000,10,1,0,0,0),('1075',2011,1,0,1,3,0,5,0,1,1,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,10,1,1.0000,10,1,1.0000,10,1,0,0,0),('1076',2011,1,0,1,3,0,5,0,1,1,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,10,1,1.0000,10,1,1.0000,10,1,0,0,0),('1101',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1102',2011,1,0,1,3,0,5,0,1,1,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,10,1,1.0000,10,1,1.0000,10,1,0,0,0),('1112',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1160',2011,4,0,1,2,0,5,0,1,1,8,0.00000,0.00000,0.00000,0.08330,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,1),('1161',2011,4,0,1,2,0,5,0,1,1,8,0.00000,0.00000,0.00000,0.03580,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,1),('1162',2011,1,0,1,3,0,5,0,1,1,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,10,1,1.0000,10,1,1.0000,10,1,0,0,0),('1200',2011,1,0,1,3,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1201',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1202',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1210',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1211',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1212',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1213',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1214',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1215',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1216',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1217',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1218',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1219',2011,1,0,1,4,0,0,0,0,0,1,0.00000,0.00000,0.00000,0.00000,0.00000,0.0000,'0',0,0.00,0.00,0.00,1.0000,10,1,1.0000,10,1,1.0000,10,1,0,0,0),('1230',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1231',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1232',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1250',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1310',2011,1,0,1,2,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1330',2011,1,0,1,2,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1410',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1500',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1501',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1503',2011,1,0,1,3,0,5,0,1,1,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,10,1,1.0000,10,1,1.0000,10,1,0,0,0),('1902',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1910',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1950',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1961',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1962',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1971',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1972',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1973',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1975',2011,1,0,1,3,0,5,0,1,1,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,10,1,1.0000,10,1,1.0000,10,1,0,0,0),('1976',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1977',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1980',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('1999',2011,1,0,0,2,0,5,0,1,0,9,0.00000,0.00000,0.00000,0.00000,10.00000,0.0000,'0',0,0.00,0.00,0.00,1.0000,10,1,1.0000,10,1,1.0000,10,1,1,5,0),('2000',2011,1,0,1,3,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('2005',2011,1,0,1,3,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('2010',2011,1,0,1,3,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('2015',2011,1,0,1,3,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('2020',2011,1,0,1,3,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('2025',2011,1,0,1,3,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('2030',2011,1,0,1,3,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('2035',2011,1,0,1,3,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('2050',2011,1,1,1,1,0,5,0,1,0,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('2060',2011,1,1,1,3,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('2065',2011,1,0,1,3,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('2070',2011,1,0,1,3,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('2075',2011,1,0,1,3,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('3000',2011,1,0,1,4,0,0,0,0,0,1,0.00000,0.00000,0.00000,0.00000,0.00000,0.0000,'0',0,0.00,0.00,0.00,1.0000,10,1,1.0000,10,1,1.0000,10,1,0,0,0),('3030',2011,1,0,1,3,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('3031',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('3032',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('3034',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('5000',2011,5,0,1,0,0,0,0,0,0,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,1,0,0),('5010',2011,6,1,2,0,0,5,0,1,1,3,0.00000,0.01000,0.00000,0.05050,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('5013',2011,7,0,3,0,0,0,0,0,0,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('5020',2011,7,1,0,0,3,5,1,1,1,3,0.00000,0.01000,0.00000,0.01000,0.00000,0.0500,'9120',0,106800.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('5023',2011,8,0,0,0,0,0,0,0,0,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('5025',2011,7,1,0,0,3,5,1,1,1,3,0.00000,0.01000,0.00000,0.00500,0.00000,0.0500,'9120',0,267000.00,106800.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('5030',2011,8,1,0,0,0,5,0,1,1,3,0.00000,0.01000,0.00000,0.01460,0.00000,0.0500,'0',0,106800.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('5031',2011,8,1,0,0,0,5,0,1,1,3,0.00000,0.01000,0.00000,0.01620,0.00000,0.0500,'0',0,106800.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('5032',2011,8,1,0,0,0,5,0,1,1,3,0.00000,0.01000,0.00000,0.01190,0.00000,0.0500,'0',0,106800.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('5035',2011,7,0,3,0,3,5,1,1,1,4,0.00000,0.01000,0.00000,0.60900,0.00000,0.0500,'9130',0,106800.00,0.00,0.00,1.0000,10,0,1.0000,10,0,1.0000,10,0,0,0,0),('5036',2011,7,0,3,0,3,5,1,1,1,4,0.00000,0.01000,0.00000,0.94000,0.00000,0.0500,'9131',0,106800.00,0.00,0.00,1.0000,10,0,1.0000,10,0,1.0000,10,0,0,0,0),('5037',2011,7,0,3,0,3,5,1,1,1,4,0.00000,0.01000,0.00000,0.53000,0.00000,0.0500,'9132',0,106800.00,0.00,0.00,1.0000,10,0,1.0000,10,0,1.0000,10,0,0,0,0),('5039',2011,9,0,0,0,0,5,0,1,0,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0000,'0',0,0.00,0.00,0.00,1.0000,10,0,1.0000,10,0,1.0000,10,0,0,0,0),('5040',2011,7,1,0,0,3,5,1,1,1,3,0.00000,0.01000,0.00000,0.00201,0.00000,0.0500,'9140',0,106800.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('5041',2011,7,1,0,0,3,5,1,1,1,3,0.00000,0.01000,0.00000,0.00634,0.00000,0.0500,'9141',0,300000.00,106800.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('5050',2011,7,1,0,0,3,5,1,1,1,3,0.00000,0.01000,0.00000,0.00543,0.00000,0.0500,'9150',0,200000.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('5051',2011,7,1,0,0,3,5,1,1,1,3,0.00000,0.01000,0.00000,0.00678,0.00000,0.0500,'9151',0,500000.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('5060',2011,6,1,0,0,0,5,0,1,1,3,0.00000,0.01000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('5065',2011,1,1,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('5070',2011,1,1,1,0,0,0,0,0,0,1,0.00000,0.00000,0.00000,0.00000,0.00000,0.0000,'0',0,0.00,0.00,0.00,1.0000,10,1,1.0000,10,1,1.0000,10,1,1,0,0),('5100',2011,2,1,1,1,0,5,0,1,1,8,0.00000,0.00000,0.00000,-1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('5110',2011,2,1,0,1,0,5,0,1,1,8,0.00000,0.00000,0.00000,-1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('5111',2011,2,1,1,1,0,5,0,1,1,8,0.00000,0.00000,0.00000,-1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('5112',2011,2,1,1,1,0,5,0,1,1,8,0.00000,0.00000,0.00000,-1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('5930',2011,1,0,0,5,0,0,0,0,0,1,0.00000,0.00000,0.00000,0.00000,0.00000,0.0000,'0',0,0.00,0.00,0.00,1.0000,10,0,1.0000,10,0,1.0000,10,0,0,0,0),('6000',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('6001',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('6002',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('6010',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('6020',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('6030',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('6040',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('6050',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('6060',2011,1,0,1,4,0,0,0,0,0,1,0.00000,0.00000,0.00000,0.00000,0.00000,0.0000,'0',0,0.00,0.00,0.00,1.0000,10,1,1.0000,10,1,1.0000,10,1,0,0,0),('6070',2011,1,0,1,1,3,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('6500',2011,10,0,0,3,0,0,0,0,1,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,1,0,0),('6510',2011,1,1,1,4,0,0,0,0,0,1,0.00000,0.00000,0.00000,0.00000,0.00000,0.0000,'0',0,0.00,0.00,0.00,1.0000,10,1,1.0000,10,1,1.0000,10,1,0,0,0),('8000',2011,11,0,1,0,0,0,0,0,0,1,0.00000,0.00000,0.00000,0.00000,0.00000,0.0000,'0',0,0.00,0.00,0.00,1.0000,10,0,1.0000,10,0,1.0000,10,1,1,0,0),('9010',2011,5,0,1,0,0,0,0,0,1,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,1,0,0),('9015',2011,7,0,1,0,0,0,0,0,0,9,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,1,0,0),('9019',2011,8,0,1,0,0,0,0,0,0,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,1,0,0),('9020',2011,5,0,1,0,0,0,0,0,1,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,1,0,0),('9025',2011,8,0,1,0,0,0,0,0,0,9,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,1,0,0),('9027',2011,8,0,1,0,0,0,0,0,0,9,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,1,0,0),('9029',2011,9,0,2,0,0,0,0,0,0,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,1,0,0),('9030',2011,5,0,1,0,0,0,0,0,1,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,1,0,0),('9035',2011,8,0,1,0,0,0,0,0,0,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,1,0,0),('9040',2011,5,0,1,0,0,0,0,0,1,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,1,0,0),('9045',2011,8,0,1,0,0,0,0,0,0,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,1,0,0),('9050',2011,5,0,1,0,0,0,0,0,1,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,1,0,0),('9055',2011,8,0,1,0,0,0,0,0,0,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,1,0,0),('9060',2011,5,0,1,0,0,0,0,0,1,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,1,0,0),('9065',2011,7,0,1,0,0,5,0,1,1,8,0.00000,0.00000,0.00000,1.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,1,0,0),('9070',2011,5,0,2,0,0,0,0,0,1,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,1,0,0),('9090',2011,3,0,1,0,0,0,0,0,1,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,1,0,0),('9120',2011,6,0,0,0,0,0,0,0,0,1,0.00000,0.00000,0.00000,0.00000,0.00000,0.0000,'0',0,0.00,0.00,0.00,1.0000,10,0,1.0000,10,0,1.0000,10,0,0,0,0),('9130',2011,6,0,0,0,0,0,0,0,0,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('9131',2011,6,0,0,0,0,0,0,0,0,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('9132',2011,6,0,0,0,0,0,0,0,0,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('9140',2011,6,0,0,0,0,0,0,0,0,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('9141',2011,6,0,0,0,0,0,0,0,0,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('9150',2011,6,0,0,0,0,0,0,0,0,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('9151',2011,6,0,0,0,0,0,0,0,0,8,0.00000,0.00000,0.00000,0.00000,0.00000,0.0500,'0',0,0.00,0.00,0.00,1.0000,2,1,1.0000,2,1,1.0000,2,1,0,0,0),('9999',2011,1,0,0,0,0,0,0,0,0,1,0.00000,0.00000,0.00000,0.00000,0.00000,0.0000,'0',0,0.00,0.00,0.00,1.0000,10,1,1.0000,10,1,1.0000,10,1,0,0,0);
/*!40000 ALTER TABLE `payroll_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_account_label`
--

DROP TABLE IF EXISTS `payroll_account_label`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_account_label` (
  `payroll_account_ID` varchar(5) CHARACTER SET latin1 NOT NULL,
  `payroll_year_ID` smallint(6) NOT NULL,
  `language` varchar(2) CHARACTER SET latin1 NOT NULL,
  `label` varchar(50) CHARACTER SET latin1 NOT NULL,
  `quantity_unit` varchar(10) NOT NULL,
  `rate_unit` varchar(10) NOT NULL,
  KEY `fk_payroll_account_label_payroll_account1_idx` (`payroll_account_ID`,`payroll_year_ID`),
  CONSTRAINT `fk_payroll_account_label_payroll_account1` FOREIGN KEY (`payroll_account_ID`, `payroll_year_ID`) REFERENCES `payroll_account` (`id`, `payroll_year_ID`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_account_label`
--

LOCK TABLES `payroll_account_label` WRITE;
/*!40000 ALTER TABLE `payroll_account_label` DISABLE KEYS */;
INSERT INTO `payroll_account_label` VALUES ('1005',2011,'de','Stundenlohn','','Std.'),('1007',2011,'de','Wochenlohn','','Woche'),('1015',2011,'de','Aushilfslohn','',''),('1016',2011,'de','Heimarbeitlohn','',''),('1017',2011,'de','Reinigungslohn','',''),('1018',2011,'de','Akkordlohn','',''),('1021',2011,'de','Behörde- und Kommissionsmitglieder','',''),('1030',2011,'de','Dienstalterszulage','',''),('1031',2011,'de','Funktionszulage','',''),('1032',2011,'de','Stellvertretungszulage','',''),('1055',2011,'de','Wegentschädigung','',''),('1065',2011,'de','Ueberzeit','','Std.'),('1072',2011,'de','Einsatzzulage','',''),('1101',2011,'de','Erschwerniszulage','',''),('1112',2011,'de','Durchhalteprämie','',''),('1160',2011,'de','Ferienvergütung','','\\%'),('1161',2011,'de','Feiertagsentschädigung','','\\%'),('1200',2011,'de','13. Monatslohn','',''),('1201',2011,'de','Gratifikation','',''),('1202',2011,'de','Weihnachtszulage','',''),('1210',2011,'de','Bonuszahlung','',''),('1211',2011,'de','Gewinnbeteiligung','',''),('1212',2011,'de','Sonderzulage','',''),('1213',2011,'de','Erfolgsprämie','',''),('1214',2011,'de','Leistungsprämie','',''),('1215',2011,'de','Anerkennungsprämie','',''),('1216',2011,'de','Verbesserungsvorschläge','',''),('1217',2011,'de','Umsatzprämie','',''),('1218',2011,'de','Provision','',''),('1230',2011,'de','Dienstaltersgeschenke','',''),('1231',2011,'de','Jubiläumsgeschenke','',''),('1232',2011,'de','Treueprämie','',''),('1250',2011,'de','Schadenverhütungsprämie','',''),('1310',2011,'de','Anzahl gearbeitete Stunden','','Std'),('1330',2011,'de','Anzahl Lektionen','','Lek'),('1410',2011,'de','Kapitalleistung mit Vorsorgecharakter','',''),('1500',2011,'de','Verwaltungsratshonorar','',''),('1501',2011,'de','Verwaltungsratsentschädigung','',''),('1902',2011,'de','Gratiswohnung','',''),('1910',2011,'de','Privatanteil Geschäftswagen','',''),('1950',2011,'de','Verbilligung Mietwohnung','',''),('1961',2011,'de','Arbeitnehmeraktien','',''),('1962',2011,'de','Mitarbeiteroptionen','',''),('1971',2011,'de','Vom AG übern. AN-Anteil KTG','',''),('1972',2011,'de','Vom AG übern. AN-Anteil BVG','',''),('1973',2011,'de','Vom AG übern. AN-Anteil Einkauf BVG','',''),('1976',2011,'de','Vom Arbeitgeber übern. Säule 3b','',''),('1977',2011,'de','Vom Arbeitgeber übern. Säule 3a','',''),('1980',2011,'de','Weiterbildung (Lohnausweis)','',''),('2000',2011,'de','EO-Taggeld','','Tage'),('2005',2011,'de','Militärdienstkasse (MDK)','',''),('2010',2011,'de','Militärergänzungskasse (MEK)','',''),('2015',2011,'de','Parifonds','',''),('2020',2011,'de','MV-Taggeld','',''),('2025',2011,'de','IV-Taggeld','',''),('2030',2011,'de','Unfall-Taggeld','',''),('2035',2011,'de','Kranken-Taggeld','',''),('2060',2011,'de','Lohnabzug KA/SW (ML)','',''),('2065',2011,'de','Lohnausfall KA/SW (SL)','',''),('2070',2011,'de','ALV-Entschädigung','',''),('2075',2011,'de','Karenztag KA/SW','',''),('3030',2011,'de','Familienzulage','',''),('3031',2011,'de','Haushaltszulage','',''),('3032',2011,'de','Geburtszulage','',''),('3034',2011,'de','Betreuungszulage','',''),('5065',2011,'de','BVG-Einkaufs-Beiträge','',''),('9065',2011,'de','BVG-Lohn','',''),('9045',2011,'de','UVGZ-Lohn','',''),('9055',2011,'de','KTG-Lohn','',''),('9010',2011,'de','AHV-Basis','',''),('9030',2011,'de','UVG-Basis','',''),('9040',2011,'de','UVGZ-Basis','',''),('9050',2011,'de','KTG-Basis','',''),('9060',2011,'de','BVG-Basis','',''),('9070',2011,'de','QST-Basis','',''),('9090',2011,'de','Ferien-Basis','',''),('9140',2011,'de','UVGZ1 effektiv','',''),('9141',2011,'de','UVGZ2 effektiv','',''),('9150',2011,'de','KTG1 effektiv','',''),('9151',2011,'de','KTG2 effektiv','',''),('6500',2011,'de','Nettolohn','',''),('5039',2011,'de','UVG-Nettolohn-Link','',''),('9120',2011,'de','ALV effektiv','',''),('9020',2011,'de','ALV-Basis','',''),('5050',2011,'de','KTG1-Abzug','',''),('5051',2011,'de','KTG2-Abzug','',''),('5040',2011,'de','UVGZ1-Abzug','',''),('5041',2011,'de','UVGZ2-Abzug','',''),('5010',2011,'de','AHV-Beitrag','',''),('5020',2011,'de','ALV-Beitrag','',''),('5025',2011,'de','ALVZ-Beitrag','',''),('5060',2011,'de','BVG-Beitrag','',''),('8000',2011,'de','Auszahlung','',''),('9035',2011,'de','UVG-Lohn','',''),('5030',2011,'de','UVG1-Beitrag (NBUV)','',''),('5035',2011,'de','UVG1 AG-Beitrag (BUV)','',''),('5036',2011,'de','UVG2 AG-Beitrag (BUV)','',''),('5031',2011,'de','UVG2-Beitrag (NBUV)','',''),('5032',2011,'de','UVG3-Beitrag (NBUV)','',''),('5037',2011,'de','UVG3 AG-Beitrag (BUV)','',''),('5110',2011,'de','Ausgleich geldwerte Vorteile','',''),('6000',2011,'de','Reisespesen','',''),('6010',2011,'de','Übernachtungsspesen','',''),('6040',2011,'de','Pauschale Repräsentationsspesen','',''),('5100',2011,'de','Ausgleich Naturalleistungen','',''),('5111',2011,'de','Ausgleich BVG-Beiträge AG','',''),('5112',2011,'de','Ausgleich BVG-Einkauf AG','',''),('6001',2011,'de','Autospesen','',''),('6002',2011,'de','Verpflegungsspesen','',''),('6020',2011,'de','Effektive Spesen Expatriates','',''),('6030',2011,'de','Übrige effektive Spesen','',''),('6050',2011,'de','Pauschale Autospesen','',''),('6070',2011,'de','Übrige Pauschalspesen','',''),('9027',2011,'de','ALVZ-Lohn','',''),('9025',2011,'de','ALV-Lohn','',''),('9015',2011,'de','AHV-Lohn','',''),('9019',2011,'de','Nicht AHV-pflichtig','',''),('5013',2011,'de','AHV-Rückstellung AG','',''),('5023',2011,'de','ALV-Rückstellung AG','',''),('9029',2011,'de','Nicht ALV-pflichtig','',''),('1999',2011,'de','Test Saldo-LOA','',''),('5930',2011,'de','QST-Berechnung Tage','',''),('9130',2011,'de','UVG1 effektiv','',''),('9131',2011,'de','UVG2 effektiv','',''),('9132',2011,'de','UVG3 effektiv','',''),('9999',2011,'de','Durchschn. Std-Lohn','',''),('1010',2011,'de','Honorare','',''),('1000',2011,'de','Monatslohn','',''),('2050',2011,'de','Korrektur Taggelder','',''),('1050',2011,'de','Wohnungszulage','',''),('1061',2011,'de','Überstunden 125%','Std.','Std-Lohn'),('1071',2011,'de','Pikettentschädigung','',''),('1073',2011,'de','Sonntagszulage','',''),('1074',2011,'de','Inkonvenienzzulage','',''),('1075',2011,'de','Nachtdienstzulage','',''),('1076',2011,'de','Nachtzulage','',''),('1102',2011,'de','Schmutzzulage','',''),('1162',2011,'de','Ferienauszahlung','',''),('1219',2011,'de','Präsenzprämie','',''),('1503',2011,'de','Sitzungsgelder VR','',''),('1975',2011,'de','Vom AG übern. AN-Anteil UVGZ','',''),('3000',2011,'de','Kinderzulage','',''),('5000',2011,'de','Bruttolohn','',''),('5070',2011,'de','QST-Abzug','',''),('6060',2011,'de','Pauschalspesen Expatriates','',''),('6510',2011,'de','Vorauszahlung','',''),('1001',2011,'de','Gehalt','','');
/*!40000 ALTER TABLE `payroll_account_label` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_account_linker`
--

DROP TABLE IF EXISTS `payroll_account_linker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_account_linker` (
  `payroll_account_ID` varchar(5) NOT NULL,
  `payroll_year_ID` smallint(6) NOT NULL,
  `payroll_child_account_ID` varchar(5) NOT NULL,
  `field_assignment` tinyint(4) NOT NULL,
  `fwd_neg_values` tinyint(1) NOT NULL DEFAULT '1',
  `invert_value` tinyint(1) NOT NULL DEFAULT '0',
  `child_account_field` tinyint(4) NOT NULL DEFAULT '0',
  KEY `fk_table3_payroll_account1_idx` (`payroll_account_ID`,`payroll_year_ID`),
  KEY `child_account` (`payroll_child_account_ID`) USING HASH,
  CONSTRAINT `fk_table3_payroll_account1` FOREIGN KEY (`payroll_account_ID`, `payroll_year_ID`) REFERENCES `payroll_account` (`id`, `payroll_year_ID`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_account_linker`
--

LOCK TABLES `payroll_account_linker` WRITE;
/*!40000 ALTER TABLE `payroll_account_linker` DISABLE KEYS */;
INSERT INTO `payroll_account_linker` VALUES ('1005',2011,'9010',5,1,0,5),('1007',2011,'9010',5,1,0,5),('1015',2011,'9010',5,1,0,5),('1016',2011,'9010',5,1,0,5),('1017',2011,'9010',5,1,0,5),('1018',2011,'9010',5,1,0,5),('1021',2011,'9010',5,1,0,5),('1030',2011,'9010',5,1,0,5),('1031',2011,'9010',5,1,0,5),('1032',2011,'9010',5,1,0,5),('1055',2011,'9010',5,1,0,5),('1065',2011,'9010',5,1,0,5),('1072',2011,'9010',5,1,0,5),('1101',2011,'9010',5,1,0,5),('1112',2011,'9010',5,1,0,5),('1160',2011,'9010',5,1,0,5),('1161',2011,'9010',5,1,0,5),('1200',2011,'9010',5,1,0,5),('1201',2011,'9010',5,1,0,5),('1202',2011,'9010',5,1,0,5),('1210',2011,'9010',5,1,0,5),('1211',2011,'9010',5,1,0,5),('1212',2011,'9010',5,1,0,5),('1213',2011,'9010',5,1,0,5),('1214',2011,'9010',5,1,0,5),('1215',2011,'9010',5,1,0,5),('1216',2011,'9010',5,1,0,5),('1217',2011,'9010',5,1,0,5),('1218',2011,'9010',5,1,0,5),('1230',2011,'9010',5,1,0,5),('1231',2011,'9010',5,1,0,5),('1232',2011,'9010',5,1,0,5),('1250',2011,'9010',5,1,0,5),('1500',2011,'9010',5,1,0,5),('1501',2011,'9010',5,1,0,5),('1902',2011,'9010',5,1,0,5),('1910',2011,'9010',5,1,0,5),('1950',2011,'9010',5,1,0,5),('1961',2011,'9010',5,1,0,5),('1962',2011,'9010',5,1,0,5),('1972',2011,'9010',5,1,0,5),('1973',2011,'9010',5,1,0,5),('1976',2011,'9010',5,1,0,5),('1977',2011,'9010',5,1,0,5),('2000',2011,'9010',5,1,0,5),('2005',2011,'9010',5,1,0,5),('2010',2011,'9010',5,1,0,5),('2015',2011,'9010',5,1,0,5),('2020',2011,'9010',5,1,0,5),('2025',2011,'9010',5,1,0,5),('2065',2011,'9010',5,1,0,5),('1005',2011,'9030',5,1,0,5),('1007',2011,'9030',5,1,0,5),('1015',2011,'9030',5,1,0,5),('1016',2011,'9030',5,1,0,5),('1017',2011,'9030',5,1,0,5),('1018',2011,'9030',5,1,0,5),('1021',2011,'9030',5,1,0,5),('1030',2011,'9030',5,1,0,5),('1031',2011,'9030',5,1,0,5),('1032',2011,'9030',5,1,0,5),('1055',2011,'9030',5,1,0,5),('1065',2011,'9030',5,1,0,5),('1072',2011,'9030',5,1,0,5),('1101',2011,'9030',5,1,0,5),('1112',2011,'9030',5,1,0,5),('1160',2011,'9030',5,1,0,5),('1161',2011,'9030',5,1,0,5),('1200',2011,'9030',5,1,0,5),('1201',2011,'9030',5,1,0,5),('1202',2011,'9030',5,1,0,5),('1210',2011,'9030',5,1,0,5),('1211',2011,'9030',5,1,0,5),('1212',2011,'9030',5,1,0,5),('1213',2011,'9030',5,1,0,5),('1214',2011,'9030',5,1,0,5),('1215',2011,'9030',5,1,0,5),('1216',2011,'9030',5,1,0,5),('1217',2011,'9030',5,1,0,5),('1218',2011,'9030',5,1,0,5),('1230',2011,'9030',5,1,0,5),('1231',2011,'9030',5,1,0,5),('1232',2011,'9030',5,1,0,5),('1250',2011,'9030',5,1,0,5),('1500',2011,'9030',5,1,0,5),('1501',2011,'9030',5,1,0,5),('1902',2011,'9030',5,1,0,5),('1910',2011,'9030',5,1,0,5),('1950',2011,'9030',5,1,0,5),('1961',2011,'9030',5,1,0,5),('1962',2011,'9030',5,1,0,5),('1972',2011,'9030',5,1,0,5),('1973',2011,'9030',5,1,0,5),('1976',2011,'9030',5,1,0,5),('1977',2011,'9030',5,1,0,5),('2005',2011,'9030',5,1,0,5),('2010',2011,'9030',5,1,0,5),('2015',2011,'9030',5,1,0,5),('2065',2011,'9030',5,1,0,5),('1005',2011,'9040',5,1,0,5),('1007',2011,'9040',5,1,0,5),('1015',2011,'9040',5,1,0,5),('1016',2011,'9040',5,1,0,5),('1017',2011,'9040',5,1,0,5),('1018',2011,'9040',5,1,0,5),('1021',2011,'9040',5,1,0,5),('1030',2011,'9040',5,1,0,5),('1031',2011,'9040',5,1,0,5),('1032',2011,'9040',5,1,0,5),('1055',2011,'9040',5,1,0,5),('1065',2011,'9040',5,1,0,5),('1072',2011,'9040',5,1,0,5),('1101',2011,'9040',5,1,0,5),('1112',2011,'9040',5,1,0,5),('1160',2011,'9040',5,1,0,5),('1161',2011,'9040',5,1,0,5),('1200',2011,'9040',5,1,0,5),('1201',2011,'9040',5,1,0,5),('1202',2011,'9040',5,1,0,5),('1210',2011,'9040',5,1,0,5),('1211',2011,'9040',5,1,0,5),('1212',2011,'9040',5,1,0,5),('1213',2011,'9040',5,1,0,5),('1214',2011,'9040',5,1,0,5),('1215',2011,'9040',5,1,0,5),('1216',2011,'9040',5,1,0,5),('1217',2011,'9040',5,1,0,5),('1218',2011,'9040',5,1,0,5),('1230',2011,'9040',5,1,0,5),('1231',2011,'9040',5,1,0,5),('1232',2011,'9040',5,1,0,5),('1250',2011,'9040',5,1,0,5),('1500',2011,'9040',5,1,0,5),('1501',2011,'9040',5,1,0,5),('1902',2011,'9040',5,1,0,5),('1910',2011,'9040',5,1,0,5),('1950',2011,'9040',5,1,0,5),('1961',2011,'9040',5,1,0,5),('1962',2011,'9040',5,1,0,5),('1972',2011,'9040',5,1,0,5),('1973',2011,'9040',5,1,0,5),('1976',2011,'9040',5,1,0,5),('1977',2011,'9040',5,1,0,5),('2005',2011,'9040',5,1,0,5),('2010',2011,'9040',5,1,0,5),('2015',2011,'9040',5,1,0,5),('2065',2011,'9040',5,1,0,5),('1005',2011,'9050',5,1,0,5),('1007',2011,'9050',5,1,0,5),('1015',2011,'9050',5,1,0,5),('1016',2011,'9050',5,1,0,5),('1017',2011,'9050',5,1,0,5),('1018',2011,'9050',5,1,0,5),('1021',2011,'9050',5,1,0,5),('1030',2011,'9050',5,1,0,5),('1031',2011,'9050',5,1,0,5),('1032',2011,'9050',5,1,0,5),('1055',2011,'9050',5,1,0,5),('1065',2011,'9050',5,1,0,5),('1072',2011,'9050',5,1,0,5),('1101',2011,'9050',5,1,0,5),('1112',2011,'9050',5,1,0,5),('1160',2011,'9050',5,1,0,5),('1161',2011,'9050',5,1,0,5),('1200',2011,'9050',5,1,0,5),('1201',2011,'9050',5,1,0,5),('1202',2011,'9050',5,1,0,5),('1210',2011,'9050',5,1,0,5),('1211',2011,'9050',5,1,0,5),('1212',2011,'9050',5,1,0,5),('1213',2011,'9050',5,1,0,5),('1214',2011,'9050',5,1,0,5),('1215',2011,'9050',5,1,0,5),('1216',2011,'9050',5,1,0,5),('1217',2011,'9050',5,1,0,5),('1218',2011,'9050',5,1,0,5),('1230',2011,'9050',5,1,0,5),('1231',2011,'9050',5,1,0,5),('1232',2011,'9050',5,1,0,5),('1250',2011,'9050',5,1,0,5),('1500',2011,'9050',5,1,0,5),('1501',2011,'9050',5,1,0,5),('1902',2011,'9050',5,1,0,5),('1910',2011,'9050',5,1,0,5),('1950',2011,'9050',5,1,0,5),('1961',2011,'9050',5,1,0,5),('1962',2011,'9050',5,1,0,5),('1972',2011,'9050',5,1,0,5),('1973',2011,'9050',5,1,0,5),('1976',2011,'9050',5,1,0,5),('1977',2011,'9050',5,1,0,5),('2000',2011,'9050',5,1,0,5),('2005',2011,'9050',5,1,0,5),('2010',2011,'9050',5,1,0,5),('2015',2011,'9050',5,1,0,5),('2020',2011,'9050',5,1,0,5),('2025',2011,'9050',5,1,0,5),('2065',2011,'9050',5,1,0,5),('1005',2011,'9060',5,1,0,5),('1007',2011,'9060',5,1,0,5),('1015',2011,'9060',5,1,0,5),('1016',2011,'9060',5,1,0,5),('1017',2011,'9060',5,1,0,5),('1018',2011,'9060',5,1,0,5),('1021',2011,'9060',5,1,0,5),('1030',2011,'9060',5,1,0,5),('1031',2011,'9060',5,1,0,5),('1032',2011,'9060',5,1,0,5),('1055',2011,'9060',5,1,0,5),('1065',2011,'9060',5,1,0,5),('1072',2011,'9060',5,1,0,5),('1101',2011,'9060',5,1,0,5),('1112',2011,'9060',5,1,0,5),('1160',2011,'9060',5,1,0,5),('1161',2011,'9060',5,1,0,5),('1200',2011,'9060',5,1,0,5),('1201',2011,'9060',5,1,0,5),('1202',2011,'9060',5,1,0,5),('1210',2011,'9060',5,1,0,5),('1211',2011,'9060',5,1,0,5),('1212',2011,'9060',5,1,0,5),('1213',2011,'9060',5,1,0,5),('1214',2011,'9060',5,1,0,5),('1215',2011,'9060',5,1,0,5),('1216',2011,'9060',5,1,0,5),('1217',2011,'9060',5,1,0,5),('1218',2011,'9060',5,1,0,5),('1230',2011,'9060',5,1,0,5),('1231',2011,'9060',5,1,0,5),('1232',2011,'9060',5,1,0,5),('1250',2011,'9060',5,1,0,5),('1500',2011,'9060',5,1,0,5),('1501',2011,'9060',5,1,0,5),('1902',2011,'9060',5,1,0,5),('1910',2011,'9060',5,1,0,5),('1950',2011,'9060',5,1,0,5),('1961',2011,'9060',5,1,0,5),('1962',2011,'9060',5,1,0,5),('1972',2011,'9060',5,1,0,5),('1973',2011,'9060',5,1,0,5),('1976',2011,'9060',5,1,0,5),('1977',2011,'9060',5,1,0,5),('2000',2011,'9060',5,1,0,5),('2005',2011,'9060',5,1,0,5),('2010',2011,'9060',5,1,0,5),('2015',2011,'9060',5,1,0,5),('2020',2011,'9060',5,1,0,5),('2025',2011,'9060',5,1,0,5),('2065',2011,'9060',5,1,0,5),('1005',2011,'9070',5,1,0,5),('1007',2011,'9070',5,1,0,5),('1015',2011,'9070',5,1,0,5),('1016',2011,'9070',5,1,0,5),('1017',2011,'9070',5,1,0,5),('1018',2011,'9070',5,1,0,5),('1021',2011,'9070',5,1,0,5),('1030',2011,'9070',5,1,0,5),('1031',2011,'9070',5,1,0,5),('1032',2011,'9070',5,1,0,5),('1055',2011,'9070',5,1,0,5),('1065',2011,'9070',5,1,0,5),('1072',2011,'9070',5,1,0,5),('1101',2011,'9070',5,1,0,5),('1112',2011,'9070',5,1,0,5),('1160',2011,'9070',5,1,0,5),('1161',2011,'9070',5,1,0,5),('1200',2011,'9070',5,1,0,5),('1201',2011,'9070',5,1,0,5),('1202',2011,'9070',5,1,0,5),('1210',2011,'9070',5,1,0,5),('1211',2011,'9070',5,1,0,5),('1212',2011,'9070',5,1,0,5),('1213',2011,'9070',5,1,0,5),('1214',2011,'9070',5,1,0,5),('1215',2011,'9070',5,1,0,5),('1216',2011,'9070',5,1,0,5),('1217',2011,'9070',5,1,0,5),('1218',2011,'9070',5,1,0,5),('1230',2011,'9070',5,1,0,5),('1231',2011,'9070',5,1,0,5),('1232',2011,'9070',5,1,0,5),('1250',2011,'9070',5,1,0,5),('1410',2011,'9070',5,1,0,5),('1500',2011,'9070',5,1,0,5),('1501',2011,'9070',5,1,0,5),('1902',2011,'9070',5,1,0,5),('1910',2011,'9070',5,1,0,5),('1950',2011,'9070',5,1,0,5),('1961',2011,'9070',5,1,0,5),('1962',2011,'9070',5,1,0,5),('1971',2011,'9070',5,1,0,5),('1972',2011,'9070',5,1,0,5),('1973',2011,'9070',5,1,0,5),('1976',2011,'9070',5,1,0,5),('1977',2011,'9070',5,1,0,5),('2000',2011,'9070',5,1,0,5),('2005',2011,'9070',5,1,0,5),('2010',2011,'9070',5,1,0,5),('2015',2011,'9070',5,1,0,5),('2020',2011,'9070',5,1,0,5),('2025',2011,'9070',5,1,0,5),('2030',2011,'9070',5,1,0,5),('2035',2011,'9070',5,1,0,5),('2060',2011,'9070',5,1,0,5),('2070',2011,'9070',5,1,0,5),('2075',2011,'9070',5,1,0,5),('3030',2011,'9070',5,1,0,5),('3031',2011,'9070',5,1,0,5),('3032',2011,'9070',5,1,0,5),('3034',2011,'9070',5,1,0,5),('1005',2011,'9090',5,1,0,5),('1007',2011,'9090',5,1,0,5),('1015',2011,'9090',5,1,0,5),('1016',2011,'9090',5,1,0,5),('1017',2011,'9090',5,1,0,5),('1018',2011,'9090',5,1,0,5),('1021',2011,'9090',5,1,0,5),('1030',2011,'9090',5,1,0,5),('1031',2011,'9090',5,1,0,5),('1032',2011,'9090',5,1,0,5),('1055',2011,'9090',5,1,0,5),('1065',2011,'9090',5,1,0,5),('1072',2011,'9090',5,1,0,5),('1101',2011,'9090',5,1,0,5),('1200',2011,'9090',5,1,0,5),('1218',2011,'9090',5,1,0,5),('9090',2011,'1160',5,1,0,3),('9090',2011,'1161',5,1,0,3),('9040',2011,'9140',5,1,0,5),('9040',2011,'9141',5,1,0,5),('9050',2011,'9150',5,1,0,5),('9050',2011,'9151',5,1,0,5),('5065',2011,'6500',5,1,0,5),('5039',2011,'6500',5,1,0,5),('1005',2011,'9020',5,1,0,5),('1007',2011,'9020',5,1,0,5),('1015',2011,'9020',5,1,0,5),('1016',2011,'9020',5,1,0,5),('1017',2011,'9020',5,1,0,5),('1018',2011,'9020',5,1,0,5),('1021',2011,'9020',5,1,0,5),('1030',2011,'9020',5,1,0,5),('1031',2011,'9020',5,1,0,5),('1032',2011,'9020',5,1,0,5),('1055',2011,'9020',5,1,0,5),('1065',2011,'9020',5,1,0,5),('1072',2011,'9020',5,1,0,5),('1101',2011,'9020',5,1,0,5),('1112',2011,'9020',5,1,0,5),('1160',2011,'9020',5,1,0,5),('1161',2011,'9020',5,1,0,5),('1200',2011,'9020',5,1,0,5),('1201',2011,'9020',5,1,0,5),('1202',2011,'9020',5,1,0,5),('1210',2011,'9020',5,1,0,5),('1211',2011,'9020',5,1,0,5),('1212',2011,'9020',5,1,0,5),('1213',2011,'9020',5,1,0,5),('1214',2011,'9020',5,1,0,5),('1215',2011,'9020',5,1,0,5),('1216',2011,'9020',5,1,0,5),('1217',2011,'9020',5,1,0,5),('1218',2011,'9020',5,1,0,5),('1230',2011,'9020',5,1,0,5),('1231',2011,'9020',5,1,0,5),('1232',2011,'9020',5,1,0,5),('1250',2011,'9020',5,1,0,5),('1500',2011,'9020',5,1,0,5),('1501',2011,'9020',5,1,0,5),('1902',2011,'9020',5,1,0,5),('1910',2011,'9020',5,1,0,5),('1950',2011,'9020',5,1,0,5),('1961',2011,'9020',5,1,0,5),('1962',2011,'9020',5,1,0,5),('1972',2011,'9020',5,1,0,5),('1973',2011,'9020',5,1,0,5),('1976',2011,'9020',5,1,0,5),('1977',2011,'9020',5,1,0,5),('2000',2011,'9020',5,1,0,5),('2005',2011,'9020',5,1,0,5),('2010',2011,'9020',5,1,0,5),('2015',2011,'9020',5,1,0,5),('2020',2011,'9020',5,1,0,5),('2025',2011,'9020',5,1,0,5),('2065',2011,'9020',5,1,0,5),('9020',2011,'9120',5,1,0,5),('9150',2011,'5050',5,1,0,0),('5050',2011,'9055',3,1,0,5),('5050',2011,'6500',5,1,0,5),('9151',2011,'5051',5,1,0,0),('5051',2011,'9055',3,1,0,5),('5051',2011,'6500',5,1,0,5),('9140',2011,'5040',5,1,0,0),('5040',2011,'9045',3,1,0,5),('5040',2011,'6500',5,1,0,5),('9141',2011,'5041',5,1,0,0),('5041',2011,'9045',3,1,0,5),('5041',2011,'6500',5,1,0,5),('9010',2011,'5010',5,1,0,3),('5010',2011,'6500',5,1,0,5),('9120',2011,'5020',5,1,0,0),('5020',2011,'6500',5,1,0,5),('9120',2011,'5025',5,1,0,0),('5025',2011,'6500',5,1,0,5),('9060',2011,'5060',5,1,0,3),('5060',2011,'9065',5,1,0,3),('5060',2011,'6500',5,1,0,5),('6500',2011,'8000',5,1,0,5),('5030',2011,'5039',5,1,0,3),('5035',2011,'9035',3,1,0,5),('5035',2011,'5030',3,1,0,3),('5036',2011,'9035',3,1,0,5),('5036',2011,'5031',3,1,0,3),('5031',2011,'5039',5,1,0,3),('5032',2011,'5039',5,1,0,3),('5037',2011,'9035',3,1,0,5),('5037',2011,'5032',3,1,0,3),('1910',2011,'5110',5,1,0,3),('5110',2011,'6500',5,1,0,5),('6000',2011,'8000',5,1,0,5),('6010',2011,'8000',5,1,0,5),('6040',2011,'8000',5,1,0,5),('1950',2011,'5100',5,1,0,3),('5100',2011,'6500',5,1,0,5),('1972',2011,'5111',5,1,0,3),('5111',2011,'6500',5,1,0,5),('1973',2011,'5112',5,1,0,3),('5112',2011,'6500',5,1,0,5),('6001',2011,'8000',5,1,0,5),('6002',2011,'8000',5,1,0,5),('6020',2011,'8000',5,1,0,5),('6030',2011,'8000',5,1,0,5),('6050',2011,'8000',5,1,0,5),('6070',2011,'8000',5,1,0,5),('5025',2011,'9027',3,1,0,5),('5020',2011,'9025',3,1,0,5),('5010',2011,'9015',3,1,0,5),('9010',2011,'9019',5,1,0,5),('9015',2011,'9019',5,1,1,5),('5010',2011,'5013',5,1,1,5),('5020',2011,'5023',5,1,1,5),('9020',2011,'9029',5,1,0,5),('9027',2011,'9029',5,1,1,5),('9025',2011,'9029',5,1,1,5),('9030',2011,'9130',5,1,0,5),('9130',2011,'5035',5,1,0,0),('9030',2011,'9131',5,1,0,5),('9131',2011,'5036',5,1,0,0),('9030',2011,'9132',5,1,0,5),('9132',2011,'5037',5,1,0,0),('1010',2011,'9010',5,1,0,5),('1010',2011,'9030',5,1,0,5),('1010',2011,'9040',5,1,0,5),('1010',2011,'9050',5,1,0,5),('1010',2011,'9060',5,1,0,5),('1010',2011,'9070',5,1,0,5),('1010',2011,'9090',5,1,0,5),('1010',2011,'9020',5,1,0,5),('1000',2011,'9010',5,1,0,5),('1000',2011,'9030',5,1,0,5),('1000',2011,'9040',5,1,0,5),('1000',2011,'9050',5,1,0,5),('1000',2011,'9060',5,1,0,5),('1000',2011,'9070',5,1,0,5),('1000',2011,'9090',5,1,0,5),('1000',2011,'9020',5,1,0,5),('2050',2011,'9010',5,1,0,5),('2050',2011,'9030',5,1,0,5),('2050',2011,'9040',5,1,0,5),('2050',2011,'9050',5,1,0,5),('2050',2011,'9060',5,1,0,5),('2050',2011,'9070',5,1,0,5),('2050',2011,'9020',5,1,0,5),('1050',2011,'9010',5,1,0,5),('1050',2011,'9030',5,1,0,5),('1050',2011,'9040',5,1,0,5),('1050',2011,'9050',5,1,0,5),('1050',2011,'9060',5,1,0,5),('1050',2011,'9070',5,1,0,5),('1050',2011,'9020',5,1,0,5),('9999',2011,'1061',5,1,0,4),('1061',2011,'9010',5,1,0,5),('1061',2011,'9020',5,1,0,5),('1061',2011,'9030',5,1,0,5),('1061',2011,'9040',5,1,0,5),('1061',2011,'9050',5,1,0,5),('1061',2011,'9060',5,1,0,5),('1061',2011,'9070',5,1,0,5),('1061',2011,'9090',5,1,0,5),('1071',2011,'9010',5,1,0,5),('1071',2011,'9020',5,1,0,5),('1071',2011,'9030',5,1,0,5),('1071',2011,'9040',5,1,0,5),('1071',2011,'9050',5,1,0,5),('1071',2011,'9060',5,1,0,5),('1071',2011,'9070',5,1,0,5),('1073',2011,'9010',5,1,0,5),('1073',2011,'9020',5,1,0,5),('1073',2011,'9030',5,1,0,5),('1073',2011,'9040',5,1,0,5),('1073',2011,'9050',5,1,0,5),('1073',2011,'9060',5,1,0,5),('1073',2011,'9070',5,1,0,5),('1074',2011,'9010',5,1,0,5),('1074',2011,'9020',5,1,0,5),('1074',2011,'9030',5,1,0,5),('1074',2011,'9040',5,1,0,5),('1074',2011,'9050',5,1,0,5),('1074',2011,'9060',5,1,0,5),('1074',2011,'9070',5,1,0,5),('1075',2011,'9010',5,1,0,5),('1075',2011,'9020',5,1,0,5),('1075',2011,'9030',5,1,0,5),('1075',2011,'9040',5,1,0,5),('1075',2011,'9050',5,1,0,5),('1075',2011,'9060',5,1,0,5),('1075',2011,'9070',5,1,0,5),('1076',2011,'9010',5,1,0,5),('1076',2011,'9020',5,1,0,5),('1076',2011,'9030',5,1,0,5),('1076',2011,'9040',5,1,0,5),('1076',2011,'9050',5,1,0,5),('1076',2011,'9060',5,1,0,5),('1076',2011,'9070',5,1,0,5),('1102',2011,'9010',5,1,0,5),('1102',2011,'9020',5,1,0,5),('1102',2011,'9030',5,1,0,5),('1102',2011,'9040',5,1,0,5),('1102',2011,'9050',5,1,0,5),('1102',2011,'9060',5,1,0,5),('1102',2011,'9070',5,1,0,5),('1162',2011,'9010',5,1,0,5),('1162',2011,'9020',5,1,0,5),('1162',2011,'9030',5,1,0,5),('1162',2011,'9040',5,1,0,5),('1162',2011,'9050',5,1,0,5),('1162',2011,'9060',5,1,0,5),('1162',2011,'9070',5,1,0,5),('1219',2011,'9010',5,1,0,5),('1219',2011,'9020',5,1,0,5),('1219',2011,'9030',5,1,0,5),('1219',2011,'9040',5,1,0,5),('1219',2011,'9050',5,1,0,5),('1219',2011,'9060',5,1,0,5),('1219',2011,'9070',5,1,0,5),('1503',2011,'9010',5,1,0,5),('1503',2011,'9020',5,1,0,5),('1503',2011,'9030',5,1,0,5),('1503',2011,'9040',5,1,0,5),('1503',2011,'9050',5,1,0,5),('1503',2011,'9060',5,1,0,5),('1503',2011,'9070',5,1,0,5),('1975',2011,'9070',5,1,0,5),('3000',2011,'9070',5,1,0,5),('1005',2011,'5000',5,1,0,5),('1007',2011,'5000',5,1,0,5),('1015',2011,'5000',5,1,0,5),('1016',2011,'5000',5,1,0,5),('1017',2011,'5000',5,1,0,5),('1018',2011,'5000',5,1,0,5),('1021',2011,'5000',5,1,0,5),('1030',2011,'5000',5,1,0,5),('1031',2011,'5000',5,1,0,5),('1032',2011,'5000',5,1,0,5),('1055',2011,'5000',5,1,0,5),('1065',2011,'5000',5,1,0,5),('1072',2011,'5000',5,1,0,5),('1101',2011,'5000',5,1,0,5),('1112',2011,'5000',5,1,0,5),('1160',2011,'5000',5,1,0,5),('1161',2011,'5000',5,1,0,5),('1200',2011,'5000',5,1,0,5),('1201',2011,'5000',5,1,0,5),('1202',2011,'5000',5,1,0,5),('1210',2011,'5000',5,1,0,5),('1211',2011,'5000',5,1,0,5),('1212',2011,'5000',5,1,0,5),('1213',2011,'5000',5,1,0,5),('1214',2011,'5000',5,1,0,5),('1215',2011,'5000',5,1,0,5),('1216',2011,'5000',5,1,0,5),('1217',2011,'5000',5,1,0,5),('1218',2011,'5000',5,1,0,5),('1230',2011,'5000',5,1,0,5),('1231',2011,'5000',5,1,0,5),('1232',2011,'5000',5,1,0,5),('1250',2011,'5000',5,1,0,5),('1410',2011,'5000',5,1,0,5),('1500',2011,'5000',5,1,0,5),('1501',2011,'5000',5,1,0,5),('1902',2011,'5000',5,1,0,5),('1910',2011,'5000',5,1,0,5),('1950',2011,'5000',5,1,0,5),('1961',2011,'5000',5,1,0,5),('1962',2011,'5000',5,1,0,5),('1971',2011,'5000',5,1,0,5),('1972',2011,'5000',5,1,0,5),('1973',2011,'5000',5,1,0,5),('1976',2011,'5000',5,1,0,5),('1977',2011,'5000',5,1,0,5),('1980',2011,'5000',5,1,0,5),('2000',2011,'5000',5,1,0,5),('2005',2011,'5000',5,1,0,5),('2010',2011,'5000',5,1,0,5),('2015',2011,'5000',5,1,0,5),('2020',2011,'5000',5,1,0,5),('2025',2011,'5000',5,1,0,5),('2030',2011,'5000',5,1,0,5),('2035',2011,'5000',5,1,0,5),('2060',2011,'5000',5,1,0,5),('2070',2011,'5000',5,1,0,5),('2075',2011,'5000',5,1,0,5),('3030',2011,'5000',5,1,0,5),('3031',2011,'5000',5,1,0,5),('3032',2011,'5000',5,1,0,5),('3034',2011,'5000',5,1,0,5),('1010',2011,'5000',5,1,0,5),('1000',2011,'5000',5,1,0,5),('2050',2011,'5000',5,1,0,5),('1050',2011,'5000',5,1,0,5),('1975',2011,'5000',5,1,0,5),('3000',2011,'5000',5,1,0,5),('1061',2011,'5000',5,1,0,5),('1071',2011,'5000',5,1,0,5),('1073',2011,'5000',5,1,0,5),('1074',2011,'5000',5,1,0,5),('1075',2011,'5000',5,1,0,5),('1076',2011,'5000',5,1,0,5),('1102',2011,'5000',5,1,0,5),('1162',2011,'5000',5,1,0,5),('1219',2011,'5000',5,1,0,5),('1503',2011,'5000',5,1,0,5),('2065',2011,'5000',5,1,0,5),('5000',2011,'6500',5,1,0,5),('5070',2011,'6500',5,1,0,5),('6060',2011,'8000',5,1,0,5),('6510',2011,'8000',5,1,0,5),('1001',2011,'5000',5,1,0,5),('1001',2011,'9010',5,1,0,5),('1001',2011,'9020',5,1,0,5),('1001',2011,'9030',5,1,0,5),('1001',2011,'9040',5,1,0,5),('1001',2011,'9050',5,1,0,5),('1001',2011,'9060',5,1,0,5),('1001',2011,'9070',5,1,0,5),('1001',2011,'9090',5,1,0,5);
/*!40000 ALTER TABLE `payroll_account_linker` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_account_mapping`
--

DROP TABLE IF EXISTS `payroll_account_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_account_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_account_ID` varchar(5) NOT NULL,
  `AccountType` tinyint(4) NOT NULL,
  `Description` varchar(20) NOT NULL,
  `ProcessingMethod` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_account_mapping`
--

LOCK TABLES `payroll_account_mapping` WRITE;
/*!40000 ALTER TABLE `payroll_account_mapping` DISABLE KEYS */;
INSERT INTO `payroll_account_mapping` VALUES (1,'1001',3,'Gehalt',2),(2,'1000',2,'Monatslohn',2),(3,'9010',3,'AHV Basis',1),(4,'9015',4,'AHV Lohn',1),(5,'9019',5,'Nicht AHV-pflichtig',1),(6,'9020',6,'ALV Basis',1),(7,'9025',7,'ALV Lohn',1),(8,'9029',8,'Nicht ALV-pflichtig',1),(9,'9027',9,'ALVZ-Lohn',1),(10,'9030',10,'UVG Basis',1),(11,'9035',11,'UVG Lohn',1),(12,'5039',12,'UVG-Netto-Link',1),(13,'9040',13,'UVGZ Basis',1),(14,'9045',14,'UVGZ Lohn',1),(15,'9050',15,'KTG Basis',1),(16,'9055',16,'KTG Lohn',1),(17,'9060',17,'BVG Basis',1),(18,'9065',18,'BVG Lohn',1),(19,'5000',19,'Bruttolohn',1),(20,'6500',20,'Nettolohn',1),(21,'8000',21,'Auszahlung',1),(22,'1005',1,'Stundenlohn',2),(23,'5010',1,'AHV-Beitrag',3),(24,'9999',1,'durchschn. Std-Satz',4);
/*!40000 ALTER TABLE `payroll_account_mapping` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_attended_time`
--

DROP TABLE IF EXISTS `payroll_attended_time`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_attended_time` (
  `id` varchar(2) NOT NULL,
  `percentage` tinyint(4) NOT NULL,
  `attended_time` decimal(5,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_attended_time`
--

LOCK TABLES `payroll_attended_time` WRITE;
/*!40000 ALTER TABLE `payroll_attended_time` DISABLE KEYS */;
INSERT INTO `payroll_attended_time` VALUES ('0',0,0.00),('1',100,160.00),('2',80,128.00),('3',50,80.00);
/*!40000 ALTER TABLE `payroll_attended_time` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_calculation_current`
--

DROP TABLE IF EXISTS `payroll_calculation_current`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_calculation_current` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_year_ID` smallint(6) NOT NULL,
  `payroll_period_ID` int(11) NOT NULL,
  `payroll_employee_ID` int(11) NOT NULL,
  `payroll_account_ID` varchar(5) NOT NULL,
  `quantity` decimal(12,5) NOT NULL,
  `rate` decimal(12,5) NOT NULL,
  `amount` decimal(12,5) NOT NULL,
  `allowable_workdays` tinyint(4) NOT NULL DEFAULT '0',
  `position` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_payroll_calculation_current_payroll_employee1_idx` (`payroll_employee_ID`),
  KEY `fk_payroll_calculation_current_payroll_account1_idx` (`payroll_account_ID`,`payroll_year_ID`)
) ENGINE=MEMORY AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_calculation_current`
--

LOCK TABLES `payroll_calculation_current` WRITE;
/*!40000 ALTER TABLE `payroll_calculation_current` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_calculation_current` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_calculation_entry`
--

DROP TABLE IF EXISTS `payroll_calculation_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_calculation_entry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_year_ID` smallint(6) NOT NULL,
  `payroll_period_ID` int(11) NOT NULL,
  `payroll_employee_ID` int(11) NOT NULL,
  `payroll_account_ID` varchar(5) NOT NULL,
  `quantity` decimal(12,5) NOT NULL,
  `rate` decimal(12,5) NOT NULL,
  `amount` decimal(12,5) NOT NULL,
  `allowable_workdays` tinyint(4) NOT NULL DEFAULT '0',
  `position` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_payroll_calculation_entry_payroll_employee1_idx` (`payroll_employee_ID`),
  KEY `fk_payroll_calculation_entry_payroll_account1_idx` (`payroll_account_ID`,`payroll_year_ID`),
  KEY `fk_payroll_calculation_entry_payroll_period1` (`payroll_period_ID`),
  CONSTRAINT `fk_payroll_calculation_entry_payroll_account1` FOREIGN KEY (`payroll_account_ID`, `payroll_year_ID`) REFERENCES `payroll_account` (`id`, `payroll_year_ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_payroll_calculation_entry_payroll_employee1` FOREIGN KEY (`payroll_employee_ID`) REFERENCES `payroll_employee` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_payroll_calculation_entry_payroll_period1` FOREIGN KEY (`payroll_period_ID`) REFERENCES `payroll_period` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_calculation_entry`
--

LOCK TABLES `payroll_calculation_entry` WRITE;
/*!40000 ALTER TABLE `payroll_calculation_entry` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_calculation_entry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_calculation_modifier`
--

DROP TABLE IF EXISTS `payroll_calculation_modifier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_calculation_modifier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_account_ID` varchar(5) NOT NULL,
  `payroll_empl_filter_ID` int(11) NOT NULL,
  `processing_order` tinyint(3) unsigned NOT NULL,
  `ModifierType` tinyint(4) NOT NULL,
  `FieldName` varchar(30) NOT NULL,
  `TargetField` tinyint(4) NOT NULL DEFAULT '4',
  `TargetValue` decimal(12,5) NOT NULL,
  `max_limit` decimal(8,2) NOT NULL,
  `min_limit` decimal(8,2) NOT NULL,
  `deduction` decimal(8,2) NOT NULL,
  `major_period` tinyint(1) NOT NULL DEFAULT '1',
  `minor_period` tinyint(1) NOT NULL DEFAULT '1',
  `major_period_bonus` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_calculation_modifier`
--

LOCK TABLES `payroll_calculation_modifier` WRITE;
/*!40000 ALTER TABLE `payroll_calculation_modifier` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_calculation_modifier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_company`
--

DROP TABLE IF EXISTS `payroll_company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_company` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_shortname` varchar(20) NOT NULL,
  `HR-RC-Name` varchar(60) NOT NULL,
  `Street` varchar(60) NOT NULL,
  `ZIP-Code` varchar(15) NOT NULL,
  `City` varchar(50) NOT NULL,
  `Country` varchar(2) NOT NULL,
  `UID-EHRA` varchar(45) NOT NULL,
  `BUR-REE-Number` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_company`
--

LOCK TABLES `payroll_company` WRITE;
/*!40000 ALTER TABLE `payroll_company` DISABLE KEYS */;
INSERT INTO `payroll_company` VALUES (1,'Muster AG','Muster AG','Bahnhofstrasse 1','6002','Luzern','CH','CH-100.3.032.254-8','23434567');
/*!40000 ALTER TABLE `payroll_company` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_empl_filter`
--

DROP TABLE IF EXISTS `payroll_empl_filter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_empl_filter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `FilterName` varchar(45) CHARACTER SET latin1 NOT NULL,
  `FilterPriority` tinyint(3) unsigned NOT NULL,
  `FilterCriteria` text CHARACTER SET latin1 NOT NULL,
  `GlobalFilter` tinyint(1) NOT NULL DEFAULT '0',
  `TemporaryFilter` tinyint(1) NOT NULL DEFAULT '1',
  `ValidForEmplOverview` tinyint(1) NOT NULL DEFAULT '0',
  `ValidForCalculation` tinyint(1) NOT NULL DEFAULT '0',
  `dirtyData` tinyint(1) NOT NULL DEFAULT '0',
  `dirtyCriteria` tinyint(1) NOT NULL DEFAULT '0',
  `datetime_created` datetime NOT NULL,
  `core_user_id_created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_empl_filter`
--

LOCK TABLES `payroll_empl_filter` WRITE;
/*!40000 ALTER TABLE `payroll_empl_filter` DISABLE KEYS */;
INSERT INTO `payroll_empl_filter` VALUES (1,'TestFilter',2,'`Sex`=\'F\' AND ( MONTH(`DateOfBirth`)<4 OR MONTH(`DateOfBirth`)>9 )',1,0,1,1,0,0,'2013-04-22 00:00:00',1),(2,'TestFilter2',1,'`Sex`=\'F\' AND MONTH(`DateOfBirth`)<4',1,0,1,1,0,0,'2013-04-22 00:00:00',1),(5,'Abzocker (weiblich)',1,'`BaseWage`>=20000 AND `Sex`=\'F\'',1,0,1,1,0,0,'2013-07-23 16:11:18',1),(6,'Abzocker (männlich)',1,'`BaseWage`>=16000 AND `Sex`=\'M\'',1,0,1,1,0,0,'2013-07-23 16:13:30',1);
/*!40000 ALTER TABLE `payroll_empl_filter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_empl_filter_cache`
--

DROP TABLE IF EXISTS `payroll_empl_filter_cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_empl_filter_cache` (
  `payroll_empl_filter_ID` int(11) NOT NULL,
  `payroll_employee_ID` int(11) NOT NULL,
  KEY `fk_payroll_empl_filter_cache_payroll_empl_filter1_idx` (`payroll_empl_filter_ID`),
  KEY `fk_payroll_empl_filter_cache_payroll_employee1_idx` (`payroll_employee_ID`),
  CONSTRAINT `fk_payroll_empl_filter_cache_payroll_employee1` FOREIGN KEY (`payroll_employee_ID`) REFERENCES `payroll_employee` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_payroll_empl_filter_cache_payroll_empl_filter1` FOREIGN KEY (`payroll_empl_filter_ID`) REFERENCES `payroll_empl_filter` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_empl_filter_cache`
--

LOCK TABLES `payroll_empl_filter_cache` WRITE;
/*!40000 ALTER TABLE `payroll_empl_filter_cache` DISABLE KEYS */;
INSERT INTO `payroll_empl_filter_cache` VALUES (2,8),(2,13),(2,19),(2,25),(2,29),(1,7),(1,8),(1,13),(1,15),(1,23),(1,25),(1,29),(1,30),(1,32),(5,5),(5,11),(5,23),(5,25),(5,27),(5,28),(5,29),(5,32),(6,22),(6,31);
/*!40000 ALTER TABLE `payroll_empl_filter_cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_empl_filter_crit`
--

DROP TABLE IF EXISTS `payroll_empl_filter_crit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_empl_filter_crit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_empl_filter_ID` int(11) NOT NULL,
  `CriteriaType` tinyint(4) NOT NULL,
  `FieldName` varchar(30) NOT NULL,
  `FieldModifier` tinyint(4) NOT NULL,
  `Conjunction` tinyint(4) NOT NULL,
  `Comparison` tinyint(4) NOT NULL,
  `SortOrder` tinyint(4) NOT NULL,
  `ComparativeValues` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_empl_filter_crit`
--

LOCK TABLES `payroll_empl_filter_crit` WRITE;
/*!40000 ALTER TABLE `payroll_empl_filter_crit` DISABLE KEYS */;
INSERT INTO `payroll_empl_filter_crit` VALUES (1,1,1,'Sex',0,0,1,1,'F'),(2,1,2,'',0,1,0,2,''),(3,1,3,'',0,0,0,3,''),(4,1,1,'DateOfBirth',2,0,6,4,'4'),(5,1,2,'',0,2,0,5,''),(6,1,1,'DateOfBirth',2,0,5,6,'9'),(7,1,4,'',0,0,0,7,''),(12,5,1,'BaseWage',0,0,3,0,'20000'),(13,5,2,'',0,1,0,1,''),(14,5,1,'Sex',0,0,1,2,'F'),(15,6,1,'BaseWage',0,0,3,0,'16000'),(16,6,2,'',0,1,0,1,''),(17,6,1,'Sex',0,0,1,2,'M');
/*!40000 ALTER TABLE `payroll_empl_filter_crit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_empl_form`
--

DROP TABLE IF EXISTS `payroll_empl_form`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_empl_form` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `FormName` varchar(30) NOT NULL,
  `FormElements` text NOT NULL,
  `temporary` tinyint(1) NOT NULL DEFAULT '0',
  `global` tinyint(1) NOT NULL DEFAULT '0',
  `datetime_created` datetime NOT NULL,
  `core_user_id_created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_empl_form`
--

LOCK TABLES `payroll_empl_form` WRITE;
/*!40000 ALTER TABLE `payroll_empl_form` DISABLE KEYS */;
INSERT INTO `payroll_empl_form` VALUES (1,'First Light','[{\"tabName\":\"Personalien\",\"tabID\":\"new1\",\"tabRow\":1,\"elements\":[\"EmployeeNumber\",\"Firstname\",\"Lastname\",\"AdditionalAddrLine1\",\"Street\",\"ZipCity\",\"AdditionalAddrLine2\",\"AdditionalAddrLine3\"]}]',0,1,'2013-02-16 18:41:18',1),(2,'Alle Felder','[{\"tabName\":\"Wohnadresse\",\"tabID\":\"new1\",\"tabRow\":2,\"elements\":[\"EmployeeNumber\",\"Firstname\",\"Lastname\",\"AdditionalAddrLine1\",\"AdditionalAddrLine2\",\"AdditionalAddrLine3\",\"AdditionalAddrLine4\",\"Street\",\"ZipCity\",\"ResidenceCanton\",\"Country\"]},{\"tabName\":\"Tab3\",\"tabID\":\"new3\",\"tabRow\":2,\"elements\":[\"payroll_company_ID\",\"Sex\",\"Nationality\",\"DateOfBirth\",\"Age\",\"RetirementDate\",\"PlaceOfOrigin\",\"WorkplaceCanton\",\"Skill\",\"Education\",\"CivilStatus\",\"Position\"]},{\"tabName\":\"Kinder\",\"tabID\":\"new5\",\"tabRow\":2,\"elements\":[\"tblchld\"]},{\"tabName\":\"Ausbildung\",\"tabID\":\"new6\",\"tabRow\":2,\"elements\":[\"tbledu\"]},{\"tabName\":\"Ein-\\/Austritte\",\"tabID\":\"new7\",\"tabRow\":2,\"elements\":[\"tblprd\"]},{\"tabName\":\"Lohnentwicklung\",\"tabID\":\"new8\",\"tabRow\":2,\"elements\":[\"tblslry\"]},{\"tabName\":\"Besch\\u00e4ftigungsdaten\",\"tabID\":\"new9\",\"tabRow\":2,\"elements\":[\"tblcrer\"]},{\"tabName\":\"Tab2\",\"tabID\":\"new2\",\"tabRow\":1,\"elements\":[\"SV-AS-Number\",\"AHV-AVS-Number\",\"SeniorityJoining\",\"YearsOfService\",\"MonthsOfService\",\"WageCode\",\"BaseWage\"]},{\"tabName\":\"Checkboxen\",\"tabID\":\"new4\",\"tabRow\":1,\"elements\":[\"SingleParent\",\"CanteenLunchCheck\",\"AgriculturalEmployee\",\"Apprentice\",\"PensionerExemption\",\"FreeTransport\"]},{\"tabName\":\"Versicherungen\",\"tabID\":\"new10\",\"tabRow\":1,\"elements\":[\"CodeAHV\",\"CodeALV\",\"CodeBVG\",\"CodeKTG\",\"CodeUVG\",\"CodeUVGZ1\",\"CodeUVGZ2\"]},{\"tabName\":\"Diverse\",\"tabID\":\"new11\",\"tabRow\":1,\"elements\":[\"WorkplaceCity\",\"EmploymentStatus\",\"AliensCategory\",\"AttendedTimeCode\",\"Language\",\"DedAtSrcMode\",\"DedAtSrcCanton\",\"DedAtSrcCompany\",\"DedAtSrcCode\",\"DedAtSrcMunicipality\",\"DedAtSrcPercentage\",\"Department\",\"ManagementLevel\"]}]',0,1,'2013-02-16 21:22:50',1),(5,'Presida','[{\"tabName\":\"Neuer Tab\",\"tabID\":\"new3\",\"tabRow\":1,\"elements\":[\"SeniorityJoining\"]},{\"tabName\":\"Adressdaten\",\"tabID\":\"new1\",\"tabRow\":1,\"elements\":[\"EmployeeNumber\",\"Firstname\",\"Lastname\",\"AdditionalAddrLine1\",\"AdditionalAddrLine2\",\"Street\",\"ZipCity\",\"Country\"]},{\"tabName\":\"Reiter XY\",\"tabID\":\"new2\",\"tabRow\":1,\"elements\":[\"YearsOfService\",\"MonthsOfService\"]}]',0,1,'2013-03-01 15:01:06',1),(6,'Swissdec','[{\"tabName\":\"Ein-\\/Austritt\",\"tabID\":\"new1\",\"tabRow\":2,\"elements\":[\"tblprd\"]},{\"tabName\":\"Personalien\",\"tabID\":\"new2\",\"tabRow\":2,\"elements\":[\"Lastname\",\"Firstname\",\"Street\",\"ZipCity\",\"DateOfBirth\",\"Sex\",\"Nationality\",\"ResidenceCanton\",\"WorkplaceCanton\",\"EmployeeNumber\",\"AHV-AVS-Number\"]},{\"tabName\":\"Lohnausweis\",\"tabID\":\"new4\",\"tabRow\":2,\"elements\":[\"CanteenLunchCheck\",\"FreeTransport\"]},{\"tabName\":\"Lohnstrukturerhebung\",\"tabID\":\"new3\",\"tabRow\":2,\"elements\":[\"Apprentice\",\"AliensCategory\",\"Education\",\"Skill\",\"Position\",\"SeniorityJoining\",\"YearsOfService\",\"MonthsOfService\"]},{\"tabName\":\"Versicherungscodes\",\"tabID\":\"new5\",\"tabRow\":1,\"elements\":[\"CodeAHV\",\"CodeALV\",\"CodeUVG\",\"CodeUVGZ1\",\"CodeUVGZ2\",\"CodeKTG\",\"CodeBVG\"]},{\"tabName\":\"Basisdaten\",\"tabID\":\"new6\",\"tabRow\":1,\"elements\":[\"WageCode\",\"BaseWage\",\"CivilStatus\",\"EmploymentPercentage\",\"AttendedTimeCode\",\"AttendedTimeHours\",\"Age\",\"RetirementDate\",\"EmploymentStatus\"]},{\"tabName\":\"Kinder\",\"tabID\":\"new7\",\"tabRow\":1,\"elements\":[\"tblchld\"]},{\"tabName\":\"Lohnentwicklung\",\"tabID\":\"new8\",\"tabRow\":1,\"elements\":[\"tblslry\"]}]',0,1,'2013-08-03 11:26:01',4);
/*!40000 ALTER TABLE `payroll_empl_form` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_empl_list`
--

DROP TABLE IF EXISTS `payroll_empl_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_empl_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ListGroup` tinyint(3) unsigned NOT NULL,
  `ListType` tinyint(3) unsigned NOT NULL,
  `ListItemOrder` tinyint(3) unsigned NOT NULL,
  `ListItemToken` varchar(45) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  `core_user_ID_delete` int(11) NOT NULL,
  `datetime_delete` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=264 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_empl_list`
--

LOCK TABLES `payroll_empl_list` WRITE;
/*!40000 ALTER TABLE `payroll_empl_list` DISABLE KEYS */;
INSERT INTO `payroll_empl_list` VALUES (1,1,1,1,'shortTerm',0,0,'0000-00-00 00:00:00'),(2,1,1,2,'annual',0,0,'0000-00-00 00:00:00'),(3,1,1,3,'settled',0,0,'0000-00-00 00:00:00'),(4,1,1,4,'crossBorder',0,0,'0000-00-00 00:00:00'),(5,1,1,5,'othersNotSwiss',0,0,'0000-00-00 00:00:00'),(6,2,1,1,'unknown',0,0,'0000-00-00 00:00:00'),(7,2,1,2,'single',0,0,'0000-00-00 00:00:00'),(8,2,1,3,'married',0,0,'0000-00-00 00:00:00'),(9,2,1,4,'divorced',0,0,'0000-00-00 00:00:00'),(10,2,1,5,'widowed',0,0,'0000-00-00 00:00:00'),(11,2,1,6,'separated',0,0,'0000-00-00 00:00:00'),(12,2,1,7,'registeredPartnership',0,0,'0000-00-00 00:00:00'),(13,2,1,8,'partnershipDissolvedByLaw',0,0,'0000-00-00 00:00:00'),(14,2,1,9,'partnershipDissolvedByDead',0,0,'0000-00-00 00:00:00'),(15,2,1,10,'partnershipDissolvedByDeclarationOfLost',0,0,'0000-00-00 00:00:00'),(16,3,2,0,'AG',0,0,'0000-00-00 00:00:00'),(17,3,2,0,'AI',0,0,'0000-00-00 00:00:00'),(18,3,2,0,'AR',0,0,'0000-00-00 00:00:00'),(19,3,2,0,'BE',0,0,'0000-00-00 00:00:00'),(20,3,2,0,'BL',0,0,'0000-00-00 00:00:00'),(21,3,2,0,'BS',0,0,'0000-00-00 00:00:00'),(22,3,2,0,'FR',0,0,'0000-00-00 00:00:00'),(23,3,2,0,'GE',0,0,'0000-00-00 00:00:00'),(24,3,2,0,'GL',0,0,'0000-00-00 00:00:00'),(25,3,2,0,'GR',0,0,'0000-00-00 00:00:00'),(26,3,2,0,'JU',0,0,'0000-00-00 00:00:00'),(27,3,2,0,'LU',0,0,'0000-00-00 00:00:00'),(28,3,2,0,'NE',0,0,'0000-00-00 00:00:00'),(29,3,2,0,'NW',0,0,'0000-00-00 00:00:00'),(30,3,2,0,'OW',0,0,'0000-00-00 00:00:00'),(31,3,2,0,'SG',0,0,'0000-00-00 00:00:00'),(32,3,2,0,'SH',0,0,'0000-00-00 00:00:00'),(33,3,2,0,'SO',0,0,'0000-00-00 00:00:00'),(34,3,2,0,'SZ',0,0,'0000-00-00 00:00:00'),(35,3,2,0,'TG',0,0,'0000-00-00 00:00:00'),(36,3,2,0,'TI',0,0,'0000-00-00 00:00:00'),(37,3,2,0,'UR',0,0,'0000-00-00 00:00:00'),(38,3,2,0,'VD',0,0,'0000-00-00 00:00:00'),(39,3,2,0,'VS',0,0,'0000-00-00 00:00:00'),(40,3,2,0,'ZG',0,0,'0000-00-00 00:00:00'),(41,3,2,0,'ZH',0,0,'0000-00-00 00:00:00'),(42,3,2,1,'EX',0,0,'0000-00-00 00:00:00'),(43,4,1,10,'doctorate',0,0,'0000-00-00 00:00:00'),(44,4,1,9,'university',0,0,'0000-00-00 00:00:00'),(45,4,1,8,'higherEducation',0,0,'0000-00-00 00:00:00'),(46,4,1,7,'higherVocEducation',0,0,'0000-00-00 00:00:00'),(47,4,1,6,'teacherCertificate',0,0,'0000-00-00 00:00:00'),(48,4,1,5,'universityEntranceCertificate',0,0,'0000-00-00 00:00:00'),(49,4,1,4,'vocEducationCompl',0,0,'0000-00-00 00:00:00'),(50,4,1,3,'enterpriseEducation',0,0,'0000-00-00 00:00:00'),(51,4,1,2,'mandatorySchoolOnly',0,0,'0000-00-00 00:00:00'),(52,4,1,1,'other',0,0,'0000-00-00 00:00:00'),(53,5,1,5,'highestCadre',0,0,'0000-00-00 00:00:00'),(54,5,1,4,'middleCadre',0,0,'0000-00-00 00:00:00'),(55,5,1,3,'lowerCadre',0,0,'0000-00-00 00:00:00'),(56,5,1,2,'lowestCadre',0,0,'0000-00-00 00:00:00'),(57,5,1,1,'noCadre',0,0,'0000-00-00 00:00:00'),(58,6,1,1,'simple',0,0,'0000-00-00 00:00:00'),(59,6,1,2,'specialized',0,0,'0000-00-00 00:00:00'),(60,6,1,3,'qualified',0,0,'0000-00-00 00:00:00'),(61,6,1,4,'mostDemanding',0,0,'0000-00-00 00:00:00'),(62,7,1,0,'F',0,0,'0000-00-00 00:00:00'),(63,7,1,0,'M',0,0,'0000-00-00 00:00:00'),(66,9,1,0,'CH',0,0,'0000-00-00 00:00:00'),(67,9,1,0,'DE',0,0,'0000-00-00 00:00:00'),(68,9,1,0,'FR',0,0,'0000-00-00 00:00:00'),(69,9,1,0,'AT',0,0,'0000-00-00 00:00:00'),(70,9,1,0,'IT',0,0,'0000-00-00 00:00:00'),(71,9,1,0,'LI',0,0,'0000-00-00 00:00:00'),(73,10,2,1,'1',0,0,'0000-00-00 00:00:00'),(74,10,2,2,'2',0,0,'0000-00-00 00:00:00'),(75,10,2,3,'3',0,0,'0000-00-00 00:00:00'),(76,10,2,4,'4',0,0,'0000-00-00 00:00:00'),(77,11,2,1,'1',0,0,'0000-00-00 00:00:00'),(78,11,2,2,'2',0,0,'0000-00-00 00:00:00'),(79,11,2,3,'3',0,0,'0000-00-00 00:00:00'),(80,11,2,4,'4',0,0,'0000-00-00 00:00:00'),(81,11,2,5,'5',0,0,'0000-00-00 00:00:00'),(82,12,2,1,'1',0,0,'0000-00-00 00:00:00'),(83,12,2,2,'2',0,0,'0000-00-00 00:00:00'),(84,12,2,3,'3',0,0,'0000-00-00 00:00:00'),(85,12,2,4,'4',0,0,'0000-00-00 00:00:00'),(86,13,2,1,'0',0,0,'0000-00-00 00:00:00'),(87,13,2,2,'1',0,0,'0000-00-00 00:00:00'),(88,13,2,3,'2',0,0,'0000-00-00 00:00:00'),(89,13,2,4,'3',0,0,'0000-00-00 00:00:00'),(90,8,1,1,'01',0,0,'0000-00-00 00:00:00'),(91,8,1,2,'02',0,0,'0000-00-00 00:00:00'),(92,8,1,3,'03',0,0,'0000-00-00 00:00:00'),(93,8,1,4,'04',0,0,'0000-00-00 00:00:00'),(94,8,1,5,'05',0,0,'0000-00-00 00:00:00'),(95,8,1,6,'06',0,0,'0000-00-00 00:00:00'),(96,8,1,7,'07',0,0,'0000-00-00 00:00:00'),(97,8,1,9,'09',0,0,'0000-00-00 00:00:00'),(98,8,1,10,'10',0,0,'0000-00-00 00:00:00'),(99,8,1,11,'11',0,0,'0000-00-00 00:00:00'),(100,8,1,12,'12',0,0,'0000-00-00 00:00:00'),(101,8,1,13,'13',0,0,'0000-00-00 00:00:00'),(102,8,1,14,'14',0,0,'0000-00-00 00:00:00'),(103,8,1,15,'15',0,0,'0000-00-00 00:00:00'),(104,8,1,16,'16',0,0,'0000-00-00 00:00:00'),(105,8,1,17,'17',0,0,'0000-00-00 00:00:00'),(106,8,1,18,'18',0,0,'0000-00-00 00:00:00'),(107,8,1,20,'20',0,0,'0000-00-00 00:00:00'),(108,8,1,21,'21',0,0,'0000-00-00 00:00:00'),(109,8,1,22,'22',0,0,'0000-00-00 00:00:00'),(110,8,1,23,'23',0,0,'0000-00-00 00:00:00'),(111,8,1,24,'24',0,0,'0000-00-00 00:00:00'),(112,8,1,26,'26',0,0,'0000-00-00 00:00:00'),(113,8,1,27,'27',0,0,'0000-00-00 00:00:00'),(114,8,1,28,'28',0,0,'0000-00-00 00:00:00'),(115,8,1,29,'29',0,0,'0000-00-00 00:00:00'),(116,8,1,30,'30',0,0,'0000-00-00 00:00:00'),(117,8,1,31,'31',0,0,'0000-00-00 00:00:00'),(118,8,1,32,'32',0,0,'0000-00-00 00:00:00'),(119,8,1,33,'33',0,0,'0000-00-00 00:00:00'),(120,8,1,34,'34',0,0,'0000-00-00 00:00:00'),(121,8,1,35,'35',0,0,'0000-00-00 00:00:00'),(122,8,1,36,'36',0,0,'0000-00-00 00:00:00'),(123,8,1,37,'37',0,0,'0000-00-00 00:00:00'),(124,8,1,38,'38',0,0,'0000-00-00 00:00:00'),(125,8,1,39,'39',0,0,'0000-00-00 00:00:00'),(126,8,1,40,'40',0,0,'0000-00-00 00:00:00'),(127,8,1,41,'41',0,0,'0000-00-00 00:00:00'),(128,8,1,42,'42',0,0,'0000-00-00 00:00:00'),(129,8,1,44,'44',0,0,'0000-00-00 00:00:00'),(130,8,1,45,'45',0,0,'0000-00-00 00:00:00'),(131,8,1,46,'46',0,0,'0000-00-00 00:00:00'),(132,8,1,47,'47',0,0,'0000-00-00 00:00:00'),(133,8,1,48,'48',0,0,'0000-00-00 00:00:00'),(134,8,1,50,'50',0,0,'0000-00-00 00:00:00'),(135,8,1,51,'51',0,0,'0000-00-00 00:00:00'),(136,8,1,52,'52',0,0,'0000-00-00 00:00:00'),(137,8,1,54,'54',0,0,'0000-00-00 00:00:00'),(138,8,1,98,'98',0,0,'0000-00-00 00:00:00'),(139,8,1,99,'99',0,0,'0000-00-00 00:00:00'),(140,14,1,0,'0',0,0,'0000-00-00 00:00:00'),(141,14,1,1,'01',0,0,'0000-00-00 00:00:00'),(142,14,1,2,'02',0,0,'0000-00-00 00:00:00'),(143,14,1,3,'03',0,0,'0000-00-00 00:00:00'),(144,14,1,4,'04',0,0,'0000-00-00 00:00:00'),(145,14,1,5,'05',0,0,'0000-00-00 00:00:00'),(146,14,1,6,'06',0,0,'0000-00-00 00:00:00'),(154,15,1,0,'ABF',0,0,'0000-00-00 00:00:00'),(155,15,1,0,'ADM',0,0,'0000-00-00 00:00:00'),(156,15,1,0,'AUS',0,0,'0000-00-00 00:00:00'),(157,15,1,0,'AVO',0,0,'0000-00-00 00:00:00'),(158,15,1,0,'BAH',0,0,'0000-00-00 00:00:00'),(159,15,1,0,'BEO',0,0,'0000-00-00 00:00:00'),(160,15,1,0,'BET',0,0,'0000-00-00 00:00:00'),(161,15,1,0,'BEW',0,0,'0000-00-00 00:00:00'),(162,15,1,0,'BEZ',0,0,'0000-00-00 00:00:00'),(163,15,1,0,'BL',0,0,'0000-00-00 00:00:00'),(164,15,1,0,'BLG',0,0,'0000-00-00 00:00:00'),(165,15,1,0,'BLT',0,0,'0000-00-00 00:00:00'),(166,15,1,0,'BOF',0,0,'0000-00-00 00:00:00'),(167,15,1,0,'BS',0,0,'0000-00-00 00:00:00'),(168,15,1,0,'BV',0,0,'0000-00-00 00:00:00'),(169,15,1,0,'CAF',0,0,'0000-00-00 00:00:00'),(170,15,1,0,'CCS',0,0,'0000-00-00 00:00:00'),(171,15,1,0,'CP',0,0,'0000-00-00 00:00:00'),(172,15,1,0,'CS',0,0,'0000-00-00 00:00:00'),(173,15,1,0,'DIR',0,0,'0000-00-00 00:00:00'),(174,15,1,0,'DIS',0,0,'0000-00-00 00:00:00'),(175,15,1,0,'DOK',0,0,'0000-00-00 00:00:00'),(176,15,1,0,'EDV',0,0,'0000-00-00 00:00:00'),(177,15,1,0,'EDV-CH',0,0,'0000-00-00 00:00:00'),(178,15,1,0,'EFP',0,0,'0000-00-00 00:00:00'),(179,15,1,0,'EIN',0,0,'0000-00-00 00:00:00'),(180,15,1,0,'EL',0,0,'0000-00-00 00:00:00'),(181,15,1,0,'ETL',0,0,'0000-00-00 00:00:00'),(182,15,1,0,'F&E',0,0,'0000-00-00 00:00:00'),(183,15,1,0,'FAA',0,0,'0000-00-00 00:00:00'),(184,15,1,0,'FAB',0,0,'0000-00-00 00:00:00'),(185,15,1,0,'FAP',0,0,'0000-00-00 00:00:00'),(186,15,1,0,'FLM',0,0,'0000-00-00 00:00:00'),(187,15,1,0,'FM',0,0,'0000-00-00 00:00:00'),(188,15,1,0,'FMM',0,0,'0000-00-00 00:00:00'),(189,15,1,0,'FRE',0,0,'0000-00-00 00:00:00'),(190,15,1,0,'GEB',0,0,'0000-00-00 00:00:00'),(191,15,1,0,'GEL',0,0,'0000-00-00 00:00:00'),(192,15,1,0,'HAW',0,0,'0000-00-00 00:00:00'),(193,15,1,0,'HSW',0,0,'0000-00-00 00:00:00'),(194,15,1,0,'ITA',0,0,'0000-00-00 00:00:00'),(195,15,1,0,'ITI',0,0,'0000-00-00 00:00:00'),(196,15,1,0,'JED',0,0,'0000-00-00 00:00:00'),(197,15,1,0,'KAM',0,0,'0000-00-00 00:00:00'),(198,15,1,0,'KEY',0,0,'0000-00-00 00:00:00'),(199,15,1,0,'LAD',0,0,'0000-00-00 00:00:00'),(200,15,1,0,'LJE',0,0,'0000-00-00 00:00:00'),(201,15,1,0,'LOG',0,0,'0000-00-00 00:00:00'),(202,15,1,0,'LV',0,0,'0000-00-00 00:00:00'),(203,15,1,0,'MAL',0,0,'0000-00-00 00:00:00'),(204,15,1,0,'MAR',0,0,'0000-00-00 00:00:00'),(205,15,1,0,'MON',0,0,'0000-00-00 00:00:00'),(206,15,1,0,'NEU',0,0,'0000-00-00 00:00:00'),(207,15,1,0,'OBE',0,0,'0000-00-00 00:00:00'),(208,15,1,0,'OP',0,0,'0000-00-00 00:00:00'),(209,15,1,0,'OPA',0,0,'0000-00-00 00:00:00'),(210,15,1,0,'OPC',0,0,'0000-00-00 00:00:00'),(211,15,1,0,'OPD',0,0,'0000-00-00 00:00:00'),(212,15,1,0,'OPO',0,0,'0000-00-00 00:00:00'),(213,15,1,0,'OPS',0,0,'0000-00-00 00:00:00'),(214,15,1,0,'OPW',0,0,'0000-00-00 00:00:00'),(215,15,1,0,'OPZ',0,0,'0000-00-00 00:00:00'),(216,15,1,0,'OSG',0,0,'0000-00-00 00:00:00'),(217,15,1,0,'OTI',0,0,'0000-00-00 00:00:00'),(218,15,1,0,'OVD',0,0,'0000-00-00 00:00:00'),(219,15,1,0,'OVS',0,0,'0000-00-00 00:00:00'),(220,15,1,0,'OZE',0,0,'0000-00-00 00:00:00'),(221,15,1,0,'OZH',0,0,'0000-00-00 00:00:00'),(222,15,1,0,'P',0,0,'0000-00-00 00:00:00'),(223,15,1,0,'PEK',0,0,'0000-00-00 00:00:00'),(224,15,1,0,'PRO',0,0,'0000-00-00 00:00:00'),(225,15,1,0,'PVL',0,0,'0000-00-00 00:00:00'),(226,15,1,0,'PVO',0,0,'0000-00-00 00:00:00'),(227,15,1,0,'PVW',0,0,'0000-00-00 00:00:00'),(228,15,1,0,'PVZ',0,0,'0000-00-00 00:00:00'),(229,15,1,0,'PWS',0,0,'0000-00-00 00:00:00'),(230,15,1,0,'RD',0,0,'0000-00-00 00:00:00'),(231,15,1,0,'REV',0,0,'0000-00-00 00:00:00'),(232,15,1,0,'RL',0,0,'0000-00-00 00:00:00'),(233,15,1,0,'RW',0,0,'0000-00-00 00:00:00'),(234,15,1,0,'SAF',0,0,'0000-00-00 00:00:00'),(235,15,1,0,'SER',0,0,'0000-00-00 00:00:00'),(236,15,1,0,'SPC',0,0,'0000-00-00 00:00:00'),(237,15,1,0,'SPE',0,0,'0000-00-00 00:00:00'),(238,15,1,0,'STC',0,0,'0000-00-00 00:00:00'),(239,15,1,0,'STO',0,0,'0000-00-00 00:00:00'),(240,15,1,0,'SUP',0,0,'0000-00-00 00:00:00'),(241,15,1,0,'SWF',0,0,'0000-00-00 00:00:00'),(242,15,1,0,'SYS',0,0,'0000-00-00 00:00:00'),(243,15,1,0,'SYT',0,0,'0000-00-00 00:00:00'),(244,15,1,0,'TEB',0,0,'0000-00-00 00:00:00'),(245,15,1,0,'TEL',0,0,'0000-00-00 00:00:00'),(246,15,1,0,'TKD',0,0,'0000-00-00 00:00:00'),(247,15,1,0,'TKO',0,0,'0000-00-00 00:00:00'),(248,15,1,0,'TKW',0,0,'0000-00-00 00:00:00'),(249,15,1,0,'TKZ',0,0,'0000-00-00 00:00:00'),(250,15,1,0,'TLO',0,0,'0000-00-00 00:00:00'),(251,15,1,0,'TSU',0,0,'0000-00-00 00:00:00'),(252,15,1,0,'VAD',0,0,'0000-00-00 00:00:00'),(253,15,1,0,'VJE',0,0,'0000-00-00 00:00:00'),(254,15,1,0,'VK',0,0,'0000-00-00 00:00:00'),(255,15,1,0,'VKC',0,0,'0000-00-00 00:00:00'),(256,15,1,0,'VKO',0,0,'0000-00-00 00:00:00'),(257,15,1,0,'VKP',0,0,'0000-00-00 00:00:00'),(258,15,1,0,'VKS',0,0,'0000-00-00 00:00:00'),(259,15,1,0,'VKW',0,0,'0000-00-00 00:00:00'),(260,15,1,0,'VKZ',0,0,'0000-00-00 00:00:00'),(261,15,1,0,'WAE',0,0,'0000-00-00 00:00:00'),(262,15,1,0,'WER',0,0,'0000-00-00 00:00:00'),(263,15,1,0,'WKS',0,0,'0000-00-00 00:00:00');
/*!40000 ALTER TABLE `payroll_empl_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_empl_list_label`
--

DROP TABLE IF EXISTS `payroll_empl_list_label`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_empl_list_label` (
  `payroll_empl_list_ID` int(11) NOT NULL,
  `language` varchar(2) NOT NULL,
  `label` varchar(80) NOT NULL,
  `tokenLabel` varchar(25) NOT NULL,
  PRIMARY KEY (`payroll_empl_list_ID`,`language`),
  KEY `fk_payroll_empl_list_idx` (`payroll_empl_list_ID`),
  CONSTRAINT `fk_payroll_empl_list` FOREIGN KEY (`payroll_empl_list_ID`) REFERENCES `payroll_empl_list` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_empl_list_label`
--

LOCK TABLES `payroll_empl_list_label` WRITE;
/*!40000 ALTER TABLE `payroll_empl_list_label` DISABLE KEYS */;
INSERT INTO `payroll_empl_list_label` VALUES (1,'de','Kurzaufenthalter (Kat. L)','L'),(2,'de','Jahresaufenthalter (Kat. B)','B'),(3,'de','Niedergelassene (Kat. C)','C'),(4,'de','Grenzgänger (Kat. G)','G'),(5,'de','Andere (nicht Schweizer)',''),(6,'de','Unbekannt',''),(7,'de','Ledig',''),(8,'de','Verheiratet',''),(9,'de','Geschieden',''),(10,'de','Verwitwet',''),(11,'de','Getrennt',''),(12,'de','Eingetragene Partnerschaft',''),(13,'de','Gerichtlich aufgelöste Partnerschaft',''),(14,'de','Durch Tod aufgelöste Partnerschaft',''),(15,'de','Durch Verschollenerklärung aufgelöste Partnerschaft',''),(16,'de','Aargau',''),(17,'de','Appenzell Innerrhoden',''),(18,'de','Appenzell Ausserrhoden',''),(19,'de','Bern',''),(20,'de','Basel-Landschaft',''),(21,'de','Basel-Stadt',''),(22,'de','Freiburg',''),(23,'de','Genf',''),(24,'de','Glarus',''),(25,'de','Graubünden',''),(26,'de','Jura',''),(27,'de','Luzern',''),(28,'de','Neuenburg',''),(29,'de','Nidwalden',''),(30,'de','Obwalden',''),(31,'de','St. Gallen',''),(32,'de','Schaffhausen',''),(33,'de','Solothurn',''),(34,'de','Schwyz',''),(35,'de','Thurgau',''),(36,'de','Tessin',''),(37,'de','Uri',''),(38,'de','Waadt',''),(39,'de','Wallis',''),(40,'de','Zug',''),(41,'de','Zürich',''),(42,'de','Wohnsitz im Ausland',''),(43,'de','Doktorat',''),(44,'de','Uni, ETH',''),(45,'de','Fachhochschule (FH), Pädagogische (PH)',''),(46,'de','Höhere Berufsausbildung, höhere Fachschule',''),(47,'de','Lehrerpatent',''),(48,'de','Matura',''),(49,'de','Abgeschlossene Berufsausbildung',''),(50,'de','Ausschliesslich unternehmensintern',''),(51,'de','Obligatorische Schule, ohne abgeschlossene Berufsausbildung',''),(52,'de','Andere Ausbildungsabschlüsse',''),(53,'de','Oberstes und oberes Kader',''),(54,'de','Mittleres Kader',''),(55,'de','Unteres Kader',''),(56,'de','Unterstes Kader',''),(57,'de','Ohne Kaderfunktion',''),(58,'de','Einfache und repetitive Tätigkeiten',''),(59,'de','Berufs- und Fachkenntnisse sind vorausgesetzt',''),(60,'de','Selbständiges und qualifiziertes Arbeiten',''),(61,'de','Höchst anspruchsvolle und schwierigste Arbeiten',''),(62,'de','weiblich','w'),(63,'de','männlich','m'),(66,'de','Schweiz',''),(67,'de','Deutschland',''),(68,'de','Frankreich',''),(69,'de','Österreich',''),(70,'de','Italien',''),(71,'de','Liechtenstein',''),(73,'de','Stundenlohn',''),(74,'de','Monatslohn',''),(75,'de','Gehalt',''),(76,'de','Rentner',''),(77,'de','keine Quellensteuer',''),(78,'de','Abzug Betrag',''),(79,'de','Abzug Prozent',''),(80,'de','Abzug Prozent ab Tarif',''),(81,'de','Abzug Betrag ab Tarif',''),(82,'de','Neueintritt',''),(83,'de','Aktive',''),(84,'de','ausgetreten',''),(85,'de','Austritt bevorstehend',''),(86,'de','Zeit auf Stamm',''),(87,'de','100% Pensum',''),(88,'de','80% Pensum',''),(89,'de','50% Pensum',''),(90,'de','AG: Fristloser Austritt',''),(91,'de','AG: Ungenüg. Leistungen/Verhalten',''),(91,'fr','AG: Travail insuffisant / comportement',''),(92,'de','AG: Wirtschaftliche Gründe',''),(92,'fr','AG: Raison économique',''),(93,'de','AG: Reorganisation/Schliessg.Abtlg. etc.',''),(93,'fr','AG: Fermeture Division/point de vente/succursale',''),(94,'de','AG: Unentschuldigte Absenzen (frst.Entl.)',''),(94,'fr','AG: Absences sans excuse (FE)',''),(95,'de','AG: Fehlbeträge',''),(95,'fr','AG: Difference financière',''),(96,'de','AG: Veruntreuung (fristl.Entlassung)',''),(96,'fr','AG: Détournement',''),(97,'de','AG: Gesundheitliche Gründe',''),(97,'fr','AG: Raisons de santé',''),(98,'de','AG: Befristete Anstellung',''),(98,'fr','AG: Contrat limité',''),(99,'de','AG: Ende Lehrverhältnis',''),(99,'fr','AG: Fin d\'aprenntissage',''),(100,'de','AG: Unentschuldigte Absenzen',''),(100,'fr','AG: Absences sans excuse',''),(101,'de','AG: Für diese Arbeit nicht geeignet',''),(101,'fr','AG: Inapte pour le travail',''),(102,'de','AG: Pensionierung',''),(102,'fr','AG: Retraite',''),(103,'de','AG: Kundenreklamationen',''),(103,'fr','AG: Reclamations du clientèle',''),(104,'de','AG: Verletzung Betriebsvorschriften',''),(104,'fr','AG: Violation des directives d\'entreprise',''),(105,'de','AG: Unwahre Bewerbungsangaben',''),(105,'fr','AG: Offre de travail faux',''),(106,'de','AG: Ueberforderung',''),(107,'de','AG: Differenzen in Zusammenarbeit',''),(107,'fr','AG: Autres raisons',''),(108,'de','AG: Mobbing',''),(108,'fr','AG: Mobbing',''),(109,'de','AG: Sexuelle Belästigung',''),(109,'fr','AG: Harcèlement sexuel',''),(110,'de','AG: Austrittsvereinbarung',''),(111,'de','AG: Differenzen Vorgesetzte/Mitarbeiter',''),(112,'de','AN: Familiär/Kinder',''),(112,'fr','AN: Maternité / enfants',''),(113,'de','AN: Gesundheitliche Gründe',''),(113,'fr','AN: Raisons de santé',''),(114,'de','AN: Invalidität',''),(114,'fr','AN: Invalidité',''),(115,'de','AN: Tod',''),(115,'fr','AN: Décès',''),(116,'de','AN: Arbeitsweg/Wohnort',''),(116,'fr','AN: Chemin de travail/lieu de domicile',''),(117,'de','AN: Auslandaufenthalt',''),(117,'fr','AN: Séjour à l\'etrangère',''),(118,'de','AN: Auswanderung',''),(118,'fr','AN: Emigration',''),(119,'de','AN: Weiterbildung',''),(119,'fr','AN: Formation',''),(120,'de','AN: Pensionierung',''),(120,'fr','AN: Retraite',''),(121,'de','AN: Mobbing',''),(121,'fr','AN: Mobbing',''),(122,'de','AN: Sexuelle Belästigung',''),(122,'fr','AN: Harcèlement sexuel',''),(123,'de','AN: Kündigung nahegelegt durch AG',''),(123,'fr','AN: Résiliation proposé par l\'employeur',''),(124,'de','AN: Übertr.i.anderen Konzern',''),(124,'fr','AN: Rangement du côte de l\'entreprise',''),(125,'de','AN: Mit Arbeitsplatz unzufrieden',''),(125,'fr','AN: Place de travail',''),(126,'de','AN: Mit Salär unzufrieden',''),(126,'fr','AN: Mécontent du salaire',''),(127,'de','AN: Berufliche Veränderung',''),(127,'fr','AN: Changement du profession',''),(128,'de','AN: Mangelnde Aufstiegsmöglichkeiten',''),(128,'fr','AN: Faute d\'avancement',''),(129,'de','AN: Wirtschaftliche Sicherheit',''),(129,'fr','AN: Sertitude économique',''),(130,'de','AN: Differenzen mit Vorgesetzten',''),(130,'fr','AN: Différences avec le supérieur',''),(131,'de','AN: Differenzen mit Mitarbeitern',''),(131,'fr','AN: Différences avac collaborateurs',''),(132,'de','AN: Überforderung',''),(132,'fr','AN: Etre dépassé',''),(133,'de','AN: Unbefriedigendes Aufgabengebiet',''),(133,'fr','AN: Travail peu satisfaisant',''),(134,'de','AN: Persönliche Gründe',''),(134,'fr','AN: Raisons personnel',''),(135,'de','AN: Stelle nicht angetreten',''),(135,'fr','AN: Pas commancé au poste d\'emploi',''),(136,'de','AN: Kündigung durch AN; Sperrliste',''),(136,'fr','AN: Résiliation de l\'employé; Sperrliste',''),(137,'de','AN: Austrittsvereinbarung',''),(138,'de','AN: wünscht keine Post mehr!',''),(139,'de','Unauffindbar',''),(140,'de','Nicht im Kader',''),(140,'fr','Pas dans le cadre',''),(141,'de','Funktionsstufe 1',''),(141,'fr','Niveau de fonction 1',''),(142,'de','Funktionsstufe 2',''),(142,'fr','Niveau de fonction 2',''),(143,'de','Funktionsstufe 3',''),(143,'fr','Niveau de fonction 3',''),(144,'de','Funktionsstufe 4',''),(144,'fr','Niveau de fonction 4',''),(145,'de','Funktionsstufe 5',''),(145,'fr','Niveau de fonction 5',''),(146,'de','Funktionsstufe 6',''),(146,'fr','Niveau de fonction 6',''),(154,'fr','ABFUELLANLAGE',''),(155,'de','Administration',''),(155,'fr','Administration',''),(156,'de','Formation',''),(156,'fr','Formation',''),(157,'de','AVOR/PPS',''),(157,'fr','AVOR/PPS',''),(158,'fr','LAGER BAHNEN',''),(159,'fr','LEITUNG BEREICH OST',''),(160,'fr','LEITUNG BEREICH TECHNIK',''),(161,'fr','LEITUNG BEREICH WEST',''),(162,'fr','LEITUNG BEREICH ZENTRAL',''),(163,'de','Bereichsleitung',''),(163,'fr','BEREICHSLEITUNG',''),(164,'de','Bestandteillager',''),(164,'fr','Dép. de pièces de rechange',''),(165,'fr','BETRIEBSLEITUNG',''),(166,'de','Back Office',''),(167,'de','Basel',''),(168,'fr','BV',''),(169,'de','Cafeterias',''),(170,'de','Contact Center',''),(171,'de','Customer Projects',''),(172,'de','Customer Services',''),(173,'de','Direktion/Administration',''),(173,'fr','Direction/Administration',''),(174,'de','Distribution',''),(174,'fr','Distribution',''),(175,'de','Dokumentation',''),(175,'fr','Documentation',''),(176,'de','Informatik',''),(176,'fr','EDV',''),(177,'fr','EDV BV-CH',''),(178,'de','Einfüllprodukte',''),(178,'fr','Produits EFP',''),(179,'de','Einkauf',''),(179,'fr','EINKAUF SCHWEIZ',''),(180,'de','Elektronik',''),(180,'fr','Electronique',''),(181,'de','Département de pièces de rechange',''),(181,'fr','Pièces de rechange',''),(182,'fr','FORSCHUNG UND ENTWICKLUNG',''),(183,'de','Fakt. Automaten',''),(183,'fr','Fact. d\'automates',''),(184,'de','Fabrication',''),(184,'fr','Fabrication',''),(185,'fr','FA PRODUKTE',''),(186,'de','Fleet-Management',''),(187,'de','Facility Management',''),(188,'de','Fertigungsmanagement',''),(189,'fr','FREMDAUTOMATEN',''),(190,'de','Gebäude',''),(190,'fr','GEBÄUDE',''),(191,'fr','GELDZAEHLEREI',''),(192,'de','Hauswart',''),(192,'fr','HAUSWART',''),(193,'fr','HAUSWART',''),(194,'de','IT-Aplikationen',''),(195,'de','IT-Infrastruktur',''),(196,'de','Verwaltung Jedematic',''),(196,'fr','VERW. JEDEMATIC',''),(197,'de','Key Account Management',''),(197,'fr','Key Account Management',''),(198,'fr','KEY-ACCOUNT',''),(199,'de','Leitg./Management DACH',''),(199,'fr','Direction/Administration',''),(200,'fr','LAGER JEDEMATIC',''),(201,'de','Logistik',''),(201,'fr','Logistique',''),(202,'de','Lager/Vertrieb',''),(202,'fr','LAGER/VERTRIEB',''),(203,'fr','MALEREI',''),(204,'de','Marketing',''),(205,'de','Montage Swiss Finish',''),(205,'fr','Montage',''),(206,'fr','NEUAUTOMATEN',''),(207,'de','Operating BE/VS',''),(207,'fr','SBB / OP BE',''),(208,'de','Operating DACH',''),(208,'fr','Operating',''),(209,'de','OP-Administration',''),(209,'fr','OP-Administration',''),(210,'de','Operating + Cafeterias',''),(210,'fr','Operating CH',''),(211,'de','OP-Dienste',''),(212,'de','Operating-Ost',''),(213,'de','Operating Schweiz',''),(214,'de','Operating-West',''),(215,'de','Operating-Zentral',''),(216,'de','SBB / OP SG',''),(217,'de','SBB / OP TI',''),(218,'fr','SBB / OP VD',''),(219,'fr','SBB / OP VS',''),(220,'de','Operating Zentral',''),(221,'fr','SBB / OP ZH',''),(222,'fr','PERSONAL',''),(223,'de','Personalentwicklung',''),(224,'de','Projekte',''),(225,'de','Public Vending Leitung/Adm.',''),(226,'de','Public Vending Ost',''),(227,'de','Public Vending West',''),(228,'de','Public Vending Zentral',''),(229,'de','Personalwesen',''),(229,'fr','Département du personnel',''),(230,'de','Entwicklung',''),(231,'de','Revision',''),(231,'fr','REVISION',''),(232,'fr','REGIONALLEITUNG',''),(233,'de','Rechnungswesen',''),(233,'fr','RECHNUNGSWESEN',''),(234,'fr','SAFFA',''),(235,'fr','SERVICE',''),(236,'de','Support Center',''),(237,'fr','SPEDITION',''),(238,'de','Stock Control',''),(239,'fr','STOREMATIC',''),(240,'de','Schulung',''),(240,'fr','SUPPORT',''),(241,'de','Swiss Finish',''),(242,'de','Systeme',''),(242,'fr','SYSTEME',''),(243,'de','Systemtechnik',''),(244,'fr','TECHNISCHES BUERO',''),(245,'de','Telesales',''),(245,'fr','TELEFON',''),(246,'de','Techn. Kundendienst',''),(246,'fr','Service clients technique',''),(247,'de','Techn. Kundendienst Ost',''),(248,'de','Techn. Kundendienst West',''),(249,'de','Techn. Kundendienst Zentral',''),(250,'de','Techn. Logistik',''),(250,'fr','TECHNISCHE LOGISTIK',''),(251,'de','Techn. Support',''),(252,'de','Verkaufs-Administration',''),(252,'fr','Administration de vente',''),(253,'de','VERKAUF JEDEMATIC',''),(254,'de','Verkauf',''),(254,'fr','Vente',''),(255,'de','Vente suisse',''),(255,'fr','Vente suisse',''),(256,'de','Verkauf Ost',''),(257,'de','Verkauf Public Vending',''),(258,'de','Verkauf Support',''),(259,'de','Verkauf West',''),(260,'de','Verkauf Zentral',''),(261,'de','WAESCHEREI',''),(262,'de','WERBUNG',''),(263,'de','WERKSTATT','');
/*!40000 ALTER TABLE `payroll_empl_list_label` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_employee`
--

DROP TABLE IF EXISTS `payroll_employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_employee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_company_ID` int(11) NOT NULL,
  `EmployeeNumber` int(10) unsigned NOT NULL,
  `Lastname` varchar(30) CHARACTER SET latin1 NOT NULL,
  `Firstname` varchar(30) CHARACTER SET latin1 NOT NULL,
  `AdditionalAddrLine1` varchar(60) CHARACTER SET latin1 NOT NULL,
  `AdditionalAddrLine2` varchar(60) CHARACTER SET latin1 NOT NULL,
  `AdditionalAddrLine3` varchar(60) CHARACTER SET latin1 NOT NULL,
  `AdditionalAddrLine4` varchar(60) CHARACTER SET latin1 NOT NULL,
  `Street` varchar(60) CHARACTER SET latin1 NOT NULL,
  `ZIP-Code` varchar(15) CHARACTER SET latin1 NOT NULL,
  `City` varchar(50) CHARACTER SET latin1 NOT NULL,
  `ResidenceCanton` varchar(2) CHARACTER SET latin1 NOT NULL,
  `Country` varchar(2) CHARACTER SET latin1 NOT NULL,
  `PlaceOfOrigin` varchar(50) CHARACTER SET latin1 NOT NULL,
  `DateOfBirth` date NOT NULL,
  `RetirementDate` date NOT NULL,
  `Age` tinyint(4) NOT NULL,
  `SeniorityJoining` date NOT NULL,
  `YearsOfService` tinyint(4) NOT NULL,
  `MonthsOfService` tinyint(4) NOT NULL,
  `AHV-AVS-Number` varchar(15) CHARACTER SET latin1 NOT NULL,
  `SV-AS-Number` varchar(17) CHARACTER SET latin1 NOT NULL,
  `Sex` varchar(1) CHARACTER SET latin1 NOT NULL DEFAULT 'M',
  `CivilStatus` int(11) NOT NULL,
  `WorkplaceCanton` varchar(2) NOT NULL,
  `Education` int(11) NOT NULL,
  `Skill` int(11) NOT NULL,
  `Position` int(11) NOT NULL,
  `PensionerExemption` tinyint(1) NOT NULL,
  `FreeTransport` tinyint(1) NOT NULL,
  `CanteenLunchCheck` tinyint(1) NOT NULL,
  `SingleParent` tinyint(1) NOT NULL,
  `AgriculturalEmployee` tinyint(1) NOT NULL,
  `Apprentice` tinyint(1) NOT NULL,
  `Nationality` varchar(2) NOT NULL,
  `CodeAHV` varchar(1) NOT NULL,
  `CodeALV` varchar(1) NOT NULL,
  `CodeUVG` varchar(2) NOT NULL,
  `CodeUVGZ1` varchar(2) NOT NULL,
  `CodeUVGZ2` varchar(2) NOT NULL,
  `CodeBVG` varchar(2) NOT NULL,
  `CodeKTG` varchar(2) NOT NULL,
  `WageCode` varchar(1) NOT NULL DEFAULT '2',
  `BaseWage` decimal(11,2) NOT NULL,
  `EmploymentStatus` tinyint(4) NOT NULL,
  `Language` varchar(2) NOT NULL,
  `PhoneNumber1` varchar(25) NOT NULL,
  `PhoneNumber2` varchar(25) NOT NULL,
  `PhoneNumber3` varchar(25) NOT NULL,
  `PhoneNumber4` varchar(25) NOT NULL,
  `EmailCompany` varchar(75) NOT NULL,
  `EmailHome` varchar(75) NOT NULL,
  `WorkplaceCity` varchar(50) NOT NULL,
  `AttendedTimeCode` tinyint(4) NOT NULL,
  `AttendedTimeHours` decimal(5,2) NOT NULL,
  `EmploymentPercentage` decimal(5,2) NOT NULL,
  `DedAtSrcMode` tinyint(4) NOT NULL,
  `DedAtSrcCanton` varchar(2) NOT NULL,
  `DedAtSrcCode` varchar(3) NOT NULL,
  `DedAtSrcPercentage` decimal(8,2) NOT NULL,
  `DedAtSrcMunicipality` varchar(14) NOT NULL,
  `DedAtSrcCompany` tinyint(1) NOT NULL,
  `CostCenter` varchar(45) NOT NULL,
  `CostUnit` varchar(2) NOT NULL,
  `AliensCategory` int(11) NOT NULL,
  `AliensPermitNo` varchar(25) NOT NULL,
  `FRFE05` varchar(1) NOT NULL,
  `FRFE07` varchar(2) NOT NULL,
  `FRFE10` varchar(2) NOT NULL,
  `FRFE11` varchar(3) NOT NULL,
  `FRFE12` varchar(3) NOT NULL,
  `FRFE14` varchar(3) NOT NULL,
  `FRFE15` varchar(3) NOT NULL,
  `FRFE18` varchar(15) NOT NULL,
  `FRFE22` smallint(6) NOT NULL,
  `FRFE24` smallint(6) NOT NULL,
  `FRFE27` smallint(6) NOT NULL,
  `ManagementLevel` int(11) NOT NULL,
  `Department` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_payroll_employee_payroll_company1_idx` (`payroll_company_ID`),
  CONSTRAINT `fk_payroll_employee_payroll_company1` FOREIGN KEY (`payroll_company_ID`) REFERENCES `payroll_company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_employee`
--

LOCK TABLES `payroll_employee` WRITE;
/*!40000 ALTER TABLE `payroll_employee` DISABLE KEYS */;
INSERT INTO `payroll_employee` VALUES (5,1,21,'Ganz','Edith','','','','','Hangweg 6','3098','Köniz','BE','CH','Köniz','1955-06-18','2019-06-18',58,'2010-06-01',3,2,'379.49.680.119','','F',10,'BE',46,60,55,0,0,0,0,0,0,'CH','1','1','A1','12','','2','12','2',22000.00,2,'','','','','','','','',1,0.00,100.00,0,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(6,1,20,'Paganini','Maria','','','','','Stubenweid','6030','Ebikon','LU','CH','Italien','1948-09-30','2012-09-30',64,'2009-06-01',4,2,'701.42.792.158','','F',8,'LU',52,58,57,0,0,0,0,0,0,'IT','1','1','Z1','11','','2','11','1',30.00,2,'','','','','','','','',1,0.00,100.00,0,'','',0.00,'',0,'','',3,'','','','','','','','','',0,0,0,0,0),(7,1,17,'Nestler','Paula','','','','','Bollstrasse 4','6064','Kerns','OW','CH','Kerns','1979-10-04','2043-10-04',33,'2011-01-01',2,7,'678.73.804.111','','F',8,'LU',49,59,57,0,1,0,0,0,0,'CH','1','1','A1','11','12','2','12','2',9450.00,2,'','','','','','','','',1,0.00,100.00,0,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(8,1,18,'Nunez','Maria','','','','','Grüneggstrasse 22','6005','Luzern','LU','CH','Spanien','1948-02-04','2012-02-04',65,'2011-01-01',2,7,'687.42.535.154','','F',9,'LU',50,58,57,0,0,0,0,0,0,'ES','4','5','Z1','12','','1','12','2',1500.00,2,'','','','','','','','',1,0.00,100.00,0,'','',0.00,'',0,'12345','',2,'','','','','','','','','',0,0,0,0,0),(9,1,19,'Ott','Hans','','','','','Unterdorf 5','6037','Root','LU','CH','Root','1994-12-30','2059-12-30',18,'2011-09-01',1,11,'698.88.492.100','','M',7,'LU',49,59,57,0,0,0,0,0,0,'CH','1','1','A1','11','','1','11','2',6400.00,2,'','','','','','','','',1,0.00,100.00,0,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(10,1,8,'Estermann','Michael','','','','','Löwengrube 12','6014','Littau','LU','CH','Deutschland','1946-01-01','2011-01-01',67,'2012-01-01',1,7,'322.40.101.156','','M',8,'LU',49,59,57,0,0,0,0,0,0,'DE','4','5','Z3','','','0','12','4',0.00,2,'','','','','','','','',1,0.00,100.00,0,'','',0.00,'',0,'','',2,'','','','','','','','','',0,0,0,0,0),(11,1,9,'Farine','Corinne','','','','','Simplonstrasse 26','3900','Brig','VS','CH','Brig','1986-06-17','2050-06-17',27,'2010-08-01',3,0,'329.80.679.119','','F',7,'VS',50,59,57,0,0,0,1,0,0,'CH','1','1','Z1','12','','1','11','2',20000.00,2,'','','','','','','','',1,0.00,100.00,0,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(12,1,10,'Ganz','Heinz','','','','','Neuhofstrasse 49','6020','Emmenbrücke','LU','CH','Emmenbrücke','1986-02-28','2051-02-28',27,'2012-02-01',1,6,'379.80.159.117','','M',7,'LU',51,58,57,0,1,1,0,0,0,'CH','1','1','A1','11','','1','11','2',5000.00,2,'','','','','','','','',1,0.00,100.00,0,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(13,1,15,'Lusser','Pia','','','','','Acherweg 24','6370','Stans','NW','CH','Stans','1948-02-05','2012-02-05',65,'2011-08-01',2,0,'619.42.536.118','','F',8,'LU',49,59,57,0,0,0,0,0,0,'CH','4','5','A1','11','','1','11','2',800.00,2,'de','','','','','','','',0,33.60,20.00,1,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(14,1,16,'Martin','René','','','','','Mettlen 12','6363','Fürigen','NW','CH','Fürigen','1947-09-28','2012-09-28',65,'2012-03-01',1,5,'633.41.390.119','','M',8,'LU',52,59,57,0,0,1,0,0,0,'CH','4','5','Z1','11','','1','11','3',0.00,2,'','','','','','','','',1,0.00,100.00,0,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(15,1,12,'Inglese','Rosa','','','','','Bachstrasse 6','6048','Horw','LU','CH','Italien','1979-10-15','2043-10-15',33,'1998-01-01',15,7,'502.73.815.160','','F',7,'LU',52,58,57,0,0,1,0,0,0,'IT','1','1','Z1','11','','1','11','2',4700.00,2,'','','','','','','','',1,0.00,100.00,0,'','',0.00,'',0,'','',1,'','','','','','','','','',0,0,0,0,0),(16,1,13,'Jung','Claude','','','','','Sonnengasse 2','6210','Sursee','LU','CH','Sursee','1988-03-23','2053-03-23',25,'2011-10-01',1,10,'522.82.185.113','','M',7,'LU',46,61,54,0,1,0,0,0,0,'CH','1','1','A2','11','','1','11','2',15000.00,2,'de','','','','','','','',1,0.00,100.00,1,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(17,1,14,'Kaiser','Beat','','','','','Bahnstrasse 6','68540','Feldkirch','EX','AT','Österreich','1960-08-15','2025-08-15',52,'2009-01-01',4,7,'526.54.346.178','','M',8,'LU',48,60,57,0,0,0,0,0,0,'AT','1','1','Z1','11','','1','11','2',5000.00,2,'','','','','','','','',1,0.00,100.00,0,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(18,1,11,'Herz','Monica','','','','','Sustenweg 12','6020','Emmenbrücke','LU','CH','Emmenbrücke','1966-06-30','2030-06-30',47,'2011-11-01',1,9,'','','F',8,'LU',46,60,55,0,0,0,0,0,0,'CH','1','1','Z1','11','','1','11','1',30.00,2,'','','','','','','','',1,0.00,100.00,0,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(19,1,7,'Egli','Anna','','','','','Ahornweg 9','9999','Weggis','LU','CH','Weggis','1967-07-13','2031-07-13',46,'2011-12-01',1,8,'307.61.713.112','','F',11,'LU',52,58,57,0,1,0,0,0,0,'CH','4','5','Z1','11','','0','10','2',5000.00,2,'de','','','','','','','',3,0.00,50.00,1,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(20,1,6,'Combertaldi','Renato','','','','','Brügglistrasse 2','6004','Luzern','LU','CH','Italien','1995-01-01','2060-01-01',18,'2010-06-01',3,2,'268.89.101.155','','M',7,'LU',49,59,57,0,0,0,0,0,1,'IT','2','8','Z0','','','0','10','2',0.00,2,'','','','','','','','',1,0.00,100.00,0,'','',0.00,'',0,'','',3,'','','','','','','','','',0,0,0,0,0),(21,1,1,'Bosshard','Peter','','','','','Hauptstrasse 5','6072','Sachseln','OW','CH','Sachseln','1968-04-11','2033-04-11',45,'2009-07-01',4,1,'197.62.211.117','','M',7,'LU',44,61,53,0,1,1,0,0,0,'CH','1','1','A0','','','2','12','3',0.00,2,'','','','','','','','',1,0.00,100.00,0,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(22,1,24,'Fankhauser','Markus','','','','','Talweg 6','3150','Schwarzenburg','BE','CH','Schwarzenburg','1956-10-19','2021-10-19',56,'1998-01-01',15,7,'328.50.419.115','','M',7,'BE',44,61,53,0,0,0,0,0,0,'CH','1','1','A1','12','','2','12','3',280000.00,2,'','','','','','','','',1,0.00,100.00,0,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(23,1,23,'Rieder','Catia','','','','','Höheweg 10','2532','Magglingen/Macolin','BE','CH','Magglingen','1981-11-01','2045-11-01',31,'2004-07-01',9,1,'743.75.832.112','','F',8,'BE',45,60,56,0,0,0,0,0,0,'CH','1','1','A1','11','','1','11','2',80000.00,2,'','','','','','','','',1,0.00,100.00,0,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(24,1,22,'Lamon','René','','','','','Grande rue','90100','Delle','EX','FR','Frankreich','1974-03-16','2039-03-16',39,'2011-03-15',2,5,'576.68.178.253','','M',6,'BE',49,59,57,0,0,0,0,0,0,'FR','1','1','B1','11','','1','11','1',31.00,2,'','','','','','','','',1,0.00,100.00,0,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(25,1,25,'Burri','Heidi','','','','','Laupenstrasse 45','3008','Bern','BE','CH','Bern','1948-01-16','2012-01-16',65,'1976-09-02',36,11,'243.42.516.110','','F',8,'BE',49,59,57,0,0,0,0,0,0,'CH','4','5','A1','11','','1','11','2',84000.00,2,'','','','','','','','',1,0.00,100.00,0,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(26,1,26,'Moser','Johann','','','','','Kramgasse 8','3011','Bern','BE','CH','Bern','1945-04-15','2010-04-15',68,'1971-03-01',42,5,'665.39.215.117','','M',8,'BE',50,58,57,0,0,0,0,0,0,'CH','4','5','B3','11','','0','11','4',0.00,2,'','','','','','','','',2,0.00,80.00,0,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(27,1,27,'Zahnd','Anita','','','','','Bachstrasse 10','3072','Ostermundigen','BE','CH','Ostermundigen','1966-05-23','2030-05-23',47,'1991-02-01',22,6,'976.60.654.112','','F',7,'BE',46,60,55,0,0,0,0,0,0,'CH','1','1','Z1','11','','1','11','2',110000.00,2,'','','','','','','','',1,0.00,100.00,0,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(28,1,28,'Racine','Susette','','','','','Grand-Rue 98','2603','Péry','BE','CH','Péry','1946-08-26','2010-08-26',66,'1966-04-01',47,4,'725.40.757.128','','F',8,'BE',51,58,57,0,0,0,0,0,0,'CH','4','5','A1','11','','0','11','2',20000.00,2,'','','','','','','','',1,0.00,100.00,0,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(29,1,29,'Perret','Michelle','','','','','Bahnhofstrasse 54','2502','Biel/Bienne','BE','CH','Biel','1984-03-16','2048-03-16',29,'1998-01-01',15,7,'707.78.578.113','','F',7,'BE',48,60,56,0,0,0,0,0,0,'CH','1','1','A1','11','','1','11','2',95000.00,2,'','','','','','','','',1,0.00,100.00,0,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(30,1,2,'Aebi','Anna','','','','','Bundesstrasse 5','6003','Luzern','LU','CH','Luzern','1947-12-31','2011-12-31',65,'2012-02-01',1,6,'101.41.893.128','','F',7,'LU',48,60,54,0,0,0,0,0,0,'CH','4','5','A1','12','','0','10','1',59.30,2,'de','','','','','','','',1,0.00,100.00,1,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(31,1,3,'Casanova','Renato','','','','','Bahnhofstrasse 20','6048','Horw','LU','CH','Horw','1994-01-01','2059-01-01',19,'2012-02-27',1,6,'255.88.101.110','','M',7,'LU',46,60,55,0,0,0,0,0,0,'CH','1','1','A2','12','','2','12','2',30000.00,2,'','','','','','','','',1,0.00,100.00,0,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(32,1,5,'Duss','Regula','','','','','Maihofstrasse 42','6004','Luzern','LU','CH','Luzern','1975-12-11','2039-12-11',37,'2011-05-01',2,3,'298.69.873.118','','F',7,'LU',45,61,55,0,0,0,0,0,0,'CH','1','1','A1','11','','1','10','2',35000.00,2,'','','','','','','','',0,126.00,75.00,0,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(33,1,4,'Degelo','Lorenz','','','','','Lopperstrasse 8','6010','Kriens','LU','CH','Kriens','1976-02-28','2041-02-28',37,'2006-02-28',7,6,'279.70.159.113','','M',8,'LU',47,59,57,0,0,0,0,0,0,'CH','1','1','A3','11','','0','10','1',10.00,2,'','','','','','','','',1,0.00,100.00,0,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(34,1,30,'Schüpbach','Ernst','','','','','Hirschhorn','3153','Rüschegg Gambach','BE','CH','Rüschegg','1946-11-11','2011-11-11',66,'2011-02-20',2,6,'837.40.442.115','','M',6,'BE',52,58,57,0,0,0,0,0,0,'CH','4','5','Z3','','','0','10','2',0.00,2,'de','','','','','','','',1,0.00,100.00,1,'','',0.00,'',0,'','',0,'','','','','','','','','',0,0,0,0,0),(35,1,99,'Mister','X','','','','','Scotland Yard','GB','London','AG','DE','London','1979-01-01','2044-01-01',34,'2011-01-01',2,7,'','','M',6,'AG',52,58,57,0,0,0,0,0,0,'US','1','1','A0','','','0','10','1',0.00,1,'de','','','','','','','',1,0.00,0.00,1,'','',0.00,'',0,'','',3,'','','','','','','','','',0,0,0,0,0);
/*!40000 ALTER TABLE `payroll_employee` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_employee_account`
--

DROP TABLE IF EXISTS `payroll_employee_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_employee_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_employee_ID` int(11) NOT NULL,
  `payroll_account_ID` varchar(5) CHARACTER SET latin1 NOT NULL,
  `PayrollDataType` tinyint(4) NOT NULL DEFAULT '1',
  `account_text` varchar(75) CHARACTER SET latin1 NOT NULL,
  `quantity` decimal(12,5) NOT NULL,
  `rate` decimal(12,5) NOT NULL,
  `amount` decimal(12,5) NOT NULL,
  `TargetField` tinyint(4) NOT NULL DEFAULT '0',
  `max_limit` decimal(8,2) NOT NULL,
  `min_limit` decimal(8,2) NOT NULL,
  `deduction` decimal(8,2) NOT NULL,
  `CostCenter` varchar(45) CHARACTER SET latin1 NOT NULL,
  `DateFrom` date NOT NULL,
  `DateTo` date NOT NULL,
  `major_period` tinyint(1) NOT NULL DEFAULT '1',
  `minor_period` tinyint(1) NOT NULL DEFAULT '1',
  `major_period_bonus` tinyint(1) NOT NULL DEFAULT '1',
  `allowable_workdays` tinyint(4) NOT NULL,
  `allowable_workdays_sum` smallint(6) NOT NULL,
  `amount_balance` decimal(13,5) NOT NULL,
  `having_pensioner_calc` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=248 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_employee_account`
--

LOCK TABLES `payroll_employee_account` WRITE;
/*!40000 ALTER TABLE `payroll_employee_account` DISABLE KEYS */;
INSERT INTO `payroll_employee_account` VALUES (12,30,'1005',2,'',0.00000,59.30000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(13,30,'5010',8,'',0.00000,5.05000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(14,30,'5013',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(16,30,'5030',8,'',0.00000,1.46000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(17,30,'5035',8,'',0.00000,0.60900,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(18,30,'5039',8,'',0.00000,1.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(19,30,'5041',8,'',0.00000,0.63400,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(20,30,'9130',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(21,30,'9141',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(23,33,'1005',2,'',0.00000,10.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(24,33,'5010',8,'',0.00000,5.05000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(25,33,'5013',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(26,33,'5020',8,'',0.00000,1.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(27,33,'5025',8,'',0.00000,0.50000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(28,33,'5023',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(31,33,'5035',8,'',0.00000,0.60900,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(32,33,'5040',8,'',0.00000,0.20100,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(33,33,'9120',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(34,33,'9130',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(35,33,'9140',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(36,33,'5020',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(37,33,'5025',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(38,33,'5035',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(39,33,'5036',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(40,33,'5037',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(41,33,'5040',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(42,33,'5041',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(43,33,'5050',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(44,33,'5051',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(51,32,'5010',8,'',0.00000,5.05000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(52,32,'5013',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(53,32,'5020',8,'',0.00000,1.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(54,32,'5025',8,'',0.00000,0.50000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(55,32,'5023',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(58,32,'5030',8,'',0.00000,1.46000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(59,32,'5035',8,'',0.00000,0.60900,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(60,32,'5039',8,'',0.00000,1.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(61,32,'5040',8,'',0.00000,0.20100,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(62,32,'9120',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(63,32,'9130',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(64,32,'9140',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(65,20,'5020',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(66,20,'5025',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(67,20,'5035',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(68,20,'5036',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(69,20,'5037',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(70,20,'5040',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(71,20,'5041',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(72,20,'5050',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(73,20,'5051',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(80,19,'5032',8,'',0.00000,1.19000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(81,19,'5037',8,'',0.00000,0.53000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(82,19,'5039',8,'',0.00000,1.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(83,19,'5040',8,'',0.00000,0.20100,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(84,19,'9132',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(85,19,'9140',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(87,18,'1005',2,'',0.00000,30.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(88,16,'5010',8,'',0.00000,5.05000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(89,16,'5013',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(90,16,'5020',8,'',0.00000,1.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(91,16,'5025',8,'',0.00000,0.50000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(92,16,'5023',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(95,16,'5039',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(96,16,'5030',8,'',0.00000,1.46000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(97,16,'5035',8,'',0.00000,0.60900,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(98,16,'5040',8,'',0.00000,0.20100,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(99,16,'5050',8,'',0.00000,0.54300,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(101,16,'9120',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(102,16,'9130',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(103,16,'9140',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(104,16,'9150',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(108,13,'5010',8,'',0.00000,5.05000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(109,13,'5013',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(111,13,'5030',8,'',0.00000,1.46000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(112,13,'5035',8,'',0.00000,0.60900,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(113,13,'5039',8,'',0.00000,1.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(114,13,'5040',8,'',0.00000,0.20100,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(115,13,'5050',8,'',0.00000,0.61000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(117,13,'9130',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(118,13,'9140',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(119,13,'9150',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(120,14,'1001',2,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(121,14,'5010',8,'',0.00000,5.05000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(122,14,'5013',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(124,14,'5032',8,'',0.00000,1.19000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(125,14,'5037',8,'',0.00000,0.53000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(126,14,'5039',8,'',0.00000,1.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(127,14,'5040',8,'',0.00000,0.20100,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(128,14,'5050',8,'',0.00000,0.54300,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(130,14,'9132',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(131,14,'9140',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(132,14,'9150',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(133,8,'5010',8,'',0.00000,5.05000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(134,8,'5013',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(136,8,'5032',8,'',0.00000,1.19000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(137,8,'5037',8,'',0.00000,0.53000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(138,8,'5039',8,'',0.00000,1.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(139,8,'5041',8,'',0.00000,0.63400,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(140,8,'5051',8,'',0.00000,0.72000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(142,8,'9132',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(143,8,'9141',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(144,8,'9151',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(145,6,'1005',2,'',0.00000,30.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(146,6,'5032',8,'',0.00000,1.19000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(147,6,'5037',8,'',0.00000,0.53000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(148,6,'5039',8,'',0.00000,1.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(149,6,'5040',8,'',0.00000,0.20100,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(150,6,'5050',8,'',0.00000,0.61000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(152,6,'9132',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(153,6,'9140',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(154,6,'9150',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(155,6,'5020',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(156,6,'5025',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(157,6,'5035',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(158,6,'5036',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(159,6,'5037',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(160,6,'5040',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(161,6,'5041',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(162,6,'5050',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(163,6,'5051',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(170,5,'5010',8,'',0.00000,5.05000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(171,5,'5013',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(172,5,'5020',8,'',0.00000,1.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(173,5,'5025',8,'',0.00000,0.50000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(174,5,'5023',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(177,5,'5030',8,'',0.00000,1.46000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(178,5,'5035',8,'',0.00000,0.60900,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(179,5,'5039',8,'',0.00000,1.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(180,5,'5041',8,'',0.00000,0.63400,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(181,5,'5051',8,'',0.00000,0.72000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(183,5,'9120',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(184,5,'9130',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(185,5,'9141',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(186,5,'9151',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(190,5,'5020',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(191,5,'5025',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(192,5,'5035',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(193,5,'5036',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(194,5,'5037',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(195,5,'5040',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(196,5,'5041',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(197,5,'5050',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(198,5,'5051',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(205,24,'1005',2,'',0.00000,31.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(206,22,'1001',2,'',0.00000,0.00000,280000.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(207,25,'5010',8,'',0.00000,5.05000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(208,25,'5013',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(210,25,'5030',8,'',0.00000,1.46000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(211,25,'5035',8,'',0.00000,0.60900,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(212,25,'5039',8,'',0.00000,1.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(213,25,'5040',8,'',0.00000,0.20100,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(214,25,'5050',8,'',0.00000,0.61000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(216,25,'9130',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(217,25,'9140',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(218,25,'9150',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(219,25,'5020',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(220,25,'5025',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(221,25,'5035',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(222,25,'5036',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(223,25,'5037',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(224,25,'5040',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(225,25,'5041',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(226,25,'5050',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(227,25,'5051',9,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,30,0,0.00000,0),(234,34,'5010',8,'',0.00000,5.05000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(235,34,'5013',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(237,34,'5037',8,'',0.00000,0.53000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(238,34,'9132',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(239,35,'1005',2,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(240,35,'5010',8,'',0.00000,5.05000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(241,35,'5013',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(242,35,'5020',8,'',0.00000,1.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(243,35,'5025',8,'',0.00000,0.50000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(244,35,'5023',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0),(247,35,'9120',8,'',0.00000,0.00000,0.00000,0,0.00,0.00,0.00,'','0000-00-00','0000-00-00',1,1,1,0,0,0.00000,0);
/*!40000 ALTER TABLE `payroll_employee_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_employee_career`
--

DROP TABLE IF EXISTS `payroll_employee_career`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_employee_career` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_employee_ID` int(11) NOT NULL,
  `DateFrom` date NOT NULL,
  `CostCenter` varchar(20) CHARACTER SET latin1 NOT NULL,
  `Department` varchar(45) CHARACTER SET latin1 NOT NULL,
  `Position` varchar(45) CHARACTER SET latin1 NOT NULL,
  `Workplace` varchar(50) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_table3_payroll_employee1_idx` (`payroll_employee_ID`),
  CONSTRAINT `fk_table3_payroll_employee1` FOREIGN KEY (`payroll_employee_ID`) REFERENCES `payroll_employee` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_employee_career`
--

LOCK TABLES `payroll_employee_career` WRITE;
/*!40000 ALTER TABLE `payroll_employee_career` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_employee_career` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_employee_children`
--

DROP TABLE IF EXISTS `payroll_employee_children`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_employee_children` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_employee_ID` int(11) NOT NULL,
  `Lastname` varchar(30) CHARACTER SET latin1 NOT NULL,
  `Firstname` varchar(30) CHARACTER SET latin1 NOT NULL,
  `Sex` varchar(1) CHARACTER SET latin1 NOT NULL,
  `DateOfBirth` date NOT NULL,
  `DateFrom` date NOT NULL,
  `DateTo` date NOT NULL,
  `Reason` varchar(60) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_payroll_employee_children_payroll_employee1_idx` (`payroll_employee_ID`),
  CONSTRAINT `fk_payroll_employee_children_payroll_employee1` FOREIGN KEY (`payroll_employee_ID`) REFERENCES `payroll_employee` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_employee_children`
--

LOCK TABLES `payroll_employee_children` WRITE;
/*!40000 ALTER TABLE `payroll_employee_children` DISABLE KEYS */;
INSERT INTO `payroll_employee_children` VALUES (1,21,'Kind','1','M','2012-01-01','2012-01-01','2028-12-31','Ende KiZu'),(2,21,'Kind','2','M','2000-01-01','2000-01-01','2015-12-31','Ende KiZu'),(3,11,'Kind','1','F','1996-01-01','1996-01-01','2020-12-31','Ende Ausbildung');
/*!40000 ALTER TABLE `payroll_employee_children` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_employee_education`
--

DROP TABLE IF EXISTS `payroll_employee_education`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_employee_education` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_employee_ID` int(11) NOT NULL,
  `DateFrom` date NOT NULL,
  `DateTo` date NOT NULL,
  `Education` varchar(40) CHARACTER SET latin1 NOT NULL,
  `Comment` varchar(160) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_payroll_employee_education_payroll_employee1_idx` (`payroll_employee_ID`),
  CONSTRAINT `fk_payroll_employee_education_payroll_employee1` FOREIGN KEY (`payroll_employee_ID`) REFERENCES `payroll_employee` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_employee_education`
--

LOCK TABLES `payroll_employee_education` WRITE;
/*!40000 ALTER TABLE `payroll_employee_education` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_employee_education` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_employee_field_def`
--

DROP TABLE IF EXISTS `payroll_employee_field_def`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_employee_field_def` (
  `fieldName` varchar(30) NOT NULL,
  `fieldType` tinyint(3) unsigned NOT NULL,
  `containerElement` tinyint(1) NOT NULL,
  `childOf` varchar(30) NOT NULL,
  `childOrder` tinyint(3) unsigned NOT NULL,
  `minLength` tinyint(3) unsigned NOT NULL,
  `maxLength` tinyint(3) unsigned NOT NULL,
  `system` tinyint(1) NOT NULL,
  `mandatory` tinyint(1) NOT NULL,
  `read-only` tinyint(1) NOT NULL,
  `regexPattern` varchar(255) NOT NULL,
  `minVal` decimal(12,5) NOT NULL,
  `maxVal` decimal(12,5) NOT NULL,
  `dataSource` varchar(30) NOT NULL,
  `dataSourceGroup` tinyint(4) unsigned NOT NULL,
  `dataSourceToken` tinyint(1) NOT NULL,
  `callback` tinyint(1) NOT NULL,
  `guiWidth` varchar(2) NOT NULL,
  PRIMARY KEY (`fieldName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_employee_field_def`
--

LOCK TABLES `payroll_employee_field_def` WRITE;
/*!40000 ALTER TABLE `payroll_employee_field_def` DISABLE KEYS */;
INSERT INTO `payroll_employee_field_def` VALUES ('AdditionalAddrLine1',1,0,'',0,0,60,1,0,0,'',0.00000,0.00000,'',0,0,0,'XL'),('AdditionalAddrLine2',1,0,'',0,0,60,1,0,0,'',0.00000,0.00000,'',0,0,0,'XL'),('AdditionalAddrLine3',1,0,'',0,0,60,1,0,0,'',0.00000,0.00000,'',0,0,0,'XL'),('AdditionalAddrLine4',1,0,'',0,0,60,1,0,0,'',0.00000,0.00000,'',0,0,0,'XL'),('Age',3,0,'',0,0,3,1,0,1,'/^[0-9]{2,3}$/',15.00000,120.00000,'',0,0,0,'S'),('AgriculturalEmployee',2,0,'',0,0,0,1,0,0,'/^[01]{1,1}$/',0.00000,0.00000,'',0,0,0,'S'),('AHV-AVS-Number',1,0,'',0,0,15,1,0,0,'/^[1-9][0-9]{2}\\.[0-9]{2}\\.[1-8][0-9]{2}\\.[0-9]{3}$/',0.00000,0.00000,'',0,0,0,'L'),('AliensCategory',4,0,'',0,0,2,1,0,0,'',0.00000,0.00000,'payroll_empl_list',1,0,0,'L'),('AliensPermitNo',1,0,'',0,0,25,1,0,0,'',0.00000,0.00000,'',0,0,0,'L'),('Apprentice',2,0,'',0,0,0,1,0,0,'/^[01]{1,1}$/',0.00000,0.00000,'',0,0,0,'S'),('AttendedTimeCode',4,0,'',0,0,1,1,1,0,'',0.00000,0.00000,'payroll_empl_list',13,1,0,'L'),('AttendedTimeHours',3,0,'',0,0,6,1,0,0,'/^[0-9]{1,3}(\\.[0-9]{1,2})?$/',0.00000,999.99000,'',0,0,0,'M'),('BaseWage',3,0,'',0,0,10,1,1,0,'/^[0-9]{1,7}(\\.[0-9]{1,2})?$/',0.00000,9999999.00000,'',0,0,0,'M'),('CanteenLunchCheck',2,0,'',0,0,0,1,0,0,'/^[01]{1,1}$/',0.00000,0.00000,'',0,0,0,'S'),('City',1,0,'ZipCity',2,0,50,1,1,0,'',0.00000,0.00000,'',0,0,0,'L'),('CivilStatus',4,0,'',0,0,25,1,1,0,'/^[0-9]{1,9}$/',0.00000,0.00000,'payroll_empl_list',2,0,0,'XL'),('CodeAHV',4,0,'',0,0,1,1,1,0,'/^[1234568]{1,1}$/',0.00000,0.00000,'payroll_insurance_code',1,0,0,'L'),('CodeALV',4,0,'',0,0,1,1,1,0,'/^[12358]{1,1}$/',0.00000,0.00000,'payroll_insurance_code',2,0,0,'L'),('CodeBVG',4,0,'',0,0,2,1,1,0,'/^[0-9]{1,1}$/',0.00000,0.00000,'payroll_insurance_code',7,0,0,'L'),('CodeKTG',4,0,'',0,0,2,1,1,0,'/^[A-Z0-9]{2,2}$/',0.00000,0.00000,'payroll_insurance_code',6,0,0,'L'),('CodeUVG',4,0,'',0,0,2,1,1,0,'/^[A-Z]{1,1}[0-3]{1,1}$/',0.00000,0.00000,'payroll_insurance_code',4,0,0,'L'),('CodeUVGZ1',4,0,'',0,0,2,1,0,0,'/^([A-Z0-9]{2,2})?$/',0.00000,0.00000,'payroll_insurance_code',5,0,0,'L'),('CodeUVGZ2',4,0,'',0,0,2,1,0,0,'/^([A-Z0-9]{2,2})?$/',0.00000,0.00000,'payroll_insurance_code',5,0,0,'L'),('CostCenter',1,0,'',0,0,25,1,0,0,'',0.00000,0.00000,'',0,0,0,'M'),('CostUnit',1,0,'',0,0,2,1,0,0,'',0.00000,0.00000,'',0,0,0,'S'),('Country',4,0,'',0,0,25,1,1,0,'/^[A-Z]{2,2}$/',0.00000,0.00000,'payroll_empl_list',9,1,0,'L'),('DateOfBirth',5,0,'',0,0,10,1,1,0,'',0.00000,0.00000,'',0,0,1,'M'),('DedAtSrcCanton',4,0,'',0,0,2,1,0,0,'/^([A-Z]{2,2})?$/',0.00000,0.00000,'payroll_empl_list',3,1,0,'L'),('DedAtSrcCode',1,0,'',0,0,3,1,0,0,'',0.00000,0.00000,'',0,0,0,'S'),('DedAtSrcCompany',2,0,'',0,0,0,1,0,0,'/^[01]{1,1}$/',0.00000,0.00000,'',0,0,0,'S'),('DedAtSrcMode',4,0,'',0,0,1,1,1,0,'/^[1-5]{1,1}$/',0.00000,0.00000,'payroll_empl_list',11,1,0,'L'),('DedAtSrcMunicipality',1,0,'',0,0,14,1,0,0,'',0.00000,0.00000,'',0,0,0,'M'),('DedAtSrcPercentage',3,0,'',0,0,9,1,0,0,'/^[0-9]{1,6}(\\.[0-9]{1,2})?$/',0.00000,999999.99000,'',0,0,0,'M'),('Department',4,0,'',0,0,10,1,0,0,'',0.00000,0.00000,'payroll_empl_list',15,0,0,'L'),('Education',4,0,'',0,0,50,1,1,0,'/^[0-9]{1,9}$/',0.00000,0.00000,'payroll_empl_list',4,0,0,'XL'),('EmailCompany',1,0,'',0,0,75,1,0,0,'',0.00000,0.00000,'',0,0,0,'XL'),('EmailHome',1,0,'',0,0,75,1,0,0,'',0.00000,0.00000,'',0,0,0,'XL'),('EmployeeNumber',1,0,'',0,0,9,1,1,0,'/^[0-9]{1,9}$/',0.00000,0.00000,'',0,0,0,'M'),('EmploymentPercentage',3,0,'',0,0,6,1,0,0,'/^[0-9]{1,3}(\\.[0-9]{1,2})?$/',0.00000,999.00000,'',0,0,0,'M'),('EmploymentStatus',4,0,'',0,0,1,1,1,0,'',0.00000,0.00000,'payroll_empl_list',12,1,0,'L'),('Firstname',1,0,'',0,0,30,1,1,0,'',0.00000,0.00000,'',0,0,0,'L'),('FreeTransport',2,0,'',0,0,0,1,0,0,'/^[01]{1,1}$/',0.00000,0.00000,'',0,0,0,'S'),('FRFE05',1,0,'',0,0,1,0,0,0,'',0.00000,0.00000,'',0,0,0,'S'),('FRFE07',1,0,'',0,0,2,0,0,0,'',0.00000,0.00000,'',0,0,0,'S'),('FRFE10',1,0,'',0,0,2,0,0,0,'',0.00000,0.00000,'',0,0,0,'S'),('FRFE11',1,0,'',0,0,3,0,0,0,'',0.00000,0.00000,'',0,0,0,'S'),('FRFE12',1,0,'',0,0,3,0,0,0,'',0.00000,0.00000,'',0,0,0,'S'),('FRFE14',1,0,'',0,0,3,0,0,0,'',0.00000,0.00000,'',0,0,0,'S'),('FRFE15',1,0,'',0,0,3,0,0,0,'',0.00000,0.00000,'',0,0,0,'S'),('FRFE18',1,0,'',0,0,15,0,0,0,'',0.00000,0.00000,'',0,0,0,'M'),('FRFE22',3,0,'',0,0,3,0,0,0,'/^[0-9]{1,3}$/',-999.99000,999.99000,'',0,0,0,'S'),('FRFE24',3,0,'',0,0,3,0,0,0,'/^[0-9]{1,3}$/',-999.99000,999.99000,'',0,0,0,'S'),('FRFE27',3,0,'',0,0,3,0,0,0,'/^[0-9]{1,3}$/',-999.99000,999.99000,'',0,0,0,'S'),('id',3,0,'',0,0,0,1,0,0,'/^[0-9]{1,10}$/',0.00000,0.00000,'',0,0,0,'M'),('Language',4,0,'',0,0,2,1,1,0,'/^[a-z]{2,2}$/',0.00000,0.00000,'payroll_languages',0,1,0,'L'),('Lastname',1,0,'',0,0,30,1,1,0,'',0.00000,0.00000,'',0,0,0,'L'),('ManagementLevel',4,0,'',0,0,10,1,0,0,'/^[0-9]{1,9}$/',0.00000,0.00000,'payroll_empl_list',14,0,0,'L'),('MonthsOfService',3,0,'',0,0,2,1,0,1,'/^[0-9]{1,2}$/',0.00000,11.00000,'',0,0,0,'S'),('Nationality',4,0,'',0,0,50,1,0,0,'/^[A-Z]{2,2}$/',0.00000,0.00000,'core_intl_country_names',0,0,0,'L'),('payroll_company_ID',4,0,'',0,0,25,1,1,0,'/^[0-9]{1,10}$/',0.00000,0.00000,'payroll_company',0,0,0,'M'),('PensionerExemption',2,0,'',0,0,0,1,0,0,'/^[01]{1,1}$/',0.00000,0.00000,'',0,0,0,'S'),('PhoneNumber1',1,0,'',0,0,25,1,0,0,'',0.00000,0.00000,'',0,0,0,'L'),('PhoneNumber2',1,0,'',0,0,25,1,0,0,'',0.00000,0.00000,'',0,0,0,'L'),('PhoneNumber3',1,0,'',0,0,25,1,0,0,'',0.00000,0.00000,'',0,0,0,'L'),('PhoneNumber4',1,0,'',0,0,25,1,0,0,'',0.00000,0.00000,'',0,0,0,'L'),('PlaceOfOrigin',1,0,'',0,0,50,1,0,0,'',0.00000,0.00000,'',0,0,0,'L'),('Position',4,0,'',0,0,50,1,1,0,'/^[0-9]{1,9}$/',0.00000,0.00000,'payroll_empl_list',5,0,0,'L'),('ResidenceCanton',4,0,'',0,0,50,1,1,0,'/^[A-Z]{2,2}$/',0.00000,0.00000,'payroll_empl_list',3,1,0,'L'),('RetirementDate',5,0,'',0,0,10,1,0,1,'',0.00000,0.00000,'',0,0,0,'M'),('SeniorityJoining',5,0,'',0,0,10,1,0,0,'',0.00000,0.00000,'',0,0,1,'M'),('Sex',4,0,'',0,0,25,1,1,0,'/^[MF]{1}$/',0.00000,0.00000,'payroll_empl_list',7,1,1,'M'),('SingleParent',2,0,'',0,0,0,1,0,0,'/^[01]{1,1}$/',0.00000,0.00000,'',0,0,0,'S'),('Skill',4,0,'',0,0,50,1,1,0,'/^[0-9]{1,9}$/',0.00000,0.00000,'payroll_empl_list',6,0,0,'XL'),('Street',1,0,'',0,0,60,1,0,0,'',0.00000,0.00000,'',0,0,0,'XL'),('SV-AS-Number',1,0,'',0,0,17,1,0,0,'/^[0-9]{3}\\.[0-9]{4}\\.[0-9]{4}\\.[0-9]{1,2}$/',0.00000,0.00000,'',0,0,0,'L'),('tblchld',110,1,'',0,0,0,1,0,0,'',0.00000,0.00000,'payroll_employee_children',0,0,0,'XL'),('tblchld_DateFrom',5,0,'tblchld',5,0,10,1,1,0,'',0.00000,0.00000,'',0,0,0,'9'),('tblchld_DateOfBirth',5,0,'tblchld',4,0,10,1,1,0,'',0.00000,0.00000,'',0,0,0,'9'),('tblchld_DateTo',5,0,'tblchld',6,0,10,1,1,0,'',0.00000,0.00000,'',0,0,0,'9'),('tblchld_Firstname',1,0,'tblchld',2,0,30,1,1,0,'',0.00000,0.00000,'',0,0,0,'9'),('tblchld_Lastname',1,0,'tblchld',1,0,30,1,1,0,'',0.00000,0.00000,'',0,0,0,'9'),('tblchld_Reason',1,0,'tblchld',7,0,60,1,1,0,'',0.00000,0.00000,'',0,0,0,'9'),('tblchld_Sex',4,0,'tblchld',3,0,25,1,1,0,'/^[MF]{1}$/',0.00000,0.00000,'payroll_empl_list',7,1,0,'9'),('tblcrer',110,1,'',0,0,0,1,0,0,'',0.00000,0.00000,'payroll_employee_career',0,0,0,'XL'),('tblcrer_CostCenter',1,0,'tblcrer',2,0,20,1,0,0,'',0.00000,0.00000,'',0,0,0,'14'),('tblcrer_DateFrom',5,0,'tblcrer',1,0,10,1,1,0,'',0.00000,0.00000,'',0,0,0,'9'),('tblcrer_Department',1,0,'tblcrer',3,0,45,1,1,0,'',0.00000,0.00000,'',0,0,0,'14'),('tblcrer_Position',1,0,'tblcrer',4,0,45,1,1,0,'',0.00000,0.00000,'',0,0,0,'14'),('tblcrer_Workplace',1,0,'tblcrer',5,0,50,1,1,0,'',0.00000,0.00000,'',0,0,0,'12'),('tbledu',110,1,'',0,0,0,1,0,0,'',0.00000,0.00000,'payroll_employee_education',0,0,0,'XL'),('tbledu_Comment',1,0,'tbledu',4,0,160,1,0,0,'',0.00000,0.00000,'',0,0,0,'25'),('tbledu_DateFrom',5,0,'tbledu',1,0,10,1,1,0,'',0.00000,0.00000,'',0,0,0,'9'),('tbledu_DateTo',5,0,'tbledu',2,0,10,1,1,0,'',0.00000,0.00000,'',0,0,0,'9'),('tbledu_Education',1,0,'tbledu',3,0,40,1,1,0,'',0.00000,0.00000,'',0,0,0,'20'),('tblprd',110,1,'',0,0,0,1,0,0,'',0.00000,0.00000,'payroll_employment_period',0,0,0,'XL'),('tblprd_DateFrom',5,0,'tblprd',1,0,10,1,1,0,'',0.00000,0.00000,'',0,0,0,'9'),('tblprd_DateTo',5,0,'tblprd',2,0,10,1,0,0,'',0.00000,0.00000,'',0,0,0,'9'),('tblprd_Reason',4,0,'tblprd',3,0,50,1,0,0,'/^[0-9]{1,9}$/',0.00000,0.00000,'payroll_empl_list',8,0,0,'45'),('tblslry',110,1,'',0,0,0,1,0,0,'',0.00000,0.00000,'payroll_employee_salary',0,0,0,'XL'),('tblslry_BasicWage',3,0,'tblslry',3,0,10,1,1,0,'/^[0-9]{1,7}(\\.[0-9]{1,2})?$/',0.00000,9999999.00000,'',0,0,0,'9'),('tblslry_CostCenter',1,0,'tblslry',2,0,20,1,0,0,'',0.00000,0.00000,'',0,0,0,'30'),('tblslry_DateFrom',5,0,'tblslry',1,0,10,1,1,0,'',0.00000,0.00000,'',0,0,0,'9'),('tblslry_HourlyWage',3,0,'tblslry',4,0,7,1,1,0,'/^[0-9]{1,4}(\\.[0-9]{1,2})?$/',0.00000,9999.00000,'',0,0,0,'9'),('tblslry_Hours',3,0,'tblslry',5,0,6,1,1,0,'/^[0-9]{1,3}(\\.[0-9]{1,2})?$/',0.00000,999.00000,'',0,0,0,'6'),('WageCode',4,0,'',0,0,1,1,1,0,'/^[1-4]{1,1}$/',0.00000,0.00000,'payroll_empl_list',10,1,0,'L'),('WorkplaceCanton',4,0,'',0,0,25,1,1,0,'/^[A-Z]{2,2}$/',0.00000,0.00000,'payroll_empl_list',3,1,0,'L'),('WorkplaceCity',1,0,'',0,0,50,1,0,0,'',0.00000,0.00000,'',0,0,0,'L'),('YearsOfService',3,0,'',0,0,2,1,0,1,'/^[0-9]{1,2}$/',0.00000,99.00000,'',0,0,0,'S'),('ZIP-Code',1,0,'ZipCity',1,0,15,1,1,0,'',0.00000,0.00000,'',0,0,0,'S'),('ZipCity',100,1,'',0,0,0,1,0,0,'',0.00000,0.00000,'',0,0,0,'XL');
/*!40000 ALTER TABLE `payroll_employee_field_def` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_employee_field_label`
--

DROP TABLE IF EXISTS `payroll_employee_field_label`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_employee_field_label` (
  `fieldName` varchar(30) NOT NULL,
  `language` varchar(2) NOT NULL,
  `label` varchar(50) NOT NULL,
  `helptext` text NOT NULL,
  PRIMARY KEY (`fieldName`),
  KEY `language` (`language`),
  CONSTRAINT `fk_payroll_employee_field_label_payroll_employee_field_def1` FOREIGN KEY (`fieldName`) REFERENCES `payroll_employee_field_def` (`fieldName`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_employee_field_label`
--

LOCK TABLES `payroll_employee_field_label` WRITE;
/*!40000 ALTER TABLE `payroll_employee_field_label` DISABLE KEYS */;
INSERT INTO `payroll_employee_field_label` VALUES ('AdditionalAddrLine1','de','Adresszeile 1',''),('AdditionalAddrLine2','de','Adresszeile 2',''),('AdditionalAddrLine3','de','Adresszeile 3',''),('AdditionalAddrLine4','de','Adresszeile 4',''),('Age','de','Alter',''),('AgriculturalEmployee','de','Landwirtschaftl. Angestellter',''),('AHV-AVS-Number','de','AHV-Nummer',''),('AliensCategory','de','Ausländerkategorie',''),('AliensPermitNo','de','Bewill-nr. Ausländer',''),('Apprentice','de','Lehrling/Praktikant',''),('AttendedTimeCode','de','Präsenzcode',''),('AttendedTimeHours','de','Präsenzstunden',''),('BaseWage','de','Basislohn',''),('CanteenLunchCheck','de','Kantinenverpfl./Lunch-Checks',''),('City','de','Ort',''),('CivilStatus','de','Zivilstand',''),('CodeAHV','de','AHV-Code',''),('CodeALV','de','ALV-Code',''),('CodeBVG','de','BVG-Code',''),('CodeKTG','de','KTG-Code',''),('CodeUVG','de','UVG-Code',''),('CodeUVGZ1','de','UVGZ1-Code',''),('CodeUVGZ2','de','UVGZ2-Code',''),('CostCenter','de','Stamm-Kostenstelle',''),('CostUnit','de','Kostenplatz',''),('Country','de','Land',''),('DateOfBirth','de','Geburtsdatum',''),('DedAtSrcCanton','de','QST Kanton',''),('DedAtSrcCode','de','QST Code',''),('DedAtSrcCompany','de','QST-Abz.d.AG',''),('DedAtSrcMode','de','QST Verarbeitung',''),('DedAtSrcMunicipality','de','QST pol. Gemeinde',''),('DedAtSrcPercentage','de','QST % oder CHF',''),('Department','de','Abteilung',''),('Education','de','Ausbildung',''),('EmailCompany','de','E-Mail Geschäft',''),('EmailHome','de','E-Mail privat',''),('EmployeeNumber','de','Personalnummer',''),('EmploymentPercentage','de','Prozentuale Arbeitszeit',''),('EmploymentStatus','de','Beschäftigungsstatus',''),('Firstname','de','Vorname',''),('FreeTransport','de','Unentgeltliche Beförderung',''),('FRFE05','de','Freies Feld 5',''),('FRFE07','de','Freies Feld 7',''),('FRFE10','de','Freies Feld 10',''),('FRFE11','de','Freies Feld 11',''),('FRFE12','de','Freies Feld 12',''),('FRFE14','de','Freies Feld 14',''),('FRFE15','de','Freies Feld 15',''),('FRFE18','de','Freies Feld 18',''),('FRFE22','de','Freies Feld 22',''),('FRFE24','de','Freies Feld 24',''),('FRFE27','de','Freies Feld 27',''),('id','de','Record ID',''),('Language','de','Sprache',''),('Lastname','de','Nachname',''),('ManagementLevel','de','Kaderstufe',''),('MonthsOfService','de','Dienstalter Monate',''),('Nationality','de','Nationalität',''),('payroll_company_ID','de','Firma ID',''),('PensionerExemption','de','Rentnerfreibetrag deaktiviert',''),('PhoneNumber1','de','Telefon 1',''),('PhoneNumber2','de','Telefon 2',''),('PhoneNumber3','de','Telefon 3',''),('PhoneNumber4','de','Telefon 4',''),('PlaceOfOrigin','de','Heimatort',''),('Position','de','Berufliche Stellung',''),('ResidenceCanton','de','Wohnkanton',''),('RetirementDate','de','Pensionsdatum',''),('SeniorityJoining','de','Dienstalter Eintritt',''),('Sex','de','Geschlecht',''),('SingleParent','de','Alleinerziehend',''),('Skill','de','Anforderungsniveau',''),('Street','de','Strasse',''),('SV-AS-Number','de','Versicherungsnummer',''),('tblchld','de','Kinderdaten',''),('tblchld_DateFrom','de','Datum von',''),('tblchld_DateOfBirth','de','Geb.Datum',''),('tblchld_DateTo','de','Datum bis',''),('tblchld_Firstname','de','Vorname',''),('tblchld_Lastname','de','Nachname',''),('tblchld_Reason','de','Grund',''),('tblchld_Sex','de','Geschlecht',''),('tblcrer','de','Beschäftigungsdaten',''),('tblcrer_CostCenter','de','Kostenstelle',''),('tblcrer_DateFrom','de','Datum von',''),('tblcrer_Department','de','Abteilung',''),('tblcrer_Position','de','Stellung',''),('tblcrer_Workplace','de','Arbeitsort',''),('tbledu','de','Ausbildungsdaten',''),('tbledu_Comment','de','Bemerkung',''),('tbledu_DateFrom','de','Datum von',''),('tbledu_DateTo','de','Datum bis',''),('tbledu_Education','de','Ausbildung',''),('tblprd','de','Beschäftigungsperioden',''),('tblprd_DateFrom','de','Datum von',''),('tblprd_DateTo','de','Datum bis',''),('tblprd_Reason','de','Kündigungsgrund',''),('tblslry','de','Lohnentwicklung',''),('tblslry_BasicWage','de','Basislohn',''),('tblslry_CostCenter','de','Kostenstelle',''),('tblslry_DateFrom','de','Datum von',''),('tblslry_HourlyWage','de','Stundenlohn',''),('tblslry_Hours','de','Anz.Std.',''),('WageCode','de','Lohn-Code',''),('WorkplaceCanton','de','Arbeitsplatzkanton',''),('WorkplaceCity','de','Arbeitsort',''),('YearsOfService','de','Dienstalter Jahre',''),('ZIP-Code','de','PLZ',''),('ZipCity','de','PLZ/Ort','');
/*!40000 ALTER TABLE `payroll_employee_field_label` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_employee_salary`
--

DROP TABLE IF EXISTS `payroll_employee_salary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_employee_salary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_employee_ID` int(11) NOT NULL,
  `DateFrom` date NOT NULL,
  `CostCenter` varchar(20) CHARACTER SET latin1 NOT NULL,
  `BasicWage` decimal(9,2) NOT NULL,
  `HourlyWage` decimal(6,2) NOT NULL,
  `Hours` decimal(5,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_table3_payroll_employee2_idx` (`payroll_employee_ID`),
  CONSTRAINT `fk_table3_payroll_employee2` FOREIGN KEY (`payroll_employee_ID`) REFERENCES `payroll_employee` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_employee_salary`
--

LOCK TABLES `payroll_employee_salary` WRITE;
/*!40000 ALTER TABLE `payroll_employee_salary` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_employee_salary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_employment_period`
--

DROP TABLE IF EXISTS `payroll_employment_period`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_employment_period` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_employee_ID` int(11) NOT NULL,
  `DateFrom` date NOT NULL,
  `DateTo` date NOT NULL,
  `Reason` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_payroll_employment_period_payroll_employee1_idx` (`payroll_employee_ID`),
  CONSTRAINT `fk_payroll_employment_period_payroll_employee1` FOREIGN KEY (`payroll_employee_ID`) REFERENCES `payroll_employee` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_employment_period`
--

LOCK TABLES `payroll_employment_period` WRITE;
/*!40000 ALTER TABLE `payroll_employment_period` DISABLE KEYS */;
INSERT INTO `payroll_employment_period` VALUES (9,5,'2010-06-01','2012-02-27',106),(10,6,'2009-06-01','0000-00-00',0),(11,7,'2011-01-01','0000-00-00',0),(12,8,'2011-01-01','0000-00-00',0),(13,9,'2011-09-01','0000-00-00',0),(14,10,'2012-01-01','2012-12-31',0),(15,11,'2010-08-01','2012-02-28',0),(16,12,'2012-02-01','2012-10-31',0),(17,13,'2011-08-01','0000-00-00',0),(18,14,'2012-03-01','0000-00-00',0),(19,15,'2011-10-01','2012-03-31',94),(20,16,'2011-10-01','2012-10-31',104),(21,17,'2009-01-01','2012-02-28',128),(22,18,'2011-11-01','2012-03-31',0),(23,19,'2011-12-01','0000-00-00',0),(24,20,'2010-06-01','0000-00-00',0),(25,21,'2009-07-01','0000-00-00',0),(26,22,'1976-08-01','2011-12-31',137),(27,23,'2004-07-01','2011-10-31',127),(28,24,'2011-03-15','2011-12-31',123),(29,25,'1976-09-02','2011-10-31',120),(30,26,'1971-03-01','2011-12-31',95),(31,27,'1991-02-01','2011-12-31',91),(32,28,'1966-04-01','2011-12-31',130),(33,29,'2009-10-01','2011-10-31',108),(34,30,'2012-02-01','2012-03-27',0),(35,31,'2006-02-27','0000-00-00',0),(36,32,'2011-05-01','2012-10-31',0),(37,33,'2006-02-28','0000-00-00',0),(38,34,'2012-02-20','2012-03-15',134);
/*!40000 ALTER TABLE `payroll_employment_period` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_fin_acc_assign`
--

DROP TABLE IF EXISTS `payroll_fin_acc_assign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_fin_acc_assign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_account_ID` int(11) NOT NULL,
  `payroll_employee_ID` int(11) NOT NULL,
  `payroll_company_ID` int(11) NOT NULL,
  `account_no` varchar(15) NOT NULL,
  `counter_account_no` varchar(15) NOT NULL,
  `cost_center` varchar(15) NOT NULL,
  `debitcredit` tinyint(1) NOT NULL,
  `entry_text` varchar(50) NOT NULL,
  `invert_value` tinyint(1) NOT NULL,
  `processing_order` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fin_acc_prcord` (`processing_order`)
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_fin_acc_assign`
--

LOCK TABLES `payroll_fin_acc_assign` WRITE;
/*!40000 ALTER TABLE `payroll_fin_acc_assign` DISABLE KEYS */;
INSERT INTO `payroll_fin_acc_assign` VALUES (1,1000,0,0,'5000','','',0,'Bruttolohn',0,9),(5,1005,0,0,'5000','','',0,'Bruttolohn',0,9),(6,1007,0,0,'5000','','',0,'Bruttolohn',0,9),(7,1010,0,0,'5000','','',0,'Bruttolohn',0,9),(8,1015,0,0,'5000','','',0,'Bruttolohn',0,9),(9,1016,0,0,'5000','','',0,'Bruttolohn',0,9),(10,1017,0,0,'5000','','',0,'Bruttolohn',0,9),(11,1018,0,0,'5000','','',0,'Bruttolohn',0,9),(13,1021,0,0,'5600','','',0,'Löhne Verwaltung',0,9),(14,1030,0,0,'5001','','',0,'Zulagen',0,9),(15,1031,0,0,'5001','','',0,'Zulagen',0,9),(16,1032,0,0,'5001','','',0,'Zulagen',0,9),(18,1050,0,0,'5001','','',0,'Zulagen',0,9),(19,1055,0,0,'5001','','',0,'Zulagen',0,9),(20,1061,0,0,'5002','','',0,'Zulagen',0,9),(21,1065,0,0,'5002','','',0,'Zulagen',0,9),(22,1071,0,0,'5001','','',0,'Zulagen',0,9),(23,1072,0,0,'5001','','',0,'Pikettentschädigung',0,9),(24,1073,0,0,'5001','','',0,'Zulagen',0,9),(25,1074,0,0,'5001','','',0,'Zulage',0,9),(26,1075,0,0,'5001','','',0,'Zulage',0,9),(27,1076,0,0,'5001','','',0,'Zulage',0,9),(28,1101,0,0,'5001','','',0,'Zulage',0,9),(29,1102,0,0,'5001','','',0,'Zulage',0,9),(30,1112,0,0,'5001','','',0,'Zulage',0,9),(31,1160,0,0,'5004','','',0,'Ferien',0,9),(32,1161,0,0,'5004','','',0,'Feiertage',0,9),(33,1162,0,0,'5004','','',0,'Ferien',0,9),(34,1200,0,0,'5005','','',0,'13. ML',0,9),(35,1201,0,0,'5006','','',0,'Grati',0,9),(36,1202,0,0,'5006','','',0,'Grati',0,9),(37,1210,0,0,'5007','','',0,'Boni',0,9),(38,1211,0,0,'5007','','',0,'Boni',0,9),(39,1212,0,0,'5007','','',0,'Boni',0,9),(40,1213,0,0,'5007','','',0,'Boni',0,9),(41,1214,0,0,'5003','','',0,'Prämien',0,9),(42,1215,0,0,'5003','','',0,'Prämien',0,9),(43,1216,0,0,'5003','','',0,'Prämien',0,9),(44,1217,0,0,'5007','','',0,'Boni',0,9),(45,1218,0,0,'5007','','',0,'Provision',0,9),(46,1219,0,0,'5003','','',0,'Prämien',0,9),(47,1230,0,0,'5009','','',0,'Dienstaltersgesch.',0,9),(48,1231,0,0,'5009','','',0,'Jubiläum',0,9),(49,1232,0,0,'5009','','',0,'Treueprämie',0,9),(50,1250,0,0,'5003','','',0,'Prämien',0,9),(51,1410,0,0,'5035','','',0,'Abfindung',0,9),(52,1500,0,0,'5601','','',0,'VR-Entschädigung',0,9),(53,1501,0,0,'5601','','',0,'VR-Entschädigung',0,9),(54,1503,0,0,'5601','','',0,'VR-Entschädigung',0,9),(55,1902,0,0,'5030','','',0,'Geldwerte Leistungen',0,9),(56,1910,0,0,'5030','','',0,'Geldwerte Leistungen',0,9),(57,1950,0,0,'5030','','',0,'Geldwerte Leistungen',0,9),(58,1961,0,0,'5032','','',0,'MA-Beteiligung',0,9),(59,1962,0,0,'5032','','',0,'MA-Beteiligung',0,9),(60,1971,0,0,'5740','','',0,'KTG',0,9),(61,1972,0,0,'5720','','',0,'BVG',0,9),(62,1973,0,0,'5721','','',0,'Einkauf BVG',0,9),(63,1975,0,0,'5731','','',0,'UVGZ',0,9),(64,1976,0,0,'5722','','',0,'3. Säule',0,9),(65,1977,0,0,'5722','','',0,'3. Säule',0,9),(66,1980,0,0,'5034','','',0,'Weiterbildung LA',0,9),(67,2000,0,0,'5010','','',0,'Taggelder',0,9),(68,2005,0,0,'5010','','',0,'Taggelder',0,9),(69,2010,0,0,'5010','','',0,'Taggelder',0,9),(70,1015,0,0,'5010','','',0,'Taggelder',0,9),(71,2020,0,0,'5010','','',0,'Taggelder',0,9),(72,2025,0,0,'5010','','',0,'Taggelder',0,9),(73,2030,0,0,'5010','','',0,'Taggelder',0,9),(74,2050,0,0,'5010','','',1,'Korr. Taggelder',0,9),(75,2060,0,0,'5000','','',1,'Bruttlohn',0,9),(76,2070,0,0,'5010','','',0,'Taggelder',0,9),(77,2075,0,0,'5000','','',0,'Bruttolohn',0,9),(78,3000,0,0,'5040','','',0,'Fam.Zulagen',0,9),(79,3030,0,0,'5040','','',0,'Fam.Zulagen',0,9),(80,3031,0,0,'5040','','',0,'Fam.Zulagen',0,9),(81,3032,0,0,'5040','','',0,'Fam.Zulagen',0,9),(82,3034,0,0,'5040','','',0,'Fam.Zulagen',0,9),(83,5010,0,0,'5700','','',1,'AHV',0,9),(84,5020,0,0,'5701','','',1,'ALV',0,9),(85,5030,0,0,'5701','','',1,'ALVZ',0,9),(86,5040,0,0,'5730','','',1,'UVG',0,9),(87,5041,0,0,'5731','','',1,'UVGZ',0,9),(88,5050,0,0,'5740','','',1,'KTG',0,9),(89,5051,0,0,'5740','','',1,'KTG',0,9),(90,5060,0,0,'5720','','',1,'BVG',0,9),(91,5065,0,0,'5720','','',1,'BVG',0,9),(92,5070,0,0,'5790','','',1,'QST',0,9),(93,5100,0,0,'5039','','',1,'Ausgl. geldw. Leistung',0,9),(94,5110,0,0,'5039','','',1,'Ausgl. geldw. Leistung',0,9),(95,5111,0,0,'5039','','',1,'Ausgl. geldw. Leistung',0,9),(96,5112,0,0,'5039','','',1,'Ausgl. geldw. Leistung',0,9),(97,6000,0,0,'5820','','',0,'Spesen',0,9),(98,6001,0,0,'5820','','',0,'Spesen',0,9),(99,6002,0,0,'5821','','',0,'Spesen',0,9),(100,6010,0,0,'5822','','',0,'Spesen',0,9),(101,6020,0,0,'5320','','',0,'Spesen',0,9),(102,6030,0,0,'5820','','',0,'Spesen',0,9),(103,6040,0,0,'5830','','',0,'Spesen',0,9),(104,6050,0,0,'5831','','',1,'Spesen',0,9),(105,6060,0,0,'5331','','',0,'Spesen',0,9),(106,6070,0,0,'5832','','',0,'Spesen',0,9),(107,6510,0,0,'1990','','',1,'Auszahlung',0,9),(108,8000,0,0,'1990','','',1,'Auszahlung',0,9),(109,1001,0,0,'5000','','',0,'Bruttolohn',0,9);
/*!40000 ALTER TABLE `payroll_fin_acc_assign` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_fin_acc_entry`
--

DROP TABLE IF EXISTS `payroll_fin_acc_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_fin_acc_entry` (
  `payroll_period_ID` int(11) NOT NULL,
  `payroll_employee_ID` int(11) NOT NULL,
  `payroll_account_ID` varchar(5) NOT NULL,
  `account_no` varchar(15) NOT NULL,
  `counter_account_no` varchar(15) NOT NULL,
  `cost_center` varchar(15) NOT NULL,
  `amount_local` decimal(11,2) NOT NULL,
  `debitcredit` tinyint(1) NOT NULL,
  `entry_text` varchar(50) NOT NULL,
  `amount_quantity` tinyint(1) NOT NULL,
  KEY `idx_fin_acc_ent_prd` (`payroll_period_ID`),
  KEY `idx_fin_acc_ent_emp` (`payroll_employee_ID`),
  KEY `idx_fin_acc_ent_acc` (`payroll_account_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_fin_acc_entry`
--

LOCK TABLES `payroll_fin_acc_entry` WRITE;
/*!40000 ALTER TABLE `payroll_fin_acc_entry` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_fin_acc_entry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_formula`
--

DROP TABLE IF EXISTS `payroll_formula`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_formula` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `formula` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_formula`
--

LOCK TABLES `payroll_formula` WRITE;
/*!40000 ALTER TABLE `payroll_formula` DISABLE KEYS */;
INSERT INTO `payroll_formula` VALUES (1,'(amount/rate+surcharge)*factor'),(2,'(amount*rate+surcharge)*factor'),(3,'(quantity*rate+surcharge)*factor'),(4,'quantity*(rate*factor+surcharge)'),(5,'(amount*rate/100+surcharge)*factor'),(6,'amount*rate/100*factor+surcharge'),(7,'(quantity/rate+surcharge)*factor'),(8,'quantity*rate'),(9,'amount*rate');
/*!40000 ALTER TABLE `payroll_formula` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_insurance`
--

DROP TABLE IF EXISTS `payroll_insurance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_insurance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `CompanyName` varchar(45) CHARACTER SET latin1 NOT NULL,
  `InsuranceID` varchar(15) CHARACTER SET latin1 NOT NULL,
  `IsSUVA` tinyint(1) NOT NULL DEFAULT '0',
  `SubNumber` varchar(4) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_insurance`
--

LOCK TABLES `payroll_insurance` WRITE;
/*!40000 ALTER TABLE `payroll_insurance` DISABLE KEYS */;
INSERT INTO `payroll_insurance` VALUES (1,'Ausgleichskasse Luzern','003.000',0,''),(2,'Ausgleichskasse Kanton Bern','002',0,''),(3,'Caisse de compensation du Canton du Valais','023',0,''),(4,'Pensionskasse Oldsoft Luzern','2600.88.1',0,''),(5,'Suva Wetzikon','',1,'01'),(6,'Backwork Versicherungen','2345.88 1',0,'');
/*!40000 ALTER TABLE `payroll_insurance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_insurance_cd_label`
--

DROP TABLE IF EXISTS `payroll_insurance_cd_label`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_insurance_cd_label` (
  `payroll_insurance_code_ID` int(11) NOT NULL,
  `language` varchar(2) NOT NULL,
  `label` varchar(45) NOT NULL,
  PRIMARY KEY (`language`,`payroll_insurance_code_ID`),
  KEY `idxInsCdLang` (`payroll_insurance_code_ID`,`language`),
  KEY `fkInsCode_idx` (`payroll_insurance_code_ID`),
  CONSTRAINT `fkInsCode` FOREIGN KEY (`payroll_insurance_code_ID`) REFERENCES `payroll_insurance_code` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_insurance_cd_label`
--

LOCK TABLES `payroll_insurance_cd_label` WRITE;
/*!40000 ALTER TABLE `payroll_insurance_cd_label` DISABLE KEYS */;
INSERT INTO `payroll_insurance_cd_label` VALUES (1,'de','Ausländer'),(2,'de','Ausländer'),(5,'de','BE KiZu bis 12J.'),(6,'de','LU KiZu bis 12J.'),(7,'de','VS KiZu bis 16J.'),(8,'de','Nicht UVG-versichert'),(9,'de','Nicht UVG-versichert'),(10,'de','Nicht UVG-versichert'),(11,'de','UVGZ Kat. 1'),(12,'de','UVGZ Kat. 2'),(13,'de','nicht KTG versichert'),(14,'de','bis HL 200\'000'),(15,'de','bis HL 500\'000'),(20,'de','Jugendliche'),(21,'de','AHV-pflichtig'),(22,'de','Rentner, pflichtig m. Freibetrag'),(23,'de','ALV-pflichtig'),(24,'de','BUV- und NBUV-versichert, mit NBUV-Abzug'),(25,'de','BUV- und NBUV-versichert, mit NBUV-Abzug'),(26,'de','BUV- und NBUV-versichert, mit NBUV-Abzug'),(27,'de','BUV- und NBUV-versichert, ohne NBUV-Abzug'),(28,'de','BUV- und NBUV-versichert, ohne NBUV-Abzug'),(29,'de','BUV- und NBUV-versichert, ohne NBUV-Abzug'),(30,'de','Nur BUV-versichert, deshalb kein NBUV-Abzug'),(31,'de','Nur BUV-versichert, deshalb kein NBUV-Abzug'),(32,'de','Nur BUV-versichert, deshalb kein NBUV-Abzug'),(33,'de','ALV-pflichtig, ohne Abzug'),(34,'de','Rentner, nicht pflichtig'),(35,'de','Rentner, pflichtig m. Freibetrag, ohne Abzug'),(36,'de','Jugendliche'),(37,'de','ALV-pflichtig, ohne Abzug'),(38,'de','Rentner, nicht pflichtig'),(40,'de','BE KiZu 12-16J.'),(41,'de','LU KiZu 12-16J.'),(42,'de','LU KiZu 16-25J.'),(43,'de','VS KiZu 16-25J.'),(44,'de','Nicht UVGZ versichert');
/*!40000 ALTER TABLE `payroll_insurance_cd_label` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_insurance_code`
--

DROP TABLE IF EXISTS `payroll_insurance_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_insurance_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_insurance_ID` int(11) NOT NULL,
  `payroll_insurance_type_ID` int(11) NOT NULL,
  `payroll_company_ID` int(11) NOT NULL,
  `InsuranceCode` varchar(10) NOT NULL,
  `CustomerIdentity` varchar(15) NOT NULL,
  `ContractIdentity` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idxCompany` (`payroll_company_ID`),
  KEY `fkInsuranceTp_idx` (`payroll_insurance_type_ID`),
  CONSTRAINT `fkCompany` FOREIGN KEY (`payroll_company_ID`) REFERENCES `payroll_company` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fkInsuranceTp` FOREIGN KEY (`payroll_insurance_type_ID`) REFERENCES `payroll_insurance_type` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_insurance_code`
--

LOCK TABLES `payroll_insurance_code` WRITE;
/*!40000 ALTER TABLE `payroll_insurance_code` DISABLE KEYS */;
INSERT INTO `payroll_insurance_code` VALUES (1,1,1,1,'8','100-9976.9',''),(2,1,2,1,'8','100-9976.9',''),(5,2,3,1,'BE1','100-2136.90',''),(6,1,3,1,'LU1','100-9976.70',''),(7,3,3,1,'VS1','100-5467.80',''),(8,5,4,1,'A0','1501-12577.6',''),(9,5,4,1,'B0','1501-12577.6',''),(10,5,4,1,'Z0','1501-12577.6',''),(11,6,5,1,'11','7651-873.1','4566-4'),(12,6,5,1,'12','7651-873.1','4566-4'),(13,6,6,1,'10','7651-873.1','4567-4'),(14,6,6,1,'11','7651-873.1','4567-4'),(15,6,6,1,'12','7651-873.1','4567-4'),(16,4,7,1,'0','1099-8777.1','4500-0'),(17,4,7,1,'1','1099-8777.1','4500-0'),(18,4,7,1,'2','1099-8777.1','4500-0'),(20,1,1,1,'2','100-9976.9',''),(21,1,1,1,'1','100-9976.9',''),(22,1,1,1,'4','100-9976.9',''),(23,1,2,1,'1','100-9976.9',''),(24,5,4,1,'A1','1501-12577.6',''),(25,5,4,1,'B1','1501-12577.6',''),(26,5,4,1,'Z1','1501-12577.6',''),(27,5,4,1,'A2','1501-12577.6',''),(28,5,4,1,'B2','1501-12577.6',''),(29,5,4,1,'Z2','1501-12577.6',''),(30,5,4,1,'A3','1501-12577.6',''),(31,5,4,1,'B3','1501-12577.6',''),(32,5,4,1,'Z3','1501-12577.6',''),(33,1,1,1,'3','100-9976.9',''),(34,1,1,1,'5','100-9976.9',''),(35,1,1,1,'6','100-9976.9',''),(36,1,2,1,'2','100-9976.9',''),(37,1,2,1,'3','100-9976.9',''),(38,1,2,1,'5','100-9976.9',''),(40,2,3,1,'BE2','100-2136.90',''),(41,1,3,1,'LU2','100-9976.70',''),(42,1,3,1,'LU3','100-9976.70',''),(43,1,3,1,'VS3','100-5467.80',''),(44,6,5,1,'10','7651-873.1','4566-4');
/*!40000 ALTER TABLE `payroll_insurance_code` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_insurance_rate`
--

DROP TABLE IF EXISTS `payroll_insurance_rate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_insurance_rate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_insurance_code_ID` int(11) NOT NULL,
  `payroll_account_ID` varchar(5) NOT NULL,
  `Description` varchar(15) NOT NULL,
  `rate` decimal(12,5) NOT NULL,
  `Sex` varchar(1) NOT NULL,
  `AgeFrom` tinyint(4) NOT NULL DEFAULT '0',
  `AgeTo` tinyint(4) NOT NULL DEFAULT '120',
  `CodeFrom` tinyint(4) NOT NULL,
  `CodeTo` tinyint(4) NOT NULL,
  `CodeAlph` varchar(5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fkInsuranceCd_idx` (`payroll_insurance_code_ID`),
  CONSTRAINT `fkInsuranceCd` FOREIGN KEY (`payroll_insurance_code_ID`) REFERENCES `payroll_insurance_code` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_insurance_rate`
--

LOCK TABLES `payroll_insurance_rate` WRITE;
/*!40000 ALTER TABLE `payroll_insurance_rate` DISABLE KEYS */;
INSERT INTO `payroll_insurance_rate` VALUES (1,21,'5010','AHV',5.05000,'F',18,64,0,0,''),(2,21,'5010','AHV',5.05000,'M',18,65,0,0,''),(3,23,'5020','ALV',1.00000,'F',18,64,0,0,''),(4,23,'5020','ALVZ',1.00000,'M',18,65,0,0,''),(5,23,'5025','ALVZ',0.50000,'F',18,64,0,0,''),(6,23,'5025','ALVZ',0.50000,'M',18,65,0,0,''),(23,24,'5030','NBU',1.46000,'',0,120,1,2,''),(24,25,'5031','NBU',1.62000,'',0,120,1,2,''),(25,26,'5032','NBU',1.19000,'',0,120,1,2,''),(26,24,'5035','BU',0.60900,'',0,120,1,3,''),(27,25,'5036','BU',0.94000,'',0,120,1,3,''),(28,26,'5037','BU',0.53000,'',0,120,1,3,''),(29,27,'5039','NBU AG',0.00000,'',0,120,0,0,''),(30,28,'5039','NBU AG',0.00000,'',0,120,0,0,''),(31,29,'5039','NBU AG',0.00000,'',0,120,0,0,''),(35,24,'5039','NBU AG',1.00000,'',0,120,0,0,''),(36,25,'5039','NBU AG',1.00000,'',0,120,0,0,''),(37,26,'5039','NBU AG',1.00000,'',0,120,0,0,''),(38,11,'5040','UVGZ1',0.20100,'F',0,120,0,0,''),(39,11,'5040','UVGZ1',0.20100,'M',0,120,0,0,''),(40,12,'5041','UVGZ2',0.63400,'F',0,120,0,0,''),(41,12,'5041','UVGZ2',0.63400,'M',0,120,0,0,''),(42,14,'5050','KTG1',0.61000,'F',0,120,0,0,''),(43,14,'5050','KTG1',0.54300,'M',0,120,0,0,''),(44,15,'5051','KTG2',0.72000,'F',0,120,0,0,''),(45,15,'5051','KTG2',0.67800,'M',0,120,0,0,''),(46,22,'5010','AHV',5.05000,'F',64,120,0,0,''),(47,22,'5010','AHV',5.05000,'M',65,120,0,0,''),(48,27,'5030','NBU',1.46000,'',0,120,0,0,''),(49,28,'5031','NBU',1.62000,'',0,120,0,0,''),(50,29,'5032','NBU',1.19000,'',0,120,0,0,''),(51,27,'5035','BU',0.60900,'',0,120,0,0,''),(52,28,'5036','BU',0.94000,'',0,120,0,0,''),(53,29,'5037','BU',0.53000,'',0,120,0,0,''),(54,30,'5035','BU',0.60900,'',0,120,0,0,''),(55,31,'5036','BU',0.94000,'',0,120,0,0,''),(56,32,'5037','BU',0.53000,'',0,120,0,0,''),(57,21,'5013','AHV AG',0.00000,'F',18,64,0,0,''),(58,21,'5013','AHV AG',0.00000,'M',18,65,0,0,''),(59,23,'5023','ALV AG',0.00000,'F',18,64,0,0,''),(60,23,'5023','ALV AG',0.00000,'M',18,65,0,0,''),(61,22,'5013','AHV AG',0.00000,'F',64,120,0,0,''),(62,22,'5013','AHV AG',0.00000,'M',65,120,0,0,''),(66,5,'3000','BE1',160.00000,'',0,12,0,0,''),(67,40,'3000','BE2',190.00000,'',12,16,0,0,''),(68,6,'3000','LU1',180.00000,'',0,12,0,0,''),(69,41,'3000','LU2',200.00000,'',12,16,0,0,''),(70,42,'3000','LU3',230.00000,'',16,25,0,0,''),(71,7,'3000','VS1',260.00000,'',0,16,0,0,''),(72,43,'3000','VS3',360.00000,'',16,25,0,0,''),(73,44,'5040','kein UVGZ',0.00000,'',0,120,0,0,''),(74,13,'5050','kein KTG',0.00000,'',0,120,0,0,''),(75,16,'5060','kein BVG',0.00000,'',0,120,0,0,''),(76,17,'5060','BVG Gruppe1',0.00000,'',0,120,0,0,''),(77,18,'506','BVG Gruppe2',0.00000,'',0,12,0,0,'');
/*!40000 ALTER TABLE `payroll_insurance_rate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_insurance_type`
--

DROP TABLE IF EXISTS `payroll_insurance_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_insurance_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Description` varchar(15) NOT NULL,
  `SelectRateByAge` tinyint(1) NOT NULL,
  `SelectRateBySex` tinyint(1) NOT NULL,
  `SelectRateByNumCode` tinyint(1) NOT NULL,
  `SelectRateByAlphCode` tinyint(1) NOT NULL,
  `CodeField` varchar(15) NOT NULL,
  `InsCodeRegex` varchar(45) NOT NULL,
  `DeletePermission` tinyint(1) NOT NULL,
  `AppendPermission` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_insurance_type`
--

LOCK TABLES `payroll_insurance_type` WRITE;
/*!40000 ALTER TABLE `payroll_insurance_type` DISABLE KEYS */;
INSERT INTO `payroll_insurance_type` VALUES (1,'AHV',1,1,1,0,'CodeAHV','',0,0),(2,'ALV',1,1,1,0,'CodeALV','',0,0),(3,'FAK',0,0,0,1,'ResidenceCanton','',1,1),(4,'UVG',0,0,0,0,'','',1,1),(5,'UVGZ',0,1,0,0,'','',1,1),(6,'KTG',0,1,0,0,'','',1,1),(7,'BVG',1,1,0,0,'','',1,1);
/*!40000 ALTER TABLE `payroll_insurance_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_languages`
--

DROP TABLE IF EXISTS `payroll_languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_languages` (
  `core_intl_language_ID` varchar(2) NOT NULL,
  `DefaultLanguage` tinyint(1) NOT NULL,
  `UseForAccounts` tinyint(1) NOT NULL,
  `UseForEmployees` tinyint(1) NOT NULL,
  PRIMARY KEY (`core_intl_language_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_languages`
--

LOCK TABLES `payroll_languages` WRITE;
/*!40000 ALTER TABLE `payroll_languages` DISABLE KEYS */;
INSERT INTO `payroll_languages` VALUES ('de',1,1,1),('fr',0,0,1),('it',0,0,1);
/*!40000 ALTER TABLE `payroll_languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_mgmt_acc_assign`
--

DROP TABLE IF EXISTS `payroll_mgmt_acc_assign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_mgmt_acc_assign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_account_ID` int(11) NOT NULL,
  `payroll_employee_ID` int(11) NOT NULL,
  `payroll_company_ID` int(11) NOT NULL,
  `account_no` varchar(15) NOT NULL,
  `counter_account_no` varchar(15) NOT NULL,
  `cost_center` varchar(15) NOT NULL,
  `debitcredit` tinyint(1) NOT NULL,
  `entry_text` varchar(50) NOT NULL,
  `invert_value` tinyint(1) NOT NULL,
  `processing_order` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fin_acc_prcord` (`processing_order`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_mgmt_acc_assign`
--

LOCK TABLES `payroll_mgmt_acc_assign` WRITE;
/*!40000 ALTER TABLE `payroll_mgmt_acc_assign` DISABLE KEYS */;
INSERT INTO `payroll_mgmt_acc_assign` VALUES (1,1000,0,0,'5122','','',0,'Text eins',0,9),(2,1000,8,0,'5133','','',0,'Text zwei',0,1),(3,5010,0,0,'5144','','',1,'Text drei',0,9);
/*!40000 ALTER TABLE `payroll_mgmt_acc_assign` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_mgmt_acc_entry`
--

DROP TABLE IF EXISTS `payroll_mgmt_acc_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_mgmt_acc_entry` (
  `payroll_period_ID` int(11) NOT NULL,
  `payroll_employee_ID` int(11) NOT NULL,
  `payroll_account_ID` varchar(5) NOT NULL,
  `account_no` varchar(15) NOT NULL,
  `counter_account_no` varchar(15) NOT NULL,
  `cost_center` varchar(15) NOT NULL,
  `amount_local` decimal(11,2) NOT NULL,
  `debitcredit` tinyint(1) NOT NULL,
  `entry_text` varchar(50) NOT NULL,
  `amount_quantity` tinyint(1) NOT NULL,
  KEY `idx_fin_acc_ent_prd` (`payroll_period_ID`),
  KEY `idx_fin_acc_ent_emp` (`payroll_employee_ID`),
  KEY `idx_fin_acc_ent_acc` (`payroll_account_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_mgmt_acc_entry`
--

LOCK TABLES `payroll_mgmt_acc_entry` WRITE;
/*!40000 ALTER TABLE `payroll_mgmt_acc_entry` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_mgmt_acc_entry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_mgmt_acc_split`
--

DROP TABLE IF EXISTS `payroll_mgmt_acc_split`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_mgmt_acc_split` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_company_ID` int(11) NOT NULL,
  `payroll_employee_ID` int(11) NOT NULL,
  `payroll_account_ID` varchar(5) NOT NULL,
  `cost_center` varchar(15) NOT NULL,
  `amount` decimal(5,2) NOT NULL,
  `invert_value` tinyint(1) NOT NULL,
  `remainder` tinyint(1) NOT NULL,
  `processing_order` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_mgmt_acc_split`
--

LOCK TABLES `payroll_mgmt_acc_split` WRITE;
/*!40000 ALTER TABLE `payroll_mgmt_acc_split` DISABLE KEYS */;
INSERT INTO `payroll_mgmt_acc_split` VALUES (1,0,0,'1000','94100',100.00,0,1,9),(2,0,0,'5010','92123',100.00,1,1,9),(3,0,8,'1000','94100',50.00,0,1,1),(4,0,8,'1000','91400',25.00,0,1,1);
/*!40000 ALTER TABLE `payroll_mgmt_acc_split` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_payment_current`
--

DROP TABLE IF EXISTS `payroll_payment_current`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_payment_current` (
  `payroll_period_ID` int(11) NOT NULL,
  `payroll_employee_ID` int(11) NOT NULL,
  `payroll_payment_split_ID` int(11) NOT NULL,
  `amount_initial` decimal(10,2) NOT NULL,
  `amount_available` decimal(10,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `processing_order` tinyint(4) NOT NULL,
  `split_mode` tinyint(4) NOT NULL,
  `payroll_account_ID` varchar(5) NOT NULL,
  `having_rounding` tinyint(1) NOT NULL,
  `round_param` decimal(7,2) NOT NULL,
  KEY `prl_pmnt_cur_main` (`payroll_period_ID`,`payroll_employee_ID`,`payroll_account_ID`),
  KEY `prl_pmnt_cur_order` (`processing_order`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_payment_current`
--

LOCK TABLES `payroll_payment_current` WRITE;
/*!40000 ALTER TABLE `payroll_payment_current` DISABLE KEYS */;
INSERT INTO `payroll_payment_current` VALUES (2,34,64,23017.75,22913.75,22913.75,2,2,'8000',0,0.00),(2,34,63,23017.75,23017.75,104.00,1,3,'8000',1,0.05),(2,33,62,21434.85,13289.60,13289.60,2,2,'8000',0,0.00),(2,33,61,21434.85,21434.85,8145.25,1,2,'8000',1,0.05),(2,32,60,20760.85,20398.85,20398.85,2,2,'8000',0,0.00),(2,32,59,20760.85,20760.85,362.00,1,3,'8000',1,0.05),(2,31,65,131.25,0.00,131.25,3,1,'9999',0,0.00),(2,31,58,19360.90,14907.90,14907.90,2,2,'8000',0,0.00),(2,31,57,19360.90,19360.90,4453.00,1,2,'8000',1,0.05),(2,27,50,15616.55,8589.10,8589.10,2,2,'8000',0,0.00),(2,27,49,15616.55,15616.55,7027.45,1,2,'8000',1,0.05),(2,26,48,14927.20,14688.20,14688.20,2,2,'8000',0,0.00),(2,26,47,14927.20,14927.20,239.00,1,3,'8000',1,0.05),(2,25,46,14235.65,8826.10,8826.10,2,2,'8000',0,0.00),(2,25,45,14235.65,14235.65,5409.55,1,2,'8000',1,0.05),(2,24,44,12800.40,12455.40,12455.40,2,2,'8000',0,0.00),(2,24,43,12800.40,12800.40,345.00,1,3,'8000',1,0.05),(2,23,42,12208.35,8545.85,8545.85,2,2,'8000',0,0.00),(2,23,41,12208.35,12208.35,3662.50,1,2,'8000',1,0.05),(2,22,40,10938.55,10674.55,10674.55,2,2,'8000',0,0.00),(2,22,39,10938.55,10938.55,264.00,1,3,'8000',1,0.05),(2,21,38,0.00,0.00,0.00,2,2,'8000',0,0.00),(2,21,37,0.00,0.00,0.00,1,2,'8000',1,0.05),(2,20,36,946.80,528.80,528.80,2,2,'8000',0,0.00),(2,20,35,946.80,946.80,418.00,1,3,'8000',1,0.05),(2,19,34,1435.35,818.15,818.15,2,2,'8000',0,0.00),(2,19,33,1435.35,1435.35,617.20,1,2,'8000',1,0.05),(2,18,32,8187.30,7939.30,7939.30,2,2,'8000',0,0.00),(2,18,31,8187.30,8187.30,248.00,1,3,'8000',1,0.05),(2,17,30,7736.35,3868.15,3868.15,2,2,'8000',0,0.00),(2,17,29,7736.35,7736.35,3868.20,1,2,'8000',1,0.05),(2,16,28,7259.65,7080.65,7080.65,2,2,'8000',0,0.00),(2,16,27,7259.65,7259.65,179.00,1,3,'8000',1,0.05),(2,15,26,7012.40,4277.55,4277.55,2,2,'8000',0,0.00),(2,15,25,7012.40,7012.40,2734.85,1,2,'8000',1,0.05),(2,14,24,6441.15,6237.15,6237.15,2,2,'8000',0,0.00),(2,14,23,6441.15,6441.15,204.00,1,3,'8000',1,0.05),(2,13,22,6124.90,5267.40,5267.40,2,2,'8000',0,0.00),(2,13,21,6124.90,6124.90,857.50,1,2,'8000',1,0.05),(2,12,20,5444.75,4982.75,4982.75,2,2,'8000',0,0.00),(2,12,19,5444.75,5444.75,462.00,1,3,'8000',1,0.05),(2,11,18,5013.25,2957.80,2957.80,2,2,'8000',0,0.00),(2,11,17,5013.25,5013.25,2055.45,1,2,'8000',1,0.05),(2,10,16,4663.60,4230.60,4230.60,2,2,'8000',0,0.00),(2,10,15,4663.60,4663.60,433.00,1,3,'8000',1,0.05),(2,9,14,4083.55,2531.80,2531.80,2,2,'8000',0,0.00),(2,9,13,4083.55,4083.55,1551.75,1,2,'8000',1,0.05),(2,8,12,3788.00,3551.00,3551.00,2,2,'8000',0,0.00),(2,8,11,3788.00,3788.00,237.00,1,3,'8000',1,0.05),(2,7,10,3259.15,3031.00,3031.00,2,2,'8000',0,0.00),(2,7,9,3259.15,3259.15,228.15,1,2,'8000',1,0.05),(2,6,8,2909.95,2674.95,2674.95,2,2,'8000',0,0.00),(2,6,7,2909.95,2909.95,235.00,1,3,'8000',1,0.05),(2,5,6,2305.75,902.85,902.85,6,2,'8000',0,0.00),(2,5,5,2305.75,2305.75,250.00,1,3,'8000',0,0.00),(2,5,2,2305.75,2055.75,1152.90,3,2,'8000',1,0.05),(6,11,18,5013.25,2957.80,2957.80,2,2,'8000',0,0.00),(6,5,6,2305.75,902.85,902.85,6,2,'8000',0,0.00),(6,5,2,2305.75,2055.75,1152.90,3,2,'8000',1,0.05),(6,28,52,16802.90,16557.90,16557.90,2,2,'8000',0,0.00),(6,7,10,3259.15,3031.00,3031.00,2,2,'8000',0,0.00),(6,7,9,3259.15,3259.15,228.15,1,2,'8000',1,0.05),(6,10,15,4734.30,4734.30,433.00,1,3,'8000',1,0.05),(6,5,5,2305.75,2305.75,250.00,1,3,'8000',0,0.00),(6,33,62,21434.85,13289.60,13289.60,2,2,'8000',0,0.00),(6,17,29,7736.35,7736.35,3868.20,1,2,'8000',1,0.05),(6,13,21,6124.90,6124.90,857.50,1,2,'8000',1,0.05),(6,10,16,4734.30,4301.30,4301.30,2,2,'8000',0,0.00),(6,8,12,3788.00,3551.00,3551.00,2,2,'8000',0,0.00),(6,6,7,2909.95,2909.95,235.00,1,3,'8000',1,0.05),(6,17,30,7736.35,3868.15,3868.15,2,2,'8000',0,0.00),(6,13,22,6124.90,5267.40,5267.40,2,2,'8000',0,0.00),(6,11,17,5013.25,5013.25,2055.45,1,2,'8000',1,0.05),(6,9,13,4083.55,4083.55,1551.75,1,2,'8000',1,0.05),(6,6,8,2909.95,2674.95,2674.95,2,2,'8000',0,0.00),(6,28,51,16802.90,16802.90,245.00,1,3,'8000',1,0.05),(6,14,23,6441.15,6441.15,204.00,1,3,'8000',1,0.05),(6,12,19,5444.75,5444.75,462.00,1,3,'8000',1,0.05),(6,9,14,4083.55,2531.80,2531.80,2,2,'8000',0,0.00),(6,8,11,3788.00,3788.00,237.00,1,3,'8000',1,0.05),(6,33,61,21434.85,21434.85,8145.25,1,2,'8000',1,0.05),(6,14,24,6441.15,6237.15,6237.15,2,2,'8000',0,0.00),(6,12,20,5444.75,4982.75,4982.75,2,2,'8000',0,0.00),(7,5,2,0.00,0.00,0.00,3,2,'8000',1,0.05),(7,5,5,0.00,0.00,0.00,1,3,'8000',0,0.00),(7,5,6,0.00,0.00,0.00,6,2,'8000',0,0.00),(7,6,7,0.00,0.00,0.00,1,3,'8000',1,0.05),(7,6,8,0.00,0.00,0.00,2,2,'8000',0,0.00),(7,7,9,0.00,0.00,0.00,1,2,'8000',1,0.05),(7,7,10,0.00,0.00,0.00,2,2,'8000',0,0.00),(7,8,11,0.00,0.00,0.00,1,3,'8000',1,0.05),(7,8,12,0.00,0.00,0.00,2,2,'8000',0,0.00),(7,9,13,0.00,0.00,0.00,1,2,'8000',1,0.05),(7,9,14,0.00,0.00,0.00,2,2,'8000',0,0.00),(7,34,64,23017.75,22913.75,22913.75,2,2,'8000',0,0.00),(7,34,63,23017.75,23017.75,104.00,1,3,'8000',1,0.05),(7,33,62,21434.85,13289.60,13289.60,2,2,'8000',0,0.00),(7,33,61,21434.85,21434.85,8145.25,1,2,'8000',1,0.05),(7,32,60,20760.85,20398.85,20398.85,2,2,'8000',0,0.00),(7,32,59,20760.85,20760.85,362.00,1,3,'8000',1,0.05),(7,31,65,131.25,0.00,131.25,3,1,'9999',0,0.00),(7,31,58,19360.90,14907.90,14907.90,2,2,'8000',0,0.00),(7,31,57,19360.90,19360.90,4453.00,1,2,'8000',1,0.05),(7,30,56,15292.15,14979.15,14979.15,2,2,'8000',0,0.00),(7,30,55,15292.15,15292.15,313.00,1,3,'8000',1,0.05),(7,29,54,17946.00,10947.05,10947.05,2,2,'8000',0,0.00),(7,29,53,17946.00,17946.00,6998.95,1,2,'8000',1,0.05),(7,28,52,15964.60,15719.60,15719.60,2,2,'8000',0,0.00),(7,28,51,15964.60,15964.60,245.00,1,3,'8000',1,0.05),(7,27,50,15616.55,8589.10,8589.10,2,2,'8000',0,0.00),(7,27,49,15616.55,15616.55,7027.45,1,2,'8000',1,0.05),(7,26,48,14331.30,14092.30,14092.30,2,2,'8000',0,0.00),(7,26,47,14331.30,14331.30,239.00,1,3,'8000',1,0.05),(7,25,46,14235.65,8826.10,8826.10,2,2,'8000',0,0.00),(7,25,45,14235.65,14235.65,5409.55,1,2,'8000',1,0.05),(7,24,44,12800.40,12455.40,12455.40,2,2,'8000',0,0.00),(7,24,43,12800.40,12800.40,345.00,1,3,'8000',1,0.05),(7,23,42,12208.35,8545.85,8545.85,2,2,'8000',0,0.00),(7,23,41,12208.35,12208.35,3662.50,1,2,'8000',1,0.05),(7,22,40,10938.55,10674.55,10674.55,2,2,'8000',0,0.00),(7,22,39,10938.55,10938.55,264.00,1,3,'8000',1,0.05),(7,21,38,10160.40,7112.30,7112.30,2,2,'8000',0,0.00),(7,21,37,10160.40,10160.40,3048.10,1,2,'8000',1,0.05),(7,20,36,946.80,528.80,528.80,2,2,'8000',0,0.00),(7,20,35,946.80,946.80,418.00,1,3,'8000',1,0.05),(7,19,34,1472.85,839.50,839.50,2,2,'8000',0,0.00),(7,19,33,1472.85,1472.85,633.35,1,2,'8000',1,0.05),(7,18,32,8187.30,7939.30,7939.30,2,2,'8000',0,0.00),(7,18,31,8187.30,8187.30,248.00,1,3,'8000',1,0.05),(7,17,30,7736.35,3868.15,3868.15,2,2,'8000',0,0.00),(7,17,29,7736.35,7736.35,3868.20,1,2,'8000',1,0.05),(7,16,28,7259.65,7080.65,7080.65,2,2,'8000',0,0.00),(7,16,27,7259.65,7259.65,179.00,1,3,'8000',1,0.05),(7,15,26,7012.40,4277.55,4277.55,2,2,'8000',0,0.00),(7,15,25,7012.40,7012.40,2734.85,1,2,'8000',1,0.05),(7,14,24,6441.15,6237.15,6237.15,2,2,'8000',0,0.00),(7,14,23,6441.15,6441.15,204.00,1,3,'8000',1,0.05),(7,13,22,6124.90,5267.40,5267.40,2,2,'8000',0,0.00),(7,13,21,6124.90,6124.90,857.50,1,2,'8000',1,0.05),(7,12,20,5444.75,4982.75,4982.75,2,2,'8000',0,0.00),(7,12,19,5444.75,5444.75,462.00,1,3,'8000',1,0.05),(7,11,18,5013.25,2957.80,2957.80,2,2,'8000',0,0.00),(7,11,17,5013.25,5013.25,2055.45,1,2,'8000',1,0.05),(7,10,16,4552.50,4119.50,4119.50,2,2,'8000',0,0.00),(7,10,15,4552.50,4552.50,433.00,1,3,'8000',1,0.05),(8,34,64,23017.75,22913.75,22913.75,2,2,'8000',0,0.00),(8,34,63,23017.75,23017.75,104.00,1,3,'8000',1,0.05),(8,33,62,21434.85,13289.60,13289.60,2,2,'8000',0,0.00),(8,33,61,21434.85,21434.85,8145.25,1,2,'8000',1,0.05),(8,32,60,20760.85,20398.85,20398.85,2,2,'8000',0,0.00),(8,32,59,20760.85,20760.85,362.00,1,3,'8000',1,0.05),(8,31,65,131.25,0.00,131.25,3,1,'9999',0,0.00),(8,31,58,19360.90,14907.90,14907.90,2,2,'8000',0,0.00),(8,31,57,19360.90,19360.90,4453.00,1,2,'8000',1,0.05),(8,30,56,15292.15,14979.15,14979.15,2,2,'8000',0,0.00),(8,30,55,15292.15,15292.15,313.00,1,3,'8000',1,0.05),(8,29,54,17946.00,10947.05,10947.05,2,2,'8000',0,0.00),(8,29,53,17946.00,17946.00,6998.95,1,2,'8000',1,0.05),(8,28,52,15126.30,14881.30,14881.30,2,2,'8000',0,0.00),(8,28,51,15126.30,15126.30,245.00,1,3,'8000',1,0.05),(8,27,50,15616.55,8589.10,8589.10,2,2,'8000',0,0.00),(8,27,49,15616.55,15616.55,7027.45,1,2,'8000',1,0.05),(8,26,48,13594.00,13355.00,13355.00,2,2,'8000',0,0.00),(8,26,47,13594.00,13594.00,239.00,1,3,'8000',1,0.05),(8,25,46,14235.65,8826.10,8826.10,2,2,'8000',0,0.00),(8,25,45,14235.65,14235.65,5409.55,1,2,'8000',1,0.05),(8,24,44,12800.40,12455.40,12455.40,2,2,'8000',0,0.00),(8,24,43,12800.40,12800.40,345.00,1,3,'8000',1,0.05),(8,23,42,12208.35,8545.85,8545.85,2,2,'8000',0,0.00),(8,23,41,12208.35,12208.35,3662.50,1,2,'8000',1,0.05),(8,22,40,10938.55,10674.55,10674.55,2,2,'8000',0,0.00),(8,22,39,10938.55,10938.55,264.00,1,3,'8000',1,0.05),(8,21,38,10160.40,7112.30,7112.30,2,2,'8000',0,0.00),(8,21,37,10160.40,10160.40,3048.10,1,2,'8000',1,0.05),(8,20,36,946.80,528.80,528.80,2,2,'8000',0,0.00),(8,20,35,946.80,946.80,418.00,1,3,'8000',1,0.05),(8,19,34,1472.85,839.50,839.50,2,2,'8000',0,0.00),(8,19,33,1472.85,1472.85,633.35,1,2,'8000',1,0.05),(8,18,32,8187.30,7939.30,7939.30,2,2,'8000',0,0.00),(8,18,31,8187.30,8187.30,248.00,1,3,'8000',1,0.05),(8,17,30,7736.35,3868.15,3868.15,2,2,'8000',0,0.00),(8,17,29,7736.35,7736.35,3868.20,1,2,'8000',1,0.05),(8,16,28,7259.65,7080.65,7080.65,2,2,'8000',0,0.00),(8,16,27,7259.65,7259.65,179.00,1,3,'8000',1,0.05),(8,15,26,7012.40,4277.55,4277.55,2,2,'8000',0,0.00),(8,15,25,7012.40,7012.40,2734.85,1,2,'8000',1,0.05),(8,14,24,6441.15,6237.15,6237.15,2,2,'8000',0,0.00),(8,14,23,6441.15,6441.15,204.00,1,3,'8000',1,0.05),(8,13,22,6124.90,5267.40,5267.40,2,2,'8000',0,0.00),(8,13,21,6124.90,6124.90,857.50,1,2,'8000',1,0.05),(8,12,20,5444.75,4982.75,4982.75,2,2,'8000',0,0.00),(8,12,19,5444.75,5444.75,462.00,1,3,'8000',1,0.05),(8,11,18,5013.25,2957.80,2957.80,2,2,'8000',0,0.00),(8,11,17,5013.25,5013.25,2055.45,1,2,'8000',1,0.05),(8,10,16,4370.70,3937.70,3937.70,2,2,'8000',0,0.00),(8,10,15,4370.70,4370.70,433.00,1,3,'8000',1,0.05),(8,9,14,4083.55,2531.80,2531.80,2,2,'8000',0,0.00),(8,9,13,4083.55,4083.55,1551.75,1,2,'8000',1,0.05),(8,8,12,3788.00,3551.00,3551.00,2,2,'8000',0,0.00),(8,8,11,3788.00,3788.00,237.00,1,3,'8000',1,0.05),(8,7,10,3259.15,3031.00,3031.00,2,2,'8000',0,0.00),(8,7,9,3259.15,3259.15,228.15,1,2,'8000',1,0.05),(8,6,8,2909.95,2674.95,2674.95,2,2,'8000',0,0.00),(8,6,7,2909.95,2909.95,235.00,1,3,'8000',1,0.05),(8,5,6,2305.75,902.85,902.85,6,2,'8000',0,0.00),(8,5,5,2305.75,2305.75,250.00,1,3,'8000',0,0.00),(8,5,2,2305.75,2055.75,1152.90,3,2,'8000',1,0.05);
/*!40000 ALTER TABLE `payroll_payment_current` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_payment_entry`
--

DROP TABLE IF EXISTS `payroll_payment_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_payment_entry` (
  `payroll_period_ID` int(11) NOT NULL,
  `payroll_employee_ID` int(11) NOT NULL,
  `payroll_payment_split_ID` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  KEY `idx_paymetry_prdemp` (`payroll_period_ID`,`payroll_employee_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_payment_entry`
--

LOCK TABLES `payroll_payment_entry` WRITE;
/*!40000 ALTER TABLE `payroll_payment_entry` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_payment_entry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_payment_split`
--

DROP TABLE IF EXISTS `payroll_payment_split`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_payment_split` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_employee_ID` int(11) NOT NULL,
  `payroll_bank_source_ID` int(11) NOT NULL,
  `payroll_bank_destination_ID` int(11) NOT NULL,
  `processing_order` tinyint(4) NOT NULL,
  `split_mode` tinyint(4) NOT NULL,
  `payroll_account_ID` varchar(5) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `major_period` tinyint(1) NOT NULL,
  `minor_period` tinyint(1) NOT NULL,
  `major_period_bonus` tinyint(1) NOT NULL,
  `major_period_num` tinyint(4) NOT NULL,
  `minor_period_num` tinyint(4) NOT NULL,
  `major_period_bonus_num` tinyint(4) NOT NULL,
  `having_rounding` tinyint(1) NOT NULL,
  `round_param` decimal(7,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_main` (`payroll_employee_ID`,`payroll_bank_destination_ID`,`processing_order`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_payment_split`
--

LOCK TABLES `payroll_payment_split` WRITE;
/*!40000 ALTER TABLE `payroll_payment_split` DISABLE KEYS */;
INSERT INTO `payroll_payment_split` VALUES (1,5,1,1,2,2,'',40.00,1,0,0,12,0,0,1,0.05),(2,5,1,1,3,2,'',50.00,1,0,0,0,0,0,1,0.05),(3,5,1,1,4,2,'',100.00,0,0,1,0,0,0,1,0.05),(4,5,1,1,5,2,'',25.00,0,1,0,0,0,0,1,0.05),(5,5,1,2,1,3,'',250.00,1,1,0,0,0,0,0,0.00),(6,5,1,3,6,2,'',100.00,1,1,1,0,0,0,0,0.00),(7,6,1,4,1,3,'',235.00,1,1,1,0,0,0,1,0.05),(8,6,1,5,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(9,7,1,6,1,2,'',7.00,1,1,1,0,0,0,1,0.05),(10,7,1,7,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(11,8,1,8,1,3,'',237.00,1,1,1,0,0,0,1,0.05),(12,8,1,9,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(13,9,1,10,1,2,'',38.00,1,1,1,0,0,0,1,0.05),(14,9,1,11,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(15,10,1,12,1,3,'',433.00,1,1,1,0,0,0,1,0.05),(16,10,1,13,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(17,11,1,14,1,2,'',41.00,1,1,1,0,0,0,1,0.05),(18,11,1,15,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(19,12,1,16,1,3,'',462.00,1,1,1,0,0,0,1,0.05),(20,12,1,17,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(21,13,1,18,1,2,'',14.00,1,1,1,0,0,0,1,0.05),(22,13,1,19,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(23,14,1,20,1,3,'',204.00,1,1,1,0,0,0,1,0.05),(24,14,1,21,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(25,15,1,22,1,2,'',39.00,1,1,1,0,0,0,1,0.05),(26,15,1,23,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(27,16,1,24,1,3,'',179.00,1,1,1,0,0,0,1,0.05),(28,16,1,25,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(29,17,1,26,1,2,'',50.00,1,1,1,0,0,0,1,0.05),(30,17,1,27,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(31,18,1,28,1,3,'',248.00,1,1,1,0,0,0,1,0.05),(32,18,1,29,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(33,19,1,30,1,2,'',43.00,1,1,1,0,0,0,1,0.05),(34,19,1,31,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(35,20,1,32,1,3,'',418.00,1,1,1,0,0,0,1,0.05),(36,20,1,33,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(37,21,1,34,1,2,'',30.00,1,1,1,0,0,0,1,0.05),(38,21,1,35,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(39,22,1,36,1,3,'',264.00,1,1,1,0,0,0,1,0.05),(40,22,1,37,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(41,23,1,38,1,2,'',30.00,1,1,1,0,0,0,1,0.05),(42,23,1,39,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(43,24,1,40,1,3,'',345.00,1,1,1,0,0,0,1,0.05),(44,24,1,41,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(45,25,1,42,1,2,'',38.00,1,1,1,0,0,0,1,0.05),(46,25,1,43,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(47,26,1,44,1,3,'',239.00,1,1,1,0,0,0,1,0.05),(48,26,1,45,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(49,27,1,46,1,2,'',45.00,1,1,1,0,0,0,1,0.05),(50,27,1,47,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(51,28,1,48,1,3,'',245.00,1,1,1,0,0,0,1,0.05),(52,28,1,49,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(53,29,1,50,1,2,'',39.00,1,1,1,0,0,0,1,0.05),(54,29,1,51,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(55,30,1,52,1,3,'',313.00,1,1,1,0,0,0,1,0.05),(56,30,1,53,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(57,31,1,54,1,2,'',23.00,1,1,1,0,0,0,1,0.05),(58,31,1,55,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(59,32,1,56,1,3,'',362.00,1,1,1,0,0,0,1,0.05),(60,32,1,57,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(61,33,1,58,1,2,'',38.00,1,1,1,0,0,0,1,0.05),(62,33,1,59,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(63,34,1,60,1,3,'',104.00,1,1,1,0,0,0,1,0.05),(64,34,1,61,2,2,'',100.00,1,1,1,0,0,0,0,0.00),(65,31,1,62,3,1,'9999',0.00,1,1,1,0,0,0,0,0.00);
/*!40000 ALTER TABLE `payroll_payment_split` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_period`
--

DROP TABLE IF EXISTS `payroll_period`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_period` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_year_ID` smallint(6) NOT NULL,
  `major_period` smallint(5) unsigned NOT NULL,
  `minor_period` smallint(5) unsigned NOT NULL,
  `StatementDate` date NOT NULL,
  `Wage_DateFrom` date NOT NULL,
  `Wage_DateTo` date NOT NULL,
  `Salary_DateFrom` date NOT NULL,
  `Salary_DateTo` date NOT NULL,
  `HourlyWage_DateFrom` date NOT NULL,
  `HourlyWage_DateTo` date NOT NULL,
  `datetime_created` datetime NOT NULL,
  `core_user_ID_created` int(11) NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  `datetime_locked` datetime NOT NULL,
  `core_user_ID_locked` int(11) NOT NULL,
  `finalized` tinyint(1) NOT NULL DEFAULT '0',
  `datetime_finalized` datetime NOT NULL,
  `core_user_ID_finalized` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_payroll_period_payroll_year1_idx` (`payroll_year_ID`),
  CONSTRAINT `fk_payroll_period_payroll_year1` FOREIGN KEY (`payroll_year_ID`) REFERENCES `payroll_year` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_period`
--

LOCK TABLES `payroll_period` WRITE;
/*!40000 ALTER TABLE `payroll_period` DISABLE KEYS */;
INSERT INTO `payroll_period` VALUES (2,2011,1,0,'0000-00-00','2011-01-01','2011-01-31','2011-01-01','2011-01-31','2011-01-01','2011-01-31','0000-00-00 00:00:00',0,0,'0000-00-00 00:00:00',0,0,'0000-00-00 00:00:00',0);
/*!40000 ALTER TABLE `payroll_period` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_period_employee`
--

DROP TABLE IF EXISTS `payroll_period_employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_period_employee` (
  `payroll_period_ID` int(11) NOT NULL,
  `payroll_employee_ID` int(11) NOT NULL,
  `processing` tinyint(4) NOT NULL,
  `calc_datetime` datetime NOT NULL,
  `core_user_ID_calc` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `interest_date` date NOT NULL,
  `core_user_ID_payment` int(11) NOT NULL,
  `fin_acc_date` date NOT NULL,
  `core_user_ID_fin_acc` int(11) NOT NULL,
  `mgmt_acc_date` date NOT NULL,
  `core_user_ID_mgmt_acc` int(11) NOT NULL,
  PRIMARY KEY (`payroll_period_ID`,`payroll_employee_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_period_employee`
--

LOCK TABLES `payroll_period_employee` WRITE;
/*!40000 ALTER TABLE `payroll_period_employee` DISABLE KEYS */;
INSERT INTO `payroll_period_employee` VALUES (2,5,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,6,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,7,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,8,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,9,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,10,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,11,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,12,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,13,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,14,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,15,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,16,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,17,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,18,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,19,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,20,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,21,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,22,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,23,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,24,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,25,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,26,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,27,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,28,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,29,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,30,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,31,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,32,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,33,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,34,0,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0),(2,35,1,'0000-00-00 00:00:00',0,'0000-00-00','0000-00-00',0,'0000-00-00',0,'0000-00-00',0);
/*!40000 ALTER TABLE `payroll_period_employee` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_tmp_acclnk`
--

DROP TABLE IF EXISTS `payroll_tmp_acclnk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_tmp_acclnk` (
  `payroll_account_ID` varchar(5) NOT NULL,
  `payroll_year_ID` smallint(6) NOT NULL,
  `payroll_child_account_ID` varchar(5) NOT NULL,
  `field_assignment` tinyint(4) NOT NULL,
  `fwd_neg_values` tinyint(1) NOT NULL,
  `invert_value` tinyint(1) NOT NULL,
  `child_account_field` tinyint(4) NOT NULL,
  KEY `chldAcc` (`payroll_child_account_ID`) USING HASH,
  KEY `prntAcc` (`payroll_account_ID`) USING HASH
) ENGINE=MEMORY DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_tmp_acclnk`
--

LOCK TABLES `payroll_tmp_acclnk` WRITE;
/*!40000 ALTER TABLE `payroll_tmp_acclnk` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_tmp_acclnk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_tmp_calc_modifier`
--

DROP TABLE IF EXISTS `payroll_tmp_calc_modifier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_tmp_calc_modifier` (
  `core_user_ID` int(11) NOT NULL,
  `payroll_employee_ID` int(11) NOT NULL,
  `payroll_account_ID` varchar(5) NOT NULL,
  `ModifierType` tinyint(4) NOT NULL,
  `FieldName` varchar(30) NOT NULL,
  `TargetField` tinyint(4) NOT NULL,
  `TargetValue` decimal(12,5) NOT NULL,
  `max_limit` decimal(8,2) NOT NULL,
  `min_limit` decimal(8,2) NOT NULL,
  `deduction` decimal(8,2) NOT NULL,
  `major_period` tinyint(1) NOT NULL,
  `minor_period` tinyint(1) NOT NULL,
  `major_period_bonus` tinyint(1) NOT NULL
) ENGINE=MEMORY DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_tmp_calc_modifier`
--

LOCK TABLES `payroll_tmp_calc_modifier` WRITE;
/*!40000 ALTER TABLE `payroll_tmp_calc_modifier` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_tmp_calc_modifier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_tmp_calculation`
--

DROP TABLE IF EXISTS `payroll_tmp_calculation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_tmp_calculation` (
  `core_user_ID` int(11) NOT NULL,
  `payroll_year_ID` smallint(6) NOT NULL,
  `payroll_account_ID` varchar(5) NOT NULL,
  `payroll_employee_ID` int(11) NOT NULL,
  `input` decimal(12,5) NOT NULL,
  `surcharge` decimal(12,5) NOT NULL,
  `factor` decimal(12,5) NOT NULL,
  `quantity` decimal(12,5) NOT NULL,
  `rate` decimal(12,5) NOT NULL,
  `amount` decimal(12,5) NOT NULL,
  `output` decimal(12,5) NOT NULL,
  `having_limits` tinyint(1) NOT NULL,
  `input_assignment` tinyint(4) NOT NULL,
  `having_calculation` tinyint(1) NOT NULL,
  `output_assignment` tinyint(4) NOT NULL,
  `round_param` decimal(6,4) NOT NULL,
  `limits_calc_mode` tinyint(4) NOT NULL DEFAULT '0',
  `limits_aux_account_ID` varchar(5) NOT NULL,
  `process_status` tinyint(4) NOT NULL,
  `process_order` smallint(6) NOT NULL,
  `sign` tinyint(1) NOT NULL DEFAULT '0',
  `having_rounding` tinyint(1) NOT NULL DEFAULT '0',
  `payroll_formula_ID` smallint(6) NOT NULL DEFAULT '0',
  `allowable_workdays` smallint(6) NOT NULL DEFAULT '0',
  `allowable_workdays_mt` tinyint(4) NOT NULL DEFAULT '0',
  `amount_balance` decimal(13,5) NOT NULL DEFAULT '0.00000',
  `max_limit` decimal(8,2) NOT NULL DEFAULT '0.00',
  `min_limit` decimal(8,2) NOT NULL DEFAULT '0.00',
  `deduction` decimal(8,2) NOT NULL DEFAULT '0.00',
  `tmpCumulativeBase` decimal(12,5) NOT NULL DEFAULT '0.00000',
  `tmpDeduction` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `having_pensioner_calc` tinyint(1) NOT NULL DEFAULT '0',
  KEY `idxProcOrder` (`process_order`,`having_calculation`,`payroll_formula_ID`) USING BTREE,
  KEY `idxAccID` (`payroll_account_ID`,`payroll_employee_ID`) USING HASH
) ENGINE=MEMORY DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_tmp_calculation`
--

LOCK TABLES `payroll_tmp_calculation` WRITE;
/*!40000 ALTER TABLE `payroll_tmp_calculation` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_tmp_calculation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_tmp_change_mng`
--

DROP TABLE IF EXISTS `payroll_tmp_change_mng`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_tmp_change_mng` (
  `core_user_id` int(11) NOT NULL,
  `numID` int(11) NOT NULL,
  `alphID` varchar(5) NOT NULL,
  KEY `uidnum` (`core_user_id`,`numID`),
  KEY `uidalph` (`core_user_id`,`alphID`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_tmp_change_mng`
--

LOCK TABLES `payroll_tmp_change_mng` WRITE;
/*!40000 ALTER TABLE `payroll_tmp_change_mng` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_tmp_change_mng` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_tmp_empl_accounts`
--

DROP TABLE IF EXISTS `payroll_tmp_empl_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_tmp_empl_accounts` (
  `payroll_employee_ID` int(11) NOT NULL,
  `payroll_account_ID` varchar(5) NOT NULL,
  `rate` decimal(12,5) NOT NULL,
  `mustAdd` tinyint(1) NOT NULL,
  `mustRemove` tinyint(1) NOT NULL,
  `replaceRate` tinyint(1) NOT NULL DEFAULT '0',
  `parentOfAuxAccount` varchar(5) NOT NULL,
  PRIMARY KEY (`payroll_employee_ID`,`payroll_account_ID`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_tmp_empl_accounts`
--

LOCK TABLES `payroll_tmp_empl_accounts` WRITE;
/*!40000 ALTER TABLE `payroll_tmp_empl_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_tmp_empl_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_tmp_mgmt_acc_split`
--

DROP TABLE IF EXISTS `payroll_tmp_mgmt_acc_split`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_tmp_mgmt_acc_split` (
  `payroll_period_ID` int(11) NOT NULL,
  `payroll_company_ID` int(11) NOT NULL,
  `payroll_employee_ID` int(11) NOT NULL,
  `cost_center` varchar(15) NOT NULL,
  `payroll_account_ID` varchar(5) NOT NULL,
  `amount_initial` decimal(10,2) NOT NULL,
  `amount_available` decimal(10,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `processing_order` tinyint(4) NOT NULL,
  `invert_value` tinyint(1) NOT NULL,
  `amount_quantity` tinyint(1) NOT NULL,
  `processing_done` tinyint(1) NOT NULL,
  `having_rounding` tinyint(1) NOT NULL,
  `rounding` decimal(7,2) NOT NULL,
  `remainder` tinyint(4) NOT NULL
) ENGINE=MEMORY DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_tmp_mgmt_acc_split`
--

LOCK TABLES `payroll_tmp_mgmt_acc_split` WRITE;
/*!40000 ALTER TABLE `payroll_tmp_mgmt_acc_split` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_tmp_mgmt_acc_split` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_tmp_qst_import`
--

DROP TABLE IF EXISTS `payroll_tmp_qst_import`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_tmp_qst_import` (
  `transaction_type` tinyint(4) NOT NULL,
  `canton` varchar(2) NOT NULL,
  `rate_code` varchar(10) NOT NULL,
  `date_from` date NOT NULL,
  `taxable_income` decimal(9,2) NOT NULL,
  `step_rate` decimal(9,2) NOT NULL,
  `sex` varchar(1) NOT NULL,
  `children` tinyint(4) NOT NULL,
  `tax_amount` decimal(9,2) NOT NULL,
  `tax_rate` decimal(5,2) NOT NULL
) ENGINE=MEMORY DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_tmp_qst_import`
--

LOCK TABLES `payroll_tmp_qst_import` WRITE;
/*!40000 ALTER TABLE `payroll_tmp_qst_import` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_tmp_qst_import` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_year`
--

DROP TABLE IF EXISTS `payroll_year`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_year` (
  `id` smallint(6) NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_year`
--

LOCK TABLES `payroll_year` WRITE;
/*!40000 ALTER TABLE `payroll_year` DISABLE KEYS */;
INSERT INTO `payroll_year` VALUES (2011,'2011-01-01','2011-12-31');
/*!40000 ALTER TABLE `payroll_year` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tmpDebug`
--

DROP TABLE IF EXISTS `tmpDebug`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tmpDebug` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `txt` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tmpDebug`
--

LOCK TABLES `tmpDebug` WRITE;
/*!40000 ALTER TABLE `tmpDebug` DISABLE KEYS */;
/*!40000 ALTER TABLE `tmpDebug` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'development'
--
/*!50003 DROP PROCEDURE IF EXISTS `payroll_prc_calculate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`remoteadmin`@`213.188.228.86`*/ /*!50003 PROCEDURE `payroll_prc_calculate`(currentUser INT)
BEGIN
	DECLARE x SMALLINT;
	DECLARE xMax SMALLINT;
	DECLARE xLimitMinCalc1 SMALLINT;
	DECLARE xLimitMaxCalc1 SMALLINT;
	DECLARE xLimitMinCalc2 SMALLINT;
	DECLARE xLimitMaxCalc2 SMALLINT;
	DECLARE xPensionerCalcOffset SMALLINT;
	DECLARE currentYear SMALLINT;
	DECLARE currentPeriodID SMALLINT;
	DECLARE currentMajorPeriod SMALLINT;
	DECLARE currentMinorPeriod SMALLINT;
	DECLARE strFormula VARCHAR(100);
	DECLARE idFormula INT;
    DECLARE done INT DEFAULT 0;
    DECLARE cursorFormula CURSOR FOR
		SELECT DISTINCT `payroll_formula`.`id`,`payroll_formula`.`formula` FROM `payroll_tmp_calculation` INNER JOIN `payroll_formula` ON `payroll_tmp_calculation`.`payroll_formula_ID`=`payroll_formula`.`id` WHERE `payroll_tmp_calculation`.`core_user_ID`=currentUser AND `payroll_tmp_calculation`.`process_order`=x AND `payroll_tmp_calculation`.`having_calculation`=1;
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	SELECT id,payroll_year_ID,major_period,minor_period INTO currentPeriodID, currentYear, currentMajorPeriod, currentMinorPeriod FROM payroll_period WHERE locked=0 AND finalized=0;

	DELETE FROM `payroll_tmp_calculation` WHERE `core_user_ID`=currentUser;
	INSERT INTO `payroll_tmp_acclnk` SELECT * FROM `payroll_account_linker` WHERE `payroll_year_ID`=currentYear;


	INSERT INTO `payroll_tmp_calculation`(`core_user_ID`,`payroll_year_ID`,`payroll_account_ID`,`payroll_employee_ID`,`input`,`surcharge`,`factor`,`quantity`,`rate`,`amount`,`output`,`having_limits`,`input_assignment`,`having_calculation`,`output_assignment`,`round_param`,`limits_aux_account_ID`,`process_status`,`process_order`,`sign`,`having_rounding`,`payroll_formula_ID`,`max_limit`,`min_limit`,`deduction`,`limits_calc_mode`)
	SELECT currentUser,acc.`payroll_year_ID`,acc.`id`,emplList.`numID`,0,acc.`surcharge`,acc.`factor`,acc.`quantity`,acc.`rate`,acc.`amount`,0,acc.`having_limits`,acc.`input_assignment`,acc.`having_calculation`,acc.`output_assignment`,acc.`round_param`,acc.`limits_aux_account_ID`,0,acc.`processing_order`,acc.`sign`,acc.`having_rounding`,acc.`payroll_formula_ID`,acc.`max_limit`,acc.`min_limit`,acc.`deduction`,acc.`limits_calc_mode` FROM `payroll_tmp_change_mng` emplList INNER JOIN `payroll_account` acc ON acc.`payroll_year_ID`=currentYear AND acc.`mandatory`=1 AND acc.`insertion_rules`=0 WHERE emplList.`core_user_ID`=currentUser;

	
	INSERT INTO `payroll_tmp_calculation`(`core_user_ID`,`payroll_year_ID`,`payroll_account_ID`,`payroll_employee_ID`,`input`,`surcharge`,`factor`,`quantity`,`rate`,`amount`,`output`,`having_limits`,`input_assignment`,`having_calculation`,`output_assignment`,`round_param`,`limits_aux_account_ID`,`process_status`,`process_order`,`sign`,`having_rounding`,`payroll_formula_ID`,`max_limit`,`min_limit`,`deduction`,`having_pensioner_calc`,`limits_calc_mode`)
	SELECT currentUser,acc.`payroll_year_ID`,acc.`id`,emplList.`numID`,0,acc.`surcharge`,acc.`factor`,IF(emplacc.`quantity`!=0,emplacc.`quantity`,acc.`quantity`),IF(emplacc.`rate`!=0,emplacc.`rate`,acc.`rate`),IF(emplacc.`amount`!=0,emplacc.`amount`,acc.`amount`),0,acc.`having_limits`,acc.`input_assignment`,acc.`having_calculation`,acc.`output_assignment`,acc.`round_param`,acc.`limits_aux_account_ID`,0,acc.`processing_order`,acc.`sign`,acc.`having_rounding`,acc.`payroll_formula_ID`,IF(emplacc.`having_pensioner_calc`=1,emplacc.`max_limit`,acc.`max_limit`),IF(emplacc.`having_pensioner_calc`=1,emplacc.`min_limit`,acc.`min_limit`),IF(emplacc.`having_pensioner_calc`=1,emplacc.`deduction`,acc.`deduction`),emplacc.`having_pensioner_calc`,acc.`limits_calc_mode` 
	FROM `payroll_tmp_change_mng` emplList 
	INNER JOIN `payroll_employee_account` emplacc ON emplacc.`payroll_employee_ID`=emplList.`numID` AND (emplacc.`PayrollDataType`<5 OR emplacc.`PayrollDataType`=8) 
	INNER JOIN `payroll_account` acc ON emplacc.`payroll_account_ID`=acc.`id` AND acc.`payroll_year_ID`=currentYear WHERE emplList.`core_user_ID`=currentUser;

	INSERT INTO `payroll_tmp_calculation`(`core_user_ID`,`payroll_year_ID`,`payroll_account_ID`,`payroll_employee_ID`,`input`,`surcharge`,`factor`,`quantity`,`rate`,`amount`,`output`,`having_limits`,`input_assignment`,`having_calculation`,`output_assignment`,`round_param`,`limits_aux_account_ID`,`process_status`,`process_order`,`sign`,`having_rounding`,`payroll_formula_ID`,`max_limit`,`min_limit`,`deduction`,`having_pensioner_calc`,`limits_calc_mode`)
	SELECT emplList.`core_user_ID`,acc.`payroll_year_ID`,accmap.`payroll_account_ID`,emplList.`numID`,0,0,0,0,0,IF(emp.`WageCode`='1',emp.`BaseWage`,IF(emp.`AttendedTimeCode`='0',emp.`BaseWage`/emp.`AttendedTimeHours`,emp.`BaseWage`/attm.`attended_time`)),0,0,0,acc.`having_calculation`,acc.`output_assignment`,acc.`round_param`,acc.`limits_aux_account_ID`,0,acc.`processing_order`,acc.`sign`,acc.`having_rounding`,acc.`payroll_formula_ID`,0,0,0,0,0 
	FROM `payroll_tmp_change_mng` emplList 
	INNER JOIN `payroll_account_mapping` accmap ON accmap.`ProcessingMethod`=4 
	INNER JOIN `payroll_employee` emp ON emp.`id`=emplList.`numID` 
	INNER JOIN `payroll_attended_time` attm ON emp.`AttendedTimeCode`=attm.`id` 
	INNER JOIN `payroll_account` acc ON acc.`id`=accmap.`payroll_account_ID` AND acc.`payroll_year_ID`=currentYear 
	WHERE emplList.`core_user_ID`=currentUser;

	IF currentMajorPeriod<15 AND currentMinorPeriod=0 THEN
		
		UPDATE `payroll_tmp_calculation` calc
		INNER JOIN `payroll_employee_account` empacc ON empacc.`payroll_employee_ID`=calc.`payroll_employee_ID` AND empacc.`payroll_account_ID`=calc.`payroll_account_ID` AND (empacc.`PayrollDataType`=5 OR empacc.`PayrollDataType`=7) AND empacc.`major_period`=1
		SET calc.`quantity`=IF(empacc.`TargetField`=3,empacc.`quantity`,calc.`quantity`), calc.`rate`=IF(empacc.`TargetField`=4,empacc.`rate`,calc.`rate`), calc.`amount`=IF(empacc.`TargetField`=5,empacc.`amount`,calc.`amount`);

		
		UPDATE `payroll_tmp_calculation` calc
		INNER JOIN `payroll_employee_account` empacc ON empacc.`payroll_employee_ID`=calc.`payroll_employee_ID` AND empacc.`payroll_account_ID`=calc.`payroll_account_ID` AND empacc.`PayrollDataType`=6 AND empacc.`major_period`=1
		SET calc.`max_limit`=empacc.`max_limit`,calc.`min_limit`=empacc.`min_limit`,calc.`deduction`=empacc.`deduction`;
	ELSEIF currentMajorPeriod>14 AND currentMinorPeriod=0 THEN
		
		UPDATE `payroll_tmp_calculation` calc
		INNER JOIN `payroll_employee_account` empacc ON empacc.`payroll_employee_ID`=calc.`payroll_employee_ID` AND empacc.`payroll_account_ID`=calc.`payroll_account_ID` AND (empacc.`PayrollDataType`=5 OR empacc.`PayrollDataType`=7) AND empacc.`major_period_bonus`=1
		SET calc.`quantity`=IF(empacc.`TargetField`=3,empacc.`quantity`,calc.`quantity`), calc.`rate`=IF(empacc.`TargetField`=4,empacc.`rate`,calc.`rate`), calc.`amount`=IF(empacc.`TargetField`=5,empacc.`amount`,calc.`amount`);

		
		UPDATE `payroll_tmp_calculation` calc
		INNER JOIN `payroll_employee_account` empacc ON empacc.`payroll_employee_ID`=calc.`payroll_employee_ID` AND empacc.`payroll_account_ID`=calc.`payroll_account_ID` AND empacc.`PayrollDataType`=6 AND empacc.`major_period_bonus`=1
		SET calc.`max_limit`=empacc.`max_limit`,calc.`min_limit`=empacc.`min_limit`,calc.`deduction`=empacc.`deduction`;
	ELSEIF currentMajorPeriod<15 AND currentMinorPeriod>0 THEN
		
		UPDATE `payroll_tmp_calculation` calc
		INNER JOIN `payroll_employee_account` empacc ON empacc.`payroll_employee_ID`=calc.`payroll_employee_ID` AND empacc.`payroll_account_ID`=calc.`payroll_account_ID` AND (empacc.`PayrollDataType`=5 OR empacc.`PayrollDataType`=7) AND empacc.`minor_period`=1
		SET calc.`quantity`=IF(empacc.`TargetField`=3,empacc.`quantity`,calc.`quantity`), calc.`rate`=IF(empacc.`TargetField`=4,empacc.`rate`,calc.`rate`), calc.`amount`=IF(empacc.`TargetField`=5,empacc.`amount`,calc.`amount`);

		
		UPDATE `payroll_tmp_calculation` calc
		INNER JOIN `payroll_employee_account` empacc ON empacc.`payroll_employee_ID`=calc.`payroll_employee_ID` AND empacc.`payroll_account_ID`=calc.`payroll_account_ID` AND empacc.`PayrollDataType`=6 AND empacc.`minor_period`=1
		SET calc.`max_limit`=empacc.`max_limit`,calc.`min_limit`=empacc.`min_limit`,calc.`deduction`=empacc.`deduction`;
	END IF;

	
	UPDATE `payroll_tmp_calculation` tmpcalc 
	INNER JOIN (SELECT empacc.`payroll_employee_ID`,empacc.`payroll_account_ID`,SUM(empacc.`allowable_workdays`) as curWorkdays,SUM(empacc.`allowable_workdays`+empacc.`allowable_workdays_sum`) as sumWorkdays,SUM(empacc.`amount_balance`) as sumAmountBalance FROM `payroll_employee_account` empacc INNER JOIN `payroll_tmp_change_mng` emplist ON empacc.`payroll_employee_ID`=emplist.`numID` AND emplist.`core_user_ID`=currentUser WHERE empacc.`PayrollDataType`=9 GROUP BY empacc.`payroll_employee_ID`,empacc.`payroll_account_ID`) src ON tmpcalc.`payroll_employee_ID`=src.`payroll_employee_ID` AND tmpcalc.`payroll_account_ID`=src.`payroll_account_ID` 
	SET tmpcalc.`allowable_workdays`=src.sumWorkdays, tmpcalc.`allowable_workdays_mt`=src.curWorkdays, tmpcalc.`amount_balance`=src.sumAmountBalance
	WHERE tmpcalc.`core_user_ID`=currentUser;

	SELECT MAX(`process_order`) INTO xMax FROM `payroll_tmp_calculation` WHERE `core_user_ID`=currentUser;
	-- Offset der Spezialberechnungen ermitteln
	SELECT IF(MIN(`process_order`) IS NULL, 255, MIN(`process_order`)),IF(MAX(`process_order`) IS NULL, 255, MAX(`process_order`)) INTO xLimitMinCalc1,xLimitMaxCalc1 FROM `payroll_tmp_calculation` WHERE `having_limits`=1 AND `limits_calc_mode`!=2;
	SELECT IF(MIN(`process_order`) IS NULL, 255, MIN(`process_order`)),IF(MAX(`process_order`) IS NULL, 255, MAX(`process_order`)) INTO xLimitMinCalc2,xLimitMaxCalc2 FROM `payroll_tmp_calculation` WHERE `having_limits`=1 AND `limits_calc_mode`=2;
	SELECT IF(MIN(`process_order`) IS NULL, 255, MIN(`process_order`)) INTO xPensionerCalcOffset FROM `payroll_tmp_calculation` WHERE `having_pensioner_calc`=1;

	SET x = 1;
	WHILE x <= xMax DO
		IF x > 1 THEN
			IF x >= xLimitMinCalc1 AND x <= xLimitMaxCalc1 THEN
				-- LIMITEN-Verarbeitung mit JAG
				-- BasisKumuliert und monatl. Abzug berechnen
				UPDATE `payroll_tmp_calculation` uno 
				INNER JOIN `payroll_tmp_calculation` due ON uno.`limits_aux_account_ID`=due.`payroll_account_ID` AND uno.`payroll_employee_ID`=due.`payroll_employee_ID` AND uno.`core_user_ID`=due.`core_user_ID` 
				SET
					uno.`tmpCumulativeBase`=due.`amount_balance`+due.`amount`, uno.`tmpDeduction`=uno.`deduction`/360*uno.`allowable_workdays`
				WHERE uno.`core_user_ID`=currentUser AND uno.`process_order`=x AND uno.`having_limits`=1 AND uno.`limits_calc_mode`!=2;

				-- Ermittlung des aktuellen Lohns
				UPDATE `payroll_tmp_calculation` 
				SET `input`=IF(`tmpCumulativeBase`>(`max_limit`/360*`allowable_workdays`), 
						(`max_limit`/360*`allowable_workdays`) - `amount_balance` - `tmpDeduction`, 
						IF(`amount_balance`<=0 AND `min_limit`=0, 
							`tmpCumulativeBase` - `tmpDeduction`, 
							IF((`tmpCumulativeBase` - `tmpDeduction`)<0 AND `amount_balance`>0 AND `min_limit`>0, 
								(`min_limit`/360*`allowable_workdays`) -`amount_balance`, 
								IF(`tmpCumulativeBase`<(`min_limit`/360*`allowable_workdays`), 
									IF((`tmpCumulativeBase` - `tmpDeduction`)<0 AND `amount_balance`>0 AND `min_limit`=0, 
										`tmpCumulativeBase` - `tmpDeduction` - `amount_balance`, 
										`amount_balance` * -1
									), 
									`tmpCumulativeBase` - `amount_balance` - `tmpDeduction`
								)
							)
						)
					)
				WHERE `core_user_ID`=currentUser AND `process_order`=x AND `having_limits`=1 AND `limits_calc_mode`!=2;

				-- Resultat ermitteln und runden
				UPDATE `payroll_tmp_calculation` 
				SET `input`=IF(`input`>0 AND `tmpDeduction`=0,
					ROUND((`input`-(`min_limit`/360*`allowable_workdays`))/`round_param`)*`round_param`, 
					IF(`input`<0 AND `tmpDeduction`>0 AND `amount_balance`=0, 
						ROUND((`min_limit`/360*`allowable_workdays`)/`round_param`)*`round_param`, 
						ROUND(`input`/`round_param`)*`round_param`
					)
				) 
				WHERE `core_user_ID`=currentUser AND `process_order`=x AND `having_limits`=1 AND `limits_calc_mode`!=2;
			END IF;
			IF x >= xLimitMinCalc2 AND x <= xLimitMaxCalc2 THEN
				-- LIMITEN-Verarbeitung ohne JAG
				UPDATE `payroll_tmp_calculation` SET `input`=IF(`input`-`deduction` < `min_limit`,`min_limit`,IF(`input`-`deduction` > `max_limit`,`max_limit`,`input`-`deduction`)) WHERE `core_user_ID`=currentUser AND `process_order`=x AND `having_limits`=1 AND `limits_calc_mode`=2;
			END IF;
			IF x = xPensionerCalcOffset THEN
				UPDATE `payroll_tmp_calculation` 
				SET `quantity`=IF(`deduction`<0,(`max_limit`+`quantity`)-`min_limit`,IF(`deduction`>(`max_limit`+`quantity`), 0, (`max_limit`+`quantity`)-`deduction`)-`min_limit`) 
				WHERE `process_order`=x AND `having_pensioner_calc`=1 AND `core_user_ID`=currentUser;
			END IF;
			IF (x >= xLimitMinCalc1 AND x <= xLimitMaxCalc1) OR (x >= xLimitMinCalc2 AND x <= xLimitMaxCalc2) THEN
				UPDATE `payroll_tmp_calculation` SET `surcharge`=`input` WHERE `core_user_ID`=currentUser AND `process_order`=x AND `input_assignment`=1 AND `having_limits`=1;
				UPDATE `payroll_tmp_calculation` SET `factor`=`input` WHERE `core_user_ID`=currentUser AND `process_order`=x AND `input_assignment`=2 AND `having_limits`=1;
				UPDATE `payroll_tmp_calculation` SET `quantity`=`input` WHERE `core_user_ID`=currentUser AND `process_order`=x AND `input_assignment`=3 AND `having_limits`=1;
				UPDATE `payroll_tmp_calculation` SET `rate`=`input` WHERE `core_user_ID`=currentUser AND `process_order`=x AND `input_assignment`=4 AND `having_limits`=1;
				UPDATE `payroll_tmp_calculation` SET `amount`=`input` WHERE `core_user_ID`=currentUser AND `process_order`=x AND `input_assignment`=5 AND `having_limits`=1;
			END IF;
		END IF;

		OPEN cursorFormula;
		REPEAT
			FETCH cursorFormula INTO idFormula,strFormula;
			IF done = 0 THEN
				SET @vSQL := CONCAT('UPDATE `payroll_tmp_calculation` SET `output`=',strFormula,' WHERE `core_user_ID`=',currentUser,' AND `process_order`=',x,' AND `having_calculation`=1 AND `payroll_formula_ID`=',idFormula);
				PREPARE STMT FROM @vSQL;
				EXECUTE STMT;
			END IF;
		UNTIL done END REPEAT;
		CLOSE cursorFormula;
		SET done = 0;

		UPDATE `payroll_tmp_calculation` SET `output`=ROUND(`output`/`round_param`)*`round_param` WHERE `core_user_ID`=currentUser AND `process_order`=x AND `having_calculation`=1 AND `having_rounding`=1;

		UPDATE `payroll_tmp_calculation` SET `quantity`=`output` WHERE `core_user_ID`=currentUser AND `process_order`=x AND `having_calculation`=1 AND `output_assignment`=3;
		UPDATE `payroll_tmp_calculation` SET `rate`=`output` WHERE `core_user_ID`=currentUser AND `process_order`=x AND `having_calculation`=1 AND `output_assignment`=4;
		UPDATE `payroll_tmp_calculation` SET `amount`=`output` WHERE `core_user_ID`=currentUser AND `process_order`=x AND `having_calculation`=1 AND `output_assignment`=5;

		UPDATE `payroll_tmp_calculation` SET `amount`=`amount`*-1 WHERE `core_user_ID`=currentUser AND `process_order`=x AND `sign`=1;

		UPDATE `payroll_tmp_calculation` dest INNER JOIN (SELECT calc.`payroll_year_ID`,calc.`payroll_employee_ID`,lnk.`payroll_child_account_ID`,lnk.`child_account_field`,SUM(IF(lnk.`field_assignment`=5 AND (lnk.`fwd_neg_values`=1 OR (lnk.`fwd_neg_values`=0 AND calc.`amount`>=0.0)),amount,IF(lnk.`field_assignment`=4 AND (lnk.`fwd_neg_values`=1 OR (lnk.`fwd_neg_values`=0 AND calc.`rate`>=0.0)),rate,IF(lnk.`field_assignment`=3 AND (lnk.`fwd_neg_values`=1 OR (lnk.`fwd_neg_values`=0 AND calc.`quantity`>=0.0)),quantity,0)))*IF(lnk.`invert_value`=1,-1,1)) as inputSum FROM `payroll_tmp_calculation` calc INNER JOIN `payroll_tmp_acclnk` lnk ON calc.`payroll_year_ID`=lnk.`payroll_year_ID` AND calc.`payroll_account_ID`=lnk.`payroll_account_ID` WHERE calc.`core_user_ID`=currentUser AND calc.`process_order`=x AND lnk.`child_account_field`=0 GROUP BY calc.`payroll_year_ID`,calc.`payroll_employee_ID`,lnk.`payroll_child_account_ID`,lnk.`child_account_field`) AS src ON dest.`payroll_year_ID`=src.`payroll_year_ID` AND dest.`payroll_account_ID`=src.`payroll_child_account_ID` AND dest.`payroll_employee_ID`=src.`payroll_employee_ID` SET dest.`input`=dest.`input`+src.inputSum WHERE src.child_account_field=0;
		UPDATE `payroll_tmp_calculation` dest INNER JOIN (SELECT calc.`payroll_year_ID`,calc.`payroll_employee_ID`,lnk.`payroll_child_account_ID`,lnk.`child_account_field`,SUM(IF(lnk.`field_assignment`=5 AND (lnk.`fwd_neg_values`=1 OR (lnk.`fwd_neg_values`=0 AND calc.`amount`>=0.0)),amount,IF(lnk.`field_assignment`=4 AND (lnk.`fwd_neg_values`=1 OR (lnk.`fwd_neg_values`=0 AND calc.`rate`>=0.0)),rate,IF(lnk.`field_assignment`=3 AND (lnk.`fwd_neg_values`=1 OR (lnk.`fwd_neg_values`=0 AND calc.`quantity`>=0.0)),quantity,0)))*IF(lnk.`invert_value`=1,-1,1)) as inputSum FROM `payroll_tmp_calculation` calc INNER JOIN `payroll_tmp_acclnk` lnk ON calc.`payroll_year_ID`=lnk.`payroll_year_ID` AND calc.`payroll_account_ID`=lnk.`payroll_account_ID` WHERE calc.`core_user_ID`=currentUser AND calc.`process_order`=x AND lnk.`child_account_field`=1 GROUP BY calc.`payroll_year_ID`,calc.`payroll_employee_ID`,lnk.`payroll_child_account_ID`,lnk.`child_account_field`) AS src ON dest.`payroll_year_ID`=src.`payroll_year_ID` AND dest.`payroll_account_ID`=src.`payroll_child_account_ID` AND dest.`payroll_employee_ID`=src.`payroll_employee_ID` SET dest.`factor`=dest.`factor`+src.inputSum WHERE src.child_account_field=1;
		UPDATE `payroll_tmp_calculation` dest INNER JOIN (SELECT calc.`payroll_year_ID`,calc.`payroll_employee_ID`,lnk.`payroll_child_account_ID`,lnk.`child_account_field`,SUM(IF(lnk.`field_assignment`=5 AND (lnk.`fwd_neg_values`=1 OR (lnk.`fwd_neg_values`=0 AND calc.`amount`>=0.0)),amount,IF(lnk.`field_assignment`=4 AND (lnk.`fwd_neg_values`=1 OR (lnk.`fwd_neg_values`=0 AND calc.`rate`>=0.0)),rate,IF(lnk.`field_assignment`=3 AND (lnk.`fwd_neg_values`=1 OR (lnk.`fwd_neg_values`=0 AND calc.`quantity`>=0.0)),quantity,0)))*IF(lnk.`invert_value`=1,-1,1)) as inputSum FROM `payroll_tmp_calculation` calc INNER JOIN `payroll_tmp_acclnk` lnk ON calc.`payroll_year_ID`=lnk.`payroll_year_ID` AND calc.`payroll_account_ID`=lnk.`payroll_account_ID` WHERE calc.`core_user_ID`=currentUser AND calc.`process_order`=x AND lnk.`child_account_field`=2 GROUP BY calc.`payroll_year_ID`,calc.`payroll_employee_ID`,lnk.`payroll_child_account_ID`,lnk.`child_account_field`) AS src ON dest.`payroll_year_ID`=src.`payroll_year_ID` AND dest.`payroll_account_ID`=src.`payroll_child_account_ID` AND dest.`payroll_employee_ID`=src.`payroll_employee_ID` SET dest.`surcharge`=dest.`surcharge`+src.inputSum WHERE src.child_account_field=2;
		UPDATE `payroll_tmp_calculation` dest INNER JOIN (SELECT calc.`payroll_year_ID`,calc.`payroll_employee_ID`,lnk.`payroll_child_account_ID`,lnk.`child_account_field`,SUM(IF(lnk.`field_assignment`=5 AND (lnk.`fwd_neg_values`=1 OR (lnk.`fwd_neg_values`=0 AND calc.`amount`>=0.0)),amount,IF(lnk.`field_assignment`=4 AND (lnk.`fwd_neg_values`=1 OR (lnk.`fwd_neg_values`=0 AND calc.`rate`>=0.0)),rate,IF(lnk.`field_assignment`=3 AND (lnk.`fwd_neg_values`=1 OR (lnk.`fwd_neg_values`=0 AND calc.`quantity`>=0.0)),quantity,0)))*IF(lnk.`invert_value`=1,-1,1)) as inputSum FROM `payroll_tmp_calculation` calc INNER JOIN `payroll_tmp_acclnk` lnk ON calc.`payroll_year_ID`=lnk.`payroll_year_ID` AND calc.`payroll_account_ID`=lnk.`payroll_account_ID` WHERE calc.`core_user_ID`=currentUser AND calc.`process_order`=x AND lnk.`child_account_field`=3 GROUP BY calc.`payroll_year_ID`,calc.`payroll_employee_ID`,lnk.`payroll_child_account_ID`,lnk.`child_account_field`) AS src ON dest.`payroll_year_ID`=src.`payroll_year_ID` AND dest.`payroll_account_ID`=src.`payroll_child_account_ID` AND dest.`payroll_employee_ID`=src.`payroll_employee_ID` SET dest.`quantity`=dest.`quantity`+src.inputSum WHERE src.child_account_field=3;
		UPDATE `payroll_tmp_calculation` dest INNER JOIN (SELECT calc.`payroll_year_ID`,calc.`payroll_employee_ID`,lnk.`payroll_child_account_ID`,lnk.`child_account_field`,SUM(IF(lnk.`field_assignment`=5 AND (lnk.`fwd_neg_values`=1 OR (lnk.`fwd_neg_values`=0 AND calc.`amount`>=0.0)),amount,IF(lnk.`field_assignment`=4 AND (lnk.`fwd_neg_values`=1 OR (lnk.`fwd_neg_values`=0 AND calc.`rate`>=0.0)),rate,IF(lnk.`field_assignment`=3 AND (lnk.`fwd_neg_values`=1 OR (lnk.`fwd_neg_values`=0 AND calc.`quantity`>=0.0)),quantity,0)))*IF(lnk.`invert_value`=1,-1,1)) as inputSum FROM `payroll_tmp_calculation` calc INNER JOIN `payroll_tmp_acclnk` lnk ON calc.`payroll_year_ID`=lnk.`payroll_year_ID` AND calc.`payroll_account_ID`=lnk.`payroll_account_ID` WHERE calc.`core_user_ID`=currentUser AND calc.`process_order`=x AND lnk.`child_account_field`=4 GROUP BY calc.`payroll_year_ID`,calc.`payroll_employee_ID`,lnk.`payroll_child_account_ID`,lnk.`child_account_field`) AS src ON dest.`payroll_year_ID`=src.`payroll_year_ID` AND dest.`payroll_account_ID`=src.`payroll_child_account_ID` AND dest.`payroll_employee_ID`=src.`payroll_employee_ID` SET dest.`rate`=dest.`rate`+src.inputSum WHERE src.child_account_field=4;
		UPDATE `payroll_tmp_calculation` dest INNER JOIN (SELECT calc.`payroll_year_ID`,calc.`payroll_employee_ID`,lnk.`payroll_child_account_ID`,lnk.`child_account_field`,SUM(IF(lnk.`field_assignment`=5 AND (lnk.`fwd_neg_values`=1 OR (lnk.`fwd_neg_values`=0 AND calc.`amount`>=0.0)),amount,IF(lnk.`field_assignment`=4 AND (lnk.`fwd_neg_values`=1 OR (lnk.`fwd_neg_values`=0 AND calc.`rate`>=0.0)),rate,IF(lnk.`field_assignment`=3 AND (lnk.`fwd_neg_values`=1 OR (lnk.`fwd_neg_values`=0 AND calc.`quantity`>=0.0)),quantity,0)))*IF(lnk.`invert_value`=1,-1,1)) as inputSum FROM `payroll_tmp_calculation` calc INNER JOIN `payroll_tmp_acclnk` lnk ON calc.`payroll_year_ID`=lnk.`payroll_year_ID` AND calc.`payroll_account_ID`=lnk.`payroll_account_ID` WHERE calc.`core_user_ID`=currentUser AND calc.`process_order`=x AND lnk.`child_account_field`=5 GROUP BY calc.`payroll_year_ID`,calc.`payroll_employee_ID`,lnk.`payroll_child_account_ID`,lnk.`child_account_field`) AS src ON dest.`payroll_year_ID`=src.`payroll_year_ID` AND dest.`payroll_account_ID`=src.`payroll_child_account_ID` AND dest.`payroll_employee_ID`=src.`payroll_employee_ID` SET dest.`amount`=dest.`amount`+src.inputSum WHERE src.child_account_field=5;
		SET x = x + 1;
	END WHILE;
	DELETE FROM payroll_tmp_acclnk;
	Call payroll_prc_paymentsplit(currentUser,currentPeriodID,currentMajorPeriod,currentMinorPeriod);

	UPDATE payroll_period_employee prdempl 
	INNER JOIN `payroll_tmp_change_mng` emplList ON prdempl.`payroll_employee_ID`=emplList.`numID` AND emplList.`core_user_ID`=currentUser 
	SET prdempl.`calc_datetime`=NOW(), prdempl.`core_user_ID_calc`=currentUser 
	WHERE prdempl.`payroll_period_ID`=currentPeriodID;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `payroll_prc_employee` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`remoteadmin`@`213.188.228.86`*/ /*!50003 PROCEDURE `payroll_prc_employee`()
BEGIN
	START TRANSACTION;

	UPDATE payroll_employee SET RetirementDate=IF(Sex='M',DATE_ADD(DateOfBirth, INTERVAL 65 YEAR),DATE_ADD(DateOfBirth, INTERVAL 64 YEAR)), Age=(YEAR(CURDATE())-YEAR(DateOfBirth))-(RIGHT(CURDATE(),5)<RIGHT(DateOfBirth,5)), YearsOfService=YEAR(CURDATE())-YEAR(SeniorityJoining), MonthsOfService=MONTH(CURDATE())-MONTH(SeniorityJoining);
	UPDATE payroll_employee SET YearsOfService=YearsOfService-1, MonthsOfService=MonthsOfService+12 WHERE MonthsOfService<0;
	UPDATE payroll_employee SET YearsOfService=0, MonthsOfService=0 WHERE YearsOfService<0;

	COMMIT;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `payroll_prc_empl_acc` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`remoteadmin`@`213.188.228.86`*/ /*!50003 PROCEDURE `payroll_prc_empl_acc`(userID INT, internalTransaction TINYINT, wageCodeChange TINYINT, wageBaseChange TINYINT, insuranceChange TINYINT, modifierChange TINYINT, workdaysChange TINYINT, pensiondaysChange TINYINT)
BEGIN
	DECLARE payrollPeriodID INT;
	DECLARE payrollEmployeeID INT;
	DECLARE payrollYearID SMALLINT;
	DECLARE majorPeriod TINYINT;
	DECLARE minorPeriod TINYINT;
	DECLARE pensionMonthBasis TINYINT;
	DECLARE LoaNrAhvBasis VARCHAR(5);
	DECLARE LoaNrAhvLohn VARCHAR(5);
	DECLARE LoaNrAhvBeitrag VARCHAR(5);
	DECLARE pensionerRelief DECIMAL(8,2);
	DECLARE strFieldName VARCHAR(30);
    DECLARE done INT DEFAULT 0;
    DECLARE cursorFieldName CURSOR FOR
		SELECT DISTINCT `FieldName` FROM `payroll_tmp_calc_modifier` WHERE `FieldName`!='';
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	SELECT `id`,`payroll_year_ID`,`major_period`,`minor_period` INTO payrollPeriodID,payrollYearID,majorPeriod,minorPeriod FROM `payroll_period` WHERE `locked`=0 AND `finalized`=0;

	IF internalTransaction = 1 THEN
		START TRANSACTION;
	END IF;

	IF wageCodeChange = 1 THEN
		DELETE emplacc FROM payroll_employee_account emplacc INNER JOIN payroll_tmp_change_mng ids ON emplacc.payroll_employee_ID=ids.numID AND ids.core_user_ID=userID INNER JOIN payroll_account_mapping map ON map.ProcessingMethod=2 AND emplacc.payroll_account_ID=map.payroll_account_ID;
		INSERT INTO `payroll_employee_account`(`payroll_employee_ID`,`payroll_account_ID`,`PayrollDataType`,`account_text`,`quantity`,`rate`,`amount`,`max_limit`,`min_limit`,`deduction`,`CostCenter`,`DateFrom`,`DateTo`) SELECT empl.id,map.payroll_account_ID,2,'',0,IF(empl.WageCode=1,empl.BaseWage,0),IF(empl.WageCode>1,empl.BaseWage,0),0,0,0,'','0000-00-00','0000-00-00' FROM payroll_employee empl INNER JOIN payroll_tmp_change_mng ids ON empl.id=ids.numID AND ids.core_user_ID=userID INNER JOIN payroll_account_mapping map ON map.ProcessingMethod=2 AND map.AccountType=empl.WageCode;
		Set wageBaseChange = 0;
	END IF;

	IF wageBaseChange = 1 THEN
		UPDATE payroll_employee_account emplacc INNER JOIN payroll_tmp_change_mng ids ON emplacc.payroll_employee_ID=ids.numID AND ids.core_user_ID=userID INNER JOIN payroll_employee empl ON emplacc.payroll_employee_ID=empl.id INNER JOIN payroll_account_mapping map ON map.ProcessingMethod=2 AND map.AccountType=empl.WageCode AND map.payroll_account_ID=emplacc.payroll_account_ID SET emplacc.rate=IF(empl.WageCode=1,empl.BaseWage,0), emplacc.amount=IF(empl.WageCode>1,empl.BaseWage,0);
	END IF;

	IF insuranceChange = 1 THEN
		-- alle Versicherungseinträge löschen
		DELETE empacc FROM `payroll_employee_account` empacc INNER JOIN `payroll_tmp_change_mng` ids ON empacc.`payroll_employee_ID`=ids.`numID` AND ids.`core_user_ID`=userID WHERE empacc.`PayrollDataType`=8;

		-- AHV und ALV LOA einfügen
		INSERT INTO `payroll_employee_account`(`payroll_employee_ID`,`payroll_account_ID`,`PayrollDataType`,`account_text`,`quantity`,`rate`,`amount`,`max_limit`,`min_limit`,`deduction`,`CostCenter`,`DateFrom`,`DateTo`) 
		SELECT empl.id,insRate.payroll_account_ID,8,'',0,insRate.rate,0,0,0,0,'','0000-00-00','0000-00-00' FROM payroll_employee empl 
		INNER JOIN payroll_tmp_change_mng ids ON empl.id=ids.numID AND ids.core_user_ID=userID 
		INNER JOIN payroll_insurance_code insCode ON (empl.CodeAHV=insCode.InsuranceCode AND insCode.payroll_insurance_type_ID=1) OR (empl.CodeALV=insCode.InsuranceCode AND insCode.payroll_insurance_type_ID=2) 
		INNER JOIN payroll_insurance_rate insRate ON insCode.id=insRate.payroll_insurance_code_ID AND empl.Sex=insRate.Sex AND empl.Age>=insRate.AgeFrom AND empl.Age<insRate.AgeTo;

		-- UVG LOA einfügen
		INSERT INTO `payroll_employee_account`(`payroll_employee_ID`,`payroll_account_ID`,`PayrollDataType`,`account_text`,`quantity`,`rate`,`amount`,`max_limit`,`min_limit`,`deduction`,`CostCenter`,`DateFrom`,`DateTo`) 
		SELECT empl.id,insRate.payroll_account_ID,8,'',0,insRate.rate,0,0,0,0,'','0000-00-00','0000-00-00' FROM payroll_employee empl 
		INNER JOIN payroll_tmp_change_mng ids ON empl.id=ids.numID AND ids.core_user_ID=userID 
		INNER JOIN payroll_insurance_code insCode ON empl.CodeUVG=insCode.InsuranceCode AND insCode.payroll_insurance_type_ID=4 
		INNER JOIN payroll_insurance_rate insRate ON insCode.id=insRate.payroll_insurance_code_ID;

		-- KTG und UVGZ LOA einfügen
		INSERT INTO `payroll_employee_account`(`payroll_employee_ID`,`payroll_account_ID`,`PayrollDataType`,`account_text`,`quantity`,`rate`,`amount`,`max_limit`,`min_limit`,`deduction`,`CostCenter`,`DateFrom`,`DateTo`) 
		SELECT empl.id,insRate.payroll_account_ID,8,'',0,insRate.rate,0,0,0,0,'','0000-00-00','0000-00-00' FROM payroll_employee empl 
		INNER JOIN payroll_tmp_change_mng ids ON empl.id=ids.numID AND ids.core_user_ID=userID 
		INNER JOIN payroll_insurance_code insCode ON (empl.CodeKTG=insCode.InsuranceCode AND insCode.payroll_insurance_type_ID=6) OR (empl.CodeUVGZ1=insCode.InsuranceCode AND insCode.payroll_insurance_type_ID=5) OR (empl.CodeUVGZ2=insCode.InsuranceCode AND insCode.payroll_insurance_type_ID=5) 
		INNER JOIN payroll_insurance_rate insRate ON insCode.id=insRate.payroll_insurance_code_ID AND empl.Sex=insRate.Sex;

		-- Effektiv-LOA (Hilfs-LOA) einfügen
		INSERT INTO `payroll_employee_account`(`payroll_employee_ID`,`payroll_account_ID`,`PayrollDataType`,`account_text`,`quantity`,`rate`,`amount`,`max_limit`,`min_limit`,`deduction`,`CostCenter`,`DateFrom`,`DateTo`) 
		SELECT emplacc.payroll_employee_ID,acc.limits_aux_account_ID,8,'',0,0,0,0,0,0,'','0000-00-00','0000-00-00' FROM payroll_employee_account emplacc 
		INNER JOIN payroll_tmp_change_mng ids ON emplacc.payroll_employee_ID=ids.numID AND ids.core_user_ID=userID 
		INNER JOIN (SELECT payroll_account_ID FROM payroll_insurance_rate GROUP BY payroll_account_ID) insacc ON emplacc.payroll_account_ID=insacc.payroll_account_ID
		INNER JOIN payroll_account acc ON acc.id=insacc.payroll_account_ID AND acc.payroll_year_ID=payrollYearID AND acc.having_limits=1 AND acc.limits_aux_account_ID!='0'
		WHERE emplacc.PayrollDataType=8 
		GROUP BY emplacc.payroll_employee_ID,acc.limits_aux_account_ID;
	END IF;

	IF modifierChange = 1 THEN
		DELETE FROM `payroll_tmp_calc_modifier` WHERE `core_user_ID`=userID;
		INSERT INTO `payroll_tmp_calc_modifier`(`core_user_ID`,`payroll_employee_ID`,`payroll_account_ID`,`ModifierType`,`FieldName`,`TargetField`,`TargetValue`,`max_limit`,`min_limit`,`deduction`,`major_period`,`minor_period`,`major_period_bonus`) SELECT userID,selector.`payroll_employee_ID`,modif1.`payroll_account_ID`,modif1.`ModifierType`,modif1.`FieldName`,modif1.`TargetField`,modif1.`TargetValue`,modif1.`max_limit`,modif1.`min_limit`,modif1.`deduction`,modif1.`major_period`,modif1.`minor_period`,modif1.`major_period_bonus` FROM payroll_calculation_modifier modif1 INNER JOIN (SELECT fltche.`payroll_employee_ID`,modif.`payroll_account_ID`,MIN(modif.`processing_order`) as minOrder, modif.`ModifierType` FROM payroll_calculation_modifier modif INNER JOIN payroll_empl_filter_cache fltche ON modif.`payroll_empl_filter_ID`=fltche.`payroll_empl_filter_ID` INNER JOIN payroll_tmp_change_mng ids ON fltche.`payroll_employee_ID`=ids.`numID` AND ids.`core_user_ID`=userID GROUP BY fltche.`payroll_employee_ID`, modif.`payroll_account_ID`, modif.`ModifierType`) selector ON modif1.`ModifierType`=selector.`ModifierType` AND modif1.`payroll_account_ID`=selector.`payroll_account_ID` AND modif1.`processing_order`=selector.`minOrder`;
		OPEN cursorFieldName;
		REPEAT
			FETCH cursorFieldName INTO strFieldName;
			IF done = 0 THEN
				SET @vSQL := CONCAT('UPDATE `payroll_tmp_calc_modifier` modif INNER JOIN `payroll_employee` empl ON modif.`payroll_employee_ID`=empl.`id` SET modif.`TargetValue`=empl.`',strFieldName,'` WHERE modif.`core_user_ID`=',userID,' AND modif.`FieldName`=\'',strFieldName,'\'');
				PREPARE STMT FROM @vSQL;
				EXECUTE STMT;
			END IF;
		UNTIL done END REPEAT;
		CLOSE cursorFieldName;
		DELETE emplacc FROM `payroll_employee_account` emplacc INNER JOIN `payroll_tmp_change_mng` ids ON emplacc.`payroll_employee_ID`=ids.`numID` AND ids.`core_user_ID`=userID WHERE emplacc.`PayrollDataType`=5 OR emplacc.`PayrollDataType`=6;
		INSERT INTO `payroll_employee_account`(`payroll_employee_ID`,`payroll_account_ID`,`PayrollDataType`,`account_text`,`quantity`,`rate`,`amount`,`TargetField`,`max_limit`,`min_limit`,`deduction`,`CostCenter`,`DateFrom`,`DateTo`) SELECT `payroll_employee_ID`,`payroll_account_ID`,IF(`ModifierType`=1,5,6),'',IF(`TargetField`=3,`TargetValue`,0),IF(`TargetField`=4,`TargetValue`,0),IF(`TargetField`=5,`TargetValue`,0),`TargetField`,`max_limit`,`min_limit`,`deduction`,'','0000-00-00','0000-00-00' FROM `payroll_tmp_calc_modifier` WHERE `core_user_ID`=userID;
		DELETE FROM `payroll_tmp_calc_modifier` WHERE `core_user_ID`=userID;
	END IF;

	IF workdaysChange = 1 THEN
		DELETE emplacc FROM `payroll_employee_account` emplacc INNER JOIN `payroll_tmp_change_mng` ids ON emplacc.`payroll_employee_ID`=ids.`numID` AND ids.`core_user_ID`=userID WHERE emplacc.`PayrollDataType`=9;
		-- bisherige beträge summieren (alle ausser aktuelle Periode)
		INSERT INTO `payroll_employee_account`(`payroll_employee_ID`,`payroll_account_ID`,`PayrollDataType`,`account_text`,`quantity`,`rate`,`amount`,`max_limit`,`min_limit`,`deduction`,`CostCenter`,`DateFrom`,`DateTo`,`allowable_workdays`,`allowable_workdays_sum`,`amount_balance`) 
		SELECT calcEntry.`payroll_employee_ID`,calcEntry.`payroll_account_ID`,9,'',0,0,0,0,0,0,'','0000-00-00','0000-00-00',0,0,SUM(IF(acc.`input_assignment`=5,calcEntry.`amount`,IF(acc.`input_assignment`=4,calcEntry.`rate`,calcEntry.`quantity`))) 
		FROM `payroll_calculation_entry` calcEntry
		INNER JOIN `payroll_tmp_change_mng` emplist ON calcEntry.`payroll_employee_ID`=emplist.`numID` AND emplist.`core_user_ID`=userID 
		INNER JOIN `payroll_period` prd ON calcEntry.`payroll_period_ID`=prd.`id` AND prd.`payroll_year_ID`=payrollYearID AND prd.`locked`=1 AND prd.`finalized`=1 
		INNER JOIN `payroll_account` acc ON prd.`payroll_year_ID`=acc.`payroll_year_ID` AND calcEntry.`payroll_account_ID`=acc.`id` AND acc.`having_limits`=1 AND acc.`limits_calc_mode`!=2 
		GROUP BY calcEntry.`payroll_employee_ID`, calcEntry.`payroll_account_ID`;
		
		INSERT INTO `payroll_employee_account`(`payroll_employee_ID`,`payroll_account_ID`,`PayrollDataType`,`account_text`,`quantity`,`rate`,`amount`,`max_limit`,`min_limit`,`deduction`,`CostCenter`,`DateFrom`,`DateTo`,`allowable_workdays`,`allowable_workdays_sum`,`amount_balance`) 
		SELECT calcEntry.`payroll_employee_ID`,calcEntry.`payroll_account_ID`,9,'',0,0,0,0,0,0,'','0000-00-00','0000-00-00',0,0,SUM(IF(aux.`field_assignment`=5,calcEntry.`amount`,IF(aux.`field_assignment`=4,calcEntry.`rate`,calcEntry.`quantity`))) 
		FROM `payroll_calculation_entry` calcEntry
		INNER JOIN `payroll_tmp_change_mng` emplist ON calcEntry.`payroll_employee_ID`=emplist.`numID` AND emplist.`core_user_ID`=userID 
		INNER JOIN `payroll_period` prd ON calcEntry.`payroll_period_ID`=prd.`id` AND prd.`payroll_year_ID`=payrollYearID AND prd.`locked`=1 AND prd.`finalized`=1 
		INNER JOIN (SELECT acc.`limits_aux_account_ID`,lnk.`field_assignment` FROM `payroll_account` acc INNER JOIN `payroll_account_linker` lnk ON lnk.`payroll_year_ID`=acc.`payroll_year_ID` AND lnk.`payroll_account_ID`=acc.`limits_aux_account_ID` AND lnk.`payroll_child_account_ID`=acc.`id` WHERE acc.`payroll_year_ID`=payrollYearID AND acc.`having_limits`=1 AND acc.`limits_calc_mode`!=2 GROUP BY acc.`limits_aux_account_ID`) aux ON calcEntry.`payroll_account_ID`=aux.`limits_aux_account_ID`
		GROUP BY calcEntry.`payroll_employee_ID`, calcEntry.`payroll_account_ID`;

		IF majorPeriod < 13 THEN
			-- alle bisherigen arbeitstage der HZ summieren. ausgenommen aktueller HZ!
			INSERT INTO `payroll_employee_account`(`payroll_employee_ID`,`payroll_account_ID`,`PayrollDataType`,`account_text`,`quantity`,`rate`,`amount`,`max_limit`,`min_limit`,`deduction`,`CostCenter`,`DateFrom`,`DateTo`,`allowable_workdays`,`allowable_workdays_sum`,`amount_balance`) 
			SELECT calc.`payroll_employee_ID`,calc.`payroll_account_ID`,9,'',0,0,0,0,0,0,'','0000-00-00','0000-00-00',0,SUM(calc.`allowable_workdays`),0 
			FROM payroll_calculation_entry calc
			INNER JOIN payroll_tmp_change_mng ids ON calc.payroll_employee_ID=ids.numID AND ids.core_user_ID=userID 
			INNER JOIN payroll_period prd ON prd.id=calc.payroll_period_ID AND prd.major_period!=majorPeriod AND prd.payroll_year_ID=payrollYearID AND prd.major_period<13 AND prd.minor_period=0
			WHERE calc.allowable_workdays>0
			GROUP BY calc.payroll_employee_ID,calc.payroll_account_ID;

			-- wenn die aktuelle Periode ein Hauptzahltag ist oder zu einem gehört soll die Anzahl Arbeitstage on-the-fly berechnet werden
			Call payroll_prc_workdays(userID, payrollYearID, majorPeriod);
		ELSE
			-- alle bisherigen arbeitstage der HZ summieren
			INSERT INTO `payroll_employee_account`(`payroll_employee_ID`,`payroll_account_ID`,`PayrollDataType`,`account_text`,`quantity`,`rate`,`amount`,`max_limit`,`min_limit`,`deduction`,`CostCenter`,`DateFrom`,`DateTo`,`allowable_workdays`,`allowable_workdays_sum`,`amount_balance`) 
			SELECT calc.`payroll_employee_ID`,calc.`payroll_account_ID`,9,'',0,0,0,0,0,0,'','0000-00-00','0000-00-00',0,SUM(calc.`allowable_workdays`),0 
			FROM `payroll_calculation_entry` calc
			INNER JOIN `payroll_tmp_change_mng` ids ON calc.`payroll_employee_ID`=ids.`numID` AND ids.`core_user_ID`=userID 
			INNER JOIN `payroll_period` prd ON prd.`id`=calc.`payroll_period_ID` AND prd.`payroll_year_ID`=payrollYearID AND prd.`major_period`<13 AND prd.`minor_period`=0
			WHERE calc.`allowable_workdays`>0
			GROUP BY calc.`payroll_employee_ID`,calc.`payroll_account_ID`;
		END IF;
	END IF;

	IF pensiondaysChange = 1 THEN
		-- Zuerst prüfen ob es überhaupt Renter gibt. Falls nicht, wird Berechnung übersprungen.
		IF EXISTS(SELECT empl.`id` FROM `payroll_employee` empl INNER JOIN `payroll_tmp_change_mng` ids ON empl.`id`=ids.`numID` AND ids.`core_user_ID`=userID WHERE empl.`CodeAHV`='4' OR empl.`CodeAHV`='6') THEN
			SET pensionMonthBasis = majorPeriod;
			IF pensionMonthBasis > 12 THEN
				SET pensionMonthBasis = 12;
			END IF;
			-- Alle berechnungsrelevanten Lohnarten ermitteln
			SELECT `payroll_account_ID` INTO LoaNrAhvBasis FROM `payroll_account_mapping` WHERE `ProcessingMethod`=1 AND `AccountType`=3;
			SELECT `payroll_account_ID` INTO LoaNrAhvLohn FROM `payroll_account_mapping` WHERE `ProcessingMethod`=1 AND `AccountType`=4;
			SELECT `payroll_account_ID` INTO LoaNrAhvBeitrag FROM `payroll_account_mapping` WHERE `ProcessingMethod`=3 AND `AccountType`=1;
			-- Jahres-Freibetrag ermitteln
			SELECT CAST(`value` AS DECIMAL(8,2))/12 INTO pensionerRelief FROM `core_registry` WHERE `path`='GLOBAL/SETTINGS/CORE/payroll' AND `name`='pensioner_relief';
			-- AHV-Lohn und -Basis summieren und bei entsprechender AHV-Beitrags-LOA speichern
			-- LOA in payroll_employee_account kennzeichnen, so dass Berechnungsalgorithmus weiss, dass hier eine Freibetragsberechnung durchgeführt werden muss
			UPDATE `payroll_employee_account` dest
			INNER JOIN (SELECT calcEntry.`payroll_employee_ID`,calcEntry.`payroll_account_ID`,SUM(IF(calcEntry.`payroll_account_ID`=LoaNrAhvBasis AND (YEAR(emp.`RetirementDate`)<prd.`payroll_year_ID` OR (YEAR(emp.`RetirementDate`)=prd.`payroll_year_ID` AND prd.`major_period`>MONTH(emp.`RetirementDate`))),calcEntry.`amount`,0)) as sumBasis,SUM(IF(calcEntry.`payroll_account_ID`=LoaNrAhvLohn AND (YEAR(emp.`RetirementDate`)<prd.`payroll_year_ID` OR (YEAR(emp.`RetirementDate`)=prd.`payroll_year_ID` AND prd.`major_period`>MONTH(emp.`RetirementDate`))),calcEntry.`amount`,0)) as sumLohn FROM `payroll_calculation_entry` calcEntry 
						INNER JOIN `payroll_tmp_change_mng` ids ON calcEntry.`payroll_employee_ID`=ids.`numID` AND ids.`core_user_ID`=userID 
						INNER JOIN `payroll_period` prd on calcEntry.`payroll_period_ID`=prd.`id` AND prd.`locked`=1 AND prd.`finalized`=1 AND prd.`payroll_year_ID`=payrollYearID 
						INNER JOIN `payroll_employee` emp ON emp.`id`=calcEntry.`payroll_employee_ID` AND (YEAR(emp.`RetirementDate`)<prd.`payroll_year_ID` OR (YEAR(emp.`RetirementDate`)=prd.`payroll_year_ID` AND MONTH(emp.`RetirementDate`)<pensionMonthBasis)) 
						WHERE calcEntry.`payroll_account_ID`=LoaNrAhvBasis OR calcEntry.`payroll_account_ID`=LoaNrAhvLohn 
						GROUP BY calcEntry.`payroll_employee_ID`,calcEntry.`payroll_account_ID`) src ON src.`payroll_employee_ID`=dest.`payroll_employee_ID` 
			SET dest.`max_limit`=src.sumBasis, dest.`min_limit`=src.sumLohn, dest.`having_pensioner_calc`=1 
			WHERE dest.`PayrollDataType`=8 AND dest.`payroll_account_ID`=LoaNrAhvBeitrag;
			-- Maximal möglicher Freibetrag pro Mitarbeiter ermitteln und in Feld deduction speichern
			-- $moeglicherFreibetrag = $nAnzahlRentenMonate * $nFreibetrag;
			UPDATE `payroll_employee_account` emplacc 
			INNER JOIN `payroll_tmp_change_mng` ids ON emplacc.`payroll_employee_ID`=ids.`numID` AND ids.`core_user_ID`=userID 
			INNER JOIN `payroll_employee` emp ON emp.`id`=emplacc.`payroll_employee_ID` AND (YEAR(emp.`RetirementDate`)<payrollYearID OR (YEAR(emp.`RetirementDate`)=payrollYearID AND MONTH(emp.`RetirementDate`)<pensionMonthBasis)) 
			SET emplacc.`deduction`= IF(YEAR(emp.`RetirementDate`)<payrollYearID,pensionMonthBasis,pensionMonthBasis-MONTH(emp.`RetirementDate`))*pensionerRelief, emplacc.`having_pensioner_calc`=1 
			WHERE emplacc.`payroll_account_ID`=LoaNrAhvBeitrag;
		END IF;
	END IF;


	IF internalTransaction = 1 THEN
		COMMIT;
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `payroll_prc_filter_cache` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`remoteadmin`@`213.188.228.86`*/ /*!50003 PROCEDURE `payroll_prc_filter_cache`(internalTransaction TINYINT)
BEGIN
    DECLARE FilterID INT;
    DECLARE FilterCrit TEXT;
    DECLARE done INT DEFAULT 0;
    DECLARE cursorFilter CURSOR FOR
		SELECT `id`,`FilterCriteria` FROM `payroll_empl_filter` WHERE `dirtyData`=1;
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	IF internalTransaction = 1 THEN
		START TRANSACTION;
	END IF;

	OPEN cursorFilter;
	REPEAT
		FETCH cursorFilter INTO FilterID,FilterCrit;
			IF done = 0 THEN
				DELETE FROM `payroll_empl_filter_cache` WHERE `payroll_empl_filter_ID`=FilterID;

				SET @vSQL := CONCAT('INSERT INTO `payroll_empl_filter_cache`(`payroll_empl_filter_ID`,`payroll_employee_ID`) SELECT ',FilterID,',`id` FROM `payroll_employee` WHERE ',FilterCrit);
				PREPARE STMT FROM @vSQL;
				EXECUTE STMT;

				UPDATE `payroll_empl_filter` SET `dirtyData`=0 WHERE `id`=FilterID;
			END IF;
	UNTIL done END REPEAT;
	CLOSE cursorFilter;

	IF internalTransaction = 1 THEN
		COMMIT;
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `payroll_prc_group_accounts` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`remoteadmin`@`213.188.228.86`*/ /*!50003 PROCEDURE `payroll_prc_group_accounts`()
BEGIN
	DECLARE currentYear SMALLINT;
	DECLARE counterMax SMALLINT;
	DECLARE x SMALLINT;
	DECLARE affectedRows SMALLINT;
	DECLARE strFormula VARCHAR(100);
	DECLARE idFormula INT;
    DECLARE done INT DEFAULT 0;
    DECLARE cursorFormula CURSOR FOR
		SELECT DISTINCT `payroll_formula`.`id`,`payroll_formula`.`formula` FROM `payroll_formula` INNER JOIN `payroll_account` ON `payroll_formula`.`id`=`payroll_account`.`payroll_formula_ID` AND `payroll_account`.`mandatory`=1 AND `payroll_account`.`processing_order`=1 AND `payroll_account`.`var_fields`>0 AND `payroll_account`.`having_calculation`=1;
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	SELECT MAX(id) INTO currentYear FROM payroll_year;
	SELECT DISTINCT COUNT(payroll_child_account_ID) INTO counterMax FROM payroll_account_linker WHERE payroll_year_ID=currentYear;

	START TRANSACTION;

	UPDATE payroll_account SET processing_order=1, mandatory=1 WHERE payroll_year_ID=currentYear;

	SET x = 1;
	WHILE x <= counterMax DO
		UPDATE payroll_account ap INNER JOIN payroll_account_linker lnk ON ap.id=lnk.payroll_account_ID AND ap.payroll_year_ID=lnk.payroll_year_ID INNER JOIN payroll_account ac ON ac.id=lnk.payroll_child_account_ID AND ac.payroll_year_ID=lnk.payroll_year_ID SET ac.processing_order=x+1 WHERE ap.payroll_year_ID=currentYear AND ap.processing_order=x;

		SELECT ROW_COUNT() INTO affectedRows;
		IF affectedRows=0 THEN
			SET x = counterMax+1;
		END IF;

		SET x = x + 1;
	END WHILE;

	UPDATE payroll_account acc INNER JOIN payroll_account_mapping mp ON acc.id=mp.payroll_account_ID AND acc.payroll_year_ID=currentYear AND (mp.ProcessingMethod=2 OR mp.ProcessingMethod=4) SET acc.mandatory=0;
	UPDATE payroll_account acc INNER JOIN (SELECT payroll_account_ID FROM payroll_insurance_rate GROUP BY payroll_account_ID) rt ON acc.id=rt.payroll_account_ID AND acc.payroll_year_ID=currentYear SET acc.mandatory=0;
	UPDATE payroll_account acc INNER JOIN (SELECT acc2.limits_aux_account_ID FROM payroll_account acc2 INNER JOIN payroll_insurance_rate rt2 ON acc2.id=rt2.payroll_account_ID AND acc2.payroll_year_ID=currentYear WHERE acc2.having_limits=1 GROUP BY acc2.limits_aux_account_ID) rt ON acc.id=rt.limits_aux_account_ID AND acc.payroll_year_ID=currentYear SET acc.mandatory=0;
	UPDATE payroll_account SET mandatory=0 WHERE mandatory=1 AND processing_order=1 AND var_fields>0 AND factor=0 AND quantity=0 AND rate=0 AND surcharge=0 AND factor=0;

	OPEN cursorFormula;
	REPEAT
		FETCH cursorFormula INTO idFormula,strFormula;
		IF done = 0 THEN
			SET @vSQL := CONCAT('UPDATE `payroll_account` SET `mandatory`=0 WHERE `mandatory`=1 AND `var_fields`>0 AND `having_calculation`=1 AND `payroll_formula_ID`=',idFormula,' AND ',strFormula,'=0;');
			PREPARE STMT FROM @vSQL;
			EXECUTE STMT;
		END IF;
	UNTIL done END REPEAT;
	CLOSE cursorFormula;
	SET done = 0;

	UPDATE payroll_account SET mandatory=1 WHERE mandatory=0 AND processing_order=1 AND carry_over!=0;

	COMMIT;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `payroll_prc_mng_calc_accounts` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`remoteadmin`@`213.188.228.86`*/ /*!50003 PROCEDURE `payroll_prc_mng_calc_accounts`()
BEGIN
	DECLARE payrollPeriodID INT;
	DECLARE payrollEmployeeID INT;
	DECLARE payrollYearID SMALLINT;
    DECLARE done INT DEFAULT 0;
    DECLARE cursorEmployee CURSOR FOR
		SELECT `id` FROM `payroll_employee`;
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	SELECT id,payroll_year_ID INTO payrollPeriodID,payrollYearID FROM payroll_period WHERE locked=0 AND finalized=0;

	START TRANSACTION;

		DELETE FROM payroll_tmp_empl_accounts;
		INSERT INTO payroll_tmp_empl_accounts(payroll_employee_ID,payroll_account_ID,rate,mustAdd,mustRemove) SELECT empl.id,acc.payroll_account_ID,0.0,0,1 FROM payroll_employee empl INNER JOIN (SELECT payroll_account_ID FROM payroll_insurance_rate GROUP BY payroll_account_ID) acc ON acc.payroll_account_ID=acc.payroll_account_ID;
		REPLACE INTO payroll_tmp_empl_accounts(payroll_employee_ID,payroll_account_ID,rate,mustAdd,mustRemove) SELECT empl.id,acc.payroll_account_ID,0.0,1,0 FROM payroll_employee empl INNER JOIN (SELECT payroll_account_ID FROM payroll_account_mapping WHERE payroll_account_ID!='' AND ProcessingMethod=1 GROUP BY payroll_account_ID) acc ON acc.payroll_account_ID=acc.payroll_account_ID;
		REPLACE INTO payroll_tmp_empl_accounts(payroll_employee_ID,payroll_account_ID,rate,mustAdd,mustRemove,replaceRate) SELECT empl.id,acc.payroll_account_ID,empl.BaseWage,IF((empl.WageCode='2' AND acc.AccountType=2) OR (empl.WageCode='3' AND acc.AccountType=1),1,0),IF((empl.WageCode='2' AND acc.AccountType=2) OR (empl.WageCode='3' AND acc.AccountType=1),0,1),1 FROM payroll_employee empl INNER JOIN (SELECT payroll_account_ID,AccountType FROM payroll_account_mapping WHERE payroll_account_ID!='' AND ProcessingMethod=2) acc ON acc.payroll_account_ID=acc.payroll_account_ID;
		REPLACE INTO payroll_tmp_empl_accounts(payroll_employee_ID,payroll_account_ID,rate,mustAdd,mustRemove,replaceRate) SELECT empl.id, insRate.payroll_account_ID,insRate.rate,1,0,1 FROM payroll_employee empl INNER JOIN payroll_insurance_code insCode ON (empl.CodeAHV=insCode.InsuranceCode AND insCode.payroll_insurance_type_ID=1) OR (empl.CodeALV=insCode.InsuranceCode AND insCode.payroll_insurance_type_ID=2) INNER JOIN payroll_insurance_rate insRate ON insCode.id=insRate.payroll_insurance_code_ID AND empl.Sex=insRate.Sex AND empl.Age>=insRate.AgeFrom AND empl.Age<insRate.AgeTo;
		REPLACE INTO payroll_tmp_empl_accounts(payroll_employee_ID,payroll_account_ID,rate,mustAdd,mustRemove,replaceRate) SELECT empl.id, insRate.payroll_account_ID,insRate.rate,1,0,1 FROM payroll_employee empl INNER JOIN payroll_insurance_code insCode ON empl.CodeUVG=insCode.InsuranceCode AND insCode.payroll_insurance_type_ID=4 INNER JOIN payroll_insurance_rate insRate ON insCode.id=insRate.payroll_insurance_code_ID;
		REPLACE INTO payroll_tmp_empl_accounts(payroll_employee_ID,payroll_account_ID,rate,mustAdd,mustRemove,replaceRate) SELECT empl.id, insRate.payroll_account_ID,insRate.rate,1,0,1 FROM payroll_employee empl INNER JOIN payroll_insurance_code insCode ON (empl.CodeKTG=insCode.InsuranceCode AND insCode.payroll_insurance_type_ID=6) OR (empl.CodeUVGZ1=insCode.InsuranceCode AND insCode.payroll_insurance_type_ID=5) OR (empl.CodeUVGZ2=insCode.InsuranceCode AND insCode.payroll_insurance_type_ID=5) INNER JOIN payroll_insurance_rate insRate ON insCode.id=insRate.payroll_insurance_code_ID AND empl.Sex=insRate.Sex;
		REPLACE INTO payroll_tmp_empl_accounts(payroll_employee_ID,payroll_account_ID,rate,mustAdd,mustRemove,replaceRate,parentOfAuxAccount) SELECT emplAcc.payroll_employee_ID,mainAcc.limits_aux_account_ID,0.0,0,1,0,mainAcc.id FROM payroll_account mainAcc INNER JOIN payroll_tmp_empl_accounts emplAcc ON mainAcc.id=emplAcc.payroll_account_ID AND mainAcc.payroll_year_ID=payrollYearID WHERE mainAcc.having_limits=1;

		DELETE calc FROM payroll_calculation_entry calc INNER JOIN payroll_tmp_empl_accounts acc ON calc.payroll_employee_ID=acc.payroll_employee_ID AND calc.payroll_account_ID=acc.payroll_account_ID WHERE acc.mustRemove=1 AND calc.payroll_year_ID=payrollYearID AND calc.payroll_period_ID=payrollPeriodID;
		DELETE FROM payroll_tmp_empl_accounts WHERE mustRemove=1 AND parentOfAuxAccount='';
		UPDATE payroll_tmp_empl_accounts dest LEFT JOIN payroll_tmp_empl_accounts src ON dest.payroll_employee_ID=src.payroll_employee_ID AND dest.payroll_account_ID=src.parentOfAuxAccount AND src.parentOfAuxAccount!='' SET src.mustAdd=1,src.mustRemove=0 WHERE src.payroll_employee_ID IS NOT NULL;
		DELETE FROM payroll_tmp_empl_accounts WHERE mustRemove=1;
		UPDATE payroll_calculation_entry calc INNER JOIN payroll_tmp_empl_accounts acc ON calc.payroll_employee_ID=acc.payroll_employee_ID AND calc.payroll_account_ID=acc.payroll_account_ID SET acc.mustAdd=0 WHERE acc.mustAdd=1 AND calc.payroll_year_ID=payrollYearID AND calc.payroll_period_ID=payrollPeriodID;

		INSERT INTO `payroll_calculation_entry`(`payroll_year_ID`, `payroll_period_ID`, `payroll_employee_ID`, `payroll_account_ID`, `quantity`, `rate`, `amount`, `allowable_workdays`) SELECT payrollYearID, payrollPeriodID, `payroll_employee_ID`, `payroll_account_ID`, 0.0, `rate`, 0.0, 0 FROM payroll_tmp_empl_accounts WHERE mustAdd=1;

	COMMIT;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `payroll_prc_paymentsplit` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`remoteadmin`@`213.188.228.86`*/ /*!50003 PROCEDURE `payroll_prc_paymentsplit`(currentUser INT,currentPeriodID SMALLINT,currentMajorPeriod SMALLINT,currentMinorPeriod SMALLINT)
BEGIN
	DECLARE currentProcessingOrder TINYINT;
	DECLARE payrollAccountPayment VARCHAR(5);
    DECLARE done INT DEFAULT 0;
    DECLARE cursorProcessingOrder CURSOR FOR
		SELECT DISTINCT `processing_order` FROM `payroll_payment_current` pay INNER JOIN `payroll_tmp_change_mng` emplist ON pay.`payroll_employee_ID`=emplist.`numID` AND emplist.`core_user_id`=currentUser WHERE pay.`payroll_period_ID`=2 ORDER BY `processing_order`;
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	SELECT `payroll_account_ID` INTO payrollAccountPayment FROM `payroll_account_mapping` WHERE `ProcessingMethod`=1 AND `AccountType`=21;

	-- bereits vorhandene Records löschen (nur bei betroffenen Mitarbeitern)
	DELETE `payroll_payment_current` FROM `payroll_payment_current` 
	INNER JOIN `payroll_tmp_change_mng` ON `payroll_payment_current`.`payroll_employee_ID`=`payroll_tmp_change_mng`.`numID` AND `payroll_tmp_change_mng`.`core_user_id`=currentUser 
	WHERE `payroll_payment_current`.`payroll_period_ID`=currentPeriodID;

	-- Records der betroffenen Mitarbeiter neu anlegen
    IF currentMinorPeriod != 0 THEN
		-- falls Zwischenzahltag
		INSERT INTO `payroll_payment_current`(`payroll_period_ID`,`payroll_employee_ID`,`payroll_payment_split_ID`,`amount_initial`,`amount_available`,`amount`,`processing_order`,`split_mode`,`payroll_account_ID`,`having_rounding`,`round_param`)
		SELECT currentPeriodID,t2.`payroll_employee_ID`,t2.`id`,0,0,t2.`amount`,t2.`processing_order`,t2.`split_mode`,IF(t2.`split_mode`!=1,payrollAccountPayment,t2.`payroll_account_ID`),t2.`having_rounding`,t2.`round_param` FROM 
		(SELECT pps.payroll_employee_ID, pps.payroll_bank_destination_ID, MIN(pps.processing_order) as po FROM payroll_payment_split pps INNER JOIN payroll_tmp_change_mng emplist ON pps.payroll_employee_ID=emplist.numID AND emplist.core_user_id=currentUser WHERE pps.minor_period=1 AND (pps.minor_period_num=0 OR pps.minor_period_num=currentMinorPeriod) GROUP BY pps.payroll_employee_ID, pps.payroll_bank_destination_ID) t1 
		INNER JOIN payroll_payment_split t2 ON t1.payroll_employee_ID=t2.payroll_employee_ID AND t1.payroll_bank_destination_ID=t2.payroll_bank_destination_ID AND t1.po=t2.processing_order;
    ELSEIF currentMajorPeriod < 15 THEN
		-- falls Hauptzahltag
		INSERT INTO `payroll_payment_current`(`payroll_period_ID`,`payroll_employee_ID`,`payroll_payment_split_ID`,`amount_initial`,`amount_available`,`amount`,`processing_order`,`split_mode`,`payroll_account_ID`,`having_rounding`,`round_param`)
		SELECT currentPeriodID,t2.`payroll_employee_ID`,t2.`id`,0,0,t2.`amount`,t2.`processing_order`,t2.`split_mode`,IF(t2.`split_mode`!=1,payrollAccountPayment,t2.`payroll_account_ID`),t2.`having_rounding`,t2.`round_param` FROM 
		(SELECT pps.payroll_employee_ID, pps.payroll_bank_destination_ID, MIN(pps.processing_order) as po FROM payroll_payment_split pps INNER JOIN payroll_tmp_change_mng emplist ON pps.payroll_employee_ID=emplist.numID AND emplist.core_user_id=currentUser WHERE pps.major_period=1 AND (pps.major_period_num=0 OR pps.major_period_num=currentMajorPeriod) GROUP BY pps.payroll_employee_ID, pps.payroll_bank_destination_ID) t1 
		INNER JOIN payroll_payment_split t2 ON t1.payroll_employee_ID=t2.payroll_employee_ID AND t1.payroll_bank_destination_ID=t2.payroll_bank_destination_ID AND t1.po=t2.processing_order;
    ELSE
		-- falls Gratifikation
		INSERT INTO `payroll_payment_current`(`payroll_period_ID`,`payroll_employee_ID`,`payroll_payment_split_ID`,`amount_initial`,`amount_available`,`amount`,`processing_order`,`split_mode`,`payroll_account_ID`,`having_rounding`,`round_param`)
		SELECT currentPeriodID,t2.`payroll_employee_ID`,t2.`id`,0,0,t2.`amount`,t2.`processing_order`,t2.`split_mode`,IF(t2.`split_mode`!=1,payrollAccountPayment,t2.`payroll_account_ID`),t2.`having_rounding`,t2.`round_param` FROM 
		(SELECT pps.payroll_employee_ID, pps.payroll_bank_destination_ID, MIN(pps.processing_order) as po FROM payroll_payment_split pps INNER JOIN payroll_tmp_change_mng emplist ON pps.payroll_employee_ID=emplist.numID AND emplist.core_user_id=currentUser WHERE pps.major_period_bonus=1 AND (pps.major_period_bonus_num=0 OR pps.major_period_bonus_num=currentMajorPeriod) GROUP BY pps.payroll_employee_ID, pps.payroll_bank_destination_ID) t1 
		INNER JOIN payroll_payment_split t2 ON t1.payroll_employee_ID=t2.payroll_employee_ID AND t1.payroll_bank_destination_ID=t2.payroll_bank_destination_ID AND t1.po=t2.processing_order;
    END IF;

	-- amount_initial setzen
	UPDATE `payroll_payment_current` pay 
	INNER JOIN `payroll_calculation_current` calc ON pay.`payroll_period_ID`=calc.`payroll_period_ID` AND pay.`payroll_employee_ID`=calc.`payroll_employee_ID` AND pay.`payroll_account_ID`=calc.`payroll_account_ID` 
	INNER JOIN `payroll_tmp_change_mng` emplist ON pay.`payroll_employee_ID`=emplist.`numID` AND emplist.`core_user_id`=currentUser 
	SET pay.`amount_initial`=calc.`amount`, pay.`amount_available`=calc.`amount`;

	-- nach processing_order loopen
	OPEN cursorProcessingOrder;
	REPEAT
		FETCH cursorProcessingOrder INTO currentProcessingOrder;
		IF done = 0 THEN
			-- Berechnung gemäss Einstellungen
			UPDATE `payroll_payment_current` pay 
			INNER JOIN `payroll_tmp_change_mng` emplist ON pay.`payroll_employee_ID`=emplist.`numID` AND emplist.`core_user_id`=currentUser 
			SET pay.`amount`=IF(pay.`split_mode`=3,pay.`amount`,IF(pay.`split_mode`=2,pay.`amount_initial`*pay.`amount`/100,pay.`amount_initial`)) 
			WHERE pay.`payroll_period_ID`=currentPeriodID AND pay.`processing_order`=currentProcessingOrder;
			-- Ergebnis runden, falls erforderlich
			UPDATE `payroll_payment_current` pay 
			INNER JOIN `payroll_tmp_change_mng` emplist ON pay.`payroll_employee_ID`=emplist.`numID` AND emplist.`core_user_id`=currentUser 
			SET pay.`amount`=ROUND(pay.`amount`/pay.`round_param`)*pay.`round_param` 
			WHERE pay.`payroll_period_ID`=currentPeriodID AND pay.`processing_order`=currentProcessingOrder AND pay.`split_mode`!=1 AND pay.`having_rounding`=1;
			-- wenn Ergebnis grösser ist als amount_available, wird das Resultat auf amount_available reduziert
			UPDATE `payroll_payment_current` pay 
			INNER JOIN `payroll_tmp_change_mng` emplist ON pay.`payroll_employee_ID`=emplist.`numID` AND emplist.`core_user_id`=currentUser 
			SET pay.`amount`=IF(pay.`amount`>pay.`amount_available`,pay.`amount_available`,pay.`amount`) 
			WHERE pay.`payroll_period_ID`=currentPeriodID AND pay.`processing_order`=currentProcessingOrder AND pay.`split_mode`!=1;
			-- Ergebnis dem amount_available subtrahieren und das neue Zwischenergebnis auf die nachfolgenden Runden vortragen
			UPDATE `payroll_payment_current` pay 
			INNER JOIN `payroll_tmp_change_mng` emplist ON pay.`payroll_employee_ID`=emplist.`numID` AND emplist.`core_user_id`=currentUser 
			INNER JOIN `payroll_payment_current` pay2 ON pay2.`payroll_employee_ID`=pay.`payroll_employee_ID` AND pay2.`payroll_period_ID`=currentPeriodID AND pay2.`split_mode`!=1 AND pay2.`processing_order`=currentProcessingOrder 
			SET pay.`amount_available`=pay2.`amount_available`-pay2.`amount` 
			WHERE pay.`payroll_period_ID`=currentPeriodID AND pay.`processing_order`>currentProcessingOrder;
		END IF;
	UNTIL done END REPEAT;
	CLOSE cursorProcessingOrder;
	SET done = 0;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `payroll_prc_workdays` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`remoteadmin`@`213.188.228.86`*/ /*!50003 PROCEDURE `payroll_prc_workdays`(userID INT, currentYear SMALLINT, currentMonth TINYINT)
BEGIN
	DECLARE payrollPeriodID SMALLINT;
	DECLARE upperDaysLimit TINYINT;
	DECLARE firstDayOfCurrentMonth DATE;
	DECLARE lastDayOfCurrentMonth DATE;

	Set firstDayOfCurrentMonth = DATE(CONCAT(currentYear,'-',currentMonth,'-01'));
	Set lastDayOfCurrentMonth = LAST_DAY(firstDayOfCurrentMonth);

    IF currentMonth = 2 THEN SET upperDaysLimit = 27;
    ELSE SET upperDaysLimit = 30;
    END IF;


	START TRANSACTION;

	SELECT `id` INTO payrollPeriodID 
	FROM `payroll_period` 
	WHERE `payroll_year_ID`=currentYear AND `major_period`=currentMonth;

	INSERT INTO `payroll_employee_account`(`payroll_employee_ID`,`payroll_account_ID`,`PayrollDataType`,`account_text`,`quantity`,`rate`,`amount`,`max_limit`,`min_limit`,`deduction`,`CostCenter`,`DateFrom`,`DateTo`,`allowable_workdays`,`allowable_workdays_sum`,`amount_balance`) 
	SELECT emplList.`numID`, acc.`ID`,9,'',0,0,0,0,0,0,'','0000-00-00','0000-00-00',30,0,0 
	FROM `payroll_tmp_change_mng` emplList 
	INNER JOIN `payroll_employment_period` prd ON emplList.`numID`=prd.`payroll_employee_ID` 
	INNER JOIN `payroll_account` acc ON acc.`payroll_year_ID`=currentYear AND acc.`having_limits`=1 
	WHERE prd.`DateFrom`<firstDayOfCurrentMonth AND prd.`DateTo`='0000-00-00' AND emplList.`core_user_ID`=userID;

	INSERT INTO `payroll_employee_account`(`payroll_employee_ID`,`payroll_account_ID`,`PayrollDataType`,`account_text`,`quantity`,`rate`,`amount`,`max_limit`,`min_limit`,`deduction`,`CostCenter`,`DateFrom`,`DateTo`,`allowable_workdays`,`allowable_workdays_sum`,`amount_balance`) 
	SELECT emplList.`numID`, acc.`ID`,9,'',0,0,0,0,0,0,'','0000-00-00','0000-00-00',src.workdays,0,0 
	FROM `payroll_tmp_change_mng` emplList 
	INNER JOIN (SELECT iprd.`payroll_employee_ID`, SUM(IF(DAY(IF(iprd.`DateTo`='0000-00-00' OR iprd.`DateTo`>lastDayOfCurrentMonth,lastDayOfCurrentMonth,iprd.`DateTo`))>upperDaysLimit,30,DAY(IF(iprd.`DateTo`='0000-00-00' OR iprd.`DateTo`>lastDayOfCurrentMonth,lastDayOfCurrentMonth,iprd.`DateTo`))) - IF(DAY(IF(iprd.`DateFrom`<firstDayOfCurrentMonth,firstDayOfCurrentMonth,iprd.`DateFrom`))>upperDaysLimit,30,DAY(IF(iprd.`DateFrom`<firstDayOfCurrentMonth,firstDayOfCurrentMonth,iprd.`DateFrom`))) + 1) as workdays FROM `payroll_employment_period` iprd INNER JOIN `payroll_tmp_change_mng` iemplList ON iemplList.`numID`=iprd.`payroll_employee_ID` AND iemplList.`core_user_ID`=userID WHERE iprd.`DateFrom`<=lastDayOfCurrentMonth AND iprd.`DateTo`>=firstDayOfCurrentMonth GROUP BY iprd.`payroll_employee_ID`) src ON emplList.`numID`=src.`payroll_employee_ID` 
	INNER JOIN `payroll_account` acc ON acc.`payroll_year_ID`=currentYear AND acc.`having_limits`=1 
	WHERE emplList.`core_user_ID`=userID;

	UPDATE `payroll_employee_account` emplacc 
	INNER JOIN `payroll_tmp_change_mng` ids ON emplacc.`payroll_employee_ID`=ids.`numID` AND ids.`core_user_ID`=userID 
	SET emplacc.`allowable_workdays`=30 
	WHERE emplacc.`allowable_workdays`>30;

	COMMIT;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-08-04 11:20:16
