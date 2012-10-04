<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	xmlns:fo="http://www.w3.org/1999/XSL/Format"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:cmp3="http://www.bitmotion.de/cmp3/cmp3document"
	version="1.0">

	<xsl:include href="content.xsl" />


	<xsl:output
		encoding="UTF-8"
		method="xml"
		indent="no"
		version="1.0"></xsl:output>


	<xsl:template match="/">


		<fo:root xmlns:fo="http://www.w3.org/1999/XSL/Format">
			<fo:layout-master-set>
				<fo:simple-page-master
					page-width="595.275590551pt"
					master-name="regular-odd"
					page-height="841.889763778pt">
					<fo:region-body
						margin-left="85.03937007874016pt"
						margin-right="42.51968503937008pt"
						margin-bottom="56.69291338582677pt"
						margin-top="56.69291338582677pt"></fo:region-body>
					<fo:region-before
						display-align="before"
						region-name="regular-before-odd"
						extent="28.346456692913385pt"></fo:region-before>
					<fo:region-after
						display-align="after"
						region-name="regular-after-odd"
						extent="28.346456692913385pt"></fo:region-after>
					<fo:region-start
						display-align="after"
						region-name="regular-inner"
						extent="56.69291338582678pt"></fo:region-start>
				</fo:simple-page-master>
				<fo:page-sequence-master master-name="text-plain">
					<fo:repeatable-page-master-reference master-reference="regular-odd"></fo:repeatable-page-master-reference>
				</fo:page-sequence-master>
			</fo:layout-master-set>
			<fo:page-sequence
				id="rc_ucd"
				master-reference="text-plain">
				<fo:flow flow-name="xsl-region-body">
					<fo:block>
					    <xsl:attribute name="language">
					      <xsl:value-of select="//cmp3:record/@language"/>
					    </xsl:attribute>

						<fo:block-container
							height="182pt"
							width="246.614pt"
							background-color="rgb-icc(0, 0, 0, #CMYK,0,0,0,0.2)"
							reference-orientation="0"
							left="306.142pt"
							absolute-position="fixed"
							top="280pt">
							<fo:block
								white-space-collapse="false"
								margin="0pt"
								linefeed-treatment="ignore">
								<fo:external-graphic
									content-width="246.614pt"
									content-height="182pt"
									src="file:///home/rene/Bilder/Testbilder/fcPrints_Testbild_100ppi.jpg"></fo:external-graphic>
							</fo:block>

							<fo:block
								white-space-collapse="false"
								margin="0pt"
								linefeed-treatment="ignore">
								<!-- <fo:external-graphic> <xsl:attribute name="src"> file:///home/rene/Bilder/Testbilder/TC918_RGB-i1_iO.tif
									die XSLT-Anweisungen die dir die URL und den Namen des Bildes liefern </xsl:attribute> <fo:external-graphic> -->


							</fo:block>
						</fo:block-container>

						<fo:block-container
							height="182pt"
							overflow="hidden"
							width="246.614pt"
							background-color="rgb-icc(0, 0, 0, #CMYK,0,0,0,0.2)"
							reference-orientation="0"
							left="42.379pt"
							absolute-position="fixed"
							top="280pt">
							<fo:block
								white-space-collapse="false"
								margin="0pt"
								linefeed-treatment="ignore">
								<fo:external-graphic
									max-height="182pt"
									margin="0pt"
									content-height="scale-to-fit"
									src="file:///home/rene/Bilder/Testbilder/TC918_RGB-i1_iO.tif"
									scaling="uniform"></fo:external-graphic>
							</fo:block>
						</fo:block-container>

						<fo:block-container
							height="56.693pt"
							width="56.693pt"
							background-color="rgb-icc(250, 250, 250, #CMYK,0,0,0,0.1)"
							reference-orientation="0"
							left="496.063pt"
							absolute-position="fixed"
							top="42.52pt">
							<fo:block
								white-space-collapse="false"
								margin="0pt"
								linefeed-treatment="ignore">
									<fo:external-graphic
									margin="0pt"
									content-height="56.693pt"
									content-width="56.693pt"
									scaling="uniform"><xsl:attribute name="src">file://<xsl:value-of select="//cmp3:field[@name='test_url']/cmp3:value"/></xsl:attribute></fo:external-graphic>

							</fo:block>
						</fo:block-container>
						<fo:block-container
							reference-orientation="0"
							absolute-position="fixed"
							left="42.52pt"
							height="25.657pt"
							top="486.287pt"
							width="510.236pt">
							<fo:block
								white-space-collapse="false"
								margin="0pt"
								linefeed-treatment="ignore">
								<fo:block
									margin-right="0pt"
									line-height="28pt"
									padding-top="-2pt"
									text-align="right"
									font-size="24pt"
									margin-left="0pt"
									line-stacking-strategy="font-height"
									hyphenate="true"
									font-family="Franklin ITC BQ"
									padding-bottom="-8pt">
									<fo:leader
										line-height="0pt"
										leader-length="0pt"
										font-size="0pt"></fo:leader>
									<fo:inline
										padding-left="0pt"
										baseline-shift="0pt"
										letter-spacing="0.01em"
										color="rgb-icc(0, 0, 0, #CMYK,0,0,0,1)"
										font-style="normal"
										font-size="24pt"
										font-family="Franklin ITC BQ"
										font-weight="bold"><xsl:value-of select="//cmp3:field[@name='title']/cmp3:value"/>
</fo:inline>
								</fo:block>
							</fo:block>
						</fo:block-container>
						<fo:block-container
							reference-orientation="0"
							absolute-position="fixed"
							left="42.52pt"
							height="139.606pt"
							top="539.646pt"
							width="246.614pt">
							<fo:block
								white-space-collapse="false"
								margin="0pt"
								linefeed-treatment="ignore">
								<fo:block
									margin-right="0pt"
									line-height="14pt"
									padding-top="4.75pt"
									text-align="justify"
									font-size="9pt"
									margin-left="0pt"
									line-stacking-strategy="font-height"
									hyphenate="true"
									font-family="Franklin ITC BQ"
									text-align-last="left"
									padding-bottom="-4.75pt">
									<fo:leader
										line-height="0pt"
										leader-length="0pt"
										font-size="0pt"></fo:leader>
									<fo:inline
										padding-left="0pt"
										baseline-shift="0pt"
										letter-spacing="0em"
										color="rgb-icc(0, 0, 0, #CMYK,0,0,0,1)"
										font-style="normal"
										font-size="9pt"
										font-family="Franklin ITC BQ"
										font-weight="normal"><xsl:apply-templates select="//cmp3:field[@name='description']/cmp3:value"/>
</fo:inline>
								</fo:block>
							</fo:block>
						</fo:block-container>


						<!-- <xsl:value-of select="//cmp3:field[@name='date']/cmp3:value"/> -->
						<fo:block-container
							height="1.563mm"
							width="36.581mm"
							background-color="rgb-icc(0, 0, 0, #CMYK,1.0,0,0,0.2)"
							reference-orientation="90"
							left="7.5mm"
							absolute-position="fixed"
							top="268.165mm">
							<fo:block
								white-space-collapse="false"
								margin="0pt"
								linefeed-treatment="ignore">
								<fo:block
									margin-right="0pt"
									line-height="6mm"
									padding-top="0"
									text-align="left"
									font-size="6pt"
									margin-left="0pt"
									line-stacking-strategy="font-height"
									hyphenate="false"
									font-family="Franklin ITC BQ"
									padding-bottom="0">
									<fo:leader
										line-height="0pt"
										leader-length="0pt"
										font-size="0pt"></fo:leader>
									<fo:inline
										padding-left="0pt"
										baseline-shift="0pt"
										letter-spacing="0.01em"
										color="rgb-icc(0, 0, 0, #CMYK,0,0,0,1)"
										font-style="normal"
										font-size="6pt"
										font-family="Franklin ITC BQ"
										font-weight="normal"><xsl:value-of select="//cmp3:field[@name='date']/cmp3:value"/></fo:inline>
								</fo:block>
							</fo:block>
						</fo:block-container>


						<fo:block-container
							reference-orientation="0"
							absolute-position="fixed"
							left="306.142pt"
							height="148.48pt"
							top="539.646pt"
							width="246.614pt">
								<fo:block
									margin-right="0pt"
									line-height="14pt"
									padding-top="4.75pt"
									text-align="justify"
									font-size="9pt"
									margin-left="0pt"
									line-stacking-strategy="font-height"
									hyphenate="true"
									font-family="Franklin ITC BQ"
									text-align-last="left"
									padding-bottom="-4.75pt">
									<fo:leader
										line-height="0pt"
										leader-length="0pt"
										font-size="0pt"></fo:leader>
									<fo:inline
										padding-left="0pt"
										baseline-shift="0pt"
										letter-spacing="0em"
										color="rgb-icc(0, 0, 0, #CMYK,0,0,0,1)"
										font-style="normal"
										font-size="9pt"
										font-family="Franklin ITC BQ"
										font-weight="normal"><xsl:apply-templates select="//cmp3:field[@name='pros']/cmp3:value"/>
</fo:inline>
								</fo:block>
						</fo:block-container>
						<fo:block-container
							reference-orientation="0"
							absolute-position="fixed"
							left="42.52pt"
							height="27.905pt"
							top="708.308pt"
							width="246.614pt">
							<fo:block
								white-space-collapse="false"
								margin="0pt"
								linefeed-treatment="ignore">
								<fo:block
									margin-right="0pt"
									line-height="11pt"
									padding-top="-1.5pt"
									text-align="left"
									font-size="8pt"
									margin-left="0pt"
									line-stacking-strategy="font-height"
									hyphenate="true"
									font-family="Franklin ITC BQ"
									padding-bottom="-3.5pt">
									<fo:leader
										line-height="0pt"
										leader-length="0pt"
										font-size="0pt"></fo:leader>
									<fo:inline
										padding-left="0pt"
										baseline-shift="0pt"
										letter-spacing="0em"
										color="rgb-icc(0, 0, 0, #CMYK,0,0,0,1)"
										font-style="normal"
										font-size="8pt"
										font-family="Franklin ITC BQ"
										font-weight="bold">(8) Kontakt Name </fo:inline>
								</fo:block>
								<fo:block
									margin-right="0pt"
									line-height="11pt"
									padding-top="3.5pt"
									text-align="left"
									font-size="8pt"
									margin-left="0pt"
									line-stacking-strategy="font-height"
									hyphenate="true"
									font-family="Franklin ITC BQ"
									padding-bottom="-3.5pt">
									<fo:leader
										line-height="0pt"
										leader-length="0pt"
										font-size="0pt"></fo:leader>
									<fo:inline
										padding-left="0pt"
										baseline-shift="0pt"
										letter-spacing="0em"
										color="rgb-icc(0, 0, 0, #CMYK,0,0,0,1)"
										font-style="normal"
										font-size="8pt"
										font-family="Franklin ITC BQ"
										font-weight="normal">Kontakt Telefon und Fax </fo:inline>
								</fo:block>
								<fo:block
									margin-right="0pt"
									line-height="11pt"
									padding-top="3.5pt"
									text-align="left"
									font-size="8pt"
									margin-left="0pt"
									line-stacking-strategy="font-height"
									hyphenate="true"
									font-family="Franklin ITC BQ"
									padding-bottom="-3.5pt">
									<fo:leader
										line-height="0pt"
										leader-length="0pt"
										font-size="0pt"></fo:leader>
									<fo:inline
										padding-left="0pt"
										baseline-shift="0pt"
										letter-spacing="0em"
										color="rgb-icc(0, 0, 0, #CMYK,0,0,0,1)"
										font-style="normal"
										font-size="8pt"
										font-family="Franklin ITC BQ"
										font-weight="normal">E-Mailadresse</fo:inline>
								</fo:block>
							</fo:block>
						</fo:block-container>
						<fo:block-container
							reference-orientation="0"
							absolute-position="fixed"
							left="42.52pt"
							height="20.236pt"
							top="749.764pt"
							width="246.614pt">
							<fo:block
								white-space-collapse="false"
								margin="0pt"
								linefeed-treatment="ignore">
								<fo:block
									margin-right="0pt"
									line-height="11pt"
									padding-top="-1.5pt"
									text-align="left"
									font-size="8pt"
									margin-left="0pt"
									line-stacking-strategy="font-height"
									hyphenate="true"
									font-family="Franklin ITC BQ"
									padding-bottom="-3.5pt">
									<fo:leader
										line-height="0pt"
										leader-length="0pt"
										font-size="0pt"></fo:leader>
									<fo:inline
										padding-left="0pt"
										baseline-shift="0pt"
										letter-spacing="0em"
										color="rgb-icc(0, 0, 0, #CMYK,0,0,0,1)"
										font-style="normal"
										font-size="8pt"
										font-family="Franklin ITC BQ"
										font-weight="bold"><xsl:value-of select="//cmp3:field[@name='web_address']/cmp3:value"/></fo:inline>
								</fo:block>
							</fo:block>
						</fo:block-container>
					</fo:block>
				</fo:flow>
			</fo:page-sequence>
		</fo:root>


	</xsl:template>
</xsl:stylesheet>
