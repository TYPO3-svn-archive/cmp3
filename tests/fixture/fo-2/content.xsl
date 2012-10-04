<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	xmlns:fo="http://www.w3.org/1999/XSL/Format"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:cmp3="http://www.bitmotion.de/cmp3/cmp3document"
	version="1.0">

	<xsl:include href="rich_to_fo.xsl" />
	<!--
	<xsl:include href="xhtml-to-xslfo.xsl" />
	 -->


	<!-- match -->

	<xsl:template match="//cmp3:field[@name='description']/cmp3:value">
		<xsl:call-template name="rich-to-fo"></xsl:call-template>
	</xsl:template>

	<xsl:template match="//cmp3:field[@name='short_desription']/cmp3:value">
		<xsl:call-template name="rich-to-fo"></xsl:call-template>
	</xsl:template>

	<xsl:template match="//cmp3:field[@name='pros']/cmp3:value">
		<xsl:call-template name="rich-to-fo"></xsl:call-template>
	</xsl:template>

	<xsl:template match="//cmp3:field[@name='technical_details']/cmp3:value">
		<xsl:call-template name="rich-to-fo"></xsl:call-template>
	</xsl:template>

	<xsl:template match="//cmp3:field[@name='freitext_kontakt']/cmp3:value">
		<xsl:call-template name="rich-to-fo"></xsl:call-template>
	</xsl:template>

	<xsl:template match="//cmp3:field[@name='freitext_produkt']/cmp3:value">
		<xsl:call-template name="rich-to-fo"></xsl:call-template>
	</xsl:template>



	<!-- general with mode -->

	<xsl:template name="image_zoom_fill_box">

		<!-- the field node -->
		<xsl:param name="field"/>
		<xsl:param name="box_form_factor"/>

		<xsl:attribute name="src">url('<xsl:value-of select="$field/cmp3:value"/>')</xsl:attribute>

		<xsl:variable name="form_factor">
			<xsl:value-of select="$field/cmp3:meta/cmp3:form_factor"/>
		</xsl:variable>


		<xsl:choose>
			<xsl:when test="$form_factor &gt; $box_form_factor">
				<xsl:attribute name="content-height">scale-to-fit</xsl:attribute>
			</xsl:when>
			<xsl:otherwise>
				<xsl:attribute name="content-width">scale-to-fit</xsl:attribute>
			</xsl:otherwise>
		</xsl:choose>

	</xsl:template>



	<!-- name -->

	<xsl:template name="description">
		<xsl:apply-templates select="//cmp3:field[@name='description']/cmp3:value"/>
	</xsl:template>

	<xsl:template name="short_description">
		<xsl:apply-templates select="//cmp3:field[@name='short_description']/cmp3:value"/>
	</xsl:template>

	<xsl:template name="pros">
		<xsl:apply-templates select="//cmp3:field[@name='pros']/cmp3:value"/>
	</xsl:template>

	<xsl:template name="technical_details">
		<xsl:apply-templates select="//cmp3:field[@name='technical_details']/cmp3:value"/>
	</xsl:template>

	<xsl:template name="title">
		<xsl:value-of select="//cmp3:field[@name='title']/cmp3:value"/>
	</xsl:template>

	<xsl:template name="subtitle">
		<xsl:value-of select="//cmp3:field[@name='subtitle']/cmp3:value"/>
	</xsl:template>

	<xsl:template name="subheading">
		<xsl:value-of select="//cmp3:field[@name='subheading']/cmp3:value"/>
	</xsl:template>

	<xsl:template name="subheading_extended">
		<xsl:value-of select="//cmp3:field[@name='subheading_extended']/cmp3:value"/>
	</xsl:template>

	<xsl:template name="print_url">
		file://<xsl:value-of select="//cmp3:field[@name='print_url']/cmp3:value"/>
	</xsl:template>

	<xsl:template name="print_page_image">
		<xsl:param name="box_form_factor"/>
		<xsl:call-template name="image_zoom_fill_box">
			<xsl:with-param name="field" select="//cmp3:field[@name='print_page_image']"/>
			<xsl:with-param name="box_form_factor" select="$box_form_factor"/>
		</xsl:call-template>
	</xsl:template>

	<xsl:template name="upload_image">
		<xsl:attribute name="src">url('<xsl:value-of select="//cmp3:field[@name='bild_upload_0']/cmp3:value"/>')</xsl:attribute>
	</xsl:template>

	<xsl:template name="product_image">
		<xsl:attribute name="src">url('<xsl:value-of select="//cmp3:field[@name='bild_produkt1_image']/cmp3:value"/>')</xsl:attribute>
	</xsl:template>


	<xsl:template name="product_image_1">
		<xsl:param name="box_form_factor"/>
		<xsl:call-template name="image_zoom_fill_box">
			<xsl:with-param name="field" select="//cmp3:field[@name='bilder_produkte1_image']"/>
			<xsl:with-param name="box_form_factor" select="$box_form_factor"/>
		</xsl:call-template>
	</xsl:template>

	<xsl:template name="product_image_2">
		<xsl:param name="box_form_factor"/>
		<xsl:call-template name="image_zoom_fill_box">
			<xsl:with-param name="field" select="//cmp3:field[@name='bilder_produkte2_image']"/>
			<xsl:with-param name="box_form_factor" select="$box_form_factor"/>
		</xsl:call-template>
	</xsl:template>

	<xsl:template name="detail_image_1">
		<xsl:param name="box_form_factor"/>
		<xsl:call-template name="image_zoom_fill_box">
			<xsl:with-param name="field" select="//cmp3:field[@name='bilder_details1_image' or @name='bilder4_details1_image']"/>
			<xsl:with-param name="box_form_factor" select="$box_form_factor"/>
		</xsl:call-template>
	</xsl:template>

	<xsl:template name="detail_image_2">
		<xsl:param name="box_form_factor"/>
		<xsl:call-template name="image_zoom_fill_box">
			<xsl:with-param name="field" select="//cmp3:field[@name='bilder_details2_image' or @name='bilder4_details2_image']"/>
			<xsl:with-param name="box_form_factor" select="$box_form_factor"/>
		</xsl:call-template>
	</xsl:template>

	<xsl:template name="detail_image_3">
		<xsl:param name="box_form_factor"/>
		<xsl:call-template name="image_zoom_fill_box">
			<xsl:with-param name="field" select="//cmp3:field[@name='bilder_details3_image' or @name='bilder4_details3_image']"/>
			<xsl:with-param name="box_form_factor" select="$box_form_factor"/>
		</xsl:call-template>
	</xsl:template>

	<xsl:template name="detail_image_4">
		<xsl:param name="box_form_factor"/>
		<xsl:call-template name="image_zoom_fill_box">
			<xsl:with-param name="field" select="//cmp3:field[@name='bilder_details4_image' or @name='bilder4_details4_image']"/>
			<xsl:with-param name="box_form_factor" select="$box_form_factor"/>
		</xsl:call-template>
	</xsl:template>


	<xsl:template name="detail_image_1_description">
		<xsl:apply-templates select="//cmp3:field[@name='bilder_details1_description' or @name='bilder4_details1_description']/cmp3:value"/>
	</xsl:template>

	<xsl:template name="detail_image_2_description">
		<xsl:apply-templates select="//cmp3:field[@name='bilder_details2_description' or @name='bilder4_details2_description']/cmp3:value"/>
	</xsl:template>

	<xsl:template name="detail_image_3_description">
		<xsl:apply-templates select="//cmp3:field[@name='bilder_details3_description' or @name='bilder4_details3_description']/cmp3:value"/>
	</xsl:template>

	<xsl:template name="detail_image_4_description">
		<xsl:apply-templates select="//cmp3:field[@name='bilder_details4_description' or @name='bilder4_details4_description']/cmp3:value"/>
	</xsl:template>


	<xsl:template name="date">
		<xsl:value-of select="//cmp3:field[@name='date']/cmp3:value"/>
	</xsl:template>

	<xsl:template name="web_address">
		<xsl:value-of select="//cmp3:field[@name='web_address']/cmp3:value"/>
	</xsl:template>

	<xsl:template name="freitext">
		<xsl:apply-templates select="//cmp3:field[@name='freitext_produkt']/cmp3:value"/>
	</xsl:template>

	<xsl:template name="contact">
		<xsl:apply-templates select="//cmp3:field[@name='freitext_kontakt']/cmp3:value"/>
	</xsl:template>

</xsl:stylesheet>