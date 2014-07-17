<?php
require_once('fpdf.php');
$c="
Presida Treuhand AG                
Mitteldorfstrasse 37               
5033 Buchs                         

Beauftragte Bank:                  UBS AG                   
                                   POSTFACH                 
                                   8098 ZUERICH             Ab Konto:      CH182678257258257254
                                                            
F-NR.  P-NR.  EMPFAENGER/BEGUENSTIGTER                      IBAN NUMMER                  BETRAG
-----------------------------------------------------------------------------------------------
00001  002005 Edith Ganz                CS Konto Wilstr. 2  8700 Meilen
Credit Suisse       Musterplatz 16 3000 Ber                 CH123456                  22'865.15
-----------------------------------------------------------------------------------------------
00002  001006 Maria Paganini            Stubenweid          6030 Ebikon
UBS AG              Postfach 8098 Zuerich                   CH18EINEIBANNUMMER06       4'714.55
-----------------------------------------------------------------------------------------------
00003  001007 Paula Nestler             Bollstrasse 4       6064 Kerns
UBS AG              Postfach 8098 Zuerich                   CH18EINEIBANNUMMER07       4'342.00
-----------------------------------------------------------------------------------------------
00004  001008 Nunez Maria 2             5, rue d alsace     1000 Geneve 2
UBS AG              8005 Zuerich                            123.321654.2               4'346.60
-----------------------------------------------------------------------------------------------
00005  001009 Hans Ott                  Unterdorf 5         6037 Root
UBS AG              Postfach 8098 Zuerich                   CH18EINEIBANNUMMER09       5'450.50
-----------------------------------------------------------------------------------------------
00006  001010 Michael Estermann         Lowengrube 12       6014 Littau
UBS AG              Postfach 8098 Zuerich                   CH18EINEIBANNUMMER10       2'845.50
-----------------------------------------------------------------------------------------------
00007  001011 Corinne Farine            Simplonstrasse 26   3900 Brig
UBS AG              Postfach 8098 Zuerich                   CH18EINEIBANNUMMER11         230.00
-----------------------------------------------------------------------------------------------
00008  001012 Heinz Ganz                Neuhofstrasse 49    6020 Emmenbruecke
UBS AG              Postfach 8098 Zuerich                   CH18EINEIBANNUMMER12       4'537.35
-----------------------------------------------------------------------------------------------
00009  001013 Maria Paganini s          UBS Konto Luzerners 6032 Kerns
UBS AG              Postfach 8098 Zuerich                   CH18EINEIBANNUMMER13         879.55
-----------------------------------------------------------------------------------------------
00010  001014 Rene Martin               Mettlen 12          6363 Fuerigen
UBS AG              Postfach 8098 Zuerich                   CH18EINEIBANNUMMER14     948'594.85
-----------------------------------------------------------------------------------------------
00011  001015 Rosa Inglese              Bachstrasse 6       6048 Horw
UBS AG              Postfach 8098 Zuerich                   CH18EINEIBANNUMMER15       4'403.95
-----------------------------------------------------------------------------------------------
00012  001016 Jung, Claude                                  4123 Allschwil
UBS AG              Postfach 8098 Zuerich                   CH18UBS1JUNG1CLAUDE1      12'322.35
-----------------------------------------------------------------------------------------------
00013  001017 Beat Kaiser               Bahnstrasse 6       68540 Feldkirch
UBS AG              Postfach 8098 Zuerich                   CH18EINEIBANNUMMER17       4'837.35
-----------------------------------------------------------------------------------------------
00014  001018 Monica Herz               Sustenweg 12        6020 Emmenbruecke
UBS AG              Postfach 8098 Zuerich                   CH18EINEIBANNUMMER18       4'727.05
-----------------------------------------------------------------------------------------------
00015  001019 Anna Egli                                     8123 Eglisau
UBS AG              Postfach 8098 Zuerich                   CH18EINEIBANNUMMER19       5'189.75
-----------------------------------------------------------------------------------------------
00016  001020 Tester1                                       Aarau
UBS AG              Postfach 8098 Zuerich                   CH18EINEIBANNUMMER20       5'117.95
-----------------------------------------------------------------------------------------------
00017  001021 Peter Bosshard            Hauptstrasse 5      6072 Sachseln
                                                                                      51'190.00
-----------------------------------------------------------------------------------------------
00018  001022 Tester2                                       Aarau
UBS AG              Postfach 8098 Zuerich                   CH735                    265'580.00
-----------------------------------------------------------------------------------------------
00019  001023 Catia Rieder              Hoheweg 10          2532 Magglingen/Macolin
UBS AG              Postfach 8098 Zuerich                   CH18EINEIBANNUMMER23      75'974.85
-----------------------------------------------------------------------------------------------
00020  001024 Rene Lamon                Grande rue          90100 Delle
UBS AG              Postfach 8098 Zuerich                   CH18EINEIBANNUMMER24       4'742.50
-----------------------------------------------------------------------------------------------
                                                            SEITENTOTAL            1'512'660.65
";
$pdf = new FPDF('P','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Courier','',9);
$pdf->MultiCell( 185, 3, $c , 0, 'L', 0); 
$pdf->Output('../transfer/xxxx.pdf', 'F');
?>