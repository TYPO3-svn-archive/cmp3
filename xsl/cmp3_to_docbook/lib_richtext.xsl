<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:exsl="http://exslt.org/common"
	xmlns:fo="http://www.w3.org/1999/XSL/Format"
	xmlns:ng="http://docbook.org/docbook-ng"
	xmlns:db="http://docbook.org/ns/docbook"
	xmlns:rich="http://www.w3.org/1999/xhtml"
	exclude-result-prefixes="db ng exsl fo rich" version='1.0'>

<xsl:template name="richtext">
	<xsl:apply-templates mode="richtext" />
</xsl:template>

<!--

	BLOCK ELEMENTS

	TABLE
	P
	BLOCKQUOTE
	UL
	OL
	H1-H6
	Those should contain inline elements in specific selection

-->
	<!-- ###############################################
		<table>
			<thead>
			<tbody>
				<tr>
					<th>
					<td>
		-->
	<xsl:template mode="richtext" match="table">

		<!-- this only works if we assume that the last table row does not contain colspans -->
		<xsl:variable name="number-cols" select="count(tbody/tr[last()]/td)" />

		<xsl:if test="$number-cols &gt; 0">
			<informaltable>
				<xsl:choose>
					<xsl:when test="@class">
						<xsl:attribute name="class">
							<xsl:value-of select="@class" />
						</xsl:attribute>
					</xsl:when>
				</xsl:choose>

				<tgroup>
					<xsl:attribute name="cols">
						<xsl:value-of select="$number-cols" />
					</xsl:attribute>
					<xsl:apply-templates select="thead" />
					<xsl:apply-templates select="tbody" />
				</tgroup>
			</informaltable>
		</xsl:if>
	</xsl:template>

	<xsl:template mode="richtext" match="thead">
		<thead>
			<xsl:apply-templates select="tr" />
		</thead>
	</xsl:template>

	<xsl:template mode="richtext" match="tbody">
		<tbody>
			<xsl:apply-templates select="tr" />
		</tbody>
	</xsl:template>

	<xsl:template mode="richtext" match="tr">
		<row>
			<xsl:apply-templates select="th" />
			<xsl:apply-templates select="td" />
		</row>
	</xsl:template>

	<xsl:template mode="richtext" match="th">
		<entry>
			<xsl:choose>
				<xsl:when test="@class">
					<xsl:attribute name="class">
						<xsl:value-of select="@class" />
					</xsl:attribute>
				</xsl:when>
			</xsl:choose>
			<xsl:value-of select="." />
		</entry>
	</xsl:template>

	<xsl:template mode="richtext" match="td">
		<entry>
			<xsl:choose>
				<xsl:when test="@class">
					<xsl:attribute name="class">
						<xsl:value-of select="@class" />
					</xsl:attribute>
				</xsl:when>
			</xsl:choose>
			<xsl:value-of select="." />
		</entry>
	</xsl:template>


	<!-- ###############################################
		PARAGRAPH
		-->
	<xsl:template mode="richtext" match="rich:p">
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


	<!-- ###############################################
		BLOCKQUOTE
		Using the blockquote element. Formatting needs to be done.
		Alternatively, we could use a <para> tag with a specific class="blockquote".
		-->
	<xsl:template mode="richtext" match="blockquote">
		<blockquote>
			<xsl:value-of select="." />
		</blockquote>
	</xsl:template>


	<!-- ###############################################
		UNORDERED LIST
		-->
	<xsl:template mode="richtext" match="ul">
		<itemizedlist>
			<xsl:apply-templates select="li" />
		</itemizedlist>
	</xsl:template>


	<!-- ###############################################
		ORDERED LIST
		-->
	<xsl:template mode="richtext" match="ol">
		<orderedlist>
			<xsl:apply-templates select="li" />
		</orderedlist>
	</xsl:template>


	<!-- ###############################################
		HEADER 1
		-->
	<xsl:template mode="richtext" match="h1">
		<para class="header1">
			<xsl:value-of select="." />
		</para>
	</xsl:template>


	<!-- ###############################################
		HEADER 2
		-->
	<xsl:template mode="richtext" match="h2">
		<para class="header2">
			<xsl:value-of select="." />
		</para>
	</xsl:template>


	<!-- ###############################################
		HEADER 3
		-->
	<xsl:template mode="richtext" match="h3">
		<para class="header3">
			<xsl:value-of select="." />
		</para>
	</xsl:template>


	<!-- ###############################################
		HEADER 4
		-->
	<xsl:template mode="richtext" match="h4">
		<para class="header4">
			<xsl:value-of select="." />
		</para>
	</xsl:template>


	<!-- ###############################################
		HEADER 5
		-->
	<xsl:template mode="richtext" match="h5">
		<para class="header5">
			<xsl:value-of select="." />
		</para>
	</xsl:template>


	<!-- ###############################################
		HEADER 6
		-->
	<xsl:template mode="richtext" match="h6">
		<para class="header6">
			<xsl:value-of select="." />
		</para>
	</xsl:template>



<!--

	INLINE ELEMENTS

	IMAGE
	I
	STRONG
	SPAN
	LI
	A
	Those can not contain children

-->


	<!-- ###############################################
		IMAGE -->

	<xsl:template mode="richtext" name="imagecol">
		<!-- <xsl:param name="imageWidth" select="8.3"/> -->
		<xsl:param name="imageWidth" select="6.3"/>

		<!-- <xsl:param name="imageHeight"/> -->
		<!-- z.B. mm oder cm -->
		<xsl:param name="myunit" select="'cm'"/>
		<mediaobject>
			<imageobject>
<!--  FIXME hack: width="15cm" -->
				<imagedata width="15cm">
					<xsl:attribute name="fileref">
						<xsl:value-of select="alias" />
					</xsl:attribute>
					<xsl:attribute name="align">
						<xsl:value-of select="@align" />
					</xsl:attribute>
					<xsl:attribute name="float">
						<xsl:value-of select="@float" />
					</xsl:attribute>

					<xsl:attribute name="contentwidth">
						<xsl:copy-of select="$imageWidth" />
						<xsl:copy-of select="$myunit" />
					</xsl:attribute>
					<!--
					<xsl:attribute name="contentdepth">
						<xsl:copy-of select="$imageHeight" />
						<xsl:copy-of select="$myunit" />
					</xsl:attribute>
					-->
				</imagedata>
			</imageobject>
			<caption>
				<xsl:value-of select="caption" />
			</caption>
		</mediaobject>
	</xsl:template>

	<xsl:template mode="richtext" match="image">
		<mediaobject>
			<imageobject>
<!--  FIXME hack: width="15cm" -->
				<imagedata width="15cm">
					<xsl:attribute name="fileref">
						<xsl:value-of select="alias" />
					</xsl:attribute>
					<xsl:attribute name="align">
						<xsl:value-of select="@align" />
					</xsl:attribute>
					<xsl:attribute name="float">
						<xsl:value-of select="@float" />
					</xsl:attribute>
				</imagedata>
			</imageobject>
			<caption>
				<xsl:value-of select="caption" />
			</caption>
		</mediaobject>
	</xsl:template>

	<!-- ###############################################
		ITALIC
		DocBook does not know inline textformatting like <i>, <u>, <b> and the like from HTML.
		The only possibility is the emphasis element which is rendered italic by default
		-->
	<xsl:template mode="richtext" match="i|em">
		<emphasis>
			<!-- <xsl:value-of select="." /> -->
			<xsl:apply-templates/>
		</emphasis>
	</xsl:template>


	<!-- ###############################################
		STRONG
		DocBook does not know inline textformatting like <i>, <u>, <b> and the like from HTML.
		The only possibility is the emphasis element which can have the role="strong | bold" attribute
		-->
	<xsl:template mode="richtext" match="strong|b|B">
		<emphasis role="strong">
			<!-- <xsl:value-of select="." /> -->
			<xsl:apply-templates/>
		</emphasis>
	</xsl:template>

	<!-- ###############################################
		ITALIC/STRONG
		DocBook does not know inline textformatting like <i>, <u>, <b> and the like from HTML.
		The only possibility is the emphasis element which is rendered italic by default
		-->
	<xsl:template mode="richtext" match="i/strong|i/b|i/B|em/strong|em/b|em/B">
		<emphasis><emphasis role="strong">
			<xsl:value-of select="." />
		</emphasis></emphasis>
	</xsl:template>

	<!-- ###############################################
		/STRONG/ITALIC
		DocBook does not know inline textformatting like <i>, <u>, <b> and the like from HTML.
		The only possibility is the emphasis element which is rendered italic by default
		-->
	<xsl:template mode="richtext" match="strong/i|b/i|B/i|strong/em|b/em|B/em">
		<emphasis role="strong"><emphasis>
			<xsl:value-of select="." />
		</emphasis></emphasis>
	</xsl:template>

	<!-- ###############################################
		SPAN
		As we have not inline formatting we use the next best logical equivalent <emphasis> to transfer <span>'s
		The <emphasis> Tag is only rendered, if the <span> has the class attribute set. This is kept.
		Otherwise the contents of the <span> tag transfered directly
		-->
	<xsl:template mode="richtext" match="span">
		<xsl:choose>
			<xsl:when test="@class">
				<emphasis>
					<xsl:attribute name="class">
						<xsl:value-of select="@class" />
					</xsl:attribute>
					<xsl:value-of select="." />
				</emphasis>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="." />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


	<!-- ###############################################
		LIST ITEM
		-->
	<xsl:template mode="richtext" match="li">
		<listitem>
			<para>
				<xsl:value-of select="." />
			</para>
		</listitem>
	</xsl:template>


	<!-- ###############################################
		LINKs
		-->
	<xsl:template mode="richtext" match="a">
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
