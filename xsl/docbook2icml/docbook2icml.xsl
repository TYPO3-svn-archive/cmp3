<?xml version="1.0" encoding="UTF-8"?>
<!--
This script is copyright 2009 by John W. Maxwell, Meghan MacDonald,
and Travis Nicholson at Simon Fraser University's Master of Publishing
program.

Our intent is that this script be free licensed; you are hereby free to
use, study, modify, share, and redistribute this software as needed.
This script would be GNU GPL-licensed, except that small parts of it come
directly from Adobe's excellent IDML Cookbook and SDK and so aren't ours
to license. That said, the point of the thing is educational, so go to it.
See also http://www.adobe.com/devnet/indesign/

This script is not meant to be comprehensive or perfect. It was written
and tested in the context of the CCSP's Book Publishing 1 title, and content
from out ZWiki-based webCM system. To make it work with your content, you
will probably need to make modifications. That said, it is a working
proof-of-concept and a foundation for further work. - JMax June 5, 2009.

-->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.1" xmlns="http://docbook.org/docbook-ng" xmlns:db="http://docbook.org/ns/docbook" >


<xsl:import href="docbook2icml-idstyles.xsl"/>

<!--  xsl:import href="docbook2icml-elements.xsl"/-->

<xsl:output method="xml" encoding="UTF-8" indent="no"/>

<!-- TODO begin -->
   <xsl:param name="output"/>
    <xsl:param name="table-width">540</xsl:param>



    <xsl:template match="*|@*|comment()|processing-instruction()|text()">
                <xsl:apply-templates select="*|@*|comment()|processing-instruction()|text()"/>
    </xsl:template>
<!-- TODO end -->

<xsl:include href="docbook2icml-elements.xsl"/>






    <xsl:template match="/">
            <xsl:processing-instruction name="aid">style="50" type="snippet" readerVersion="6.0" featureSet="257" product="6.0(352)"</xsl:processing-instruction>
            <xsl:processing-instruction name="aid">SnippetType="InCopyInterchange"</xsl:processing-instruction>
            <Document DOMVersion="6.0" Self="d">

			<xsl:call-template name="idstyles" />

                <Story Self="ud4" AppliedTOCStyle="n" TrackChanges="false" StoryTitle="TODO Variable" AppliedNamedGrid="n">
                    <StoryPreference OpticalMarginAlignment="false" OpticalMarginSize="12" FrameType="TextFrameType" StoryOrientation="Horizontal" StoryDirection="LeftToRightDirection"/>
                    <InCopyExportOption IncludeGraphicProxies="true" IncludeAllResources="false"/>


<!-- Output Begin -->
					<xsl:apply-templates/>
                    <xsl:apply-imports/>
<!-- Output End -->

                </Story>
            </Document>
    </xsl:template>


</xsl:stylesheet>