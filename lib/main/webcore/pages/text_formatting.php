<?php

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

This file is part of earthli WebCore.

earthli WebCore is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli WebCore is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli WebCore; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli WebCore, visit:

http://www.earthli.com/software/webcore

****************************************************************************/

  $Page->title->subject = 'Text formatting';
  if (isset ($App))
  {
    $Page->location->append ($App->short_title, './');
  }
  $Page->location->append ($Page->title->subject);

  $Page->start_display ();
?>
<div  class="box">
  <div class="box-title">Text formatting</div>
  <div class="box-body">
    <div class="chart" style="float: right; margin: 0em 0em 1em 1em">
      <div class="chart-body">
        <ul style="margin-left: 2em; padding: 0em; margin-top: 0em; margin-bottom: 0em">
          <li><a href="#exceptions">Single-line vs. Multi-line</a></li>
          <li><a href="#new_lines">Newline handling</a></li>
          <li><a href="#characters">Special Characters</a></li>
          <li><a href="#paths">Paths/resources</a></li>
          <li><a href="#macro">Controlling the formatter</a></li>
          <li><a href="#tags">Tags</a></li>
          <li><a href="#literals">Literal characters</a></li>
          <li><a href="#Usingastext">Using &lt; as text</a></li>
          <li><a href="#pre_code">Preformatted text and source code</a></li>
          <li><a href="#headings">Headings</a></li>
          <li><a href="#links">Links</a></li>
          <li><a href="#images">Images</a></li>
          <li><a href="#videos">Videos</a></li>
          <li><a href="#fonts">Font formatting</a></li>
          <li><a href="#generic">Generic formatting</a></li>
          <li><a href="#lists">Lists</a></li>
          <li><a href="#quoting">Quoting</a></li>
          <li><a href="#notes">Notes</a></li>
          <li><a href="#boxes">Boxes</a></li>
        </ul>
      </div>
    </div>
    <p>Blocks of text in this application are mainly plain
      text. That means that newline characters will be faithfully represented
      when rendered into an output format. There are also several tags supported
      that allow you to build commonly used constructions, like ordered and
      unordered lists, preformatted blocks, quoted blocks and more.</p>
    <p>These tags look like HTML and often share a name with a similar HTML
      element, but are <em>not</em> actually HTML tags. The content you write
      is transformed to HTML when displayed in a web page, but can also be transformed
      to plain text - e.g. when generating a plain text email.</p>
    <h2 style="clear: both">Single-line vs. Multi-line<a id="exceptions"></a></h2>
    <p>Some fields, like object titles, are explicitly single-line and support only a
      limited number of tags. In particular, block and image tags are not applied and
      are instead inserted as if they were just text. Only the <span class="highlight">&lt;b&gt;</span>,
      <span class="highlight">&lt;i&gt;</span> and <span class="highlight">&lt;code&gt;</span>
      tags are supported in single-line mode. If attributes are specified,
      the tag will be rendered as text (this behavior may change in future revisions).</p>
    <h2 id="new_lines">Newline handling</h2>
    <p>If you are just writing unformatted text, you may treat newlines as you
      normally would; both the HTML and plain text formatter will honor the
      spacing you've chosen. In some cases, HTML forces extra spacing that cannot
      be avoided - e.g. if you separate a normal text run from a preformatted
      text run with a single newline, HTML will render this with a double newline
      because the blocks are separated by a margin. In these cases, it makes
      no difference if you use zero, one or two newlines -- there are always
      two newlines rendered. The plain text formatter has been written to emulate
      this behavior, so that the plain text representation is as close as possible
      to the 'main' HTML output format.</p>
    <h2 id="characters">Special Characters</h2>
    <p>Certain characters will be automatically replaced if they appear anywhere
      in regular text. You can disable this replacement for whole
      regions by using the <span class="highlight">&lt;macro&gt;</span> tag.</p>
    <table>
      <tr>
        <th>Input</th>
        <th>Output</th>
      </tr>
      <tr>
       <td>"---"</td><td>&mdash;</td>
      </tr>
      <tr>
       <td>"--"</td><td>&#8211;</td>
      </tr>
      <tr>
       <td>"1/2"</td><td>&frac12;</td>
      </tr>
      <tr>
       <td>"1/4"</td><td>&frac14;</td>
      </tr>
      <tr>
       <td>"3/4"</td><td>&frac34;</td>
      </tr>
      <tr>
       <td>"..."</td><td>&#8230;</td>
      </tr>
      <tr>
       <td>"(tm)"</td><td>&trade;</td>
      </tr>
      <tr>
       <td>"(c)"</td><td>&copy;</td>
      </tr>
      <tr>
       <td>"(r)"</td><td>&reg;</td>
      </tr>
      <tr>
       <td>" x "</td><td>&times;</td>
      </tr>
    </table>
    <p>Additionally, the following character pairs are automatically converted to
      their ligature equivalents. Some browsers don't especially like these characters,
      so this conversion is off, by default. You can enable this replacement for
      whole regions using the <span class="highlight">&lt;macro&gt;</span> tag.</p>
    <table style="text-align: center">
      <tr>
        <th>Input</th>
        <th>Output</th>
      </tr>
      <tr>
       <td>"ffi"</td><td>&#xfb03;</td>
      </tr>
      <tr>
       <td>"ffl"</td><td>&#xfb04;</td>
      </tr>
      <tr>
       <td>"ff"</td><td>&#xfb00;</td>
      </tr>
      <tr>
       <td>"fi"</td><td>&#xfb01;</td>
      </tr>
      <tr>
       <td>"fl"</td><td>&#xfb02;</td>
      </tr>
    </table>
    <h2 id="paths">Paths/resources</h2>
    <p>As you'll see below, you can add images and links to your text really easily.
      You don't have to use absolute urls though. You can base your url on a path
      defined in the WebCore application by specifying a base 'location' at the start
      of your url, like this:</p>
    <p><code>{icons}file_types/file_32px</code></p>
    <p>If you leave off the extension when referring to an icon file, the default
      application icon extension is applied, so you get:</p>
    <p><?php echo $Page->resolve_icon_as_html ('{icons}file_types/file', ' ', '32px'); ?></p>
    <p>If, at some point, you move your icons, your reference to the icons folder won't
      be broken. This works for other stuff as well, like attachments. You can refer to
      an attachment file like this:
    <p><code>{att_link}my_file.zip</code></p>
    <p>The path to the attachments folder for the current object will be prepended for
      you. If the attachment is an image, you can use that path as the 'src' attribute
      of an image and it will show up in the page. Or you can use <code>{att_thumb}</code>
      to show only the thumbnail for it.</p>
    <p>Supported locations (you can also add your own) are:
    <ul>
      <li><span class="field">application</span>: Root url for the current application.</li>
      <li><span class="field">icons</span>: Location of the application icons folder.</li>
      <li><span class="field">styles</span>: Location of the application styles folder.</li>
      <li><span class="field">scripts</span>: Location of the application scripts folder.</li>
      <li><span class="field">att_link</span>: Location of attachments for the current object.</li>
      <li><span class="field">att_thumb</span>: Converts the given attachment file to thumbnail name.</li>
      <li><span class="field">pic_image</span>: Location of images for the current album. Accepts a picture ID or file name. Only available in earthli Albums.</li>
      <li><span class="field">pic_thumb</span>: Same as "pic_image", but converts to the thumbnail name. Only available in earthli Albums.</li>
    </ul>
    <h2 id="macro">Controlling the formatter</h2>
    <p>As discussed in <a href="#characters">special characters</a>, you can control
      which types of character replacement occur within a block of text. The
      <span class="highlight">&lt;macro&gt;</span> tag applies options, which
      apply for the rest of the text or until they are changed by a subsequent
      macro.</p>
    <p>The following properties are supported:</p>
    <dl>
      <dt class="field">convert</dt>
      <dd>
        <p>Pass in a comma-delimited list of converters, which should be active. Use
          a plus (+) sign to enable a converter and a minus (-) sign to disable it.
          If no sign is present, it is assumed to be enabling the converter. The
          converters in the list are applied in order and can overwrite one another.
          Choose from the following converters:</p>
        <ul>
          <li><strong>punctuation</strong>: If enabled, punctuation marks are
            replaced with their fancier HTML equivalents. <em>enabled by default</em></li>
          <li><strong>ligature</strong>: If enabled, ligatures are
            replaced with their fancier HTML equivalents. <em>disabled by default</em></li>
          <li><strong>tags</strong>: If enabled, special html characters are converted to
            avoid being interpreted as HTML. The keyword "all" does not apply to this
            converter; you must explicitly toggle it if you want to change the setting.
            <em>enabled by default</em></li>
          <li><strong>highlight</strong>: If enabled, keywords from a search are
            highlighted within the text. <em>enabled by default</em></li>
          <li><strong>all</strong>: Applies to all converters except for "tags".</li>
        </ul>
        <p>The following examples turn on ligatures:</p>
        <p><code>&lt;macro convert="ligature"&gt;</code> or <code>&lt;macro convert="+ligature"&gt;</code></p>
        <p>The following examples turn off everything but highlighting:</p>
        <p><code>&lt;macro convert="-ligature,-punctuation"&gt;</code> or
          <code>&lt;macro convert="-all;+highlight"&gt;</code></p>
        <p>The following example makes sure that all converters are turned on:</p>
        <p><code>&lt;macro convert="+all"&gt;</code></p>
      </dd>
    </dl>
    <h2 id="tags">Tags</h2>
    <p>Tags are the same format as HTML tags (e.g. &lt;tag attr=&quot;value&quot;&gt;).
      The tokenizer recognizes as a tag any grouping of text that starts with
      &lt;, has a letter or number as the next letter, then ends with &gt;.
      This is discussed in more detail in <a href="#Usingastext">Using &lt;
      as text</a>. If a tag is not recognized, it will be rendered as text.
      The list of recognized tags follow and are recognized by both the HTML
      and plain text formatters. When a tag is recognized, that means that it
      will not be rendered as text by the formatter, but will either be used
      directly, transformed to another tag or construct or discarded, depending
      on the output format.</p>
    <p>Unknown tags are rendered as text, by default.</p>
    <h2 id="literals">Literal characters</h2>
    <p>In this version, you may type anything you like; there is no longer a
      need to specifically escape characters as HTML. The input language is
      no longer HTML, so escaping characters has been limited to the single
      case of the &lt; character, discussed next.</p>
    <h2 id="Usingastext">Using &lt; as text</h2>
    <p>Since the content can be delimited by tags, the &lt; character must necessarily
      be escaped in certain circumstances. These situations have been limited
      so that you will only very rarely have to use the escaped character. The
      only time you <em>may</em> need to escape the &lt; character is if the
      character immediately following it is a letter or number. To escape the
      &lt;, use &lt;&lt;.</p>
    <h3>Examples</h3>
    <p>If you want to write: </p>
    <p><code>x &lt; y and 5 &lt; 8</code></p>
    <p>you do not need to escape anything. Simply write it as shown above and
      the formatter detects that the &lt; characters in the text cannot be parts
      of tags.</p>
    <p>If you want to write:</p>
    <p><code>If the text in the input box is still &lt;default&gt;, then you
      have to...</code></p>
    <p>you still don't <em>have</em> to escape the bracket, since the formatter
      simply renders unknown tags as text anyway. In the next example, we see
      where we must include an escaped &lt; character.</p>
    <p>If you want to write:</p>
    <p>&lt;pre&gt; to specify a backlink, use &lt;linkname. &lt;/pre&gt;</p>
    <p>The problem here is that the tokenizer will recognize '&lt;linkname.
      &lt;/pre&gt;' as a tag, which will cause the &lt;/pre&gt; end tag to be
      ignored, with unpredictable results. The text will still be output, but
      the preformatted region will not be properly rendered. To avoid this,
      use the escaped version of the &lt; character:</p>
    <p>&lt;pre&gt; to specify a backlink, use &lt;&lt;linkname. &lt;/pre&gt;</p>
    <h2 id="pre_code">Preformatted text and source code</h2>
    <p>Whitespace is interpreted differently by different output formats, so
      the <span class="highlight">&lt;pre&gt;</span> tag can be used to force
      the formatter to use the exact whitespace you have specified. This is
      very useful for displaying code samples. For specifying that a block of
      text is source code, you may use the
      <span class="highlight">&lt;code&gt;</span> tag. This will alter
      the display in the HTML formatter, but is stripped when rendered as plain
      text. For inline code examples, use the <span class="highlight">&lt;c&gt;</span>
      tag. Text is still generally formatted the same as with the code tag, except
      that it doesn't force block formatting.</p>
    <p>Tag attributes, if specified, are <em>retained</em>.</p>
    <h2 id="headings">Headings</h2>
    <p>Headings are written with the <span class="highlight">&lt;h&gt;</span> tag. Use
      headings to delineate new sections in longer text flows. The default heading is
      slightly larger than regular text and bold. Plain text mode will maintain proper
      spacing for headings even if you don't specify it. The following attributes
      are allowed.</p>
    <dl>
      <dt class="field">level</dt>
      <dd>Heading level, analogous to the HTML heading level. The default is 3.
        Since headings are usually just used to distinguish between sections, you
        shouldn't often need to control the heading level.</dd>
    </dl>
    <h3>Example</h3>
    <p>&lt;h&gt;earthli WebCore&lt;/h&gt;</p>
    <h4>HTML result</h4>
    <h3>earthli WebCore</h3>
    <h4>Plain text result</h4>
    <p>[earthli WebCore]</p>
    <h2 id="links">Links</h2>
    <p>Links are written with the <span class="highlight">&lt;a&gt;</span> tag.
      The following attributes are allowed.</p>
    <dl>
      <dt class="field">title</dt>
      <dd>The title of the link. Can be a longer description of the resource
        to which the link goes.</dd>
      <dt class="field">class</dt>
      <dd>This will assign the specified CSS class to the link itself.</dd>
      <dt class="field">href</dt>
      <dd>The url to which the link goes.</dd>
      <dt class="field">format</dt>
      <dd>
        <p> This is used only in the plain-text renderer. The default value is 'all'.</p>
        <ul>
          <li><span class="field">url</span>: Show only the url.</li>
          <li><span class="field">all</span>: Show the url and title.</li>
          <li><span class="field">none</span>: Skip this link.</li>
        </ul>
      </dd>
    </dl>
    <h3>Example</h3>
    <p>&lt;a href=&quot;http://www.earthli.com/software/webcore/&quot; title=&quot;Try
      out the earthli WebCore!&quot;&gt;earthli WebCore&lt;/a&gt;</p>
    <h4>HTML result</h4>
    <p><a href="http://www.earthli.com/software/webcore/" title="Try out the earthli WebCore!">earthli
      WebCore</a></p>
    <h4>Plain text result</h4>
    <p><code>earthli WebCore &lt;http://www.earthli.com/software/webcore/&gt;
      (Try out the earthli WebCore!) </code></p>
    <h2 id="images">Images</h2>
    <p>Images are inserted with the <span class="highlight">&lt;img&gt;</span>
      tag. The following list of tags are supported.</p>
    <p class="notes">The image must already exist as a URL. Inserting an image link does not
      magically upload a file for you.</p>
    <dl>
      <dt class="field">src</dt>
      <dd>The url for the image itself. Use path/resource syntax or an absolute
        URL.</dd>
      <dt class="field">href</dt>
      <dd>If this is non-empty, a link is automatically wrapped around the image
        with this url. Use path/resource syntax or an absolute URL.</dd>
      <dt class="field">attachment</dt>
      <dd>If this is non-empty, both "src" and "href" are ignored. Instead, both
        properties are automatically generated from the attachment file name given.
        It's up to the user to make sure the attachment is exists and is accessible.</dd>
      <dt class="field">title</dt>
      <dd>Longer description of the linked image. If this is not given, then
        the value for 'alt' is used.</dd>
      <dt class="field">alt</dt>
      <dd>Alternate description of the image. Should be concise. Put longer
        description in 'title', if needed. If this is not given, then the value
        for 'title' is used.</dd>
      <dt class="field">align</dt>
      <dd>
        <p>The default value is 'none'. This parameter is ignored in the plain-text renderer.</p>
        <ul>
          <li><span class="field">left</span>: Float the image to the left,
            with text wrapped around to the right.</li>
          <li><span class="field">right</span>: Float the image to the right,
            with text wrapped around to the left.</li>
          <li><span class="field">center</span>: Image is centered in its own
            block. Text is split, with the flow continuing after the image.</li>
          <li><span class="field">none</span>: Image is formatted inline with
            the text</li>
        </ul>
      </dd>
      <dt class="field">class</dt>
      <dd>This will assign the specified CSS class to the image itself. This parameter is
        ignored in the plain-text renderer.</dd>
      <dt class="field">format</dt>
      <dd>
        <p> This is used only in the plain-text renderer. The default value
          is 'all'. If 'alt' is not specified, 'title' is used for the text. If a url is
          included, 'href' is always used before 'src'.</p>
        <ul>
          <li><span class="field">basic</span>: Show only the 'alt' or 'title'.</li>
          <li><span class="field">url</span>: Show a url and 'alt', but no title.</li>
          <li><span class="field">all</span>: Show a url, 'alt' and the title
            (if different than 'alt' and 'alt' is non-empty).</li>
          <li><span class="field">none</span>: Skip this image.</li>
        </ul>
      </dd>
    </dl>
    <h3>Example</h3>
    <p><code>&lt;img src=&quot;{icons}/file_types/file_50px&quot;
      align=&quot;right&quot; alt=&quot;WebCore File Icon&quot; format=&quot;basic&quot;&gt;</code>This
      is purely sample text to illustrate that the text immediately following
      an image tag will be formatted around the image if it has an 'align' value
      of 'left' or 'right'. If it has an alignment of 'center', the image is
      placed in its own block, separating the text flow. If it is 'none', the
      image is simply rendered inline with the text. The image below is formatted
      differently. </p>
    <p><code>&lt;img href=&quot;http://data.earthli.com/albums/oz/images/im000185.jpg&quot;
      src=&quot;http://www.earthli.com/users/oz/images/IM000185_tn.jpg&quot;
      align=&quot;center&quot; title=&quot;Ozzie in the garden&quot;&gt;</code></p>
    <h4>HTML result</h4>
    <p><?php echo $Page->resolve_icon_as_html ('{icons}file_types/file', 'Webcore File Icon', '50px', 'margin-left: .5em; margin-bottom: .5em; float: right'); ?>
      This is purely sample text to illustrate that the text immediately following
      an image tag will be formatted around the image if it has an 'align' value
      of 'left' or 'right'. If it has an alignment of 'center', the image is
      placed in its own block, separating the text flow. If it is 'none', the
      image is simply rendered inline with the text.</p>
    <p style="text-align: center">
      <a href="http://data.earthli.com/albums/oz/images/im000185.jpg"><img title="Ozzie in the garden" alt="Ozzie in the garden" src="http://data.earthli.com/albums/oz/images/im000185_tn.jpg"></a>
    </p>
    <h4 style="clear: right">Plain text result</h4>
    <p>[WebCore Logo] This is purely sample text to illustrate that the text
      immediately following an image tag will be formatted around the image if
      it has an 'align' value of 'left' or 'right'. If it has an alignment of
      'center', the image is placed in its own block, separating the text flow.
      If it is 'none', the image is simply rendered inline with the text.</p>
    <p>[Ozzie in the garden] &lt;http://www.earthli.com/users/oz/images/IM000185.jpg&gt;</p>
    <h2 id="videos">Videos</h2>
    <p>Videos can be embedded just as easily as images with the
      <span class="highlight">&lt;img&gt;</span> tag. In fact, almost all of the
      properties documented for <a href="#images">image</a> handling work exactly
      the same for videos. You can reference local videos using the "attachment"
      property or remote videos using a full url in the "src" property. The "href"
      property is not used for videos.</p>
    <h2 id="fonts">Font formatting</h2>
    <p>There are two font formatting tags, <span class="highlight">&lt;i&gt;</span>
      and <span class="highlight">&lt;b&gt;</span>. In the HTML formatter, these
      translate to 'em' and 'strong'. They are ignored in the plain-text formatter.</p>
    <h2 id="generic">Generic formatting</h2>
    <p>You may also use <span class="highlight">&lt;span&gt;</span> and <span class="highlight">&lt;div&gt;</span>
      tags. In the HTML formatter, they are copied in as tags and will have
      whatever functionality the browser gives them. They are ignored in the
      plain-text formatter.</p>
    <h2 id="lists">Lists</h2>
    <p>Unordered, ordered and definition lists are supported. Use a <span class="highlight">&lt;ul&gt;</span>
      tag to wrap text in an unordered list, an <span class="highlight">&lt;ol&gt;</span>
      tag to create an ordered one and a <span class="highlight">&lt;dl&gt;</span> tag to create
      a definition list. A new list item is created for each newline
      encountered in the list. The first and last newlines in a list are always
      ignored and are assumed to be for tag formatting. Lists and other tags
      can be freely mixed and nested. Plain text formatting will maintain vertical margins even if none
      are specified in the source text.</p>
    <p>Tag attributes, if specified, are <em>retained</em>.</p>
    <h3>Examples</h3>
    <p>This is the way you would normally write lists, with indenting and newlines
      handled as expected. The indenting is <em>not</em> necessary here, but
      is used to make the source text clearer.</p>
    <div class="chart" style="width: 25em">
      <div class="chart-title">Example 1</div>
      <div class="chart-body">
        <table>
          <tr>
            <td> <pre>&lt;ul&gt;
One
&lt;ol&gt;
  1.25
  1.50
  1.75
&lt;/ol&gt;
Two
Three
&lt;/ul&gt;</pre> </td>
            <td style="width: 5em; text-align: center">=></td>
            <td> <ul>
                <li>One
                  <ol>
                    <li>1.25</li>
                    <li>1.50</li>
                    <li>1.75</li>
                  </ol>
                </li>
                <li>Two</li>
                <li>Three</li>
              </ul></td>
          </tr>
        </table>
      </div>
    </div>
    <p>It is possible to <em>fully</em> nest a list within an item, so that
      the item's text can continue after the list. The first item in the outer
      list is now formatted that way, so that 'Two' is no longer it's own list
      item. The only difference here is that there is no new line after 'One'
      in the text; this indicates that the item should continue after the embedded
      list.</p>
    <div class="chart" style="width: 25em">
      <div class="chart-title">Example 2</div>
      <div class="chart-body">
        <table>
          <tr>
            <td> <pre>&lt;ul&gt;
One&lt;ol&gt;
  1.25
  1.50
  1.75
&lt;/ol&gt;
Two
Three
&lt;/ul&gt;</pre> </td>
            <td style="width: 5em; text-align: center">=></td>
            <td> <ul>
                <li>One
                  <ol>
                    <li>1.25</li>
                    <li>1.50</li>
                    <li>1.75</li>
                  </ol>
                  Two</li>
                <li>Three</li>
              </ul></td>
          </tr>
        </table>
      </div>
    </div>
    <p>Inserting blank lines will generate blank list items.</p>
    <div class="chart" style="width: 25em">
      <div class="chart-title">Example 3</div>
      <div class="chart-body">
        <table>
          <tr>
            <td> <pre>&lt;ul&gt;

One
&lt;ol&gt;
  1.25
  1.50
  1.75
&lt;/ol&gt;
Two
Three
More...



&lt;/ul&gt;</pre> </td>
            <td style="width: 5em; text-align: center">=></td>
            <td> <ul>
                <li>&nbsp;</li>
                <li>One
                  <ol>
                    <li>1.25</li>
                    <li>1.50</li>
                    <li>1.75</li>
                  </ol>
                </li>
                <li>Two</li>
                <li>Three</li>
                <li>More...</li>
                <li>&nbsp;</li>
                <li>&nbsp;</li>
                <li>&nbsp;</li>
              </ul></td>
          </tr>
        </table>
      </div>
    </div>
    <p>Definition lists generate alternating terms and definitions for each newline
      between the start and end tags. Two attributes are supported: <span class="highlight">dt_class</span>
      and <span class="highlight">dd_class</span>, which define the CSS class for definition terms and
      definitions, respectively.</p>
    <div class="chart" style="width: 40em">
      <div class="chart-title">Example 4</div>
      <div class="chart-body">
        <table>
          <tr>
            <td> <pre>&lt;dl dt_class="field" dd_class="notes"&gt;
First Term
Definition.
Second Term
Definition.
&lt;/dl&gt;</pre> </td>
            <td style="width: 5em; text-align: center">=></td>
            <td> <dl>
                <dt class="field">First Term</dt>
                <dd class="notes">Definition.</dd>
                <dt class="field">Second Term</dt>
                <dd class="notes">Definition.</dd>
              </dl></td>
          </tr>
        </table>
      </div>
    </div>
    <h2 id="quoting">Quoting</h2>
    <p>Often, you pull information from other sites. You can indicate this with
      the <span class="highlight">&lt;iq&gt;</span> (inline quote) and <span class="highlight">&lt;bq&gt;</span>
      (block quote) tags. The inline-quote just applies formatting and coloring.
      The block quote will put the text in a separate block and indent it slightly,
      while also providing theme-specific coloring and formatting.</p>
    <p>Tag attributes, if specified, are <em>discarded</em>.</p>
    <h3>Inline Quote Example</h3>
    <p>As Mark Twain once said, &lt;iq&gt;A banker is a fellow who lends you
      his umbrella when the sun is shining, but wants it back the minute it
      begins to rain.&lt;/iq&gt;.</p>
    <h4>Result</h4>
    <p>As Mark Twain once said, <span class="quote-inline">"A banker is a fellow
      who lends you his umbrella when the sun is shining, but wants it back
      the minute it begins to rain."</span></p>
    <h3>Block Quote Example</h3>
    <p>As Mark Twain once said, &lt;bq&gt;A banker is a fellow who lends you
      his umbrella when the sun is shining, but wants it back the minute it
      begins to rain.&lt;/bq&gt;</p>
    <h4>Result</h4>
    <p>As Mark Twain once said,</p>
    <div class="quote-block"><p>"A banker is a fellow who lends you his umbrella
      when the sun is shining, but wants it back the minute it begins to rain."</p></div>
    <h2 id="notes">Notes</h2>
    <p>Another useful tag is the <span class="highlight">&lt;n&gt;</span>
      tag. This uses a smaller font and is generally in italics to indicate that
      the text is supplemental or tangential. In the HTML formatter, this
      translates to the 'notes' CSS style. It is ignored in the plain-text formatter.</p>
    <h3>Notes Example</h3>
    <p>This is the main flow of text. Here is where I mention that you may look below*
      for more information. The normal flow continues until the paragraph ends.</p>
    <p>&lt;n&gt;*This is the tangential extra information I referenced above.&lt;/n&gt;</p>
    <h4>Result</h4>
    <p>This is the main flow of text. Here is where I mention that you may look below*
      for more information. The normal flow continues until the paragraph ends.</p>
    <p><span class="notes">*This is the tangential extra information I referenced above.</span></p>
    <h2 id="boxes">Boxes</h2>
    <p>When formatting code or larger quoted samples, it's nice to be able box
      the content out. Use the <span class="highlight">&lt;box&gt;</span> tag
      for this, with the following (optional) attributes:</p>
    <dl>
      <dt class="field">title</dt>
      <dd>The title of the box. This is usually formatted as a title bar on
        the box itself.</dd>
      <dt class="field">align</dt>
      <dd>left, center, right or none. The default is none. Right and left float
        the box to the right or left and center leaves it centered.</dd>
      <dt class="field">width</dt>
      <dd>If you specify a left or right alignment, you will probably want to
        specify a width as well. This attribute accepts all CSS values.</dd>
      <dt class="field">class</dt>
      <dd>This will assign the specified CSS class to the innermost generated
        container (affecting the box's content).</dd>
    </dl>
    <p>All other attributes, if specified, are <em>discarded</em>.</p>
    <h3>Example</h3>
    <pre><code>&lt;box title=&quot;Listing One&quot; align=&quot;right&quot; width=&quot;35%&quot;&gt;
&lt;pre&gt;&lt;code&gt;</code>function add_parsed (&amp;$data)
{
$data = trim ($data);
if ($data)
  // if there is any data being added
{
  $this-&gt;_parse_unparsed_buffer ();
  if (! $this-&gt;_parsing_started)
  {
    // parsed buffer has not
  }
    // been initialized yet
  {
    $this-&gt;_open_buffer ();
    $this-&gt;_parsing_started = true;
  }
  $this-&gt;_parsed_buffer .= $data;
}
}
<code>&lt;/code&gt;&lt;/pre&gt;
&lt;/box&gt;</code></pre>
    <p>This is a piece of code from the MUNGER class in the WebCore library.
      This text here is just to show how the box looks when it is floated next
      to content. I'm just going to write as much as I need to in order to make
      the paragraph as long as the box itself. Here's a chunk of text quoted
      from another article:</p>
      <p><code>&lt;bq style="margin-right: 25em"&gt;</code>To Microsoft's credit, they are being
      quite aggressive about solving this particular hole. Even to the point
      of coming up with the embarassing solution of saying that they can't be
      trusted. So, perhaps they do mean it when they say they are now a security
      company and start babbling about Palladium and DRM. They just mean they
      care about telling people about security holes, but don't actually intend
      to write decent software.<code>&lt;/bq&gt;</code></p>
      <p>There, that should make it long enough.</p>
    <h4>Result</h4>
    <div class="chart" style="float: right; margin-left: .5em; width: 35%">
      <div class="chart-title">Listing One</div>
      <div class="chart-body">
        <pre><code class="detail">function add_parsed ($data)
{
$data = trim ($data);
if ($data)
  // if there is any
  //data being added
{
  $this->_parse_unparsed_buffer ();
  if (! $this->_parsing_started)
  {
    // parsed buffer has
  }
    // not been initialized yet
  {
    $this->_open_buffer ();
    $this->_parsing_started = true;
  }
  $this->_parsed_buffer .= $data;
}
}
</code></pre>
      </div>
    </div>
    <p>This is a piece of code from the MUNGER class in the WebCore library.
      This text here is just to show how the box looks when it is floated next
      to content. I'm just going to write as much as I need to in order to make
      the paragraph as long as the box itself. Here's a chunk of text quoted
      from another article:</p>
      <div class="quote-block" style="margin-right: 25em">"To Microsoft's credit,
      they are being quite aggressive about solving this particular hole. Even
      to the point of coming up with the embarassing solution of saying that
      they can't be trusted. So, perhaps they do mean it when they say they
      are now a security company and start babbling about Palladium and DRM.
      They just mean they care about telling people about security holes, but
      don't actually intend to write decent software."</div>
      <p>There, that should make it long enough.</p>
    <p style="clear: both; padding-top: 1em">The width is not specified because the &lt;pre&gt;
      tag is used and that will size the box correctly in most browsers. If
      you just have flowing text, you should use a width or the box will simply
      take up the width of the window and won't really be floating. Note also
      how the block-quote has a right margin to prevent it from flowing behind
      the floating box. This addresses a limitation in CSS2; CSS3 should provide
      a style which honors margins with respect to floating elements as well.
    </p>
  </div>
</div>
<?php
  $Page->finish_display ();
?>
