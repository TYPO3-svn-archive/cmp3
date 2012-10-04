<?xml version="1.0"?>
<!--
   Copyright 2010 Anyware Services

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
   -->
<xsl:stylesheet version="1.0"
    xmlns="http://docbook.org/ns/docbook"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xlink="http://www.w3.org/1999/xlink"
    xmlns:html="http://www.w3.org/1999/xhtml"
	exclude-result-prefixes="html">

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



    <!-- Warn about any html elements that don't match a more
       specific template.  Copy them too, since it's often
       easier to find them in the output. -->
    <xsl:template match="html:*">
        <xsl:message terminate="no">
            Unknown element <xsl:value-of select="name()"/>
        </xsl:message>
        <xsl:copy>
            <xsl:apply-templates/>
        </xsl:copy>
    </xsl:template>
    


    <xsl:template match="html:h1
        |html:h2
        |html:h3
        |html:h4
        |html:h5
        |html:h6">
		<xsl:param name="mode"/>
        <xsl:choose>
        	<!-- A <h*> is kept if it is a root element or in a <div> (which are eliminated) -->
            <xsl:when test="count(ancestor::div) + 1 = count(ancestor::*) and $mode != 'para-content'">
				<xsl:variable name="content">
					<section level="{substring(name(),2,1)}"><title><xsl:apply-templates select="node()[name() != 'table']"/></title></section>
				</xsl:variable>

				<xsl:choose>
					<xsl:when test="table">
						<xsl:apply-templates select="table"/>
						<xsl:if test="count(./*[local-name(.)!='table'])!=0 or string-length(.)!=0">
							<xsl:copy-of select="$content"/>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
						<xsl:copy-of select="$content"/>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
			<!-- For <h*> not root elements (in tables, ...) -->
			<xsl:otherwise>
				<xsl:call-template name="para-content"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

    <xsl:template match="html:p">
		<xsl:call-template name="para-content"/>
	</xsl:template>

    <xsl:template match="html:div">
		<xsl:call-template name="para-content"/>
	</xsl:template>

    <xsl:template match="html:sub">
   		<subscript>
   			<xsl:apply-templates/>
      	</subscript>
	</xsl:template>

    <xsl:template match="html:sup">
   		<supscript>
   			<xsl:apply-templates/>
      	</supscript>
	</xsl:template>

    <xsl:template match="html:br">
		<phrase role="linebreak"/>
	</xsl:template>

    <xsl:template match="html:strong|html:b">
		<emphasis role="strong">
			<xsl:apply-templates/>
		</emphasis>
	</xsl:template>

    <xsl:template match="html:em|html:i">
		<emphasis>
			<xsl:apply-templates/>
		</emphasis>
	</xsl:template>

    <xsl:template match="html:abbr">
        <abbrev title="{@title}"><xsl:apply-templates/></abbrev>
    </xsl:template>

    <xsl:template match="html:acronym">
        <acronym title="{@title}"><xsl:apply-templates/></acronym>
    </xsl:template>

    <xsl:template match="html:cite">
        <quote><xsl:apply-templates/></quote>
    </xsl:template>

    <xsl:template match="html:span[@class='language' and @lang]">
        <foreignphrase xml:lang="{@lang}"><xsl:apply-templates/></foreignphrase>
    </xsl:template>

    <xsl:template match="html:ol">
        <orderedlist>
            <xsl:if test="@class">
                <xsl:attribute name="numeration"><xsl:value-of select="@class"/></xsl:attribute>
            </xsl:if>
            <xsl:apply-templates/>
        </orderedlist>
    </xsl:template>

    <xsl:template match="html:ul">
        <itemizedlist>
            <xsl:if test="@class">
                <xsl:attribute name="mark"><xsl:value-of select="@class"/></xsl:attribute>
            </xsl:if>
            <xsl:apply-templates/>
        </itemizedlist>
    </xsl:template>

    <xsl:template match="html:ol|html:ul" mode="listitem">
        <xsl:apply-templates select="." />
    </xsl:template>

    <xsl:template match="html:li">
        <listitem>
            <para>
                <xsl:apply-templates/>
            </para>
        </listitem>
    </xsl:template>

    <xsl:template match="html:table">
        <table>

            <xsl:if test="caption/text()">
                <xsl:attribute name="title"><xsl:value-of select="html:caption/text()"/></xsl:attribute>
            </xsl:if>

            <xsl:copy-of select="@class"/>
            <xsl:copy-of select="@summary"/>
            <xsl:copy-of select="@align"/>

            <xsl:apply-templates select="html:tbody"/>
        </table>
    </xsl:template>

    <xsl:template match="tbody">
        <tbody>
            <xsl:apply-templates select="html:tr"/>
        </tbody>
    </xsl:template>

    <xsl:template match="html:tr">
        <tr>
            <xsl:apply-templates select="html:td|html:th"/>
        </tr>
    </xsl:template>

    <xsl:template match="html:td|html:th">
        <xsl:copy>
            <xsl:copy-of select="@colspan"/>
            <xsl:copy-of select="@rowspan"/>
            <xsl:apply-templates/>
        </xsl:copy>
    </xsl:template>

    <xsl:template match="html:a">
        <link>
            <xsl:attribute name="xlink:href"><xsl:value-of select="@href"/> </xsl:attribute>

            <xsl:if test="@title">
                <xsl:attribute name="xlink:title"><xsl:value-of select="@title"/></xsl:attribute>
            </xsl:if>

            <xsl:if test="@target = '_blank'">
                <xsl:attribute name="xlink:show">new</xsl:attribute>
            </xsl:if>

            <xsl:if test="@class">
                <xsl:attribute name="xrefstyle"><xsl:value-of select="@class"/></xsl:attribute>
            </xsl:if>

            <xsl:apply-templates/>
        </link>
    </xsl:template>

    <xsl:template match="html:img">
        <mediaobject>
            <xsl:copy-of select="@class"/>

            <xsl:if test="@alt">
                <alt><xsl:value-of select="@alt"/></alt>
            </xsl:if>

            <xsl:if test="@title">
                <caption><xsl:value-of select="@title"/></caption>
            </xsl:if>

            <xsl:if test="@align">
                <xsl:attribute name="align"><xsl:value-of select="@align"/></xsl:attribute>
            </xsl:if>

            <imageobject>
                <!-- todo -->
                <imagedata fileref="{@src}" type="{@ametys_type}">
                    <xsl:if test="@width"><xsl:attribute name="width"><xsl:value-of select="@width"/></xsl:attribute></xsl:if>
                    <xsl:if test="@height"><xsl:attribute name="depth"><xsl:value-of select="@height"/></xsl:attribute></xsl:if>
                </imagedata>
            </imageobject>
        </mediaobject>
    </xsl:template>

    <xsl:template name="para-content">
        <xsl:apply-templates select="html:p|html:div|html:ol|html:ul|html:table|html:form|html:h1
            |html:h2
            |html:h3
            |html:h4
            |html:h5
            |html:h6" />
        <xsl:if test="node()[name()!='p' and name()!='div' and name()!='ol' and name()!='ul' and name()!='table' and name()!='form' and not(starts-with(name(),'h') and string-length(name())=2 and string(number(substring(name(),2,1)))!='NaN')]">
            <para>
                <xsl:copy-of select="@class"/>
                <xsl:apply-templates select="node()[name()!='p' and name()!='div' and name()!='ol' and name()!='ul' and name()!='table' and name()!='form' and not(starts-with(name(),'h') and string-length(name())=2)]">
                    <xsl:with-param name="mode" select="'para-content'"/>
                </xsl:apply-templates>
            </para>
        </xsl:if>
    </xsl:template>
</xsl:stylesheet>