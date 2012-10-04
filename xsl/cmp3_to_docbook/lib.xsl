<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version='2.0' xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:xs="http://www.w3.org/2001/XMLSchema"
	xmlns:cmp3="http://www.bitmotion.de/cmp3/cmp3document"
	exclude-result-prefixes="xs cmp3">



	<xsl:output method="xml" encoding="UTF-8" indent="yes" />




	<!--
		GENERAL STRING FUNCTIONS
	-->


	<xsl:template name="left-trim">
		<xsl:param name="s" />
		<xsl:value-of select="replace($s, '^\s+', '')"/>
	</xsl:template>


	<xsl:template name="right-trim">
		<xsl:param name="s" />
		<xsl:value-of select="replace($s, '\s+$', '')"/>
	</xsl:template>


	<xsl:template name="trim">
		<xsl:param name="s" />
		<xsl:value-of select="replace(replace($s,'\s+$',''),'^\s+','')"/>
	</xsl:template>



	<!--
		SPECIAL STRING FUNCTIONS
	-->


	<!--
		trim and change newlines in <?linebreak?>

		todo multiple linebreaks will not converted to one
	-->
	<xsl:template name="multiline">

		<xsl:param name="s" />

		<xsl:param name="strimmed">
			<xsl:call-template name="trim">
				<xsl:with-param name="s" select="$s" />
			</xsl:call-template>
		</xsl:param>


		<xsl:analyze-string select="$strimmed" regex="\n">
			<xsl:matching-substring>
				<xsl:processing-instruction
					name="linebreak"><?linebreak?></xsl:processing-instruction>
				<xsl:value-of select="."/>
			</xsl:matching-substring>
			<xsl:non-matching-substring>
				<xsl:value-of select="."/>
			</xsl:non-matching-substring>
		</xsl:analyze-string>
	</xsl:template>


</xsl:stylesheet>