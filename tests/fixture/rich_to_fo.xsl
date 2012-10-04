<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:import href="../../xsl/rich_to_fo/rich_to_fo.xsl" />

	<xsl:output method="xml" version="1.0" encoding="utf-8" indent="yes"  />

	<xsl:template match="/">

<fo:root xmlns:fo="http://www.w3.org/1999/XSL/Format">
	<fo:layout-master-set>
		<fo:simple-page-master master-name="only">
			<fo:region-body region-name="xsl-region-body" margin="0.7in" padding="0" />
			<fo:region-before region-name="xsl-region-before" extent="0.7in" />
			<fo:region-after region-name="xsl-region-after" extent="0.7in" />
		</fo:simple-page-master>
	</fo:layout-master-set>
	<fo:page-sequence master-reference="only">

		<fo:flow flow-name="xsl-region-body">


		<xsl:call-template name="rich-to-fo" />

		</fo:flow>
	</fo:page-sequence>
</fo:root>



	</xsl:template>

</xsl:stylesheet>