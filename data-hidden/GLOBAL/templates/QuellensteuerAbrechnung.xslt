<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:decimal-format name="apodot" decimal-separator="." grouping-separator="'"/>
<xsl:strip-space elements="*"/>
<xsl:output method="text" encoding="UTF-8"/>

<xsl:template match="/">\nonstopmode
\documentclass[15pt,a4paper]{article}
\usepackage[utf8]{inputenc}
\usepackage{german,longtable}
\usepackage[landscape, top=20mm, bottom=25mm, left=10mm, right=10mm]{geometry}
\usepackage{fancyhdr}
\usepackage{lastpage}
\usepackage{helvet}

\pagestyle{fancy}
\renewcommand{\familydefault}{\sfdefault}
\setlength{\headheight}{70.18pt}
\setlength{\headsep}{3mm}
\setlength{\footskip}{5mm}

\pagestyle{fancy}

<xsl:apply-templates select="Report/Header"/>

\fancyfoot[C]{}
\renewcommand{\headrulewidth}{0.4pt}
\renewcommand{\footrulewidth}{0.4pt}

\setlength\LTleft{0pt} 
\setlength\LTright{0pt}

\begin{document}
\vspace*{-7.15mm}

\begin{longtable}{ p{50mm} p{5mm} p{20mm} p{22mm} p{22mm} p{22mm} p{24mm} p{22mm} p{22mm} p{24mm}}
Nr/Name/Vorname     
&amp; 
&amp; Eintritt 
&amp; Periode 
&amp;\hfill Brutto-Lohn 
&amp;\hfill Zulagen 
&amp;\hfill pfl.Beitrag 
&amp;\hfill Tarif 
&amp;\hfill Zivilstand 
&amp;\hfill Abzug \\
Versicherungsnummer 
&amp; 
&amp; Austritt 
&amp; von-bis 
&amp;             
&amp;         
&amp;             
&amp;       
&amp;\hfill Kinder    
&amp;  
\hline
\\
\endhead
<xsl:apply-templates select="Report"/>

\end{longtable}
\end{document}
</xsl:template>

<xsl:template match="Header">
\fancyhead[LO,LE]{\fontsize{18pt}{18pt}\textbf{<xsl:choose>
<xsl:when test="/Report/@lang = 'fr'"><xsl:text>Quellensteuer-Abrechnung</xsl:text></xsl:when>
<xsl:when test="/Report/@lang = 'it'"><xsl:text>Quellensteuer-Abrechnung</xsl:text></xsl:when>
<xsl:otherwise><xsl:text>Quellensteuer-Abrechnung</xsl:text></xsl:otherwise>
</xsl:choose>}\\
\vspace{2mm}
<xsl:if test="Company/Name != ''">
	<xsl:value-of select="Company/Name"/>\\
</xsl:if>
<xsl:if test="Company/Street != ''">
	<xsl:value-of select="Company/Street"/>\\
</xsl:if>
<xsl:if test="Company/ZipCity != ''">
	<xsl:value-of select="Company/ZipCity"/>\\
</xsl:if>}
\fancyhead[CO,CE]{\textbf{<xsl:choose>
<xsl:when test="/Report/@lang = 'fr'"><xsl:text>Periode</xsl:text></xsl:when>
<xsl:when test="/Report/@lang = 'it'"><xsl:text>Periode</xsl:text></xsl:when>
<xsl:otherwise><xsl:text>Periode</xsl:text></xsl:otherwise>
</xsl:choose>:} <xsl:value-of select="Period"/>\\
\\
<xsl:if test="Company/ContactPersName != ''">
	<xsl:value-of select="Company/ContactPersName"/>,
</xsl:if>
<xsl:if test="Company/ContactPersTel != ''">
	<xsl:value-of select="Company/ContactPersTel"/>,
</xsl:if>
<xsl:if test="Company/ContactPersEMail != ''">
	<xsl:value-of select="Company/ContactPersEMail"/>\\
</xsl:if>}
\fancyhead[RO,RE]{<xsl:choose>
<xsl:when test="/Report/@lang = 'fr'"><xsl:text>Date</xsl:text></xsl:when>
<xsl:when test="/Report/@lang = 'it'"><xsl:text>Dato</xsl:text></xsl:when>
<xsl:otherwise><xsl:text>Datum</xsl:text></xsl:otherwise>
</xsl:choose>: <xsl:value-of select="PrintDate"/>\\
<xsl:choose>
<xsl:when test="/Report/@lang = 'fr'"><xsl:text>Zeit</xsl:text></xsl:when>
<xsl:when test="/Report/@lang = 'it'"><xsl:text>Zeit</xsl:text></xsl:when>
<xsl:otherwise><xsl:text>Zeit</xsl:text></xsl:otherwise>
</xsl:choose>: <xsl:value-of select="PrintTime"/>\\
<xsl:choose>
<xsl:when test="/Report/@lang = 'fr'"><xsl:text>Page</xsl:text></xsl:when>
<xsl:when test="/Report/@lang = 'it'"><xsl:text>Pagina</xsl:text></xsl:when>
<xsl:otherwise><xsl:text>Seite</xsl:text></xsl:otherwise>
</xsl:choose>: \thepage\ <xsl:choose>
<xsl:when test="/Report/@lang = 'fr'"><xsl:text>de</xsl:text></xsl:when>
<xsl:when test="/Report/@lang = 'it'"><xsl:text>del</xsl:text></xsl:when>
<xsl:otherwise><xsl:text>von</xsl:text></xsl:otherwise>
</xsl:choose> \pageref{LastPage}\\}
</xsl:template>






<xsl:template match="Report"><xsl:if test="position() > 1">\pagebreak
</xsl:if>
<xsl:apply-templates select="CompanyList"/>
\pagebreak
\\
&amp;\multicolumn{3}{l}{<xsl:text>G e s a m t t o t a l :</xsl:text>}		
&amp;\multicolumn{2}{l}{ }	
&amp;\hfill <xsl:value-of select="QSTReportTotalPflichtig"/> 	
&amp;	
&amp;	
&amp;\hfill <xsl:value-of select="QSTReportTotalAbzug"/>   \\

&amp;\multicolumn{3}{l}{ }
&amp;\multicolumn{3}{l}{ }
&amp;\multicolumn{2}{l}{\hfill Provisionen der Kantone:}
&amp;\hfill <xsl:value-of select="QSTReportTotalAbzugProvision"/> \\ 

&amp;\multicolumn{3}{l}{ }
&amp;\multicolumn{3}{l}{ }	
&amp;\multicolumn{2}{l}{ }
&amp;\hfill <xsl:value-of select="QSTReportTotalAbzugNachProvision"/> \\
</xsl:template>






<xsl:template match="CompanyList">
<xsl:apply-templates select="Company"/>
</xsl:template>



<xsl:template match="Company"><xsl:if test="position() > 1">\pagebreak
</xsl:if>
<xsl:apply-templates select="KantonList"/>
\pagebreak
\\
Summe <xsl:value-of select="Krz"/>:
&amp;\multicolumn{3}{l}{<xsl:value-of select="Name"/>}		
&amp;\multicolumn{2}{l}{<xsl:value-of select="KontaktpersonName"/>}	
&amp;\hfill <xsl:value-of select="QSTCompanyTotalPflichtig"/> 	
&amp;	
&amp;	
&amp;\hfill <xsl:value-of select="QSTCompanyTotalAbzug"/>   \\

&amp;\multicolumn{3}{l}{<xsl:value-of select="Strasse"/>}
&amp;\multicolumn{3}{l}{<xsl:value-of select="KontaktpersonTel"/>}
&amp;\multicolumn{2}{l}{\hfill Provisionen der Kantone:}
&amp;\hfill <xsl:value-of select="QSTCompanyTotalAbzugProvision"/> \\ 

&amp;\multicolumn{3}{l}{<xsl:value-of select="PlzOrt"/>}
&amp;\multicolumn{3}{l}{<xsl:value-of select="KontaktpersonEMail"/>}	
&amp;\multicolumn{2}{l}{ }
&amp;\hfill <xsl:value-of select="QSTCompanyTotalAbzugNachProvision"/> \\
</xsl:template>




<xsl:template match="Kanton"><xsl:if test="position() > 1">\pagebreak
</xsl:if>
<xsl:apply-templates select="GemeindeList/Gemeinde"/>
\pagebreak
\\
<xsl:text>Summe Kanton: </xsl:text> \fontsize{14pt}{14pt}\textbf{ <xsl:value-of select="Name"/>} 
&amp;
&amp;\multicolumn{4}{l}{<xsl:text>Arbeitgebernummer: </xsl:text> <xsl:value-of select="Arbeitgebernummer"/>}
&amp;\hfill <xsl:value-of select="QSTKantonTotalPflichtig"/>
&amp;	
&amp;	
&amp;\hfill <xsl:value-of select="QSTKantonTotalAbzug"/>
\\
&amp;
&amp;
&amp;
&amp;
&amp;
&amp;
&amp;\multicolumn{2}{l}{\hfill Provision  <xsl:value-of select="ProvisionProzent"/> \%: }
&amp;\hfill <xsl:value-of select="QSTKantonTotalAbzugProvision"/>
\\
&amp;
&amp;
&amp;
&amp;
&amp;
&amp;
&amp;
&amp;
&amp;\hfill <xsl:value-of select="QSTKantonTotalAbzugNachProvision"/>\\
</xsl:template>




<xsl:template match="Gemeinde"><xsl:if test="position() > 1">\pagebreak
</xsl:if>
\multicolumn{9}{l}{<xsl:text>Gemeinde </xsl:text> <xsl:value-of select="Name"/>}
\\
\\
<xsl:apply-templates select="MitarbeiterList/Mitarbeiter"/>
\\
<xsl:text>Summe Gemeinde:</xsl:text> 
&amp;\multicolumn{3}{l}{\fontsize{11pt}{11pt}\textbf{<xsl:value-of select="Name"/>} }
&amp;
&amp;
&amp;\hfill <xsl:value-of select="QSTGemeindeTotalPflichtig"/>
&amp;
&amp;
&amp;\hfill <xsl:value-of select="QSTGemeindeTotalAbzug"/>\\
</xsl:template>




 <xsl:template match="//Mitarbeiter">
<!-- \begin{longtable}{ p{50mm} p{6mm} p{20mm} p{18mm} p{24mm} p{24mm} p{24mm} p{24mm} p{12mm} p{32mm}}
 -->\fontsize{11pt}{11pt}\textbf{<xsl:value-of select="MaName"/>} 
&amp; <xsl:value-of select="MaSex"/>
&amp; <xsl:value-of select="MaEintritt"/>
&amp; <xsl:value-of select="MaQSTPeriodeVonBis"/> 
&amp;\hfill <xsl:value-of select="MaQSTBetragBruttoLohn"/> 
&amp;\hfill <xsl:value-of select="MaQSTBetragZulagen"/> 
&amp;\hfill <xsl:value-of select="MaQSTBetragPflichtig"/> 
&amp;\hfill <xsl:value-of select="MaQSTCode"/> 
&amp;\hfill <xsl:value-of select="MaZivilstand"/> 
&amp;\hfill <xsl:value-of select="MaQSTBetragAbzug"/>
\\
\ <xsl:value-of select="MaAHVNummer"/> 
&amp; 
&amp; <xsl:value-of select="MaAustritt"/> 
&amp;  
&amp;             
&amp;         
&amp;             
&amp;       
&amp;\hfill <xsl:value-of select="MaKinder"/>     
&amp;  \\
<!-- \end{longtable} -->
</xsl:template>


</xsl:stylesheet>
