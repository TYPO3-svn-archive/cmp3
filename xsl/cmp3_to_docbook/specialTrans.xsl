<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:exsl="http://exslt.org/common" xmlns:fo="http://www.w3.org/1999/XSL/Format"
	xmlns:ng="http://docbook.org/docbook-ng" xmlns:db="http://docbook.org/ns/docbook"
	exclude-result-prefixes="db ng exsl fo" version='1.0'>
	
	<xsl:output method="xml" encoding="UTF-8" indent="yes" /> 
	
	<xsl:include href="fieldTrans.xsl" />

	
	<xsl:template match="meta">
		<xsl:copy-of select="."/>
	</xsl:template>
	
	<!-- TODO: zusaetzliches stylesheet schreiben, um record kind knoten zu erfassen, filter einbauen -->
	<!-- hier werden record-felder erfasst -->
	<xsl:template match="//record[@type='pages']">
		<para>
			<xsl:apply-templates />
		</para> 
	</xsl:template>
	
	<xsl:template match="//reocord[@type='tt_content' and @subtype='plugin_9']">
		<para>
			<xsl:apply-templates />
		</para>
	</xsl:template>
	
	<xsl:template match="//record[@type='tt_news' and @subtype='news']">
		<para>
			<xsl:apply-templates />
		</para>
	</xsl:template>
	
	<xsl:template match="//record[@type='tt_news' and @subtype='news2']"> 
		<para>
			<xsl:apply-templates />
		</para>
	</xsl:template>
	
	<xsl:template match="//record[@type='tt_content' and @subtype='text']"> 
		<para>
			<xsl:apply-templates />
		</para>
	</xsl:template>
	
	<xsl:template match="//record[@type='tt_content' and @subtype='textpic']">
		<para>
			<xsl:apply-templates />
		</para>
	</xsl:template>

	<!-- ####################################################### -->
	<!-- MAIN -->
	
	<xsl:template match="cmp3document">
		<article>
			<xsl:apply-templates />
		</article> 
	</xsl:template>	
	
</xsl:stylesheet>