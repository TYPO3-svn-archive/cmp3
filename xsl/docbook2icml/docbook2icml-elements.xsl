<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">



    <!-- TODO begin -->

    <xsl:template match="programlisting">
        <ParagraphStyleRange AppliedParagraphStyle="ParagraphStyle/programlisting">
            <xsl:value-of select="text()"/>
            <Br/>
        </ParagraphStyleRange>
    </xsl:template>

    <!-- TODO end -->




    <!-- Book headings -->

    <xsl:template match="chapter/title|/article/section/title">
        <ParagraphStyleRange AppliedParagraphStyle="ParagraphStyle/Subheadings%3aSubheading 1">
            <CharacterStyleRange AppliedCharacterStyle="CharacterStyle/$ID/[No character style]">
                <Content>
                    <xsl:value-of select="."/>
                </Content>
                <Br/>
            </CharacterStyleRange>
        </ParagraphStyleRange>
    </xsl:template>
    <xsl:template match="sect1/title|/article/section/section/title">
        <ParagraphStyleRange AppliedParagraphStyle="ParagraphStyle/Subheadings%3aSubheading 2">
            <CharacterStyleRange AppliedCharacterStyle="CharacterStyle/$ID/[No character style]">
                <Content>
                    <xsl:value-of select="."/>
                </Content>
                <Br/>
            </CharacterStyleRange>
        </ParagraphStyleRange>
    </xsl:template>
    <xsl:template match="sect2/title|/article/section/section/section/title">
        <ParagraphStyleRange AppliedParagraphStyle="ParagraphStyle/Subheadings%3aSubheading 3">
            <CharacterStyleRange AppliedCharacterStyle="CharacterStyle/$ID/[No character style]">
                <Content>
                    <xsl:value-of select="."/>
                </Content>
                <Br/>
            </CharacterStyleRange>
        </ParagraphStyleRange>
    </xsl:template>




    <!-- Paragraph -->
    <!-- This template matchs for para tags and chooses the output format from options below(see xsl:choose) -->

    <xsl:template match="para" name="para">
        <ParagraphStyleRange
            AppliedParagraphStyle="ParagraphStyle/Paragraphs%3aParagraph - without indent">
            <xsl:for-each select="*|text()">
                <xsl:choose>
                    <xsl:when test="self::ulink|self::link">
                        <CharacterStyleRange AppliedCharacterStyle="CharacterStyle/Url">
                            <Content>
                                <xsl:value-of select="normalize-space(.)"/>
                                <xsl:if test="position() != last()">
                                    <xsl:text> </xsl:text>
                                </xsl:if>
                            </Content>
                        </CharacterStyleRange>
                    </xsl:when>
                    <!-- TODO begin -->
                    <xsl:when test="self::a[@class=&quot;footnote-reference&quot;]">
                        <CharacterStyleRange AppliedCharacterStyle="CharacterStyle/fnref">
                            <Content>
                                <xsl:value-of select="substring-before(substring-after(.,'['),']')"/>
                                <xsl:if test="position() != last()">
                                    <xsl:text> </xsl:text>
                                </xsl:if>
                            </Content>
                        </CharacterStyleRange>
                    </xsl:when>
                    <xsl:when test="self::span[@class=&quot;table_figure&quot;]">
                        <CharacterStyleRange AppliedCharacterStyle="CharacterStyle/table_figure">
                            <Content>
                                <xsl:value-of select="normalize-space(.)"/>
                                <xsl:if test="position() != last()">
                                    <xsl:text> </xsl:text>
                                </xsl:if>
                            </Content>
                        </CharacterStyleRange>
                    </xsl:when>
                    <!-- TODO end -->
                    <xsl:when test="self::emphasis[@role=&quot;strong&quot;]">
                        <CharacterStyleRange
                            AppliedCharacterStyle="CharacterStyle/Text attributes%3aBold">
                            <Content>
                                <xsl:value-of select="normalize-space(.)"/>
                                <xsl:if test="position() != last()">
                                    <xsl:text> </xsl:text>
                                </xsl:if>
                            </Content>
                        </CharacterStyleRange>
                    </xsl:when>
                    <xsl:when test="self::emphasis[not(@role=&quot;strong&quot;)]">
                        <CharacterStyleRange
                            AppliedCharacterStyle="CharacterStyle/Text attributes%3aItalic">
                            <Content>
                                <xsl:value-of select="normalize-space(.)"/>
                                <xsl:if test="position() != last()">
                                    <xsl:text> </xsl:text>
                                </xsl:if>
                            </Content>
                        </CharacterStyleRange>
                    </xsl:when>
                    <xsl:when test="self::text()">
                        <CharacterStyleRange
                            AppliedCharacterStyle="CharacterStyle/[No character style]">
                            <Content>
                                <xsl:value-of select="normalize-space(.)"/>
                                <xsl:if test="position() != last()">
                                    <xsl:text> </xsl:text>
                                </xsl:if>
                            </Content>
                        </CharacterStyleRange>
                    </xsl:when>
                </xsl:choose>
            </xsl:for-each>
            <Br/>
        </ParagraphStyleRange>
    </xsl:template>


    <!-- TODO begin -->

    <xsl:template match="table[@class=&quot;docutils footnote&quot;]/tbody/tr">
        <ParagraphStyleRange AppliedParagraphStyle="ParagraphStyle/footnote">
            <xsl:for-each select="td">
                <xsl:choose>
                    <xsl:when test="self::td[@class=&quot;label&quot;]">
                        <CharacterStyleRange>
                            <Content><xsl:value-of
                                    select="substring-before(substring-after(.,'['),']')"/>.
                            </Content>
                        </CharacterStyleRange>
                    </xsl:when>
                    <xsl:otherwise>
                        <Content>
                            <xsl:value-of select="."/>
                        </Content>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </ParagraphStyleRange>
        <Br/>
    </xsl:template>

    <!-- TODO end -->

    <!-- done begin -->
    <!-- This template translate a docbook bullet list to our icml format -->
    <!-- we support 2 levels only -->
    <xsl:template match="itemizedlist">
        <ParagraphStyleRange AppliedParagraphStyle="ParagraphStyle/Other%3aBullet list - level 1">
            <CharacterStyleRange AppliedCharacterStyle="CharacterStyle/$ID/[No character style]">

                <!-- Loops every listitem in the bullet list and print out the content of a tag named <para> in docbook -->

                <xsl:for-each select="listitem">

                    <Content>
                        <xsl:value-of select="normalize-space(translate(para,'&#10;',''))"/>
                    </Content>
                    <Br/>

                    <!--This Loop managed the second level of a bullet list-->
                    <!--It selected the first itemizedlist in the current node(listitem) and calls a template for that-->
                    <!--The xsl:text methods are needed to get the right icml format   -->

                    <xsl:for-each select="itemizedlist[1]">
                        <xsl:text disable-output-escaping="yes">&lt;/CharacterStyleRange>
		&lt;/ParagraphStyleRange></xsl:text>

                        <xsl:call-template name="itemizedlist2"/>

                        <xsl:text disable-output-escaping="yes">
    &lt;ParagraphStyleRange AppliedParagraphStyle="ParagraphStyle/Other%3aBullet list - level 1">
        &lt;CharacterStyleRange AppliedCharacterStyle="CharacterStyle/$ID/[No character style]">
			            </xsl:text>
                    </xsl:for-each>


                </xsl:for-each>
            </CharacterStyleRange>
        </ParagraphStyleRange>
    </xsl:template>

    <!-- This template is needed for the second level of a bullet list -->
    <xsl:template name="itemizedlist2">
        <ParagraphStyleRange AppliedParagraphStyle="ParagraphStyle/Other%3aBullet list - level 2">
            <CharacterStyleRange AppliedCharacterStyle="CharacterStyle/$ID/[No character style]">
                <xsl:for-each select="listitem">

                    <Content>
                        <xsl:value-of select="normalize-space(translate(.,'&#10;',''))"/>
                    </Content>

                    <Br/>

                </xsl:for-each>
            </CharacterStyleRange>
        </ParagraphStyleRange>
    </xsl:template>


    <!-- DONE end-->
    <!-- Similar to itemizedlist, but here it is a numbered list with two levels -->
    <xsl:template match="orderedlist">
        <ParagraphStyleRange AppliedParagraphStyle="ParagraphStyle/Other%3aNumbered list - level 1">
            <CharacterStyleRange AppliedCharacterStyle="CharacterStyle/$ID/[No character style]">
                <xsl:for-each select="listitem">

                    <Content>
                        <xsl:value-of select="normalize-space(translate(para,'&#10;',''))"/>
                    </Content>
                    <Br/>


                    <xsl:for-each select="orderedlist[1]">
                        <xsl:text disable-output-escaping="yes">&lt;/CharacterStyleRange>
                            &lt;/ParagraphStyleRange></xsl:text>

                        <xsl:call-template name="orderedlist2"/>

                        <xsl:text disable-output-escaping="yes">
                            &lt;ParagraphStyleRange AppliedParagraphStyle="ParagraphStyle/Other%3aNumbered list - level 1">
                            &lt;CharacterStyleRange AppliedCharacterStyle="CharacterStyle/$ID/[No character style]">
                        </xsl:text>
                    </xsl:for-each>


                </xsl:for-each>
            </CharacterStyleRange>
        </ParagraphStyleRange>
    </xsl:template>


    <xsl:template name="orderedlist2">
        <ParagraphStyleRange AppliedParagraphStyle="ParagraphStyle/Other%3aNumbered list - level 2">
            <CharacterStyleRange AppliedCharacterStyle="CharacterStyle/$ID/[No character style]">
                <xsl:for-each select="listitem">

                    <Content>
                        <xsl:value-of select="normalize-space(translate(.,'&#10;',''))"/>
                    </Content>

                    <Br/>

                </xsl:for-each>
            </CharacterStyleRange>
        </ParagraphStyleRange>
    </xsl:template>



    <!-- done end -->




    <!-- TODO begin -->

    <xsl:template match="span[@class=&quot;table_figure&quot;]">
        <ParagraphStyleRange>
            <CharacterStyleRange AppliedCharacterStyle="CharacterStyle/table_figure">
                <Content>
                    <xsl:apply-templates/>
                </Content>
            </CharacterStyleRange>
        </ParagraphStyleRange>
    </xsl:template>

    <xsl:template match="table[@class=&quot;docutils&quot;]/tbody">
        <ParagraphStyleRange AppliedParagraphStyle="ParagraphStyle/table">
            <CharacterStyleRange>
                <Br/>
                <Table HeaderRowCount="0" FooterRowCount="0"
                    AppliedTableStyle="TableStyle/$ID/[Basic Table]"
                    TableDirection="LeftToRightDirection">
                    <xsl:attribute name="BodyRowCount">
                        <xsl:value-of select="count(child::tr)"/>
                    </xsl:attribute>
                    <xsl:attribute name="ColumnCount">
                        <xsl:value-of select="count(child::tr[3]/td)"/>
                    </xsl:attribute>
                    <xsl:variable name="columnWidth" select="$table-width div count(tr[3]/td)"/>
                    <xsl:for-each select="tr[3]/td">
                        <Column Name="{position() - 1}" SingleColumnWidth="{$columnWidth}"/>
                    </xsl:for-each>

                    <xsl:for-each select="tr">
                        <xsl:variable name="rowNum" select="position() - 1"/>
                        <xsl:for-each select="td">
                            <xsl:variable name="colNum" select="position() - 1"/>
                            <xsl:choose>
                                <xsl:when test="@colspan">
                                    <Cell Name="{$colNum}:{$rowNum}" RowSpan="1"
                                        ColumnSpan="{@colspan}"
                                        AppliedCellStyle="CellStyle/$ID/[None]"
                                        AppliedCellStylePriority="0">
                                        <ParagraphStyleRange
                                            AppliedParagraphStyle="ParagraphStyle/$ID/NormalParagraphStyle">
                                            <CharacterStyleRange
                                                AppliedCharacterStyle="CharacterStyle/$ID/[No character style]">
                                                <Content>
                                                  <xsl:value-of select="*|text()"/>
                                                </Content>
                                            </CharacterStyleRange>
                                        </ParagraphStyleRange>
                                    </Cell>
                                </xsl:when>
                                <xsl:otherwise>
                                    <Cell Name="{$colNum}:{$rowNum}" RowSpan="1" ColumnSpan="1"
                                        AppliedCellStyle="CellStyle/$ID/[None]"
                                        AppliedCellStylePriority="0">
                                        <ParagraphStyleRange
                                            AppliedParagraphStyle="ParagraphStyle/$ID/NormalParagraphStyle">
                                            <CharacterStyleRange
                                                AppliedCharacterStyle="CharacterStyle/$ID/[No character style]">
                                                <Content>
                                                  <xsl:value-of select="*|text()"/>
                                                </Content>
                                            </CharacterStyleRange>
                                        </ParagraphStyleRange>
                                    </Cell>
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:for-each>
                    </xsl:for-each>
                </Table>
            </CharacterStyleRange>
        </ParagraphStyleRange>
    </xsl:template>


    <xsl:template match="tr">
        <xsl:if test="position() &gt; 2">
            <Br/>
        </xsl:if>
    </xsl:template>
    <xsl:template match="td">
        <xsl:if test="position() &gt; 2">
            <Br/>
        </xsl:if>
    </xsl:template>



    <!-- TODO end -->


    <!--

	<mediaobject>
		<imageobject>
			<imagedata width="15cm" fileref="_var_www_cmp3_htdocs_uploads_pics_Kap_02_2-2.jpg" align="left" float="none" />
		</imageobject>
		<caption>Ill. 2.2 Pige og dreng.</caption>
	</mediaobject>


	<mediaobject>
		<imageobject>
			<imagedata fileref="test.jpg" format="JPG"/>
		</imageobject>
	</mediaobject>


 -->




    <xsl:template match="imagedata">
        <ParagraphStyleRange>foo <CharacterStyleRange>
                <!--				<xsl:variable name="imgwidth" select="@width div 2"/>-->
                <!--				<xsl:variable name="imgheight" select="@height div 2"/>-->
                <xsl:variable name="imgwidth" select="'500'"/>
                <xsl:variable name="imgheight" select="'300'"/>
                <xsl:variable name="halfwidth" select="$imgwidth div 2"/>
                <xsl:variable name="halfheight" select="$imgheight div 2"/>
                <Rectangle Self="uec" ItemTransform="1 0 0 1 {$halfwidth} -{$halfheight}">
                    <Properties>
                        <PathGeometry>
                            <GeometryPathType PathOpen="false">
                                <PathPointArray>
                                    <PathPointType Anchor="-{$halfwidth} -{$halfheight}"
                                        LeftDirection="-{$halfwidth} -{$halfheight}"
                                        RightDirection="-{$halfwidth} -{$halfheight}"/>
                                    <PathPointType Anchor="-{$halfwidth} {$halfheight}"
                                        LeftDirection="-{$halfwidth} {$halfheight}"
                                        RightDirection="-{$halfwidth} {$halfheight}"/>
                                    <PathPointType Anchor="{$halfwidth} {$halfheight}"
                                        LeftDirection="{$halfwidth} {$halfheight}"
                                        RightDirection="{$halfwidth} {$halfheight}"/>
                                    <PathPointType Anchor="{$halfwidth} -{$halfheight}"
                                        LeftDirection="{$halfwidth} -{$halfheight}"
                                        RightDirection="{$halfwidth} -{$halfheight}"/>
                                </PathPointArray>
                            </GeometryPathType>
                        </PathGeometry>
                    </Properties>
                    <Image Self="ue6" ItemTransform="1 0 0 1 -{$halfwidth} -{$halfheight}">
                        <Properties>
                            <Profile type="string">$ID/Embedded</Profile>
                            <GraphicBounds Left="0" Top="0" Right="{$imgwidth}"
                                Bottom="{$imgheight}"/>
                        </Properties>
                        <Link Self="ueb" LinkResourceURI="file:///{@fileref}"/>
                    </Image>
                </Rectangle>
                <Br/>
            </CharacterStyleRange>
        </ParagraphStyleRange>
    </xsl:template>



</xsl:stylesheet>
