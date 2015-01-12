<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:decimal-format name="apodot" decimal-separator="." grouping-separator="'"/>
<xsl:strip-space elements="*"/>
<xsl:output method="text" encoding="UTF-8"/>

<xsl:template match="/">\nonstopmode
\documentclass[<xsl:choose>
<xsl:when test="count(Report/Header/ExtendedMonthView/*) &gt; 2"><xsl:text>8pt,a3paper</xsl:text></xsl:when>
<xsl:when test="count(Report/Header/ExtendedMonthView/*) &gt; 1"><xsl:text>9pt,a3paper</xsl:text></xsl:when>
<xsl:otherwise><xsl:text>9pt,a4paper</xsl:text></xsl:otherwise>
</xsl:choose>]{extarticle}
\usepackage[utf8]{inputenc}
\usepackage{german,longtable}
\usepackage[landscape, top=20mm, bottom=25mm, left=8mm, right=8mm]{geometry}
\usepackage{fancyhdr}
\usepackage{lastpage}
\usepackage{helvet}
\usepackage{booktabs}
\usepackage[table]{xcolor}
\definecolor{lightgray}{gray}{0.9}

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

<xsl:apply-templates select="Report/Employees"/>

\end{document}
</xsl:template>


<xsl:template match="Employees"><xsl:apply-templates/>
</xsl:template>


<xsl:template match="Header">
\fancyhead[LO,LE]{\fontsize{18pt}{18pt}\textbf{<xsl:choose>
<xsl:when test="/Report/@lang = 'fr'"><xsl:text>Lohnkonto</xsl:text></xsl:when>
<xsl:when test="/Report/@lang = 'it'"><xsl:text>Lohnkonto</xsl:text></xsl:when>
<xsl:otherwise><xsl:text>Lohnkonto</xsl:text></xsl:otherwise>
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
<xsl:when test="/Report/@lang = 'fr'"><xsl:text>Jahr</xsl:text></xsl:when>
<xsl:when test="/Report/@lang = 'it'"><xsl:text>Jahr</xsl:text></xsl:when>
<xsl:otherwise><xsl:text>Jahr</xsl:text></xsl:otherwise>
</xsl:choose>}: <xsl:value-of select="Year"/>\\}
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




<xsl:template match="Employees"><xsl:apply-templates/>
</xsl:template>



<xsl:template match="Employee"><xsl:if test="position() > 1">\pagebreak
</xsl:if>
\rowcolors{2}{lightgray}{}
\begin{longtable}{ !{\vrule width -1pt}l l c r r r r r r r r r r r r<xsl:if test="/Report/Header/ExtendedMonthView/Prd13"> r</xsl:if><xsl:if test="/Report/Header/ExtendedMonthView/Prd14"> r</xsl:if><xsl:if test="/Report/Header/ExtendedMonthView/Prd15"> r</xsl:if><xsl:if test="/Report/Header/ExtendedMonthView/Prd16"> r</xsl:if>@{\extracolsep{\fill}} r}
\multicolumn{16}{l}{\hskip-2mm Name: \textbf{<xsl:value-of select="Lastname"/>, <xsl:value-of select="Firstname"/>} \hspace*{2mm}\textbar\hspace*{2mm} Pers.Nr.: \textbf{<xsl:value-of select="EmployeeNumber"/>} \hspace*{2mm}\textbar\hspace*{2mm} Vers.Nr.: <xsl:value-of select="SV-AS-Number"/> \hspace*{2mm}\textbar\hspace*{2mm} Geb.dat.: <xsl:call-template name="FormatDate"><xsl:with-param name="Date" select="DateOfBirth" /></xsl:call-template> \hspace*{2mm}\textbar\hspace*{2mm} <xsl:apply-templates select="EmploymentPeriods/Period"/>
\hspace*{2mm}\textbar\hspace*{2mm} AHV-Code: <xsl:value-of select="CodeAHV"/> \hspace*{2mm}\textbar\hspace*{2mm} ALV-Code: <xsl:value-of select="CodeALV"/> \hspace*{2mm}\textbar\hspace*{2mm} UVG-Code: <xsl:value-of select="CodeUVG"/> \hspace*{2mm}\textbar\hspace*{2mm} KTG-Code: <xsl:value-of select="CodeKTG"/> \hspace*{2mm}\textbar\hspace*{2mm} BVG-Code: <xsl:value-of select="CodeBVG"/>}&amp;&amp;&amp;&amp;\\
\midrule
<xsl:choose>
<xsl:when test="/Report/@lang = 'fr'"><xsl:text>\textbf{LOA}&amp;\textbf{Bezeichnung}&amp;\textbf{M/B}&amp;\textbf{Januar}&amp;\textbf{Februar}&amp;\textbf{März}&amp;\textbf{April}&amp;\textbf{Mai}&amp;\textbf{Juni}&amp;\textbf{Juli}&amp;\textbf{August}&amp;\textbf{September}&amp;\textbf{Oktober}&amp;\textbf{November}&amp;\textbf{Dezember}</xsl:text></xsl:when>
<xsl:when test="/Report/@lang = 'it'"><xsl:text>\textbf{LOA}&amp;\textbf{Bezeichnung}&amp;\textbf{M/B}&amp;\textbf{Januar}&amp;\textbf{Februar}&amp;\textbf{März}&amp;\textbf{April}&amp;\textbf{Mai}&amp;\textbf{Juni}&amp;\textbf{Juli}&amp;\textbf{August}&amp;\textbf{September}&amp;\textbf{Oktober}&amp;\textbf{November}&amp;\textbf{Dezember}</xsl:text></xsl:when>
<xsl:otherwise><xsl:text>\textbf{LOA}&amp;\textbf{Bezeichnung}&amp;\textbf{M/B}&amp;\textbf{Januar}&amp;\textbf{Februar}&amp;\textbf{März}&amp;\textbf{April}&amp;\textbf{Mai}&amp;\textbf{Juni}&amp;\textbf{Juli}&amp;\textbf{August}&amp;\textbf{September}&amp;\textbf{Oktober}&amp;\textbf{November}&amp;\textbf{Dezember}</xsl:text></xsl:otherwise>
</xsl:choose><xsl:if test="/Report/Header/ExtendedMonthView/Prd13">&amp;\textbf{Prd. 13}</xsl:if><xsl:if test="/Report/Header/ExtendedMonthView/Prd14">&amp;\textbf{Prd. 14}</xsl:if><xsl:if test="/Report/Header/ExtendedMonthView/Prd15">&amp;\textbf{Gratif.1}</xsl:if><xsl:if test="/Report/Header/ExtendedMonthView/Prd16">&amp;\textbf{Gratif.2}</xsl:if>&amp;\textbf{TOTAL}\\
\midrule
\endhead
<xsl:apply-templates select="Entries/Entry"/>
\end{longtable}
</xsl:template>


<xsl:template match="Entry"><xsl:if test="quantity"><xsl:value-of select="AccountNumber"/>&amp;<xsl:value-of select="AccountName"/>&amp;M&amp;<xsl:if test="quantity/Jan != 0"><xsl:value-of select="format-number(quantity/Jan,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>&amp;<xsl:if test="quantity/Feb != 0"><xsl:value-of select="format-number(quantity/Feb,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>&amp;<xsl:if test="quantity/Mar != 0"><xsl:value-of select="format-number(quantity/Mar,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>&amp;<xsl:if test="quantity/Apr != 0"><xsl:value-of select="format-number(quantity/Apr,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>&amp;<xsl:if test="quantity/May != 0"><xsl:value-of select="format-number(quantity/May,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>&amp;<xsl:if test="quantity/June != 0"><xsl:value-of select="format-number(quantity/June,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>&amp;<xsl:if test="quantity/July != 0"><xsl:value-of select="format-number(quantity/July,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>&amp;<xsl:if test="quantity/Aug != 0"><xsl:value-of select="format-number(quantity/Aug,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>&amp;<xsl:if test="quantity/Sept != 0"><xsl:value-of select="format-number(quantity/Sept,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>&amp;<xsl:if test="quantity/Oct != 0"><xsl:value-of select="format-number(quantity/Oct,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>&amp;<xsl:if test="quantity/Nov != 0"><xsl:value-of select="format-number(quantity/Nov,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>&amp;<xsl:if test="quantity/Dec != 0"><xsl:value-of select="format-number(quantity/Dec,&quot;#'##0.00&quot;,'apodot')"/></xsl:if><xsl:if test="/Report/Header/ExtendedMonthView/Prd13">&amp;<xsl:if test="quantity/Prd13 != 0"><xsl:value-of select="format-number(quantity/Prd13,&quot;#'##0.00&quot;,'apodot')"/></xsl:if></xsl:if><xsl:if test="/Report/Header/ExtendedMonthView/Prd14">&amp;<xsl:if test="quantity/Prd14 != 0"><xsl:value-of select="format-number(quantity/Prd14,&quot;#'##0.00&quot;,'apodot')"/></xsl:if></xsl:if><xsl:if test="/Report/Header/ExtendedMonthView/Prd15">&amp;<xsl:if test="quantity/Prd15 != 0"><xsl:value-of select="format-number(quantity/Prd15,&quot;#'##0.00&quot;,'apodot')"/></xsl:if></xsl:if><xsl:if test="/Report/Header/ExtendedMonthView/Prd16">&amp;<xsl:if test="quantity/Prd16 != 0"><xsl:value-of select="format-number(quantity/Prd16,&quot;#'##0.00&quot;,'apodot')"/></xsl:if></xsl:if>&amp;<xsl:if test="amount/Total != 0"><xsl:value-of select="format-number(quantity/Total,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>\\
</xsl:if>
<xsl:if test="amount"><xsl:value-of select="AccountNumber"/>&amp;<xsl:value-of select="AccountName"/>&amp;B&amp;<xsl:if test="amount/Jan != 0"><xsl:value-of select="format-number(amount/Jan,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>&amp;<xsl:if test="amount/Feb != 0"><xsl:value-of select="format-number(amount/Feb,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>&amp;<xsl:if test="amount/Mar != 0"><xsl:value-of select="format-number(amount/Mar,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>&amp;<xsl:if test="amount/Apr != 0"><xsl:value-of select="format-number(amount/Apr,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>&amp;<xsl:if test="amount/May != 0"><xsl:value-of select="format-number(amount/May,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>&amp;<xsl:if test="amount/June != 0"><xsl:value-of select="format-number(amount/June,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>&amp;<xsl:if test="amount/July != 0"><xsl:value-of select="format-number(amount/July,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>&amp;<xsl:if test="amount/Aug != 0"><xsl:value-of select="format-number(amount/Aug,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>&amp;<xsl:if test="amount/Sept != 0"><xsl:value-of select="format-number(amount/Sept,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>&amp;<xsl:if test="amount/Oct != 0"><xsl:value-of select="format-number(amount/Oct,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>&amp;<xsl:if test="amount/Nov != 0"><xsl:value-of select="format-number(amount/Nov,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>&amp;<xsl:if test="amount/Dec != 0"><xsl:value-of select="format-number(amount/Dec,&quot;#'##0.00&quot;,'apodot')"/></xsl:if><xsl:if test="/Report/Header/ExtendedMonthView/Prd13">&amp;<xsl:if test="amount/Prd13 != 0"><xsl:value-of select="format-number(amount/Prd13,&quot;#'##0.00&quot;,'apodot')"/></xsl:if></xsl:if><xsl:if test="/Report/Header/ExtendedMonthView/Prd14">&amp;<xsl:if test="amount/Prd14 != 0"><xsl:value-of select="format-number(amount/Prd14,&quot;#'##0.00&quot;,'apodot')"/></xsl:if></xsl:if><xsl:if test="/Report/Header/ExtendedMonthView/Prd15">&amp;<xsl:if test="amount/Prd15 != 0"><xsl:value-of select="format-number(amount/Prd15,&quot;#'##0.00&quot;,'apodot')"/></xsl:if></xsl:if><xsl:if test="/Report/Header/ExtendedMonthView/Prd16">&amp;<xsl:if test="amount/Prd16 != 0"><xsl:value-of select="format-number(amount/Prd16,&quot;#'##0.00&quot;,'apodot')"/></xsl:if></xsl:if>&amp;<xsl:if test="amount/Total != 0"><xsl:value-of select="format-number(amount/Total,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>\\
</xsl:if>
</xsl:template>

<xsl:template match="Period"><xsl:if test="position() > 1"> \hspace*{2mm}\textbar\hspace*{2mm} </xsl:if>Eintritt<xsl:if test="position() > 1"><xsl:value-of select="concat(' ',position())" /></xsl:if>: <xsl:call-template name="FormatDate"><xsl:with-param name="Date" select="From" /></xsl:call-template> \hspace*{2mm}\textbar\hspace*{2mm} Austritt<xsl:if test="position() > 1"><xsl:value-of select="concat(' ',position())" /></xsl:if>: <xsl:call-template name="FormatDate"><xsl:with-param name="Date" select="Until" /></xsl:call-template></xsl:template>

<xsl:template name="FormatDate">
	<xsl:param name="Date" />

	<xsl:choose>
	<xsl:when test="$Date = ''">
		<xsl:value-of select="'---'" />
	</xsl:when>
	<xsl:otherwise>
		<xsl:variable name="mo">
		<xsl:value-of select="substring($Date, 6, 2)" />
		</xsl:variable>

		<xsl:variable name="day">
		<xsl:value-of select="substring($Date, 9, 2)" />
		</xsl:variable>

		<xsl:variable name="year">
		<xsl:value-of select="substring($Date, 1, 4)" />
		</xsl:variable>

		<xsl:value-of select="$day" />
		<xsl:value-of select="'.'" />
		<xsl:value-of select="$mo" />
		<xsl:value-of select="'.'" />
		<xsl:value-of select="$year" />
	</xsl:otherwise>
	</xsl:choose>
</xsl:template>

</xsl:stylesheet>
