<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="xml" />

	<!-- ########################################## -->
	<!--
		Dieses xslt Sheet wandelt die Baumstruktur in eine flache Struktur um.
	-->
	<!--
		Funktionsweise der einzelnen Templates: matching über record-Knoten
		und Attribut
	-->
	
	<!-- Hier werden die meta Daten erfasst -->


	<xsl:template match="meta">
		<xsl:copy-of select="."/>
	</xsl:template>




	<!-- Hier wird das content Tag erfasst, welche die Struktur vorgibt -->

	<xsl:template match="//content[@type='tree']">
		<content>
			<xsl:attribute name="type">
 				<xsl:text>flat</xsl:text>
 			</xsl:attribute>
			<xsl:apply-templates />
		</content>
	</xsl:template>

	<!-- Hier werden die record Knoten erfasst-->
	<xsl:template match="//record[@type]">
		<record>
			<xsl:attribute name="type">
				<xsl:value-of select="./@type" />
			</xsl:attribute>
			<xsl:copy-of select="./*"/>
		</record>
	</xsl:template>
	
	<xsl:template match="//record[@type and @subtype]"> 
		<record>
			<xsl:attribute name="type">
				<xsl:value-of select="./@type" />
			</xsl:attribute>
			<xsl:attribute name="subtype">
				<xsl:value-of select="./@subtype" />
			</xsl:attribute>
			<xsl:copy-of select="./*"/>
		</record>
	</xsl:template>
	
		<xsl:template match="//record[@name and @type and @subtype]"> 
		<record>
			<xsl:attribute name="name">
				<xsl:value-of select="./@name" />
			</xsl:attribute>
			<xsl:attribute name="type">
				<xsl:value-of select="./@type" />
			</xsl:attribute>
			<xsl:attribute name="subtype">
				<xsl:value-of select="./@subtype" />
			</xsl:attribute>
			<xsl:copy-of select="./*"/>
		</record>
	</xsl:template>

	<!--
		##############################################################################
	-->
	<xsl:template match="cmp3document">
		<cmp3document>
			<xsl:apply-templates select="meta" />
			<xsl:apply-templates select="//content[@type='tree']" />
		</cmp3document>

	</xsl:template>
</xsl:stylesheet>