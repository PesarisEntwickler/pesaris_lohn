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
\begin{longtable}{@{\extracolsep{\fill}}l l l l l l l r c l}
\textbf{<xsl:choose>
<xsl:when test="/Report/@lang = 'fr'"><xsl:text>Pers.Nr.</xsl:text></xsl:when>
<xsl:when test="/Report/@lang = 'it'"><xsl:text>Pers.Nr.</xsl:text></xsl:when>
<xsl:otherwise><xsl:text>Pers.Nr.</xsl:text></xsl:otherwise>
</xsl:choose>}&amp;\textbf{<xsl:choose>
<xsl:when test="/Report/@lang = 'fr'"><xsl:text>Name</xsl:text></xsl:when>
<xsl:when test="/Report/@lang = 'it'"><xsl:text>Name</xsl:text></xsl:when>
<xsl:otherwise><xsl:text>Name</xsl:text></xsl:otherwise>
</xsl:choose>}&amp;\textbf{<xsl:choose>
<xsl:when test="/Report/@lang = 'fr'"><xsl:text>LOA</xsl:text></xsl:when>
<xsl:when test="/Report/@lang = 'it'"><xsl:text>LOA</xsl:text></xsl:when>
<xsl:otherwise><xsl:text>LOA</xsl:text></xsl:otherwise>
</xsl:choose>}&amp;\textbf{<xsl:choose>
<xsl:when test="/Report/@lang = 'fr'"><xsl:text>Bezeichnung</xsl:text></xsl:when>
<xsl:when test="/Report/@lang = 'it'"><xsl:text>Bezeichnung</xsl:text></xsl:when>
<xsl:otherwise><xsl:text>Bezeichnung</xsl:text></xsl:otherwise>
</xsl:choose>}&amp;\textbf{<xsl:choose>
<xsl:when test="/Report/@lang = 'fr'"><xsl:text>Konto</xsl:text></xsl:when>
<xsl:when test="/Report/@lang = 'it'"><xsl:text>Konto</xsl:text></xsl:when>
<xsl:otherwise><xsl:text>Konto</xsl:text></xsl:otherwise>
</xsl:choose>}&amp;\textbf{<xsl:choose>
<xsl:when test="/Report/@lang = 'fr'"><xsl:text>Gegenkonto</xsl:text></xsl:when>
<xsl:when test="/Report/@lang = 'it'"><xsl:text>Gegenkonto</xsl:text></xsl:when>
<xsl:otherwise><xsl:text>Gegenkonto</xsl:text></xsl:otherwise>
</xsl:choose>}&amp;\textbf{<xsl:choose>
<xsl:when test="/Report/@lang = 'fr'"><xsl:text>Kostenstelle</xsl:text></xsl:when>
<xsl:when test="/Report/@lang = 'it'"><xsl:text>Kostenstelle</xsl:text></xsl:when>
<xsl:otherwise><xsl:text>Kostenstelle</xsl:text></xsl:otherwise>
</xsl:choose>}&amp;\textbf{<xsl:choose>
<xsl:when test="/Report/@lang = 'fr'"><xsl:text>Betrag</xsl:text></xsl:when>
<xsl:when test="/Report/@lang = 'it'"><xsl:text>Betrag</xsl:text></xsl:when>
<xsl:otherwise><xsl:text>Betrag</xsl:text></xsl:otherwise>
</xsl:choose>}&amp;\textbf{<xsl:choose>
<xsl:when test="/Report/@lang = 'fr'"><xsl:text>S/H</xsl:text></xsl:when>
<xsl:when test="/Report/@lang = 'it'"><xsl:text>S/H</xsl:text></xsl:when>
<xsl:otherwise><xsl:text>S/H</xsl:text></xsl:otherwise>
</xsl:choose>}&amp;\textbf{<xsl:choose>
<xsl:when test="/Report/@lang = 'fr'"><xsl:text>Text</xsl:text></xsl:when>
<xsl:when test="/Report/@lang = 'it'"><xsl:text>Text</xsl:text></xsl:when>
<xsl:otherwise><xsl:text>Text</xsl:text></xsl:otherwise>
</xsl:choose>}\\
\endhead
<xsl:apply-templates select="Report/Employees"/>
\end{longtable}

\end{document}
</xsl:template>



<xsl:template match="Header">
\fancyhead[LO,LE]{\fontsize{18pt}{18pt}\textbf{<xsl:choose>
<xsl:when test="/Report/@lang = 'fr'"><xsl:text>Kontierte Mitarbeiter BEBU</xsl:text></xsl:when>
<xsl:when test="/Report/@lang = 'it'"><xsl:text>Kontierte Mitarbeiter BEBU</xsl:text></xsl:when>
<xsl:otherwise><xsl:text>Kontierte Mitarbeiter BEBU</xsl:text></xsl:otherwise>
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
</xsl:choose>}: <xsl:value-of select="Year"/>\\
\textbf{<xsl:choose>
<xsl:when test="/Report/@lang = 'fr'"><xsl:text>Periode</xsl:text></xsl:when>
<xsl:when test="/Report/@lang = 'it'"><xsl:text>Periode</xsl:text></xsl:when>
<xsl:otherwise><xsl:text>Periode</xsl:text></xsl:otherwise>
</xsl:choose>:} <xsl:value-of select="Period"/>\\}
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



<xsl:template match="Employee">
<xsl:apply-templates select="Entries"/>
<xsl:apply-templates select="Entries/Entry"/>
</xsl:template>



<xsl:template match="Entries">
<xsl:call-template name="EmployeeRow"/>
</xsl:template>

<xsl:template name="EmployeeRow">
<xsl:value-of select="ancestor::*[name()='Employee'][1]/EmployeeNumber"/>&amp;<xsl:value-of select="concat(ancestor::*[name()='Employee'][1]/Lastname,' ',ancestor::*[name()='Employee'][1]/Firstname)"/>&amp;&amp;&amp;&amp;&amp;&amp;&amp;&amp; \\
</xsl:template>


<xsl:template match="Entry">&amp;&amp;<xsl:value-of select="AccountNumber"/>&amp;<xsl:value-of select="AccountName"/>&amp;<xsl:value-of select="MainAccountNumber"/>&amp;<xsl:value-of select="CounterAccountNumber"/>&amp;<xsl:value-of select="CostCenter"/>&amp;<xsl:if test="amount != ''"><xsl:value-of select="format-number(amount,&quot;#'##0.00&quot;,'apodot')"/></xsl:if>&amp;<xsl:choose>
<xsl:when test="debitcredit = 0"><xsl:text>S</xsl:text></xsl:when>
<xsl:otherwise><xsl:text>H</xsl:text></xsl:otherwise>
</xsl:choose>&amp;<xsl:value-of select="EntryText"/>\\
</xsl:template>

</xsl:stylesheet>
