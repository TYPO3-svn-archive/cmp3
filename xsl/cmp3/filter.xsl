<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">


<!--<xsl:include href="fieldTrans.xsl" />

-->
<!-- Hier sollte der Filter entstehen, jedoch ist dieses Sheet nur ein (bis jetzt gescheiterter) Versuch, -->
<!-- da X-Path Probleme bereitet Attribute von Knoten zu vergleichen.... -->

<!-- Die Idee des Filters ist, nur bestimmte "Feldtypen" in die Ausgabedatei zu übernehmen.
	 Dieses sollte mit Hilfe von Parametern geschehen, so dass man lediglich den String im Parameter
	 ändert, um ein anderes Feld in die Ausgabe zu schreiben. Unten sieht man den Ansatz dazu. 
	 Leider war es mir nicht möglich, mit X-Path das Attribut des Eingangsknoten mit dem Parameter
	 zu vergleichen (Bemerkung: Im Attribut des Eingangsknoten steht auch ein String). --> 

<xsl:template match="//record/field">
	<xsl:param name="attribute" />
	<xsl:text>test</xsl:text>
	<xsl:choose>
		<xsl:when test="./@name = attribute">
			<xsl:text>test2</xsl:text>
			<xsl:value-of select="."/>
			
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template match="cmp3document">
		<article>
			<xsl:apply-templates select="//record/field">
				<xsl:with-param name="attribute" select="'title'" />
			</xsl:apply-templates>
		</article>
</xsl:template>
</xsl:stylesheet>