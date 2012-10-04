<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:cmp3="http://www.bitmotion.de/cmp3/cmp3document"
	exclude-result-prefixes="cmp3">

	<xsl:template match="//cmp3:record">
		<xsl:apply-templates select="//cmp3:field"/>
	</xsl:template>

	<xsl:template match="//cmp3:record[@type='tt_content']/field">
		<xsl:choose>
			<xsl:when test="./@name='header'">
				<xsl:apply-templates />
			</xsl:when>
			<xsl:when test="./@name='bodytext'">
				<xsl:apply-templates />
			</xsl:when>
			<xsl:otherwise>
<!--				nothing-->
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

</xsl:stylesheet>