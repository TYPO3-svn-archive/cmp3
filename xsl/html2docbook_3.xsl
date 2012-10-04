<?xml version="1.0"?>
<!--
/*
 * Copyright (c) 2011 Andrew E. Bruno <aeb@qnot.org>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
-->
<xsl:stylesheet version="1.0"
    xmlns="http://docbook.org/ns/docbook"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xlink="http://www.w3.org/1999/xlink"
    xmlns:html="http://www.w3.org/1999/xhtml"
    xmlns:str="http://exslt.org/strings"
    exclude-result-prefixes="html">

    <!-- Custom Parameters -->
    <xsl:param name="media.file.path"></xsl:param>
    <xsl:param name="blog.url"></xsl:param>


    <xsl:output method="xml" indent="yes"/>


    <!-- Main block-level conversions -->
    
    <xsl:template match="/">
        <xsl:apply-templates select="html:html"/>
    </xsl:template>
    
    <xsl:template match="html:html">
        <xsl:apply-templates select="html:body"/>
    </xsl:template>
        
    <xsl:template match="html:body">
        <article version="5.0">
            <xsl:apply-templates/>
        </article>
    </xsl:template>



    <!-- This template matches on all HTML header items and makes them into
     bridgeheads. It attempts to assign an ID to each bridgehead by looking
     for a named anchor as a child of the header or as the immediate preceding
     or following sibling -->
    <xsl:template match="html:h1
        |html:h2
        |html:h3
        |html:h4
        |html:h5
        |html:h6">
        <bridgehead>
            <xsl:apply-templates/>
        </bridgehead>
    </xsl:template>

<!-- div can sometimes be containers for images -->
<xsl:template match="html:div">
    <xsl:choose>
    <xsl:when test="a">
      <xsl:apply-templates select="html:a">
        <xsl:with-param name="caption" select="html:p/text()"/>
      </xsl:apply-templates>
    </xsl:when>
    <xsl:when test="img">
      <xsl:apply-templates select="html:img">
        <xsl:with-param name="caption" select="html:p/text()"/>
      </xsl:apply-templates>
    </xsl:when>
    <xsl:otherwise>
      <xsl:apply-templates />
    </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<xsl:template match="html:a">
    <xsl:param name="caption"/>
    <xsl:choose>
    <xsl:when test="img">
      <xsl:apply-templates select="html:img">
        <xsl:with-param name="caption" select="$caption"/>
        <xsl:with-param name="url" select="@href"/>
      </xsl:apply-templates>
    </xsl:when>
    <xsl:otherwise>
        <ulink url="{@href}">
            <xsl:apply-templates/>
        </ulink>
    </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!-- convert images to mediaobjects. Hard coded size to 4x3in. -->
<xsl:template match="html:img">
    <xsl:param name="caption"/>
    <xsl:param name="url"/>
    <para>
    <mediaobject>
        <imageobject>
        <xsl:choose>
        <xsl:when test="$url">
            <imagedata align="center"
                       fileref="{$media.file.path}/{substring-after($url, $blog.url)}"
                       width="4.0in"
                       depth="3.0in"
                       scalefit="1"
                       format="JPG" />
        </xsl:when>
        <xsl:otherwise>
            <imagedata align="center"
                       fileref="{$media.file.path}/{substring-after(@src, $blog.url)}"
                       width="4.0in"
                       depth="3.0in"
                       scalefit="1"
                       format="JPG" />
        </xsl:otherwise>
        </xsl:choose>
        </imageobject>
    <xsl:if test="$caption">
        <caption>
            <para><xsl:value-of select="$caption" /></para>
        </caption>
    </xsl:if>
    </mediaobject>
    </para>
</xsl:template>

<xsl:template match="html:p">
  <para><xsl:apply-templates/></para>
</xsl:template>

<xsl:template match="html:em">
  <emphasis><xsl:apply-templates/></emphasis>
</xsl:template>

<xsl:template match="html:br">
  <xsl:apply-templates/>
</xsl:template>

<xsl:template match="html:ul">
 <itemizedlist mark="opencircle">
  <xsl:apply-templates/>
 </itemizedlist>
</xsl:template>

<xsl:template match="html:ol">
 <orderedlist numeration="arabic">
  <xsl:apply-templates/>
 </orderedlist>
</xsl:template>

<xsl:template match="html:li">
 <listitem><para>
  <xsl:apply-templates/>
 </para></listitem>
</xsl:template>

<!-- copy all other elements, attributes, etc. as is -->
<xsl:template match="@*|node()">
    <xsl:copy>
        <xsl:apply-templates select="@*|node()"/>
    </xsl:copy>
</xsl:template>

</xsl:stylesheet>
