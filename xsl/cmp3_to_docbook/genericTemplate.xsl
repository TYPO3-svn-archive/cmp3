<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:exsl="http://exslt.org/common" xmlns:fo="http://www.w3.org/1999/XSL/Format"
	xmlns:ng="http://docbook.org/docbook-ng" xmlns:db="http://docbook.org/ns/docbook"
	exclude-result-prefixes="db ng exsl fo" version='1.0'>
	<!--
		TODO: neue attribute einfuegen (oder komplett ueberarbeiten?!?)
		EDIT: ist das noetig, vorher wurden bei dem generischen matching
			  auch nicht die unterknoten von record tag beruecksichtigt
			  (jetzt waere das attribut name dafuer zustaendig)
	-->

	<!-- ###################################################### -->
	<!--   hier werden attribute erfasst, unabhängig vom knoten -->
	<!--
		Diese Templates haben als Inhalt andere Knoten im Eingangsdokument
		(Frage: Wie Endgültig darstellen in DocBook?)
	-->

	<xsl:template match="//*[@type='pages']">
		<para>
			<xsl:text> hier wurde ein  pages attribut gematcht</xsl:text>
			<xsl:apply-templates />
		</para>
	</xsl:template>

	<xsl:template match="//*[@type='tt_content']">
		<para>
			<xsl:text> hier wurde ein tt_content attribut gematcht</xsl:text>
			<xsl:apply-templates />
		</para>
	</xsl:template>

	<xsl:template match="//*[@type='tt_news']">
		<para>
			<xsl:text> hier wurde ein tt_news attribut gematcht</xsl:text>
			<xsl:apply-templates />
		</para>
	</xsl:template>

	<xsl:template match="//*[@type='tt_content' and @subtype='plugin_9']">
		<para>
			<xsl:text>matching: tt_content and plugin_9</xsl:text>
			<xsl:apply-templates />
		</para>
	</xsl:template>

	<xsl:template match="//*[@type='tt_content' and @subtype='text']">
		<para>
			<xsl:text>matching: tt_content and text</xsl:text>
			<xsl:apply-templates />
		</para>
	</xsl:template>

	<xsl:template match="//*[@type='tt_content' and @subtype='textpic']">
		<para>
			<xsl:text>matching: tt_content and textpic</xsl:text>
			<xsl:apply-templates />
		</para>
	</xsl:template>

	<xsl:template match="//*[@type='tt_news' and @subtype='news']">
		<para>
			<xsl:text>matching: tt_news and news</xsl:text>
			<xsl:apply-templates />
		</para>
	</xsl:template>

	<xsl:template match="//*[@type='tt_news' and @subtype='news2']">
		<para>
			<xsl:text>matching: tt_news and news2</xsl:text>
			<xsl:apply-templates />
		</para>
	</xsl:template>

	<!-- ######################################################### -->
	<!--
		Die folgenden Templates geben den Inhalt der verschiedenen Knoten aus
		(kleinste Strukturebene)
	-->
	<!-- TODO type hat geringere priorität als format -->

	<xsl:template match="//*[@type='text']" priority="-.1">
		<para>
			<xsl:text> hier wurde ein text attribut gematcht</xsl:text>
			<xsl:value-of select="normalize-space(value)" />
		</para>
	</xsl:template>

	<xsl:template match="//*[@format='line']">
		<para>
			<xsl:text> hier wurde ein format=line attribut gematcht</xsl:text>
			<xsl:value-of select="normalize-space(value)" />
		</para>
	</xsl:template>

	<xsl:template match="//*[@format='multiline']">
		<para>
			<xsl:text>gematcht: attribute format=multiline</xsl:text>
			<xsl:value-of select="normalize-space(value)" />
		</para>
	</xsl:template>

	<xsl:template match="//*[@format='rich']">
			<xsl:text>gematcht: attribute format=rich</xsl:text>
			<xsl:call-template name="richtext"/>
	</xsl:template>

	<xsl:template match="//*[@format='date']">
		<para>
			<xsl:text>gematcht: attribute format=date</xsl:text>
			<xsl:value-of select="normalize-space(value)" />
		</para>
	</xsl:template>

	<!-- ######################################################## -->
	<!-- Generisches Einbinden von bodytext und header (erkennbar am meta Attribut) -->


	<!-- Ueberschriften auch ohne <h1>-<h6> tags?!?  -->
	<xsl:template match="//*[@type='text' and @format='line' and @meta='header']">
		<xsl:text>matchs: text line header</xsl:text>
		<xsl:apply-templates />
	</xsl:template>

	<xsl:template match="//*[@type='text' and @format='rich' and @meta='body']">
		<xsl:text>matchs: text rich body</xsl:text>
		<xsl:apply-templates />
	</xsl:template>

</xsl:stylesheet>