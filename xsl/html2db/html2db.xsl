<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:exslt="http://exslt.org/common"
                xmlns:java="http://xml.apache.org/xalan/java"
                xmlns:math="http://exslt.org/math"
                xmlns:db="urn:docbook"
                xmlns:html="http://www.w3.org/1999/xhtml"
                exclude-result-prefixes="exslt java math db html"
                version="1.0">

<!-- Copyright 2004 by Laszlo Systems, Inc.
     Released under the Artistic License.
     Written by Oliver Steele.
     Version 1.0.1
     http://osteele.com/sources/xslt/htm2db/
  -->
  <!-- Prefixed to every id generated from <a name=> and <a href="#"> -->
  <xsl:param name="anchor-id-prefix" select="''"/>


  <xsl:include href="html2db-utils.xsl"/>

  <!--
    Default templates
  -->

  <!-- pass docbook elements through unchanged; just strip the prefix
       -->
  <xsl:template match="db:*">
    <xsl:element name="{local-name()}">
      <xsl:for-each select="@*">
        <xsl:attribute name="{name()}">
          <xsl:value-of select="."/>
        </xsl:attribute>
      </xsl:for-each>
      <xsl:apply-templates/>
    </xsl:element>
  </xsl:template>

  <xsl:template match="@id">
    <xsl:copy/>
  </xsl:template>

  <!-- copy processing instructions, too -->
  <xsl:template match="processing-instruction()">
    <xsl:copy/>
  </xsl:template>

  <!-- except for html2db instructions -->
  <xsl:template match="processing-instruction('html2db')"/>

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

  <!--
    Root element and body
  -->

<xsl:template name="richtext">

    <xsl:variable name="class-pi"
                  select="processing-instruction('html2db')[starts-with(string(), 'class=&quot;')][1]"/>
    <xsl:variable name="class">
      <xsl:choose>
        <xsl:when test="count($class-pi)!=0">
          <xsl:value-of select="substring-before(substring-after(string($class-pi[0]), 'class=&quot;'), '&quot;')"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="."/>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:variable>

    <!-- Warn if there are any text nodes outside a para, etc.  See
         the note at the naked text template for why this is a
         warning. -->
    <xsl:if test="text()[normalize-space() != '']">
      <xsl:message terminate="no">
        Text must be inside a &lt;p&gt; tag.
      </xsl:message>
    </xsl:if>

    <xsl:element name="{$class}">
      <xsl:apply-templates select="@id"/>
      <xsl:call-template name="section-content">
        <xsl:with-param name="level" select="1"/>
        <xsl:with-param name="nodes" select="./node()|./text()"/>
      </xsl:call-template>
    </xsl:element>
  </xsl:template>

  <!--
    Section and section title processing
  -->

  <!--
    Nest elements that *follow* an h1, h2, etc. into <section> elements
    such that the <h1> content is the section's <title>.
  -->
  <xsl:template name="section-content">
    <xsl:param name="level"/>
    <xsl:param name="nodes"/>
    <xsl:param name="h1" select="concat('h', $level)"/>
    <xsl:param name="h2" select="concat('h', $level+1)"/>
    <xsl:param name="h2-position" select="count(exslt:node-set($nodes)[1]/following-sibling::*[local-name()=$h2])"/>

    <!-- copy up to first h2 -->
    <xsl:apply-templates select="exslt:node-set($nodes)[
                         count(following-sibling::*[local-name()=$h2])=$h2-position
                         ]"/>

    <!-- if section is empty, add an empty para so it will validate -->
    <xsl:if test="not(exslt:node-set($nodes)/html:para[
            count(following-sibling::*[local-name()=$h2])=$h2-position
            ])">
      <para/>
    </xsl:if>

    <!-- subsections -->
    <xsl:for-each select="exslt:node-set($nodes)[local-name()=$h2]">
      <section>
        <xsl:variable name="mynodes" select="exslt:node-set($nodes)[
                      count(following-sibling::*[local-name()=$h2])=
                      count(current()/following-sibling::*[local-name()=$h2])]"/>
        <xsl:for-each select="exslt:node-set($mynodes)[local-name()=$h2]">
          <xsl:choose>
            <xsl:when test="@id">
              <xsl:apply-templates select="@id"/>
            </xsl:when>
            <xsl:when test="html:a/@name">
              <xsl:attribute name="id">
                <xsl:value-of select="concat($anchor-id-prefix, html:a/@name)"/>
              </xsl:attribute>
            </xsl:when>
          </xsl:choose>
        </xsl:for-each>
        <xsl:call-template name="section-content">
          <xsl:with-param name="level" select="$level+1"/>
          <xsl:with-param name="nodes" select="exslt:node-set($nodes)[
                          count(following-sibling::*[local-name()=$h2])=
                          count(current()/following-sibling::*[local-name()=$h2])]"/>
        </xsl:call-template>
      </section>
    </xsl:for-each>
  </xsl:template>

  <!--
    Remove anchors from hn titles.  section-content attaches these as ids
    to the section (after mutilating them as described in the docs).
  -->
  <xsl:template match="html:h1|html:h2|html:h3|html:h4|html:h5|html:h6">
    <title>
      <xsl:apply-templates mode="skip-anchors" select="node()"/>
    </title>
  </xsl:template>

  <xsl:template mode="skip-anchors" match="html:a[@name]">
    <xsl:apply-templates/>
  </xsl:template>

  <xsl:template mode="skip-anchors" match="node()">
    <xsl:apply-templates select="."/>
  </xsl:template>

  <!--
    Inline elements
  -->
  <xsl:template match="html:b|html:i|html:em|html:strong">
    <emphasis role="{local-name()}">
      <xsl:apply-templates/>
    </emphasis>
  </xsl:template>

  <xsl:template match="html:dfn">
    <indexterm significance="preferred">
      <primary><xsl:apply-templates/></primary>
    </indexterm>
    <glossterm><xsl:apply-templates/></glossterm>
  </xsl:template>

  <xsl:template match="html:var">
    <replaceable><xsl:apply-templates/></replaceable>
  </xsl:template>

  <!--
    Inline elements in code
  -->
  <xsl:template match="html:code/html:i|html:tt/html:i|html:pre/html:i">
    <replaceable>
      <xsl:apply-templates/>
    </replaceable>
  </xsl:template>

  <xsl:template match="html:code|html:tt">
    <literal>
      <xsl:if test="@class">
        <xsl:attribute name="role"><xsl:value-of select="@class"/></xsl:attribute>
      </xsl:if>
      <xsl:apply-templates/>
    </literal>
  </xsl:template>

  <!-- For now, everything that doesn't have a specific match in inline
       processing mode is matched against the default processing mode. -->
  <xsl:template mode="inline" match="*">
    <xsl:apply-templates select="."/>
  </xsl:template>

  <!--
    Block elements
  -->
  <xsl:template match="html:p">
    <para>
      <xsl:apply-templates select="@id"/>
      <xsl:apply-templates mode="inline"/>
    </para>
  </xsl:template>

  <!-- Wrap naked text nodes in a <para> so that they'll process more
       correctly.  The html:body also warns about these, because even
       this preprocessing step isn't guaranteed to fix them.  This is
       because "Some <i>italic</i> text" will be preprocessed into
       "<para>Some </para> <emphasis>italic</emphasis><para>
       text</para>" instead of "<para>Some <emphasis>italic</emphasis>
       text</para>".  Getting this right would require more work than
       just maintaining the source documents. -->
  <xsl:template match="html:body/text()[normalize-space()!= '']">
    <!-- add an invalid tag to make it easy to find this in
         the generated file -->
    <naked-text>
      <para>
        <xsl:apply-templates/>
      </para>
    </naked-text>
  </xsl:template>

  <xsl:template match="html:body/html:code|html:pre">
    <programlisting>
      <xsl:apply-templates/>
    </programlisting>
  </xsl:template>

  <xsl:template match="html:blockquote">
    <blockquote>
      <xsl:apply-templates mode="item" select="."/>
    </blockquote>
  </xsl:template>

  <!--
    Images
  -->
  <xsl:template name="imageobject">
    <imageobject>
      <imagedata fileref="{@src}">
        <xsl:apply-templates select="@width|@height"/>
      </imagedata>
    </imageobject>
  </xsl:template>

  <xsl:template match="html:img/@width">
    <xsl:copy/>
  </xsl:template>

  <xsl:template match="html:img">
    <xsl:param name="informal">
      <xsl:if test="not(@title) and not(db:title)">informal</xsl:if>
    </xsl:param>
    <xsl:element name="{$informal}figure">
      <xsl:apply-templates select="@id"/>
      <xsl:choose>
        <xsl:when test="@title">
          <title><xsl:value-of select="@title"/></title>
        </xsl:when>
        <xsl:otherwise>
          <xsl:apply-templates select="db:title"/>
        </xsl:otherwise>
      </xsl:choose>
      <mediaobject>
        <xsl:call-template name="imageobject"/>
        <xsl:if test="@alt and normalize-space(@alt)!=''">
          <caption>
            <para>
              <xsl:value-of select="@alt"/>
            </para>
          </caption>
        </xsl:if>
      </mediaobject>
    </xsl:element>
  </xsl:template>

  <xsl:template mode="inline" match="html:img">
    <inlinemediaobject>
      <xsl:apply-templates select="@id"/>
      <xsl:call-template name="imageobject"/>
    </inlinemediaobject>
  </xsl:template>

  <!--
    links
  -->

  <!-- anchors -->
  <xsl:template match="html:a[@name]">
    <anchor id="{$anchor-id-prefix}{@name}"/>
    <xsl:apply-templates/>
  </xsl:template>

  <!-- internal link -->
  <xsl:template match="html:a[starts-with(@href, '#')]">
    <link linkend="{$anchor-id-prefix}{substring-after(@href, '#')}">
      <xsl:apply-templates select="@*"/>
      <xsl:apply-templates/>
    </link>
  </xsl:template>

  <!-- external link -->
  <xsl:template match="html:a">
    <ulink url="{@href}">
      <xsl:apply-templates select="@*"/>
      <xsl:apply-templates/>
    </ulink>
  </xsl:template>

  <!-- email -->
  <xsl:template match="html:a[starts-with(@href, 'mailto:')]">
    <email>
      <xsl:apply-templates select="@*"/>
      <xsl:value-of select="substring-after(@href, 'mailto:')"/>
    </email>
  </xsl:template>

  <!-- link attributes -->

  <xsl:template match="html:a/@*"/>

  <xsl:template match="html:a/@id">
    <xsl:apply-templates select="@id"/>
  </xsl:template>

  <xsl:template match="html:a/@target|html:a/@link">
    <xsl:processing-instruction name="db2html">
      <xsl:text>attribute name="</xsl:text>
      <xsl:value-of select="name()"/>
      <xsl:text>" value=</xsl:text>
      <xsl:call-template name="quote"/>
    </xsl:processing-instruction>
  </xsl:template>

  <!--
    lists
  -->

  <xsl:template match="html:dl">
    <variablelist>
      <xsl:apply-templates select="db:*"/>
      <xsl:apply-templates select="html:dt"/>
    </variablelist>
  </xsl:template>

  <xsl:template match="html:dt">
    <xsl:variable name="item-number" select="count(preceding-sibling::html:dt)+1"/>
    <varlistentry>
      <term>
        <xsl:apply-templates/>
      </term>
      <listitem>
        <!-- Select the dd that follows this dt without an intervening dd -->
        <xsl:apply-templates mode="item"
                             select="following-sibling::html:dd[
                             count(preceding-sibling::html:dt)=$item-number
                             ]"/>
        <!-- If there is no such dd, then insert an empty para -->
        <xsl:if test="count(following-sibling::html:dd[
                count(preceding-sibling::html:dt)=$item-number
                ])=0">
          <para/>
        </xsl:if>
      </listitem>
    </varlistentry>
  </xsl:template>

  <xsl:template mode="item" match="*[count(html:p) = 0]">
    <para>
      <xsl:apply-templates/>
    </para>
  </xsl:template>

  <xsl:template mode="nonblank-nodes" match="node()">
    <xsl:element name="{local-name()}"/>
  </xsl:template>

  <xsl:template mode="nonblank-nodes" match="text()[normalize-space()='']"/>

  <xsl:template mode="nonblank-nodes" match="text()">
    <text/>
  </xsl:template>

  <xsl:template mode="item" match="*">
    <!-- Test whether the first non-blank node is not a p -->
    <xsl:param name="nonblank-nodes">
      <xsl:apply-templates mode="nonblank-nodes"/>
    </xsl:param>

    <xsl:param name="tested" select="
               count(exslt:node-set($nonblank-nodes)/*) != 0 and
               local-name(exslt:node-set($nonblank-nodes)/*[1]) != 'p'"/>

    <xsl:param name="n1" select="count(*[1]/following::html:p)"/>
    <xsl:param name="n2" select="count(text()[1]/following::html:p)"/>

    <xsl:param name="n">
      <xsl:if test="$tested">
        <xsl:value-of select="java:java.lang.Math.max($n1, $n2)"/>
      </xsl:if>
    </xsl:param>

    <xsl:if test="false()">
      <nodeset tested="{$tested}" count="{count(exslt:node-set($nonblank-nodes)/*)}">
        <xsl:for-each select="exslt:node-set($nonblank-nodes)/*">
          <element name="{local-name()}"/>
        </xsl:for-each>
      </nodeset>
    </xsl:if>

    <!-- Wrap everything before the first p into a para -->
    <xsl:if test="$tested">
      <para>
        <xsl:apply-templates select="
                             node()[count(following::html:p)=$n] |
                             text()[count(following::html:p)=$n]"/>
      </para>
    </xsl:if>
    <xsl:apply-templates select="
                         node()[count(following::html:p)!=$n] |
                         text()[count(following::html:p)!=$n]"/>
  </xsl:template>

  <xsl:template match="html:ol">
    <orderedlist spacing="compact">
      <xsl:for-each select="html:li">
        <listitem>
          <xsl:apply-templates mode="item" select="."/>
        </listitem>
      </xsl:for-each>
    </orderedlist>
  </xsl:template>

  <xsl:template match="html:ul">
    <itemizedlist spacing="compact">
      <xsl:for-each select="html:li">
        <listitem>
          <xsl:apply-templates mode="item" select="."/>
        </listitem>
      </xsl:for-each>
    </itemizedlist>
  </xsl:template>

  <xsl:template match="html:ul[processing-instruction('html2db')]">
    <simplelist>
      <xsl:for-each select="html:li">
        <member type="vert">
          <xsl:apply-templates mode="item" select="."/>
        </member>
      </xsl:for-each>
    </simplelist>
  </xsl:template>

  <!--
    ignored markup
  -->
  <xsl:template match="html:br">
    <xsl:processing-instruction name="db2html">
      <xsl:text>element="</xsl:text>
      <xsl:value-of select="local-name()"/>
      <xsl:text>"</xsl:text>
    </xsl:processing-instruction>
  </xsl:template>

  <xsl:template match="html:span|html:div">
    <xsl:apply-templates select="*|node()|text()"/>
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
    <xsl:param name="cols" select="math:max(exslt:node-set($colcounts)/n)"/>
    <xsl:param name="sorted">
      <xsl:for-each select="exslt:node-set($colcounts)/n">
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
