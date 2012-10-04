<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:fo="http://www.w3.org/1999/XSL/Format"
                xmlns:html="http://www.w3.org/1999/xhtml"
                exclude-result-prefixes="xsl fo html">



<!--see http://wiki.docbook.org/topic/Html2DocBook-->


<xsl:output method="xml" indent="yes"/>
<xsl:param name="prefix">wb</xsl:param>
<xsl:param name="graphics_location">fileadmin/</xsl:param>



  <!--
    Default templates
  -->

  <!-- pass docbook elements through unchanged; just strip the prefix
       -->


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



  <!-- copy processing instructions, too -->
  <xsl:template match="processing-instruction()">
    <xsl:copy/>
  </xsl:template>

  <!-- except for html2db instructions -->
  <xsl:template match="processing-instruction('html2db')"/>

<!-- This template converts each HTML file encountered into a DocBook
     section.  For a title, it selects the first h1 element -->
<xsl:template name="richtext">
<section>
  <title>
<!--   <xsl:value-of select=".//html:h1[1]
                         |.//html:h2[1]
                         |.//html:h3[1]"/>-->
  </title>
  <xsl:apply-templates select="*"/>
 </section>
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
  <xsl:choose>
   <xsl:when test="count(html:a/@name)">
    <xsl:attribute name="id">
     <xsl:value-of select="html:a/@name"/>
    </xsl:attribute>
   </xsl:when>
<!--TODO what's this?   -->
   <xsl:when test="preceding-sibling::* = preceding-sibling::html:a[@name != '']">
    <xsl:attribute name="id">
    <xsl:value-of select="concat($prefix,preceding-sibling::html:a[1]/@name)"/>
    </xsl:attribute>
   </xsl:when>
   <xsl:when test="following-sibling::* = following-sibling::html:a[@name != '']">
    <xsl:attribute name="id">
    <xsl:value-of select="concat($prefix,following-sibling::html:a[1]/@name)"/>
    </xsl:attribute>
   </xsl:when>
  </xsl:choose>
  <xsl:apply-templates/>
 </bridgehead>
</xsl:template>

  <!--
    Block elements
  -->

<!-- These templates perform one-to-one conversions of HTML elements into
     DocBook elements -->
<xsl:template match="html:p">
<!-- if the paragraph has no text (perhaps only a child <img>), don't
     make it a para -->
 <xsl:choose>
  <xsl:when test="normalize-space(.) = ''">
   <xsl:apply-templates/>
  </xsl:when>
  <xsl:otherwise>
 <para>
  <xsl:apply-templates/>
 </para>
  </xsl:otherwise>
 </xsl:choose>
</xsl:template>

<xsl:template match="html:pre|html:code">
 <programlisting>
  <xsl:apply-templates/>
 </programlisting>
</xsl:template>


 <xsl:template match="html:blockquote">
   <blockquote>
     <xsl:apply-templates mode="item" select="."/>
   </blockquote>
 </xsl:template>

<!-- Hyperlinks -->
<xsl:template match="html:a[contains(@href,'http://')]" priority="1.5">
 <ulink>
  <xsl:attribute name="url">
   <xsl:value-of select="normalize-space(@href)"/>
  </xsl:attribute>
  <xsl:apply-templates/>
 </ulink>
</xsl:template>
<xsl:template match="html:a[contains(@href,'ftp://')]" priority="1.5">
 <ulink>
  <xsl:attribute name="url">
   <xsl:value-of select="normalize-space(@href)"/>
  </xsl:attribute>
  <xsl:apply-templates/>
 </ulink>
</xsl:template>

<xsl:template match="html:a[contains(@href,'#')]" priority="0.6">
 <xref>
  <xsl:attribute name="linkend">
   <xsl:call-template name="make_id">
    <xsl:with-param name="string" select="substring-after(@href,'#')"/>
   </xsl:call-template>
  </xsl:attribute>
 </xref>
</xsl:template>
<xsl:template match="html:a[@name != '']" priority="0.6">
 <anchor>
  <xsl:attribute name="id">
   <xsl:call-template name="make_id">
    <xsl:with-param name="string" select="@name"/>
   </xsl:call-template>
  </xsl:attribute>
  <xsl:apply-templates/>
 </anchor>
</xsl:template>

<xsl:template match="html:a[@href != '']">
 <xref>
  <xsl:attribute name="linkend">
   <xsl:value-of select="$prefix"/>
   <xsl:text>_</xsl:text>
   <xsl:call-template name="make_id">
    <xsl:with-param name="string" select="@href"/>
   </xsl:call-template>
  </xsl:attribute>
 </xref>
</xsl:template>

<!-- Need to come up with good template for converting filenames into ID's -->
<xsl:template name="make_id">
 <xsl:param name="string" select="''"/>
 <xsl:variable name="fixedname">
  <xsl:call-template name="get_filename">
   <xsl:with-param name="path" select="translate($string,' \()','_/_')"/>
  </xsl:call-template>
 </xsl:variable>
 <xsl:choose>
  <xsl:when test="contains($fixedname,'.htm')">
   <xsl:value-of select="substring-before($fixedname,'.htm')"/>
  </xsl:when>
  <xsl:otherwise>
   <xsl:value-of select="$fixedname"/>
  </xsl:otherwise>
 </xsl:choose>
</xsl:template>

<xsl:template name="string.subst">
 <xsl:param name="string" select="''"/>
 <xsl:param name="substitute" select="''"/>
 <xsl:param name="with" select="''"/>
 <xsl:choose>
  <xsl:when test="contains($string,$substitute)">
   <xsl:variable name="pre" select="substring-before($string,$substitute)"/>
   <xsl:variable name="post" select="substring-after($string,$substitute)"/>
   <xsl:call-template name="string.subst">
    <xsl:with-param name="string" select="concat($pre,$with,$post)"/>
    <xsl:with-param name="substitute" select="$substitute"/>
    <xsl:with-param name="with" select="$with"/>
   </xsl:call-template>
  </xsl:when>
  <xsl:otherwise>
   <xsl:value-of select="$string"/>
  </xsl:otherwise>
 </xsl:choose>
</xsl:template>

<!-- Images -->
<!-- Images and image maps -->
<xsl:template match="html:img">
 <xsl:variable name="tag_name">
  <xsl:choose>
   <xsl:when test="boolean(parent::html:p) and
        boolean(normalize-space(parent::html:p/text()))">
    <xsl:text>inlinemediaobject</xsl:text>
   </xsl:when>
   <xsl:otherwise>mediaobject</xsl:otherwise>
  </xsl:choose>
 </xsl:variable>
 <xsl:element name="{$tag_name}">
  <imageobject>
   <xsl:call-template name="process.image"/>
  </imageobject>
 </xsl:element>
</xsl:template>

<xsl:template name="process.image">
 <imagedata>
<xsl:attribute name="fileref">
 <xsl:call-template name="make_absolute">
  <xsl:with-param name="filename" select="@src"/>
 </xsl:call-template>
</xsl:attribute>
<xsl:if test="@height != ''">
 <xsl:attribute name="depth">
  <xsl:value-of select="@height"/>
 </xsl:attribute>
</xsl:if>
<xsl:if test="@width != ''">
 <xsl:attribute name="width">
  <xsl:value-of select="@width"/>
 </xsl:attribute>
</xsl:if>
 </imagedata>
</xsl:template>

<xsl:template name="make_absolute">
 <xsl:param name="filename"/>
 <xsl:variable name="name_only">
  <xsl:call-template name="get_filename">
   <xsl:with-param name="path" select="$filename"/>
  </xsl:call-template>
 </xsl:variable>
 <xsl:value-of select="$graphics_location"/><xsl:value-of select="$name_only"/>
</xsl:template>

<xsl:template match="html:ul[count(*) = 0]">
 <xsl:message>Matched</xsl:message>
 <blockquote>
  <xsl:apply-templates/>
 </blockquote>
</xsl:template>

<xsl:template name="get_filename">
 <xsl:param name="path"/>
 <xsl:choose>
  <xsl:when test="contains($path,'/')">
   <xsl:call-template name="get_filename">
    <xsl:with-param name="path" select="substring-after($path,'/')"/>
   </xsl:call-template>
  </xsl:when>
  <xsl:otherwise>
   <xsl:value-of select="$path"/>
  </xsl:otherwise>
 </xsl:choose>
</xsl:template>

<!-- LIST ELEMENTS -->
<xsl:template match="html:ul">
 <itemizedlist spacing="compact">
  <xsl:apply-templates/>
 </itemizedlist>
</xsl:template>

<xsl:template match="html:ol">
 <orderedlist spacing="compact">
  <xsl:apply-templates/>
 </orderedlist>
</xsl:template>

<!-- This template makes a DocBook variablelist out of an HTML definition list -->
<xsl:template match="html:dl">
 <variablelist>
  <xsl:for-each select="html:dt">
   <varlistentry>
    <term>
     <xsl:apply-templates/>
    </term>
    <listitem>
     <xsl:apply-templates select="following-sibling::html:dd[1]"/>
    </listitem>
   </varlistentry>
  </xsl:for-each>
 </variablelist>
</xsl:template>

<xsl:template match="html:dd">
 <xsl:choose>
  <xsl:when test="boolean(html:p)">
   <xsl:apply-templates/>
  </xsl:when>
  <xsl:otherwise>
   <para>
    <xsl:apply-templates/>
   </para>
  </xsl:otherwise>
 </xsl:choose>
</xsl:template>

<xsl:template match="html:li">
 <listitem>
  <xsl:choose>
   <xsl:when test="count(html:p) = 0">
    <para>
     <xsl:apply-templates/>
    </para>
   </xsl:when>
   <xsl:otherwise>
    <xsl:apply-templates/>
   </xsl:otherwise>
  </xsl:choose>
 </listitem>
</xsl:template>



 <!-- more special -->

 <xsl:template match="html:address">
  <address>
  <xsl:apply-templates/>
 </address>
 </xsl:template>


  <!--
    Inline elements
  -->


  <xsl:template match="html:dfn">
    <indexterm significance="preferred">
      <primary><xsl:apply-templates/></primary>
    </indexterm>
    <glossterm><xsl:apply-templates/></glossterm>
  </xsl:template>

  <xsl:template match="html:var">
    <replaceable><xsl:apply-templates/></replaceable>
  </xsl:template>

	<xsl:template match="html:strong|html:b">
	 <emphasis role="bold">
	  <xsl:apply-templates/>
	 </emphasis>
	</xsl:template>

	<xsl:template match="html:em|html:i">
	 <emphasis>
	  <xsl:apply-templates/>
	 </emphasis>
	</xsl:template>

	<xsl:template match="html:u">
	 <citetitle>
	  <xsl:apply-templates/>
	 </citetitle>
	</xsl:template>


  <!--
    Inline elements in code
  -->
  <xsl:template match="html:code/html:em|html:tt/html:em|html:pre/html:em">
    <replaceable>
      <xsl:apply-templates/>
    </replaceable>
  </xsl:template>


<!-- Ignored elements -->
<xsl:template match="html:hr"/>
<xsl:template match="html:br"/>
<xsl:template match="html:p[normalize-space(.) = '' and count(*) = 0]"/>

<!-- TODO what about this? set mode because matched always -->
<xsl:template mode="rich" match="text()">
 <xsl:choose>
  <xsl:when test="normalize-space(.) = ''"></xsl:when>
  <xsl:otherwise><xsl:copy/></xsl:otherwise>
 </xsl:choose>
</xsl:template>



  <!--
    Utility functions and templates for tables
  -->
  <xsl:template mode="count-columns" match="html:tr">
    <n>
      <xsl:value-of select="count(html:td)"/>
    </n>
  </xsl:template>

  <!-- tables -->
  <xsl:template match="html:table">

    <xsl:param name="informal">
      <xsl:if test="not(@summary)">informal</xsl:if>
    </xsl:param>
    <xsl:param name="colcounts">
      <xsl:apply-templates mode="count-columns" select=".//html:tr"/>
    </xsl:param>
    <xsl:param name="cols" select="max(($colcounts/n))"/>
    <xsl:param name="sorted">
      <xsl:for-each select="$colcounts/n">
        <xsl:sort order="descending" data-type="number"/>
        <n><xsl:value-of select="."/></n>
      </xsl:for-each>
    </xsl:param>
    <xsl:element name="{$informal}table">
      <xsl:apply-templates select="@id"/>
      <xsl:if test="processing-instruction('html2db')[starts-with(., 'rowsep')]">
        <xsl:attribute name="rowsep">1</xsl:attribute>
      </xsl:if>
      <xsl:apply-templates select="processing-instruction()"/>
      <xsl:if test="@summary">
        <title><xsl:value-of select="@summary"/></title>
      </xsl:if>
      <tgroup cols="{$cols}">
        <xsl:if test=".//html:tr/html:th">
          <thead>
            <xsl:for-each select=".//html:tr[count(html:th)!=0]">
              <row>
                <xsl:apply-templates select="@id"/>
                <xsl:for-each select="html:td|html:th">
                  <entry>
                    <xsl:apply-templates select="@id"/>
                    <xsl:apply-templates/>
                  </entry>
                </xsl:for-each>
              </row>
              <xsl:text>&#10;</xsl:text>
            </xsl:for-each>
          </thead>
        </xsl:if>
        <tbody>
          <xsl:for-each select=".//html:tr[count(html:th)=0]">
            <row>
              <xsl:apply-templates select="@id"/>
              <xsl:for-each select="html:td|html:th">
                <entry>
                  <xsl:apply-templates select="@id"/>
                  <xsl:apply-templates/>
                </entry>
              </xsl:for-each>
            </row>
            <xsl:text>&#10;</xsl:text>
          </xsl:for-each>
        </tbody>
      </tgroup>
    </xsl:element>
  </xsl:template>



</xsl:stylesheet>