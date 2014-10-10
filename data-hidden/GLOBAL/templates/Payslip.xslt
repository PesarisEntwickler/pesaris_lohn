<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:strip-space elements="*"/>
<xsl:output method="text" encoding="UTF-8"/>

<xsl:template match="/">\nonstopmode
\documentclass[10pt,a4paper]{extarticle}
\usepackage[utf8]{inputenc}
\usepackage{german,longtable}
\usepackage[portrait, margin=0mm]{geometry}
\usepackage{lastpage}
\usepackage[T1]{fontenc}
\usepackage{booktabs}
\usepackage{eso-pic}
\usepackage{graphicx}
\usepackage[absolute,overlay]{textpos}
\usepackage{tabularx}

\ifpdf
\pdfinfo{
/Author (Pesaris)
/Title (Lohnabrechnung)
/Creator (Pesaris PDF Engine)
/Producer (Pesaris PDF Engine)
/CreationDate (D:\pdfdate)
/ModDate (D:\pdfdate)
}
\fi

\newcommand\BackgroundPic[1]{%
\put(0,0){%
\parbox[b][\paperheight]{\paperwidth}{%
\vfill
\centering
\includegraphics[width=\paperwidth,height=\paperheight,%
keepaspectratio]{#1}%
\vfill
}}}

\setlength{\TPHorizModule}{1mm}
\setlength{\TPVertModule}{1mm}
\setlength{\parindent}{0mm}

\newcolumntype{L}[1]{>{\raggedright\arraybackslash}p{#1}} % linksbündig mit Breitenangabe
\newcolumntype{C}[1]{>{\centering\arraybackslash}p{#1}} % zentriert mit Breitenangabe
\newcolumntype{R}[1]{>{\raggedleft\arraybackslash}p{#1}} % rechtsbündig mit Breitenangabe

\begin{document}
<xsl:apply-templates select="Report/Employees"/>
\end{document}
</xsl:template>


<xsl:template match="Employees"><xsl:apply-templates/>
</xsl:template>


<xsl:template match="Employee"><xsl:if test="position() > 1">\mbox{}
\newpage
</xsl:if>\ClearShipoutPicture
<xsl:if test="DocumentSettings/PdfTemplate != ''">\AddToShipoutPicture{\BackgroundPic{<xsl:value-of select="DocumentSettings/PdfTemplate"/>}}
</xsl:if>

\begin{textblock}{100}(<xsl:value-of select="DocumentSettings/AddrOffsetLeft"/>,<xsl:value-of select="DocumentSettings/AddrOffsetTop"/>)
<xsl:call-template name="SetFont"><xsl:with-param name="FontName" select="DocumentSettings/AddrFontName" /><xsl:with-param name="FontSize" select="DocumentSettings/AddrFontSize" /></xsl:call-template>

<xsl:value-of select="concat(Firstname,' ',Lastname)"/> \\
<xsl:if test="AdditionalAddrLine1 != 'CH'"><xsl:value-of select="AdditionalAddrLine1"/> \\
</xsl:if><xsl:if test="AdditionalAddrLine1 != ''"><xsl:value-of select="AdditionalAddrLine1"/> \\
</xsl:if><xsl:if test="AdditionalAddrLine2 != ''"><xsl:value-of select="AdditionalAddrLine2"/> \\
</xsl:if><xsl:if test="AdditionalAddrLine3 != ''"><xsl:value-of select="AdditionalAddrLine3"/> \\
</xsl:if><xsl:if test="AdditionalAddrLine4 != ''"><xsl:value-of select="AdditionalAddrLine4"/> \\
</xsl:if><xsl:value-of select="Street"/> \\
<xsl:value-of select="concat(ZIP-Code,' ',City)"/> \\
<xsl:if test="Country != 'CH'"><xsl:value-of select="CountryName"/> \\
</xsl:if>\end{textblock}

\begin{textblock}{100}(<xsl:value-of select="DocumentSettings/InfoOffsetLeft"/>,<xsl:value-of select="DocumentSettings/InfoOffsetTop"/>)
<xsl:call-template name="SetFont"><xsl:with-param name="FontName" select="DocumentSettings/InfoFontName" /><xsl:with-param name="FontSize" select="floor(DocumentSettings/InfoFontSize*1.2)" /></xsl:call-template>
<xsl:choose>
<xsl:when test="DocumentSettings/Language = 'de'">\textbf{Lohnabrechnung} \vspace*{2mm} \\
</xsl:when>
<xsl:when test="DocumentSettings/Language = 'fr'">\textbf{Bulletin de salaire} \vspace*{2mm} \\
</xsl:when>
<xsl:when test="DocumentSettings/Language = 'it'">\textbf{Busta pagina} \vspace*{2mm} \\
</xsl:when>
<xsl:otherwise>\textbf{Payslip} \vspace*{2mm} \\
</xsl:otherwise>
</xsl:choose>
<xsl:call-template name="SetFont"><xsl:with-param name="FontName" select="DocumentSettings/InfoFontName" /><xsl:with-param name="FontSize" select="DocumentSettings/InfoFontSize" /></xsl:call-template>
\begin{tabular}{ @{}l l }
<xsl:apply-templates select="InfoFields/Field"/>\end{tabular}
\end{textblock}

\begin{textblock}{<xsl:value-of select="DocumentSettings/ContentWidth"/>}(<xsl:value-of select="DocumentSettings/ContentOffsetLeft"/>,<xsl:value-of select="DocumentSettings/ContentOffsetTop"/>)
<xsl:call-template name="SetFont"><xsl:with-param name="FontName" select="DocumentSettings/ContentFontName" /><xsl:with-param name="FontSize" select="DocumentSettings/ContentFontSize" /></xsl:call-template>
\begin{tabularx}{<xsl:value-of select="DocumentSettings/ContentWidth"/>mm}{ @{}X r @{}l @{}l r @{}l @{}l r @{}l }
<xsl:choose>
<xsl:when test="DocumentSettings/Language = 'de'">  \textbf{Bezeichnung}	&amp; \multicolumn{2}{r}{\textbf{Menge}} &amp; &amp; \multicolumn{2}{r}{\textbf{Ansatz}} &amp; &amp; \multicolumn{2}{r}{\textbf{Betrag}} \\
</xsl:when>
<xsl:when test="DocumentSettings/Language = 'fr'">  \textbf{Libellés}	&amp; \multicolumn{2}{r}{\textbf{Base}} &amp; &amp; \multicolumn{2}{r}{\textbf{Taux}} &amp; &amp; \multicolumn{2}{r}{\textbf{Montants}} \\
</xsl:when>
<xsl:when test="DocumentSettings/Language = 'it'">  \textbf{Descrizione}	&amp; \multicolumn{2}{r}{\textbf{Imponibile}} &amp; &amp; \multicolumn{2}{r}{\textbf{Tasse}} &amp; &amp; \multicolumn{2}{r}{\textbf{Competenze}} \\
</xsl:when>
<xsl:otherwise>  \textbf{Description}	&amp; \multicolumn{2}{r}{\textbf{Quantity}} &amp; &amp; \multicolumn{2}{r}{\textbf{Rate}} &amp; &amp; \multicolumn{2}{r}{\textbf{Amount}} \\
</xsl:otherwise>
</xsl:choose>
  \hline
  			&amp;		&amp;	&amp;	&amp;		&amp;		&amp;		&amp;				&amp;	 		\\[-1mm]
<xsl:apply-templates select="Entries/Entry"/>
<xsl:apply-templates select="Payouts/Payout"/>
\end{tabularx}


<xsl:apply-templates select="Notification"/>

\end{textblock}
</xsl:template>

<xsl:template match="Field"><xsl:choose>
<xsl:when test="Name = 'IFLD'">
	<xsl:choose>
	<xsl:when test="Value = '1'"><xsl:value-of select="Label"/> &amp; <xsl:call-template name="FormatDate"><xsl:with-param name="Date" select="../../PaymentDate" /></xsl:call-template> \\
	</xsl:when>
	<xsl:when test="Value = '2'"><xsl:value-of select="Label"/> &amp; <xsl:call-template name="FormatDate"><xsl:with-param name="Date" select="../../PeriodStartDate" /></xsl:call-template> -- <xsl:call-template name="FormatDate"><xsl:with-param name="Date" select="../../PeriodEndDate" /></xsl:call-template> \\
	</xsl:when>
	<xsl:when test="Value = '3'"><xsl:value-of select="Label"/> &amp; <xsl:call-template name="PeriodType"><xsl:with-param name="PeriodNumber" select="../../PeriodNumber" /><xsl:with-param name="Language" select="../../DocumentSettings/Language" /></xsl:call-template> \\
	</xsl:when>
	</xsl:choose>
</xsl:when>
<xsl:otherwise><xsl:if test="Value != ''"><xsl:value-of select="Label"/> &amp; <xsl:value-of select="Value"/> \\
</xsl:if>
</xsl:otherwise>
</xsl:choose>
</xsl:template>

<xsl:template match="Entry">
<xsl:choose>
<xsl:when test="@spaceBefore = 'small'"> &amp; &amp; &amp; &amp; &amp; &amp; &amp; &amp; \\[-2mm]
</xsl:when>
<xsl:when test="@spaceBefore = 'medium'"> &amp; &amp; &amp; &amp; &amp; &amp; &amp; &amp; \\
</xsl:when>
<xsl:when test="@spaceBefore = 'large'"> &amp; &amp; &amp; &amp; &amp; &amp; &amp; &amp; \\[5mm]
</xsl:when>
</xsl:choose>

<xsl:choose>
<xsl:when test="@bold = 'true'">\textbf{<xsl:value-of select="AccountName"/>} &amp; \textbf{<xsl:choose><xsl:when test="substring-before(quantity, '.') != ''"><xsl:value-of select="translate(substring-before(quantity, '.'),',',../../DocumentSettings/ThousandsSeparator)"/></xsl:when><xsl:otherwise><xsl:value-of select="quantity"/></xsl:otherwise></xsl:choose>} &amp; <xsl:if test="substring-after(quantity, '.') != ''">\textbf{<xsl:value-of select="../../DocumentSettings/DecimalPoint"/><xsl:value-of select="substring-after(quantity, '.')"/>}</xsl:if> &amp; <xsl:if test="quantityUnit != ''">\hspace*{1mm}\textbf{<xsl:value-of select="quantityUnit"/>}</xsl:if> &amp; \textbf{<xsl:choose><xsl:when test="substring-before(rate, '.') != ''"><xsl:value-of select="translate(substring-before(rate, '.'),',',../../DocumentSettings/ThousandsSeparator)"/></xsl:when><xsl:otherwise><xsl:value-of select="rate"/></xsl:otherwise></xsl:choose>} &amp; <xsl:if test="substring-after(rate, '.') != ''">\textbf{<xsl:value-of select="../../DocumentSettings/DecimalPoint"/><xsl:value-of select="substring-after(rate, '.')"/>}</xsl:if> &amp; <xsl:if test="rateUnit != ''">\hspace*{1mm}\textbf{<xsl:value-of select="rateUnit"/>}</xsl:if> &amp; \textbf{<xsl:choose><xsl:when test="substring-before(amount, '.') != ''"><xsl:value-of select="translate(substring-before(amount, '.'),',',../../DocumentSettings/ThousandsSeparator)"/></xsl:when><xsl:otherwise><xsl:value-of select="amount"/></xsl:otherwise></xsl:choose>} &amp; <xsl:if test="substring-after(amount, '.') != ''">\textbf{<xsl:value-of select="../../DocumentSettings/DecimalPoint"/><xsl:value-of select="substring-after(amount, '.')"/>}</xsl:if> \\
</xsl:when>
<xsl:otherwise><xsl:value-of select="AccountName"/> &amp; <xsl:choose><xsl:when test="substring-before(quantity, '.') != ''"><xsl:value-of select="translate(substring-before(quantity, '.'),',',../../DocumentSettings/ThousandsSeparator)"/></xsl:when><xsl:otherwise><xsl:value-of select="quantity"/></xsl:otherwise></xsl:choose> &amp; <xsl:if test="substring-after(quantity, '.') != ''"><xsl:value-of select="../../DocumentSettings/DecimalPoint"/><xsl:value-of select="substring-after(quantity, '.')"/></xsl:if> &amp; <xsl:if test="quantityUnit != ''">\hspace*{1mm}<xsl:value-of select="quantityUnit"/></xsl:if> &amp; <xsl:choose><xsl:when test="substring-before(rate, '.') != ''"><xsl:value-of select="translate(substring-before(rate, '.'),',',../../DocumentSettings/ThousandsSeparator)"/></xsl:when><xsl:otherwise><xsl:value-of select="rate"/></xsl:otherwise></xsl:choose> &amp; <xsl:if test="substring-after(rate, '.') != ''"><xsl:value-of select="../../DocumentSettings/DecimalPoint"/><xsl:value-of select="substring-after(rate, '.')"/></xsl:if> &amp; <xsl:if test="rateUnit != ''">\hspace*{1mm}<xsl:value-of select="rateUnit"/></xsl:if> &amp; <xsl:choose><xsl:when test="substring-before(amount, '.') != ''"><xsl:value-of select="translate(substring-before(amount, '.'),',',../../DocumentSettings/ThousandsSeparator)"/></xsl:when><xsl:otherwise><xsl:value-of select="amount"/></xsl:otherwise></xsl:choose> &amp; <xsl:if test="substring-after(amount, '.') != ''"><xsl:value-of select="../../DocumentSettings/DecimalPoint"/><xsl:value-of select="substring-after(amount, '.')"/></xsl:if> \\
</xsl:otherwise>
</xsl:choose>

<xsl:choose>
<xsl:when test="@spaceAfter = 'small'"> &amp; &amp; &amp; &amp; &amp; &amp; &amp; &amp; \\[-2mm]
</xsl:when>
<xsl:when test="@spaceAfter = 'medium'"> &amp; &amp; &amp; &amp; &amp; &amp; &amp; &amp; \\
</xsl:when>
<xsl:when test="@spaceAfter = 'large'"> &amp; &amp; &amp; &amp; &amp; &amp; &amp; &amp; \\[5mm]
</xsl:when>
</xsl:choose>
</xsl:template>



<xsl:template match="Payout">
		&amp;		&amp;	&amp;	&amp;		&amp;		&amp;		&amp;				&amp;			\\[-2mm]
<xsl:if test="BankAccountNo != ''"><xsl:value-of select="BankAccountNo"/>		&amp;		&amp;	&amp;	&amp;		&amp;		&amp;		&amp;				&amp;			\\
</xsl:if>
<xsl:value-of select="BankAddrLine1"/>		&amp;		&amp;<xsl:value-of select="PayoutAmountCHF"/>	&amp;	&amp;	\multicolumn{3}{r}{<xsl:value-of select="PayoutCurrency"/>}
			&amp;	<xsl:choose>
						<xsl:when test="substring-before(PayoutAmount, '.') != ''">
							<xsl:value-of select="translate(substring-before(PayoutAmount, '.'),',',../../DocumentSettings/ThousandsSeparator)"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="PayoutAmount"/></xsl:otherwise></xsl:choose> &amp; 
							<xsl:if test="substring-after(PayoutAmount, '.') != ''">
								<xsl:value-of select="../../DocumentSettings/DecimalPoint"/>
								<xsl:value-of select="substring-after(PayoutAmount, '.')"/>
							</xsl:if> \\
<xsl:if test="BankAddrLine2 != ''"><xsl:value-of select="BankAddrLine2"/>		&amp;		&amp;	&amp;	&amp;		&amp;		&amp;		&amp;				&amp;			\\
</xsl:if>
<xsl:if test="BankAddrLine3 != ''"><xsl:value-of select="BankAddrLine3"/>		&amp;		&amp;	&amp;	&amp;		&amp;		&amp;		&amp;				&amp;			\\
</xsl:if>
</xsl:template>




<xsl:template match="Notification">
\vspace{5mm}
\fbox{
\begin{minipage}{<xsl:value-of select="(../DocumentSettings/ContentWidth)-3"/>mm}
<xsl:call-template name="SetFont"><xsl:with-param name="FontName" select="../DocumentSettings/ContentFontName" /><xsl:with-param name="FontSize" select="floor(../DocumentSettings/ContentFontSize*1.2)" /></xsl:call-template>
<xsl:choose>
<xsl:when test="../DocumentSettings/Language = 'de'">\textbf{Mitteilung} \\
</xsl:when>
<xsl:when test="../DocumentSettings/Language = 'fr'">\textbf{Notification} \\
</xsl:when>
<xsl:when test="../DocumentSettings/Language = 'it'">\textbf{Notificazione} \\
</xsl:when>
<xsl:otherwise>\textbf{Notification} \\
</xsl:otherwise>
</xsl:choose>
<xsl:call-template name="SetFont"><xsl:with-param name="FontName" select="../DocumentSettings/ContentFontName" /><xsl:with-param name="FontSize" select="../DocumentSettings/ContentFontSize" /></xsl:call-template>
<xsl:apply-templates/>
\end{minipage}}
</xsl:template>

<xsl:template match="br"> \\
</xsl:template>

<xsl:template name="PeriodType">
	<xsl:param name="PeriodNumber" />
	<xsl:param name="Language" />

	<xsl:choose>
	<xsl:when test="$PeriodNumber = '13' or $PeriodNumber = '14'">
		<xsl:choose>
		<xsl:when test="$Language = 'de'">
			<xsl:value-of select="concat($PeriodNumber,'. Monatslohn')" />
		</xsl:when>
		<xsl:when test="$Language = 'fr'">
			<xsl:value-of select="concat($PeriodNumber,'. Monatslohn')" />
		</xsl:when>
		<xsl:when test="$Language = 'it'">
			<xsl:value-of select="concat($PeriodNumber,'. Monatslohn')" />
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="concat($PeriodNumber,'. Monatslohn')" />
		</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:when test="$PeriodNumber = '15' or $PeriodNumber = '16'">
		<xsl:choose>
		<xsl:when test="$Language = 'de'">
			<xsl:value-of select="'Gratifikation'" />
		</xsl:when>
		<xsl:when test="$Language = 'fr'">
			<xsl:value-of select="'Gratifikation'" />
		</xsl:when>
		<xsl:when test="$Language = 'it'">
			<xsl:value-of select="'Gratifikation'" />
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="'Gratifikation'" />
		</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:otherwise>
		<xsl:choose>
		<xsl:when test="$Language = 'de'">
			<xsl:value-of select="'Monatslohn '" />
			<xsl:choose>
			<xsl:when test="$PeriodNumber = '1'">
				<xsl:value-of select="'Januar'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '2'">
				<xsl:value-of select="'Februar'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '3'">
				<xsl:value-of select="'März'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '4'">
				<xsl:value-of select="'April'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '5'">
				<xsl:value-of select="'Mai'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '6'">
				<xsl:value-of select="'Juni'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '7'">
				<xsl:value-of select="'Juli'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '8'">
				<xsl:value-of select="'August'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '9'">
				<xsl:value-of select="'September'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '10'">
				<xsl:value-of select="'Oktober'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '11'">
				<xsl:value-of select="'November'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '12'">
				<xsl:value-of select="'Dezember'" />
			</xsl:when>
			</xsl:choose>
		</xsl:when>
		<xsl:when test="$Language = 'fr'">
			<xsl:value-of select="'Monatslohn '" />
			<xsl:choose>
			<xsl:when test="$PeriodNumber = '1'">
				<xsl:value-of select="'Januar'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '2'">
				<xsl:value-of select="'Februar'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '3'">
				<xsl:value-of select="'März'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '4'">
				<xsl:value-of select="'April'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '5'">
				<xsl:value-of select="'Mai'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '6'">
				<xsl:value-of select="'Juni'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '7'">
				<xsl:value-of select="'Juli'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '8'">
				<xsl:value-of select="'August'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '9'">
				<xsl:value-of select="'September'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '10'">
				<xsl:value-of select="'Oktober'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '11'">
				<xsl:value-of select="'November'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '12'">
				<xsl:value-of select="'Dezember'" />
			</xsl:when>
			</xsl:choose>
		</xsl:when>
		<xsl:when test="$Language = 'it'">
			<xsl:value-of select="'Monatslohn '" />
			<xsl:choose>
			<xsl:when test="$PeriodNumber = '1'">
				<xsl:value-of select="'Januar'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '2'">
				<xsl:value-of select="'Februar'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '3'">
				<xsl:value-of select="'März'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '4'">
				<xsl:value-of select="'April'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '5'">
				<xsl:value-of select="'Mai'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '6'">
				<xsl:value-of select="'Juni'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '7'">
				<xsl:value-of select="'Juli'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '8'">
				<xsl:value-of select="'August'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '9'">
				<xsl:value-of select="'September'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '10'">
				<xsl:value-of select="'Oktober'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '11'">
				<xsl:value-of select="'November'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '12'">
				<xsl:value-of select="'Dezember'" />
			</xsl:when>
			</xsl:choose>
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="'Monatslohn '" />
			<xsl:choose>
			<xsl:when test="$PeriodNumber = '1'">
				<xsl:value-of select="'Januar'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '2'">
				<xsl:value-of select="'Februar'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '3'">
				<xsl:value-of select="'März'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '4'">
				<xsl:value-of select="'April'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '5'">
				<xsl:value-of select="'Mai'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '6'">
				<xsl:value-of select="'Juni'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '7'">
				<xsl:value-of select="'Juli'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '8'">
				<xsl:value-of select="'August'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '9'">
				<xsl:value-of select="'September'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '10'">
				<xsl:value-of select="'Oktober'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '11'">
				<xsl:value-of select="'November'" />
			</xsl:when>
			<xsl:when test="$PeriodNumber = '12'">
				<xsl:value-of select="'Dezember'" />
			</xsl:when>
			</xsl:choose>
		</xsl:otherwise>
		</xsl:choose>
	</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="FormatDate">
	<xsl:param name="Date" />

	<xsl:choose>
	<xsl:when test="$Date = '0000-00-00'">
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

<xsl:template name="SetFont">
	<xsl:param name="FontName" />
	<xsl:param name="FontSize" />

	<xsl:value-of select="'\fontfamily{'" />
	<xsl:value-of select="$FontName" />
	<xsl:value-of select="'}\fontsize{'" />
	<xsl:value-of select="$FontSize" />
	<xsl:value-of select="'}{'" />
	<xsl:value-of select="floor($FontSize*1.2)" />
	<xsl:value-of select="'}\selectfont '" />
</xsl:template>

</xsl:stylesheet>

