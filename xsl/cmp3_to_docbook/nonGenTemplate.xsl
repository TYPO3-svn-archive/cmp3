<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:exsl="http://exslt.org/common"
	xmlns:fo="http://www.w3.org/1999/XSL/Format"
	xmlns:ng="http://docbook.org/docbook-ng"
	xmlns:db="http://docbook.org/ns/docbook"
	exclude-result-prefixes="db ng exsl fo" version='1.0'>

	<!-- Dieses xsl sheet ist fuer feste Tags gedacht, z.B. html tags -->
	<!-- TODO: html tags einfuegen, die noch fehlen, referenzliste siehe docu -->
	<!-- TODO: class attribut erfassen und in docbook tags einbinden -->

	<!-- ####################################################### -->
	<!-- Erfassung des <meta> Elements -->
	<xsl:template match="//meta" name="meta">
		<xsl:copy-of select="."/>
	</xsl:template>


	<!-- ######################################################## -->
	<!-- Hier werden die h1-h6 tags verarbeitet, copy-paste aus typo3_ce_lib.xsl mit folgender Aenderung: -->
	<!-- <para> wurde durch <title> ersetzt und das class Attribut wurde (erstmal) entfernt -->

	<xsl:template match="h1">
		<title class="header1">
			<xsl:value-of select="normalize-space(.)" />
		</title>
	</xsl:template>

	<xsl:template match="h2">
		<title class="header2">
			<xsl:value-of select="normalize-space(.)" />
		</title>
	</xsl:template>

	<xsl:template match="h3">
		<title class="header3">
			<xsl:value-of select="normalize-space(.)" />
		</title>
	</xsl:template>

	<xsl:template match="h4">
		<title class="header4">
			<xsl:value-of select="normalize-space(.)" />
		</title>
	</xsl:template>

	<xsl:template match="h5">
		<title class="header5">
			<xsl:value-of select="normalize-space(.)" />
		</title>
	</xsl:template>

	<xsl:template match="h6">
		<title class="header6">
			<xsl:value-of select="normalize-space(.)" />
		</title>
	</xsl:template>

	<!-- ############################################## -->
	<!-- Hier werden Tags verarbeitet, die im Attribut format=rich vorkommen koennte -->
	<!-- (Inline Tags?!?) -->

	<!-- Funktioniert, weiß aber nicht warum... da eine Anweisung zum Ausgeben des Inhaltes des p tags eigentlich fehlt-->
	<xsl:template match="p">
		<para>
			<xsl:choose>
				<xsl:when test="@class">
					<xsl:attribute name="class">
						<xsl:value-of select="@class" />
					</xsl:attribute>
				</xsl:when>
			</xsl:choose>
			<xsl:apply-templates />
		</para>
	</xsl:template>

	<xsl:template match="b">
		<emphasis role="strong">
			<xsl:value-of select="normalize-space(.)" />
		</emphasis>
	</xsl:template>

	<xsl:template match="a">
		<xsl:choose>
			<xsl:when test="starts-with(@href, 'http://')">
				<ulink>
					<xsl:attribute name="url">
						<xsl:value-of select="@href" />
					</xsl:attribute>
					<xsl:apply-templates select="*|text()" />
				</ulink>
			</xsl:when>
			<xsl:otherwise>
				<link>
					<xsl:attribute name="linkend">
						<xsl:value-of select="@href" />
					</xsl:attribute>
					<xsl:apply-templates select="*|text()" />
				</link>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

</xsl:stylesheet>