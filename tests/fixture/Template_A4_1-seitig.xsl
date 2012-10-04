<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	xmlns:fo="http://www.w3.org/1999/XSL/Format"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:cmp3="http://www.bitmotion.de/cmp3/cmp3document"
	version="1.0">

	<xsl:include href="../../xsl/rich_to_fo/rich_to_fo.xsl" />
	<!--
	<xsl:include href="xhtml-to-xslfo.xsl" />
	 -->

	<xsl:output
		encoding="UTF-8"
		method="xml"
		indent="no"
		version="1.0"></xsl:output>






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

<!-- see http://www.ibm.com/developerworks/library/x-xslfo2app/#table -->
	<xsl:template match="//cmp3:field[@name='technical_details']/cmp3:value">
		<xsl:call-template name="rich-to-fo"></xsl:call-template>
	</xsl:template>

	<xsl:template match="//cmp3:field[@name='freitext_kontakt']/cmp3:value">
		<xsl:call-template name="rich-to-fo"></xsl:call-template>
	</xsl:template>

	<xsl:template match="//cmp3:field[@name='freitext_produkt']/cmp3:value">
		<xsl:call-template name="rich-to-fo"></xsl:call-template>
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
		file://<xsl:value-of select="//cmp3:field[@name='print_page_image']/cmp3:value"/>
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






	<xsl:template match="/">


		<fo:root xmlns:fo="http://www.w3.org/1999/XSL/Format">

	<fo:layout-master-set>
		<fo:simple-page-master
			page-height="841.889763778pt"
			page-width="595.275590551pt"
			master-name="regular-odd">
			<fo:region-body
				margin-top="56.69291338582677pt"
				margin-bottom="56.69291338582677pt"
				margin-left="85.03937007874016pt"
				margin-right="42.51968503937008pt" />
			<fo:region-before
				region-name="regular-before-odd"
				extent="28.346456692913385pt"
				display-align="before" />
			<fo:region-after
				region-name="regular-after-odd"
				extent="28.346456692913385pt"
				display-align="after" />
			<fo:region-start
				region-name="regular-inner"
				extent="56.69291338582678pt"
				display-align="after" />
		</fo:simple-page-master>
		<fo:page-sequence-master master-name="text-plain">
			<fo:repeatable-page-master-reference master-reference="regular-odd" />
		</fo:page-sequence-master>
	</fo:layout-master-set>
	<fo:page-sequence
		master-reference="text-plain"
		id="rc_ucd">
		<fo:flow flow-name="xsl-region-body">
			<fo:block>
			    <xsl:attribute name="language">
			      <xsl:value-of select="//cmp3:record/@language"/>
			    </xsl:attribute>
				<fo:block-container
					absolute-position="fixed"
					left="306.142pt"
					top="280pt"
					width="246.614pt"
					height="182pt"
					overflow="hidden"
					reference-orientation="0">
					<fo:block
						font-size="0">
						<fo:external-graphic
							margin-top="-2mm"
							content-height="scale-to-fit"
							max-height="182pt"
							src="file:///home/rene/Bilder/Testbilder/Fotos/kaffee_quer.jpg"></fo:external-graphic>
					</fo:block>
				</fo:block-container>
				<fo:block-container
					absolute-position="fixed"
					left="42.379pt"
					top="280pt"
					width="246.614pt"
					height="182pt"
					overflow="hidden"
					reference-orientation="0">
					<fo:block
						font-size="0">
						<fo:external-graphic
									content-height="scale-to-fit"
									max-height="182pt"
									src="file:///home/rene/Bilder/Testbilder/Fotos/auf_der_wiese_hdr_1.jpg"></fo:external-graphic>

					</fo:block>
				</fo:block-container>
				<fo:block-container
					absolute-position="fixed"
					left="496.063pt"
					top="42.52pt"
					width="56.693pt"
					height="56.693pt"
					reference-orientation="0">
					<fo:block
						font-size="0">
									<fo:external-graphic
									content-height="56.693pt"
									content-width="56.693pt"
									scaling="uniform"><xsl:attribute name="src"><xsl:call-template name="print_url"/></xsl:attribute></fo:external-graphic>

					</fo:block>
				</fo:block-container>
				<fo:block-container
					absolute-position="fixed"
					left="42.52pt"
					top="486.287pt"
					width="510.236pt"
					height="25.657pt"
					reference-orientation="0">
					<fo:block
						white-space-collapse="false"
						linefeed-treatment="preserve"
						margin="0pt">
						<fo:block
							text-align="right"
							margin-left="0pt"
							margin-right="0pt"
							hyphenate="true"
							font-size="24pt"
							line-height="28pt"
							font-family="Franklin ITC BQ"
							line-stacking-strategy="font-height"
							padding-top="-3.5pt"
							padding-bottom="-8pt">
							<fo:inline
								font-family="Franklin ITC BQ"
								font-size="24pt"
								font-weight="bold"
								font-style="normal"
								letter-spacing="0.01em"
								baseline-shift="0pt"
								padding-left="0pt"
								color="rgb-icc(0, 0, 0, #CMYK,0,0,0,1)"><xsl:call-template name="title"/></fo:inline>
						</fo:block>
					</fo:block>
				</fo:block-container>
				<fo:block-container
					absolute-position="fixed"
					left="42.52pt"
					top="539.646pt"
					width="246.614pt"
					height="139.606pt"
					reference-orientation="0">
					<fo:block
						white-space-collapse="false"
						linefeed-treatment="preserve"
						margin="0pt">
						<fo:block
							text-align="justify"
							margin-left="0pt"
							margin-right="0pt"
							hyphenate="true"
							text-align-last="left"
							font-size="9pt"
							line-height="14pt"
							font-family="Franklin ITC BQ"
							line-stacking-strategy="font-height"
							padding-top="-2.9pt"
							padding-bottom="-4.75pt"
								font-weight="normal"
								font-style="normal"
								letter-spacing="0em"
								baseline-shift="0pt"
								padding-left="0pt"
								color="rgb-icc(0, 0, 0, #CMYK,0,0,0,1)"><xsl:call-template name="short_description"/>

						</fo:block>
					</fo:block>
				</fo:block-container>
					<fo:block-container
						height="3mm"
						width="36.581mm"
						reference-orientation="90"
						left="6.719mm"
						absolute-position="fixed"
						top="249.875mm">
						<fo:block
							white-space-collapse="false"
							linefeed-treatment="preserve"
							margin="0pt">
							<fo:block
								text-align="left"
								margin-left="0pt"
								margin-right="0pt"
								hyphenate="true"
								font-size="6pt"
								line-height="20pt"
								font-family="Franklin ITC BQ"
								line-stacking-strategy="font-height"
								padding-top="-7pt"
								padding-bottom="-8.5pt">
								<fo:inline
									font-family="Franklin ITC BQ"
									font-size="6pt"
									font-weight="normal"
									font-style="normal"
									letter-spacing="0.01em"
									baseline-shift="0pt"
									padding-left="0pt"
									color="rgb-icc(0, 0, 0, #CMYK,0,0,0,1)"><xsl:call-template name="date"/></fo:inline>
						</fo:block>
					</fo:block>
				</fo:block-container>
				<fo:block-container
					absolute-position="fixed"
					left="306.142pt"
					top="539.646pt"
					width="246.614pt"
					height="148.48pt"
					reference-orientation="0">
					<fo:block
						white-space-collapse="false"
						linefeed-treatment="preserve"
						margin="0pt">
						<fo:block
							text-align="left"
							margin-left="0pt"
							margin-right="0pt"
							hyphenate="true"
							font-size="9pt"
							line-height="14pt"
							font-family="Franklin ITC BQ"
							line-stacking-strategy="font-height"
							padding-top="-2.9pt"
							padding-bottom="-4.75pt"
								font-weight="normal"
								font-style="normal"
								letter-spacing="0.01em"
								baseline-shift="0pt"
								padding-left="0pt"
								color="rgb-icc(0, 0, 0, #CMYK,0,0,0,1)"><xsl:call-template name="pros"/>

						</fo:block>
					</fo:block>
				</fo:block-container>
				<fo:block-container
					absolute-position="fixed"
					left="42.52pt"
					top="708.308pt"
					width="246.614pt"
					height="27.905pt"
					reference-orientation="0">

					<fo:block
						margin="0pt">
						<fo:block
							text-align="left"
							margin-left="0pt"
							margin-right="0pt"
							hyphenate="true"
							font-size="8pt"
							line-height="11pt"
							font-family="Franklin ITC BQ"
							line-stacking-strategy="font-height"
							padding-top="-1.9pt"
							padding-bottom="-3.5pt">
							<fo:inline
								font-family="Franklin ITC BQ"
								font-size="8pt"
								font-weight="normal"
								font-style="normal"
								letter-spacing="0em"
								baseline-shift="0pt"
								padding-left="0pt"
								color="rgb-icc(0, 0, 0, #CMYK,0,0,0,1)"><xsl:call-template name="contact"/></fo:inline>
						</fo:block>
					</fo:block>

				</fo:block-container>
				<fo:block-container
					absolute-position="fixed"
					left="42.52pt"
					top="749.764pt"
					width="246.614pt"
					height="20.236pt"
					reference-orientation="0">
					<fo:block
						white-space-collapse="false"
						linefeed-treatment="preserve"
						margin="0pt">
						<fo:block
							text-align="left"
							margin-left="0pt"
							margin-right="0pt"
							hyphenate="true"
							font-size="8pt"
							line-height="11pt"
							font-family="Franklin ITC BQ"
							line-stacking-strategy="font-height"
							padding-top="-1.9pt"
							padding-bottom="-3.5pt">
							<fo:inline
								font-family="Franklin ITC BQ"
								font-size="8pt"
								font-weight="bold"
								font-style="normal"
								letter-spacing="0em"
								baseline-shift="0pt"
								padding-left="0pt"
								color="rgb-icc(0, 0, 0, #CMYK,0,0,0,1)"><xsl:call-template name="web_address"/></fo:inline>
						</fo:block>
					</fo:block>
				</fo:block-container>
			</fo:block>
		</fo:flow>
	</fo:page-sequence>
</fo:root>

	</xsl:template>
</xsl:stylesheet>