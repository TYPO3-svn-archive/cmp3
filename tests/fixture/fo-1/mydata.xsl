<?xml version="1.0" encoding="ISO-8859-1"?>

<xsl:stylesheet version="1.0"
        xmlns:fo="http://www.w3.org/1999/XSL/Format"
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:output method="xml" version="1.0" indent="yes" encoding="UTF-8"/>

  <xsl:template match="mydata">
    <fo:root xmlns:fo="http://www.w3.org/1999/XSL/Format">

      <fo:layout-master-set>
        <fo:simple-page-master  master-name="summarypage"
                              page-height="11in"
                              page-width="8.5in"
                              margin-top="1in"
                              margin-bottom="1in"
                              margin-left="1in"
                              margin-right="1in"
                              >
          <fo:region-body />
        </fo:simple-page-master>
      </fo:layout-master-set>

      <fo:page-sequence master-reference="summarypage">
        <fo:flow flow-name="xsl-region-body">

          <!-- Report Header -->
          <fo:block text-align="center"
                    font-size="18pt"
                    font-weight="bold">My Custom PDF Report</fo:block>

          <fo:block text-align="center"
                    font-size="14pt"
                    font-weight="bold"
                    padding-after="0.25in">Showing Some data</fo:block>

          <!-- Current Numbers -->
          <fo:block text-align="center"
                    font-size="8pt">

            <fo:table table-layout="fixed" width="100%">
              <fo:table-body>
                <fo:table-row background-color="#000"
                              border-left-style="solid"
                              border-right-style="solid"
                              border-top-style="solid">
                  <fo:table-cell >
                   <fo:block color="#fff"
                                text-align="center"
                                padding-top="0.5em"
                                padding-bottom="0.5em">
                                  Date
                   </fo:block>
                   </fo:table-cell>
                  <fo:table-cell >
                    <fo:block color="#fff"
                                 text-align="center"
                                 padding-top="0.5em"
                                 padding-bottom="0.5em">
                                   Earnings
                    </fo:block>
                  </fo:table-cell>
                </fo:table-row>
                <xsl:for-each select="record">
                  <fo:table-row border-left-style="solid"
                                    border-right-style="solid"
                                    border-bottom-style="solid">
                    <fo:table-cell >
                      <fo:block text-align="center"
                                   padding-top="0.5em">
                        <xsl:value-of select="date"/>
                      </fo:block>
                    </fo:table-cell>
                    <fo:table-cell >
                      <fo:block text-align="center"
                                   padding-top="0.5em">
                        <xsl:value-of select="earnings"/>
                      </fo:block>
                    </fo:table-cell>
                  </fo:table-row>
                </xsl:for-each>

              </fo:table-body>
            </fo:table>
          </fo:block>

        </fo:flow>
      </fo:page-sequence>
    </fo:root>
  </xsl:template>
</xsl:stylesheet>
