<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version='2.0' xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:xs="http://www.w3.org/2001/XMLSchema"

	xmlns:fo="http://www.w3.org/1999/XSL/Format"
	xmlns:ng="http://docbook.org/docbook-ng"
	xmlns:db="http://docbook.org/ns/docbook"
	xmlns:cmp3="http://www.bitmotion.de/cmp3/cmp3document"
	xmlns:rich="http://www.w3.org/1999/xhtml"
	exclude-result-prefixes="xs db ng fo cmp3 rich">


<!--
	<xsl:include href="genericTemplate.xsl"/>
	<xsl:include href="nonGenTemplate.xsl"/>
	<xsl:import href="../html2docbook.xsl"/>
	<xsl:import href="../html2db/html2db.xsl"/>
	<xsl:import href="../functx-1.0-doc-2007-01.xsl"/>
-->

	<xsl:import href="lib.xsl"/>
	<xsl:import href="../html2docbook.xsl"/>

	<xsl:output method="xml" encoding="UTF-8" indent="yes" />

	<xsl:variable name="language">de</xsl:variable>
	<xsl:variable name="country">de</xsl:variable>

	<!-- ################################################ -->
	<!-- ######################################################### -->
	<!--
		Die folgenden Templates geben den Inhalt der verschiedenen Knoten aus
		(kleinste Strukturebene)
	-->
	<!-- TODO type hat geringere priorität als format -->

	<xsl:template mode="field" match="//cmp3:field[@type='text']" priority="-.1">
		<xsl:if test="cmp3:value != ''">
			<para>
				<xsl:value-of select="normalize-space(cmp3:value)" />
			</para>
		</xsl:if>
	</xsl:template>

	<xsl:template mode="field" match="//cmp3:field[@format='line']">
		<xsl:if test="cmp3:value != ''">
			<para>
				<xsl:value-of select="normalize-space(cmp3:value)" />
			</para>
		</xsl:if>
	</xsl:template>


	<xsl:template mode="field" match="//cmp3:field[@format='multiline']">
		<xsl:if test="cmp3:value != ''">
			<para>
				<xsl:call-template name="multiline">
					<xsl:with-param name="s" select="cmp3:value" />
				</xsl:call-template>
			</para>
		</xsl:if>
	</xsl:template>





















<!--TODO bug in saxon

	Date prefixes with [Language: en]

	This is actually now working correctly in Saxon in cases where your default
	Locale is say Germany or France - any locale that Saxon supports. The set of
	locales that Saxon supports is open-ended - it depends on what localisation
	modules have been registered with the Configuration.

	In the case of Saxon, the implementation-defined default is taken from the
	Java default locale.



	you can set the default Java language
	and country from the command line using the switches

	-Duser.language=de -Duser.region=DE

	They can also be overridden in the Saxon configuration file:

	<localizations defaultLanguage="de" defaultCountry="DE">...

	see http://www.devcomments.com/format-date-for-non-English-outputs-en-at34290.htm







Since I am calling from the command line, I have those two options:

java -jar "path\saxon9pe.jar" -s:"path\src.xml" -xsl:"path\some.xsl"
-o:"path\out.xml" -config:"path\saxon.cfg"

This worked fine. It seemed I had to specify the full path to the config file.
Although I have it stored in the same folder as saxon9pe.jar, it was not found
when specifying -config:saxon.cfg . Are relative paths allowed and how are they
resolved?

The other option

java -Duser.language=de -Duser.region=DE -jar "path\saxon9pe.jar"
-s:"path\src.xml" -xsl:"path\some.xsl" -o:"D:\Projekte\8148 GDV
StructOpt\Playground\Testdateien\HTML\log.xml"

did not work for me (the [Language: en] still shows). Did I misunderstand the
necessary syntax?




-->
	<xsl:template mode="field" match="//cmp3:field[@format='date']">
		<xsl:if test="cmp3:value != ''">
			<para>
				<xsl:variable name="date" select="cmp3:value" as="xs:dateTime"/>
				<xsl:value-of select="format-dateTime($date, '[D].[M].[Y]', $language, (), $country)" />
			</para>
		</xsl:if>
	</xsl:template>

	<xsl:template mode="field" match="//cmp3:field[@format='datetime']">.
		<xsl:if test="cmp3:value != ''">
			<para>
				<xsl:variable name="date" select="cmp3:value" as="xs:dateTime"/>
				<xsl:value-of select="format-dateTime($date, '[D].[M].[Y]', $language, (), $country)" />
			</para>
		</xsl:if>
	</xsl:template>

	<xsl:template mode="field" match="//cmp3:field[@format='rich']">
		<xsl:if test="cmp3:value != ''">
			<!-- The template is called with the field node but richtext matches html namespace anyway so meta/value is ignored-->
			<xsl:call-template name="richtext" />
		</xsl:if>
	</xsl:template>


	<!-- ################################################ -->
	<!-- 			MAIN		 						  -->

	<xsl:template match="/">
		<article>
			<xsl:apply-templates select="/cmp3:cmp3document/cmp3:content"/>
		</article>
	</xsl:template>


	<xsl:template match="//cmp3:record">
		<xsl:apply-templates mode="field" select="//cmp3:field"/>
	</xsl:template>


	<xsl:template match="//cmp3:record[@type='tt_content']">
		<xsl:apply-templates mode="field" select="./cmp3:field[@name='tstamp']"/>
		<xsl:apply-templates mode="field" select="./cmp3:field[@name='header']"/>
		<xsl:apply-templates mode="field" select="./cmp3:field[@name='imagecaption']"/>
		<xsl:apply-templates mode="field" select="./cmp3:field[@name='bodytext']"/>
	</xsl:template>



</xsl:stylesheet>