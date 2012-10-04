<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:html="http://www.w3.org/1999/xhtml" exclude-result-prefixes="xsl html">

 <xsl:output method="xml" indent="yes"/>

 <!--

This stylesheet transform xhtml to docbook.

While general html should be converted fine,
some special things might be done here to transform TYPO3 stuff right.



Resources:
http://wiki.docbook.org/topic/Html2DocBook
http://osteele.com/software/xslt/html2db/


Todos:
- search for TODO
- Table transformation is buggy
- Images - the transformation is just not used/tested
- remove $prefix


Remark:
Transforming html to docbook is generally not possible.
At least not in a correct way.

For example, even if the HTML is _very_ well-behaved, we have to
regularly interpret constructs such as

<H1>the first section</H1>
<P>this is the first section</p>
<H2>and a subsection</h2>
<p>with some text</p>
<H1>the next section</h1)

and turn it into

<sect1><title>the first section</title>
<para>this is the first section</para>
<sect2><title>and a subsection</title>
<para>with some text</para>
</sect1>
<sect1> ...

There is no equivalent to the sectN tag in HTML, and the fundamental
differences only begin with this first most elemental element.

That given we just try to do our best to turn the html into a meaningful docbook section.

-->


 <xsl:param name="prefix">wb</xsl:param>
 <xsl:param name="graphics_location">file:///epicuser/AISolutions/graphics/AIWorkbench/</xsl:param>




 <!--
  GENERAL
 -->


 <!-- Warn about any html elements that don't match a more
       specific template.  Copy them too, since it's often
       easier to find them in the output. -->
 <xsl:template match="html:*">
  <xsl:message terminate="no"> Unknown element <xsl:value-of select="name()"/>
  </xsl:message>
  <xsl:copy>
   <xsl:apply-templates/>
  </xsl:copy>
 </xsl:template>


 <!-- TODO what about this? set mode because matched always

 maybe use this in mode="item" context from html2db

 -->
 <xsl:template mode="rich" match="text()">
  <xsl:choose>
   <xsl:when test="normalize-space(.) = ''"/>
   <xsl:otherwise>
    <xsl:copy/>
   </xsl:otherwise>
  </xsl:choose>
 </xsl:template>



 <!--
  MAIN TEMPLATES
 -->


 <!-- This template converts the HTML into a DocBook
     section.  For a title, it selects the first h1,h2,h3 element -->
 <xsl:template name="richtext">
  <section>
   <title>
    <xsl:value-of
     select=".//html:h1[1]|.//html:h2[1]|.//html:h3[1]"
    />
   </title>
   <xsl:apply-templates select="*"/>
  </section>
 </xsl:template>


 <!--TODO html2db seems to be more intelligent by creating sections from h1-h3 -->

 <!-- This template matches on all HTML header items and makes them into
     bridgeheads. It attempts to assign an ID to each bridgehead by looking
     for a named anchor as a child of the header or as the immediate preceding
     or following sibling -->
 <xsl:template
  match="html:h1
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
    BLOCK ELEMENTS
  -->


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

 <xsl:template match="html:pre">
  <programlisting>
  <xsl:apply-templates/>
 </programlisting>
 </xsl:template>


 <xsl:template match="html:blockquote">
 <!-- todo support cite attribute -->
  <blockquote>
<!-- TODO an <para> is needed here. See html2db mode="item"  -->
   <xsl:apply-templates/>
  </blockquote>
 </xsl:template>

 <!-- just an address tag might not be enough - to be tested -->
 <xsl:template match="html:address">
  <address>
  <xsl:apply-templates/>
 </address>
 </xsl:template>



 <!--
  LIST ELEMENTS
 -->


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



 <!--
    INLINE ELEMENTS
  -->


 <xsl:template match="html:dfn">
  <indexterm significance="preferred">
   <primary>
    <xsl:apply-templates/>
   </primary>
  </indexterm>
  <glossterm>
   <xsl:apply-templates/>
  </glossterm>
 </xsl:template>

 <xsl:template match="html:strong|html:b|html:big">
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
  <emphasis role="underline">
   <xsl:apply-templates/>
  </emphasis>
 </xsl:template>

 <xsl:template match="html:strike|html:s|html:del">
  <emphasis role="strikethrough">
   <xsl:apply-templates/>
  </emphasis>
 </xsl:template>


 <xsl:template match="html:sub">
  <subscript>
   <xsl:apply-templates/>
  </subscript>
 </xsl:template>

 <xsl:template match="html:sup">
  <superscript>
   <xsl:apply-templates/>
  </superscript>
 </xsl:template>

 <!--TODO don't use keycap-->
 <xsl:template match="html:kbd|html:samp">
  <keycap>
   <xsl:apply-templates/>
  </keycap>
 </xsl:template>

 <xsl:template match="html:var">
  <varname>
   <xsl:apply-templates/>
  </varname>
 </xsl:template>

 <xsl:template match="html:abbr">
  <abbrev>
   <xsl:apply-templates/>
  </abbrev>
 </xsl:template>

 <xsl:template match="html:acronym">
  <acronym>
   <xsl:apply-templates/>
  </acronym>
 </xsl:template>

 <xsl:template match="html:cite">
  <citation>
   <xsl:apply-templates/>
  </citation>
 </xsl:template>

 <!-- TODO support cite attribute - as xlink? -->
 <xsl:template match="html:q">
  <citation>
   <xsl:apply-templates/>
  </citation>
 </xsl:template>

 <xsl:template match="html:tt|html:code">
  <code>
   <xsl:apply-templates/>
  </code>
 </xsl:template>

 <!-- Inline elements in code -->
 <xsl:template match="html:code/html:em|html:tt/html:em|html:pre/html:em">
  <replaceable>
   <xsl:apply-templates/>
  </replaceable>
 </xsl:template>



 <!--
  IGNORED ELEMENTS
 -->


 <xsl:template match="html:hr"/>
 <xsl:template match="html:br"/>
 <xsl:template match="html:p[normalize-space(.) = '' and count(*) = 0]"/>



 <!--
  IGNORED TAGS
  inner elements will be transformed
 -->


 <xsl:template match="html:div">
  <xsl:apply-templates/>
 </xsl:template>

 <xsl:template match="html:span">
  <xsl:apply-templates/>
 </xsl:template>

 <xsl:template match="html:font">
  <xsl:apply-templates/>
 </xsl:template>

 <xsl:template match="html:small">
  <xsl:apply-templates/>
 </xsl:template>

 <xsl:template match="html:ins">
  <xsl:apply-templates/>
 </xsl:template>



 <!--
  TABLES
 -->


 <!-- Utility functions and templates for tables -->
 <xsl:template mode="count-columns" match="html:tr">
  <n>
   <xsl:value-of select="count(html:td)"/>
  </n>
 </xsl:template>

 <!-- TODO thead is renderend when th is found but this is wrong
  check for thead and tfoot and render them -->
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
    <n>
     <xsl:value-of select="."/>
    </n>
   </xsl:for-each>
  </xsl:param>
  <xsl:element name="{$informal}table">
   <xsl:apply-templates select="@id"/>
   <xsl:if test="processing-instruction('html2db')[starts-with(., 'rowsep')]">
    <xsl:attribute name="rowsep">1</xsl:attribute>
   </xsl:if>
   <xsl:apply-templates select="processing-instruction()"/>
   <xsl:if test="@summary">
    <title>
     <xsl:value-of select="@summary"/>
    </title>
   </xsl:if>
   <xsl:if test="html:caption">
    <caption>
     <xsl:value-of select="html:caption"/>
    </caption>
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




 <!--
  HYPERLINKS - TODO
 -->


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



 <!--
  IMAGES - TODO
 -->


 <!-- Images and image maps -->
 <xsl:template match="html:img">
  <xsl:variable name="tag_name">
   <xsl:choose>
    <xsl:when
     test="boolean(parent::html:p) and
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
  <xsl:value-of select="$graphics_location"/>
  <xsl:value-of select="$name_only"/>
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


</xsl:stylesheet>