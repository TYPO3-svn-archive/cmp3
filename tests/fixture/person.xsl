<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="xml" version="1.0" encoding="utf-8" indent="yes" omit-xml-declaration="yes"/>
    <xsl:param name="Gender"></xsl:param>
    <xsl:template match="/">
        <Person>
            <Gender>
                <xsl:choose>
                    <xsl:when test="$Gender='1'">
                        Male
                    </xsl:when>
                    <xsl:otherwise>
                        Female
                    </xsl:otherwise>
                </xsl:choose>
            </Gender>
            <xsl:copy-of select="/hPerson/Header"/>
                <Data>
                    <Children>
                        <xsl:for-each select="/hPerson/hChildren/hChild">
                            <xsl:sort select="hName" />
                            <Child>
                                <FirstName>
                                    <xsl:value-of select="hName"/>
                                </FirstName>
                                <Age>
                                    <xsl:value-of select="hAge"/>
                                </Age>
                            </Child>
                        </xsl:for-each>
                    </Children>
                </Data>
            </Person>
        </xsl:template>
</xsl:stylesheet>