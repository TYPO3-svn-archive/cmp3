<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<!-- Diese xslt sheets werden gebraucht, um den inhalt der felder auszugeben -->
	<!-- wird evtl. noch geändert in weitere sheets -->

	<xsl:include href="nonGenTemplate.xsl" />

	<!-- TODO: ist noch nicht so gewollt, inhalt der when anweisung zu testzwecken -->
	<!-- 	   Hier werden jetzt die feldknoten speziell umgewandelt(also nicht immer para) -->
	<!--       Wenn das fertig und "fest" ist, muss das in der generic transformation auch eingefügt werden??? -->
	<xsl:template match="//record/field">
		<xsl:choose>
			<xsl:when test="./@name='title'">
				<title>
					<xsl:value-of select="." />
				</title>
			</xsl:when>
			<xsl:when test="./@name='subtitle'">
				<subtitle>
					<xsl:value-of select="." />
				</subtitle>
			</xsl:when>
			<xsl:when test="./@name='description'">
				<para>
					<xsl:value-of select="." />
				</para>
			</xsl:when>
			<xsl:when test="./@name='abstract'">
				<para>
					<xsl:value-of select="." />
				</para>
			</xsl:when>
			<xsl:when test="./@name='author'">
				<author>
					<xsl:value-of select="." />
				</author>
			</xsl:when>
			<xsl:when test="./@name='lastUpdated'">
				<date>
					<xsl:value-of select="." />
				</date>
			</xsl:when>
			<xsl:when test="./@name='header'">
				<xsl:apply-templates />
			</xsl:when>
			<xsl:when test="./@name='bodytext'">
				<xsl:apply-templates />
			</xsl:when>
		</xsl:choose>
		
	</xsl:template>

</xsl:stylesheet>