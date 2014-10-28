<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:decimal-format name="apodot" decimal-separator="." grouping-separator="'"/>
  <xsl:strip-space elements="*"/>
  <xsl:output method="text" encoding="UTF-8"/>

  <xsl:template match="/">
    \nonstopmode
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
    <xsl:apply-templates select="Report/Corporation"/>

    \end{document}
  </xsl:template>



  <xsl:template match="Header">
    \fancyhead[LO,LE]{\fontsize{18pt}{18pt}\textbf{
    <xsl:choose>
      <xsl:when test="AccountType = 'payroll_mgmt_acc_entry'">
        <xsl:choose>
          <xsl:when test="/Report/@lang = 'fr'">
            <xsl:text>BEBU Journal: Auswertung nach Firma / Kst</xsl:text>
          </xsl:when>
          <xsl:when test="/Report/@lang = 'it'">
            <xsl:text>BEBU Journal: Auswertung nach Firma / Kst</xsl:text>
          </xsl:when>
          <xsl:otherwise>
            <xsl:text>BEBU Journal: Auswertung nach Firma / Kst</xsl:text>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:when>
      <xsl:otherwise>
        <xsl:choose>
          <xsl:when test="/Report/@lang = 'fr'">
            <xsl:text>FIBU Journal: Auswertung nach Firma / Kst</xsl:text>
          </xsl:when>
          <xsl:when test="/Report/@lang = 'it'">
            <xsl:text>FIBU Journal: Auswertung nach Firma / Kst</xsl:text>
          </xsl:when>
          <xsl:otherwise>
            <xsl:text>FIBU Journal: Auswertung nach Firma / Kst</xsl:text>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:otherwise>
    </xsl:choose>}\\
    \vspace{2mm}
    <xsl:if test="MainCompany/Name != ''">
      <xsl:value-of select="MainCompany/Name"/>\\
    </xsl:if>
    <xsl:if test="MainCompany/Street != ''">
      <xsl:value-of select="MainCompany/Street"/>\\
    </xsl:if>
    <xsl:if test="MainCompany/ZipCity != ''">
      <xsl:value-of select="MainCompany/ZipCity"/>\\
    </xsl:if>}
    \fancyhead[CO,CE]{\textbf{<xsl:choose>
      <xsl:when test="/Report/@lang = 'fr'">
        <xsl:text>Jahr</xsl:text>
      </xsl:when>
      <xsl:when test="/Report/@lang = 'it'">
        <xsl:text>Jahr</xsl:text>
      </xsl:when>
      <xsl:otherwise>
        <xsl:text>Jahr</xsl:text>
      </xsl:otherwise>
    </xsl:choose>}: <xsl:value-of select="Year"/>\\
    \textbf{<xsl:choose>
      <xsl:when test="/Report/@lang = 'fr'">
        <xsl:text>Periode</xsl:text>
      </xsl:when>
      <xsl:when test="/Report/@lang = 'it'">
        <xsl:text>Periode</xsl:text>
      </xsl:when>
      <xsl:otherwise>
        <xsl:text>Periode</xsl:text>
      </xsl:otherwise>
    </xsl:choose>:} <xsl:value-of select="Period"/>\\}
    \fancyhead[RO,RE]{<xsl:choose>
      <xsl:when test="/Report/@lang = 'fr'">
        <xsl:text>Date</xsl:text>
      </xsl:when>
      <xsl:when test="/Report/@lang = 'it'">
        <xsl:text>Dato</xsl:text>
      </xsl:when>
      <xsl:otherwise>
        <xsl:text>Datum</xsl:text>
      </xsl:otherwise>
    </xsl:choose>: <xsl:value-of select="PrintDate"/>\\
    <xsl:choose>
      <xsl:when test="/Report/@lang = 'fr'">
        <xsl:text>Zeit</xsl:text>
      </xsl:when>
      <xsl:when test="/Report/@lang = 'it'">
        <xsl:text>Zeit</xsl:text>
      </xsl:when>
      <xsl:otherwise>
        <xsl:text>Zeit</xsl:text>
      </xsl:otherwise>
    </xsl:choose>: <xsl:value-of select="PrintTime"/>\\
    <xsl:choose>
      <xsl:when test="/Report/@lang = 'fr'">
        <xsl:text>Page</xsl:text>
      </xsl:when>
      <xsl:when test="/Report/@lang = 'it'">
        <xsl:text>Pagina</xsl:text>
      </xsl:when>
      <xsl:otherwise>
        <xsl:text>Seite</xsl:text>
      </xsl:otherwise>
    </xsl:choose>: \thepage\ <xsl:choose>
      <xsl:when test="/Report/@lang = 'fr'">
        <xsl:text>de</xsl:text>
      </xsl:when>
      <xsl:when test="/Report/@lang = 'it'">
        <xsl:text>del</xsl:text>
      </xsl:when>
      <xsl:otherwise>
        <xsl:text>von</xsl:text>
      </xsl:otherwise>
    </xsl:choose> \pageref{LastPage}\\}
  </xsl:template>


  <xsl:template match="Corporation">
    <xsl:apply-templates select="Companies"/>
    \renewcommand*{\arraystretch}{1.1}
    \begin{longtable}{@{\extracolsep{\fill}}l l r r}
    \textbf{<xsl:choose>
      <xsl:when test="/Report/@lang = 'fr'">
        <xsl:text>FirmenNr</xsl:text>
      </xsl:when>
      <xsl:when test="/Report/@lang = 'it'">
        <xsl:text>FirmenNr</xsl:text>
      </xsl:when>
      <xsl:otherwise>
        <xsl:text>FirmenNr</xsl:text>
      </xsl:otherwise>
    </xsl:choose>}&amp;\textbf{<xsl:choose>
      <xsl:when test="/Report/@lang = 'fr'">
        <xsl:text>Firma</xsl:text>
      </xsl:when>
      <xsl:when test="/Report/@lang = 'it'">
        <xsl:text>Firma</xsl:text>
      </xsl:when>
      <xsl:otherwise>
        <xsl:text>Firma</xsl:text>
      </xsl:otherwise>
    </xsl:choose>}&amp;\textbf{<xsl:choose>
      <xsl:when test="/Report/@lang = 'fr'">
        <xsl:text>Soll</xsl:text>
      </xsl:when>
      <xsl:when test="/Report/@lang = 'it'">
        <xsl:text>Soll</xsl:text>
      </xsl:when>
      <xsl:otherwise>
        <xsl:text>Soll</xsl:text>
      </xsl:otherwise>
    </xsl:choose>}&amp;\textbf{<xsl:choose>
      <xsl:when test="/Report/@lang = 'fr'">
        <xsl:text>Haben</xsl:text>
      </xsl:when>
      <xsl:when test="/Report/@lang = 'it'">
        <xsl:text>Haben</xsl:text>
      </xsl:when>
      <xsl:otherwise>
        <xsl:text>Haben</xsl:text>
      </xsl:otherwise>
    </xsl:choose>}
    \hline
    \endhead
    <xsl:for-each select="Companies/Company">
      <xsl:value-of select="CompanyID"/>&amp;<xsl:value-of select="CompanyName"/>&amp;<xsl:value-of select="format-number(CompanyDebitAmount,&quot;#'##0.00&quot;,'apodot')"/>&amp;<xsl:value-of select="format-number(CompanyCreditAmount,&quot;#'##0.00&quot;,'apodot')"/>\\
    </xsl:for-each>
    \hline
    \textbf{<xsl:choose>
      <xsl:when test="/Report/@lang = 'fr'">
        <xsl:text>Totale Unternehmung</xsl:text>
      </xsl:when>
      <xsl:when test="/Report/@lang = 'it'">
        <xsl:text>Totale Unternehmung</xsl:text>
      </xsl:when>
      <xsl:otherwise>
        <xsl:text>Totale Unternehmung</xsl:text>
      </xsl:otherwise>
    </xsl:choose>}&amp;&amp;\textbf{<xsl:value-of select="format-number(CorporationDebitAmount,&quot;#'##0.00&quot;,'apodot')"/>}&amp;\textbf{<xsl:value-of select="format-number(CorporationCreditAmount,&quot;#'##0.00&quot;,'apodot')"/>}
    \end{longtable}
  </xsl:template>



  <xsl:template match="Companies">
    <xsl:apply-templates select="Company"/>
  </xsl:template>


  <xsl:template match="Company">
    <!--HEADER-->
    \renewcommand*{\arraystretch}{1.1}
    \begin{longtable}{@{\extracolsep{\fill}}l r r l}
    \textbf{<xsl:value-of select="CompanyID"/> / <xsl:value-of select="CompanyName"/>}\\
    \textbf{<xsl:choose>
      <xsl:when test="/Report/@lang = 'fr'">
        <xsl:text>Kostenstelle</xsl:text>
      </xsl:when>
      <xsl:when test="/Report/@lang = 'it'">
        <xsl:text>Kostenstelle</xsl:text>
      </xsl:when>
      <xsl:otherwise>
        <xsl:text>Kostenstelle</xsl:text>
      </xsl:otherwise>
    </xsl:choose>}&amp;\textbf{<xsl:choose>
      <xsl:when test="/Report/@lang = 'fr'">
        <xsl:text>Soll</xsl:text>
      </xsl:when>
      <xsl:when test="/Report/@lang = 'it'">
        <xsl:text>Soll</xsl:text>
      </xsl:when>
      <xsl:otherwise>
        <xsl:text>Soll</xsl:text>
      </xsl:otherwise>
    </xsl:choose>}&amp;\textbf{<xsl:choose>
      <xsl:when test="/Report/@lang = 'fr'">
        <xsl:text>Haben</xsl:text>
      </xsl:when>
      <xsl:when test="/Report/@lang = 'it'">
        <xsl:text>Haben</xsl:text>
      </xsl:when>
      <xsl:otherwise>
        <xsl:text>Haben</xsl:text>
      </xsl:otherwise>
    </xsl:choose>}&amp;\textbf{<xsl:choose>
      <xsl:when test="/Report/@lang = 'fr'">
        <xsl:text>Text</xsl:text>
      </xsl:when>
      <xsl:when test="/Report/@lang = 'it'">
        <xsl:text>Text</xsl:text>
      </xsl:when>
      <xsl:otherwise>
        <xsl:text>Text</xsl:text>
      </xsl:otherwise>
    </xsl:choose>}\\
    \hline
    \endhead
    <!--DETAILS-->
    <xsl:apply-templates select="Entries/Entry"/>
    <!--FOOTER-->
    \hline
    \textbf{<xsl:value-of select="CompanyID"/> / <xsl:value-of select="CompanyName"/>}&amp;\textbf{<xsl:value-of select="format-number(CompanyDebitAmount,&quot;#'##0.00&quot;,'apodot')"/>}&amp;\textbf{<xsl:value-of select="format-number(CompanyCreditAmount,&quot;#'##0.00&quot;,'apodot')"/>}&amp; \\
    \end{longtable}
    \pagebreak
  </xsl:template>



  <xsl:template match="Entry">
    <xsl:value-of select="CostCenter"/>
    &amp;
    <xsl:choose>
      <xsl:when test="DebitAmount = '0.00'">
      </xsl:when>
      <xsl:when test="DebitAmount != ''">
        <xsl:value-of select="format-number(DebitAmount,&quot;#'##0.00&quot;,'apodot')"/>
      </xsl:when>
      <xsl:otherwise>
      </xsl:otherwise>
    </xsl:choose>
    &amp;
    <xsl:choose>
      <xsl:when test="CreditAmount = '0.00'">
      </xsl:when>
      <xsl:when test="CreditAmount != ''">
        <xsl:value-of select="format-number(CreditAmount,&quot;#'##0.00&quot;,'apodot')"/>
      </xsl:when>
      <xsl:otherwise>
      </xsl:otherwise>
    </xsl:choose>
    &amp;
    <xsl:value-of select="EntryText"/>\\
    <xsl:if test="@doPageBreak = 'true' and following-sibling::*[1][self::Entry]">
      \\
      <xsl:choose>
        <xsl:when test="/Report/@lang = 'fr'">
          <xsl:text>Übertrag</xsl:text>
        </xsl:when>
        <xsl:when test="/Report/@lang = 'it'">
          <xsl:text>Übertrag</xsl:text>
        </xsl:when>
        <xsl:otherwise>
          <xsl:text>Übertrag</xsl:text>
        </xsl:otherwise>
      </xsl:choose>&amp;<xsl:value-of select="format-number(RunningSumDebitAmount,&quot;#'##0.00&quot;,'apodot')"/>&amp;<xsl:value-of select="format-number(RunningSumCreditAmount,&quot;#'##0.00&quot;,'apodot')"/>\\
      \pagebreak
    </xsl:if>
  </xsl:template>



</xsl:stylesheet>