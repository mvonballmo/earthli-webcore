<?php

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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

  $Page->location->add_root_link();

  $Page->location->append ($Page->title->subject);

  $Page->start_display ();
?>
<div  class="main-box">
  <div class="columns text-flow">
    <div class="left-sidebar">
      <h2>Text</h2>
      <ul>
        <li><a href="#exceptions">Single-line vs. Multi-line</a></li>
        <li><a href="#new_lines">Newline handling</a></li>
        <li><a href="#characters">Special Characters</a></li>
        <li><a href="#literals">Literal characters</a></li>
        <li><a href="#tags">Tags</a></li>
        <li><a href="#Usingastext">Using &lt; as text</a></li>
      </ul>
      <h2>Basic</h2>
      <ul>
        <li><a href="#attributes">Standard attributes</a></li>
        <li><a href="#generic">Generic formatting</a></li>
        <li><a href="#character">Character formatting</a></li>
        <li><a href="#headings">Headings</a></li>
        <li><a href="#lists">Lists</a></li>
      </ul>
      <h2>Links</h2>
      <ul>
        <li><a href="#links">Links</a></li>
        <li><a href="#images">Images</a></li>
        <li><a href="#videos">Videos</a></li>
        <li><a href="#paths">Paths/resources</a></li>
      </ul>
      <h2>Special</h2>
      <ul>
        <li><a href="#pre_text">Pre-formatted text</a></li>
        <li><a href="#code_text">Source code</a></li>
        <li><a href="#quoting">Quoting</a></li>
        <li><a href="#notes">Notes</a></li>
        <li><a href="#abstract">Abstract</a></li>
        <li><a href="#pullquotes">Pull quotes</a></li>
        <li><a href="#boxes">Boxes</a></li>
        <li><a href="#messages">Messages</a></li>
        <li><a href="#footnotes">Footnotes</a></li>
        <li><a href="#clear">Clearing floats</a></li>
        <li><a href="#anchors">Anchors</a></li>
        <li><a href="#rules">Rules</a></li>
      </ul>
      <h2>Advanced</h2>
      <ul>
        <li><a href="#macro">Controlling the formatter</a></li>
      </ul>
    </div>
    <div>
      <p>Blocks of text in this application are mainly plain
        text. That means that newline characters will be faithfully represented
        when rendered into an output format. There are also several tags supported
        that allow you to build commonly used constructions, like ordered and
        unordered lists, pre-formatted blocks, quoted blocks and more.</p>
      <p>These tags look like HTML and often share a name with a similar HTML
        element, but are <em>not</em> actually HTML tags. The content you write
        is transformed to HTML when displayed in a web page, but can also be transformed
        to plain-text&mdash;e.g. when generating a plain-text email.</p>
      <h1>Text</h1>
      <h2 id="exceptions">Single-line vs. Multi-line</h2>
      <p>Some fields, like object titles, are explicitly single-line and support only a
        limited number of nested tags. If block or image tags are included, they
        are instead inserted as if they were just text. Only the <span class="highlight">&lt;b&gt;</span>,
        <span class="highlight">&lt;i&gt;</span> and <span class="highlight">&lt;c&gt;</span>
        tags are supported in single-line mode.</p>
      <h2 id="new_lines">Newline handling</h2>
      <p>If you are just writing unformatted text, you may treat newlines as you
        normally would; both the HTML and plain-text formatter will honor the
        vertical spacing you've chosen. In some cases, HTML forces extra spacing that cannot
        be avoided&mdash;e.g. if you separate a normal text run from a pre-formatted
        text run with a single newline, HTML will render this with a double newline
        because the blocks are separated by a margin. In these cases, it makes
        no difference if you use zero, one or two newlines -- there are always
        two newlines rendered. The plain-text formatter has been written to emulate
        this behavior, so that the plain-text representation is as close as possible
        to the 'main' HTML output format.</p>
      <h2 id="characters">Special Characters</h2>
      <p>Certain characters will be automatically replaced if they appear anywhere
        in regular text. You can disable this replacement for whole
        regions by using the <a href="#macro">&lt;macro&gt;</a> tag.</p>
      <table class="basic columns">
        <tr>
          <th>Input</th>
          <th>Output</th>
        </tr>
        <tr>
          <td>---</td><td>&mdash;</td>
        </tr>
        <tr>
          <td>--</td><td>&#8211;</td>
        </tr>
        <tr>
          <td>1/2</td><td>&frac12;</td>
        </tr>
        <tr>
          <td>1/4</td><td>&frac14;</td>
        </tr>
        <tr>
          <td>3/4</td><td>&frac34;</td>
        </tr>
        <tr>
          <td>...</td><td>&#8230;</td>
        </tr>
        <tr>
          <td>(tm)</td><td>&trade;</td>
        </tr>
        <tr>
          <td>(c)</td><td>&copy;</td>
        </tr>
        <tr>
          <td>(r)</td><td>&reg;</td>
        </tr>
        <tr>
          <td> x </td><td>&times;</td>
        </tr>
        <tr>
          <td> - </td><td>&minus;</td>
        </tr>
        <tr>
          <td>(C,)</td><td>&Ccedil;</td>
        </tr>
        <tr>
          <td>(c,)</td><td>&ccedil;</td>
        </tr>
        <tr>
          <td>(C-)</td><td>&Ccaron;</td>
        </tr>
        <tr>
          <td>(c-)</td><td>&ccaron;</td>
        </tr>
        <tr>
          <td>(C')</td><td>&Cacute;</td>
        </tr>
        <tr>
          <td>(c')</td><td>&cacute;</td>
        </tr>
        <tr>
          <td>(g-)</td><td>&#287;</td>
        </tr>
        <tr>
          <td>(i-)</td><td>&#305;</td>
        </tr>
        <tr>
          <td>(I.)</td><td>&#304;</td>
        </tr>
        <tr>
          <td>(S,)</td><td>&#350;</td>
        </tr>
        <tr>
          <td>(s,)</td><td>&#351;</td>
        </tr>
        <tr>
          <td>(S-)</td><td>&Scaron;</td>
        </tr>
        <tr>
          <td>(s-)</td><td>&scaron;</td>
        </tr>
        <tr>
          <td>(Z-)</td><td>&#381;</td>
        </tr>
        <tr>
          <td>(z-)</td><td>&#382;</td>
        </tr>
        <tr>
          <td>(-cmd)</td><td>&#8984;</td>
        </tr>
        <tr>
          <td>(-del)</td><td>&#9003;</td>
        </tr>
        <tr>
          <td>(-eject)</td><td>&#9167;</td>
        </tr>
        <tr>
          <td>(-enter)</td><td>&#9166;</td>
        </tr>
        <tr>
          <td>(-opt)</td><td>&#8997;</td>
        </tr>
        <tr>
          <td>(-shift)</td><td>&#8679;</td>
        </tr>
        <tr>
          <td>(-tab)</td><td>&#8677;</td>
        </tr>
      </table>
      <p>Additionally, the following character pairs are automatically converted to
        their ligature equivalents. Most browsers don't especially like these characters<a href="#ligatures_footnote_body" id="ligatures_footnote_ref" class="footnote-number">[1]</a>,
        so this conversion is off, by default. You can enable this replacement for
        whole regions using the <a href="#macro">&lt;macro&gt;</a> tag.</p>
      <table class="basic columns">
        <tr>
          <th>Input</th>
          <th>Output</th>
        </tr>
        <tr>
          <td>ffi</td><td>&#xfb03;</td>
        </tr>
        <tr>
          <td>ffl</td><td>&#xfb04;</td>
        </tr>
        <tr>
          <td>ff</td><td>&#xfb00;</td>
        </tr>
        <tr>
          <td>fi</td><td>&#xfb01;</td>
        </tr>
        <tr>
          <td>fl</td><td>&#xfb02;</td>
        </tr>
      </table>
      <div class="footnote-reference"><span id="ligatures_footnote_body" class="footnote-number">[1]</span>As of this writing,
        in 2014, all tested browser still consistently take the ligature from a fallback font rather than the specialized web
        fonts used in the page. This is likely because such fonts (e.g. Raleway) don't have explicit ligatures defined. In such
        cases, though, falling back to using individual letters is much preferred to importing a ligature from a completely
        different font.<a href="#ligatures_footnote_ref" class="footnote-return" title="Jump back to reference.">&#8617;</a></div>
      <h2 id="literals">Literal characters</h2>
      <p>In this version, you may type anything you like; there is no longer a
        need to specifically escape characters as HTML. The input language is
        no longer HTML, so escaping characters has been limited to the single
        case of the &lt; character, discussed next.</p>
      <h2 id="tags">Tags</h2>
      <p>Tags are the same format as HTML tags (e.g. &lt;tag attr=&quot;value&quot;&gt;).
        The tokenizer recognizes as a tag any grouping of text that starts with
        &lt;, has a letter or number as the next letter, then ends with &gt;.
        This is discussed in more detail in <a href="#Usingastext">Using &lt;
          as text</a>. If a tag is not recognized, it will be rendered as text.
        The list of recognized tags follow and are recognized by both the HTML
        and plain-text formatters. When a tag is recognized, that means that it
        will not be rendered as text by the formatter, but will either be used
        directly, transformed to another tag or construct or discarded, depending
        on the output format.</p>
      <p>Unknown tags are rendered as text, by default.</p>
      <h2 id="Usingastext">Using &lt; as text</h2>
      <p>Since the content can be delimited by tags, the &lt; character must
        be escaped in certain circumstances. These situations have been limited
        so that you will only very rarely have to use the escaped character. The
        only time you <em>may</em> need to escape the &lt; character is if the
        character immediately following it is a letter or number. To escape the
        &lt;, use &lt;&lt;.</p>
      <p>If you want to write: </p>
      <pre><code>x &lt; y and 5 &lt; 8</code></pre>
      <p>you do not need to escape anything. Simply write it as shown above and
        the formatter detects that the &lt; characters in the text cannot be parts
        of tags.</p>
      <p>If you want to write:</p>
      <pre><code>If the text in the input box is still &lt;default&gt;, then you have to...</code></pre>
      <p>you still don't <em>have</em> to escape the bracket, since the formatter
        simply renders unknown tags as text anyway. In the next example, we see
        where we must include an escaped &lt; character.</p>
      <p>If you want to write:</p>
      <pre><code>&lt;pre&gt; to specify a backlink, use &lt;linkname. &lt;/pre&gt;</code></pre>
      <p>The problem here is that the tokenizer will recognize '&lt;linkname.
        &lt;/pre&gt;' as a tag, which will cause the &lt;/pre&gt; end tag to be
        ignored, with unpredictable results. The text will still be output, but
        the -matted region will not be properly rendered. To avoid this,
        use the escaped version of the &lt; character:</p>
      <pre><code>&lt;pre&gt; to specify a backlink, use &lt;&lt;linkname. &lt;/pre&gt;</code></pre>
      <h1>Basic</h1>
      <h2 id="attributes">Standard attributes</h2>
      <p>All other attributes, unless otherwise specified below, are <em>discarded</em>.</p>
      <h3>All tags</h3>
      <p>The following attributes are recognized for all tags:</p>
      <table class="basic columns left-labels">
        <tr>
          <th>Name</th>
          <th>Comments</th>
        </tr>
        <tr>
          <th>id</th><td>Can be used with the <span class="highlight">anchor</span> tag</td>
        </tr>
        <tr>
          <th>class</th><td>References classes defined in CSS</td>
        </tr>
        <tr>
          <th>style</th><td>CSS directives</td>
        </tr>
        <tr>
          <th>title</th><td>Generally shown as a tooltip when hovered</td>
        </tr>
      </table>
      <h3>All blocks</h3>
      <p>The following attributes apply to all blocks:</p>
      <table class="basic columns left-labels top">
        <tr>
          <th>align</th>
          <td>
            <p>The default value is 'none'. This parameter is ignored in the plain-text renderer.</p>
            <table class="basic columns left-labels top">
              <tr>
                <th>left</th>
                <td>Float the box to the left,
                  with text wrapped around to the right.</td>
              </tr>
              <tr>
                <th>left-column</th>
                <td>The same as <span class="highlight">left</span>, but also clears all preceding left-aligned elements.
                Use this value to stack floated elements to one side or the other.</td>
              </tr>
              <tr>
                <th>right</th>
                <td>Float the box to the right,
                  with text wrapped around to the left.</td>
              </tr>
              <tr>
                <th>right-column</th>
                <td>The same as <span class="highlight">right</span>, but also clears all preceding right-aligned elements.
                  Use this value to stack floated elements to one side or the other.</td>
              </tr>
              <tr>
                <th>center</th>
                <td>Box is centered in its own block. Text is split, with the flow continuing after the image.</td>
              </tr>
              <tr>
                <th>none</th>
                <td>Box is formatted inline with the text.</td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <th>clear</th>
          <td>
            <p>The default value is 'none'. This parameter is ignored in the plain-text renderer.</p>
            <table class="basic columns left-labels top">
              <tr>
                <th>both</th>
                <td>Clear floating elements on both sides.</td>
              </tr>
              <tr>
                <th>left</th>
                <td>Clear floating elements only on the left side.</td>
              </tr>
              <tr>
                <th>right</th>
                <td>Clear floating elements only on the right side.</td>
              </tr>
              <tr>
                <th>none</th>
                <td>Don't clear any floating elements.</td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <th>width</th>
          <td>If you specify a left or right alignment, you will probably want to
            specify a width as well. This attribute accepts all CSS values.</td>
        </tr>
      </table>
      <h3>Block captions</h3>
      <p>These attributes also apply to all blocks and are used to construct the caption for a block.</p>
      <table class="basic columns left-labels top">
        <tr>
          <th>author</th>
          <td>Included in the caption by appending "by AUTHOR"</td>
        </tr>
        <tr>
          <th>date</th>
          <td>Included in the caption by appending "on DATE"</td>
        </tr>
        <tr>
          <th>href</th>
          <td>Wrapped as a link around the <span class="highlight">caption</span>, if both are given</td>
        </tr>
        <tr>
          <th>source</th>
          <td>Included in the caption by appending "(SOURCE)" wrapped in a link that goes to the root domain
            of the <span class="highlight">href</span> value.</td>
        </tr>
        <tr>
          <th>caption</th>
          <td>If specified, it will be included with the block (top or bottom, depending
            on <span class="highlight">caption-position</span>).</td>
        </tr>
        <tr>
          <th>caption-position</th>
          <td>
            <p>The default value is 'top'. This parameter is ignored in the plain-text renderer.</p>
            <table class="basic columns left-labels top">
              <tr>
                <th>top</th>
                <td>Show the caption above the box.</td>
              </tr>
              <tr>
                <th>bottom</th>
                <td>Show the caption below the box.</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
      <div class="preview ">
        <h3 class="preview-title">Example</h3>
        <table class="basic">
          <tr>
            <td style="width: 50%">
              <span class="highlight">&lt;bq href="http://earthli.com/news" source="Earthli News" author="Marco" date="Feb 2014" caption="Some article"&gt;</span>This is the content of the quote<span class="highlight">&lt;/bq&gt;</span>
            </td>
            <td style="font-size: 150%">&rarr;</td>
            <td>
              <div><div class="auto-content-block"><blockquote class="quote quote-block"><div>&ldquo;This is the content of the quote&rdquo;</div></blockquote></div><div class="auto-content-caption"><a href="http://earthli.com/news">Some article</a> by <cite>Marco</cite> on Feb 2014 (<cite><a href="http://earthli.com/">Earthli News</a></cite>)</div></div>
            </td>
          </tr>
        </table>
      </div>
      <h2 id="generic">Generic formatting</h2>
      <p>You may also use <span class="highlight">&lt;span&gt;</span> and <span class="highlight">&lt;div&gt;</span>
        tags. In the HTML formatter, they are copied in as tags and will have
        whatever functionality the browser gives them. They are stripped by the
        plain-text formatter.</p>
      <h2 id="character">Character formatting</h2>
      <p>There are several character-formatting tags, aligned more or less with the common
        HTML tags. These are all stripped by the plain-text formatter.</p>
      <table class="basic columns left-labels">
        <tr>
          <th>Tag</th>
          <th>Input</th>
          <th>Output</th>
          <th>Description</th>
        </tr>
        <tr>
          <th>b</th>
          <td>&lt;b&gt;strong&lt;/b&gt;</td>
          <td><strong>strong</strong></td>
          <td>Strongly formatted text</td>
        </tr>
        <tr>
          <th>i</th>
          <td>&lt;i&gt;emphasized&lt;/i&gt;</td>
          <td><em>emphasized</em></td>
          <td>Emphasized text</td>
        </tr>
        <tr>
          <th>n</th>
          <td>&lt;n&gt;notes&lt;/n&gt;</td>
          <td><small class="notes">notes</small></td>
          <td>Notes/comments</td>
        </tr>
        <tr>
          <th>c</th>
          <td>&lt;c&gt;code&lt;/c&gt;</td>
          <td><code>code</code></td>
          <td>Inline code</td>
        </tr>
        <tr>
          <th>hl</th>
          <td>&lt;hl&gt;highlighted&lt;/hl&gt;</td>
          <td><strong class="highlight">highlighted</strong></td>
          <td>Highlighted text</td>
        </tr>
        <tr>
          <th>del</th>
          <td>&lt;del&gt;deleted&lt;/del&gt;</td>
          <td><del>deleted</del></td>
          <td>Deleted text</td>
        </tr>
        <tr>
          <th>var</th>
          <td>&lt;var&gt;variableOne&lt;/var&gt;</td>
          <td><var>variableOne</var></td>
          <td>Variable names</td>
        </tr>
        <tr>
          <th>kbd</th>
          <td>&lt;kbd&gt;(-cmd)&lt;/kbd&gt; + &lt;kbd&gt;B&lt;/kbd&gt;</td>
          <td><kbd>&#8984;</kbd> + <kbd>B</kbd></td>
          <td>Keyboard characters</td>
        </tr>
        <tr>
          <th>dfn</th>
          <td>&lt;dfn&gt;definition&lt;/dfn&gt;</td>
          <td><dfn>definition</dfn></td>
          <td>Definitions</td>
        </tr>
        <tr>
          <th>abbr</th>
          <td>&lt;abbr title="Computer-aided Design"&gt;CAD&lt;/abbr&gt;</td>
          <td><abbr title="Computer-aided Design">CAD</abbr></td>
          <td>Abbreviations</td>
        </tr>
        <tr>
          <th>cite</th>
          <td>&lt;cite&gt;citation&lt;/cite&gt;</td>
          <td><cite>citation</cite></td>
          <td>Citations</td>
        </tr>
        <tr>
          <th>sub</th>
          <td>10&lt;sub&gt;n&lt;/sub&gt;</td>
          <td>10<sub>n</sub></td>
          <td>Subscripts</td>
        </tr>
        <tr>
          <th>sup</th>
          <td>2&lt;sup&gt;16&lt;/sup&gt;</td>
          <td>2<sup>16</sup></td>
          <td>Superscripts</td>
        </tr>
      </table>
      <h2 id="headings">Headings</h2>
      <p>Headings are written with the <span class="highlight">&lt;h&gt;</span> tag. Use
        headings to delineate new sections in longer text flows. The default heading is
        slightly larger than regular text and bold. Plain-text mode will maintain proper
        spacing for headings even if you don't specify it.</p>
      <p>The following additional attributes are allowed.</p>
      <table class="basic columns left-labels top">
        <tr>
          <th>level</th>
          <td>Heading level, analogous to the HTML heading level. The default is 2.
            Since headings are usually just used to distinguish between sections, you
            shouldn't often need to control the heading level.</td>
        </tr>
      </table>
      <div class="preview ">
        <h3 class="preview-title">Example</h3>
        <table class="basic">
          <tr>
            <td style="width: 50%">
              <p><span class="highlight">&lt;h&gt;</span>Products<span class="highlight">&lt;/h&gt;</span></p>
              <p><span class="highlight">&lt;h level="3"&gt;</span>earthli WebCore<span class="highlight">&lt;/h&gt;</span></p>
            </td>
            <td style="font-size: 150%">&rarr;</td>
            <td>
              <h2>Products</h2>
              <h3>earthli WebCore</h3>
            </td>
          </tr>
        </table>
      </div>
      <h2 id="lists">Lists</h2>
      <p>Unordered, ordered and definition lists are supported. Use a <span class="highlight">&lt;ul&gt;</span>
        tag to wrap text in an unordered list, an <span class="highlight">&lt;ol&gt;</span>
        tag to create an ordered one and a <span class="highlight">&lt;dl&gt;</span> tag to create
        a definition list. A new list item is created for each newline
        encountered in the list. The first and last newlines in a list are always
        ignored and are assumed to be for tag formatting. Lists and other tags
        can be freely mixed and nested. Plain-text formatting will maintain vertical margins even if none
        are specified in the source text.</p>
      <p>Tag attributes, if specified, are <em>retained</em>.</p>
      <h3>Examples</h3>
      <p>This is the way you would normally write lists, with indenting and newlines
        handled as expected. The indenting is <em>not</em> necessary here, but
        is used to make the source text clearer.</p>
      <div class="preview">
        <h3 class="preview-title">Example 1</h3>
        <table class="basic">
          <tr>
            <td><span class="highlight">&lt;ul&gt;</span><br>
    One<br>
    <span class="highlight">&lt;ol&gt;</span><br>
    &nbsp;&nbsp;1.25<br>
    &nbsp;&nbsp;1.50<br>
    &nbsp;&nbsp;1.75<br>
    <span class="highlight">&lt;/ol&gt;</span><br>
    Two<br>
    Three<br>
    <span class="highlight">&lt;/ul&gt;</span></td>
            <td style="font-size: 150%">&rarr;</td>
            <td><ul>
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
      <p>It is possible to <em>fully</em> nest a list within an item, so that
        the item's text can continue after the list. The first item in the outer
        list is now formatted that way, so that 'Two' is no longer it's own list
        item. The only difference here is that there is no new line after 'One'
        in the text; this indicates that the item should continue after the embedded
        list.</p>
      <div class="preview">
        <h3 class="preview-title">Example 2</h3>
        <table class="basic">
          <tr>
            <td><span class="highlight">&lt;ul&gt;</span><br>
    One<span class="highlight">&lt;ol&gt;</span><br>
    &nbsp;&nbsp;1.25<br>
    &nbsp;&nbsp;1.50<br>
    &nbsp;&nbsp;1.75<br>
    <span class="highlight">&lt;/ol&gt;</span><br>
    Two<br>
    Three<br>
    <span class="highlight">&lt;/ul&gt;</span></td>
            <td style="font-size: 150%">&rarr;</td>
            <td><ul>
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
      <p>Inserting blank lines will generate blank list items.</p>
      <div class="preview">
        <h3 class="preview-title">Example 3</h3>
        <table class="basic">
          <tr>
            <td><span class="highlight">&lt;ul&gt;</span><br>
    <br>
    One<br>
    <span class="highlight">&lt;ol&gt;</span><br>
    &nbsp;&nbsp;1.25<br>
    &nbsp;&nbsp;1.50<br>
    &nbsp;&nbsp;1.75<br>
    <span class="highlight">&lt;/ol&gt;</span><br>
    Two<br>
    Three<br>
    More...<br>
    <br>
    <br>
    <br>
    <span class="highlight">&lt;/ul&gt;</span></td>
            <td style="font-size: 150%">&rarr;</td>
            <td><ul>
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
      <p>Definition lists generate alternating terms and definitions for each newline
        between the start and end tags. Two attributes are supported: <span class="highlight">dt_class</span>
        and <span class="highlight">dd_class</span>, which define the CSS class for definition terms and
        definitions, respectively. Use a <span class="highlight">&lt;div&gt;</span> tag to include more complex
      formatting in the definition, as shown in the example below, which has multiple paragraphs.</p>
      <?php

      ?>
      <div class="preview">
        <h3 class="preview-title">Example 4</h3>
        <table class="basic">
          <tr>
            <td><span class="highlight">&lt;dl dt_class="field" dd_class="notes"&gt;</span><br>
    First Term<br>
              Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac
              leo accumsan, semper magna in, consectetur metus. <br>
    Second Term<br>
              <span class="highlight">&lt;div&gt;</span><br>
                Mauris dictum adipiscing metus sed accumsan.<br>
                <br>
                Aenean facilisis justo lacus, et fringilla arcu luctus id.<br>
                <br>
                Nulla at tortor at erat sagittis pellentesque.<span class="highlight">&lt;/div&gt;</span><br>
    <span class="highlight">&lt;/dl&gt;</span></td>
            <td style="font-size: 150%">&rarr;</td>
            <td><dl>
                <dt class="field">First Term</dt>
                <dd class="notes">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac
                  leo accumsan, semper magna in, consectetur metus.</dd>
                <dt class="field">Second Term</dt>
                <dd class="notes">
                  <div>
                    <p>Mauris dictum adipiscing metus sed accumsan.</p>
                    <p>Aenean facilisis justo lacus, et fringilla arcu luctus id.</p>
                    <p>Nulla at tortor at erat sagittis pellentesque.</p>
                  </div>
                </dd>
              </dl></td>
          </tr>
        </table>
      </div>
      <h1>Links</h1>
      <h2 id="links">Links</h2>
      <p>Links are written with the <span class="highlight">&lt;a&gt;</span> tag.
        The following attributes are allowed.</p>
      <table class="basic columns left-labels top">
        <tr>
          <th>title</th>
          <td>The title of the link. Can be a longer description of the resource
            to which the link goes.</td>
        </tr>
        <tr>
          <th>class</th>
          <td>This will assign the specified CSS class to the link itself.</td>
        </tr>
        <tr>
          <th>href</th>
          <td>The url to which the link goes.</td>
        </tr>
        <tr>
          <th>format</th>
          <td><p> This is used only in the plain-text renderer. The default value is 'all'.</p>
            <table class="basic columns left-labels top">
              <tr>
                <th>url</th>
                <td>Show only the url.</td>
              </tr>
              <tr>
                <th>all</th>
                <td>Show the url and title.</td>
              </tr>
              <tr>
                <th>none</th>
                <td>Skip this link.</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
      <div class="preview ">
        <h3 class="preview-title">Example</h3>
        <table class="basic">
          <tr>
            <td style="width: 50%">
              <span class="highlight">&lt;a href=&quot;http://www.earthli.com/&#x200b;software/webcore/&quot; title=&quot;Try out the earthli WebCore!&quot;&gt;earthli WebCore&lt;/a&gt;</span>
            </td>
            <td style="font-size: 150%">&rarr;</td>
            <td>
              <a href="http://www.earthli.com/software/webcore/" title="Try out the earthli WebCore!">earthli WebCore</a>
            </td>
          </tr>
        </table>
      </div>
      <h2 id="images">Images</h2>
      <p>Images are inserted with the <span class="highlight">&lt;img&gt;</span>
        tag. The following list of tags are supported.</p>
      <p class="notes">The image must already exist as a URL. Inserting an image link does not
        magically upload a file for you.</p>
      <table class="basic columns left-labels top">
        <tr>
          <th>src</th>
          <td>The url for the image itself. Use path/resource syntax or an absolute
            URL.</td>
        </tr>
        <tr>
          <th>href</th>
          <td>If this is non-empty, a link is automatically wrapped around the image
            with this url. Use path/resource syntax or an absolute URL.</td>
        </tr>
        <tr>
          <th>attachment</th>
          <td>If this is non-empty, both "src" and "href" are ignored. Instead, both
            properties are automatically generated from the attachment file name given.
            It's up to the user to make sure the attachment is exists and is accessible.</td>
        </tr>
        <tr>
          <th>scale</th>
          <td>A percentage value, generally between 10% and 100%</td>
        </tr>
        <tr>
          <th>alt</th>
          <td>Alternate description of the image. Should be concise. Put longer
            description in 'title', if needed. If this is not given, then the value
            for 'title' is used.</td>
        </tr>
        <tr>
          <th>format</th>
          <td>
            <p>This is used only in the plain-text renderer. The default value
              is 'all'. If 'alt' is not specified, 'title' is used for the text. If a url is
              included, 'href' is always used before 'src'.</p>
            <table class="basic columns left-labels top">
              <tr>
                <th>basic</th>
                <td>Show only the 'alt' or 'title'.</td>
              </tr>
              <tr>
                <th>url</th>
                <td>Show a url and 'alt', but no title.</td>
              </tr>
              <tr>
                <th>all</th>
                <td>Show a url, 'alt' and the title
                  (if different than 'alt' and 'alt' is non-empty).</td>
              </tr>
              <tr>
                <th>none</th>
                <td>Skip this image.</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
      <div class="preview">
        <h3 class="preview-title">Example</h3>
        <table class="basic">
          <tr>
            <td style="width: 50%; vertical-align: top">
              <p>
                <span class="highlight">&lt;img src=&quot;{icons}/file_types/file_50px&quot; align=&quot;right&quot; alt=&quot;WebCore File Icon&quot; format=&quot;basic&quot;&gt;</span>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac
                leo accumsan, semper magna in, consectetur metus. Mauris dictum adipiscing metus
                sed accumsan. Aenean facilisis justo lacus, et fringilla arcu luctus id. Nulla at
                tortor at erat sagittis pellentesque.
              </p>
              <p>
                <span class="highlight">&lt;img href=&quot;http://data.earthli.com/&#x200b;albums/oz/images/im000185.jpg&quot;
                src=&quot;http://www.earthli.com/&#x200b;users/oz/images/IM000185_tn.jpg&quot;
                align=&quot;center&quot; title=&quot;Ozzie in the garden&quot;&gt;</span>
              </p>
            </td>
            <td style="font-size: 150%">&rarr;</td>
            <td>
              <p><?php echo $Page->resolve_icon_as_html ('{icons}file_types/file', Fifty_px, 'Webcore File Icon', 'align-right'); ?>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac
                leo accumsan, semper magna in, consectetur metus. Mauris dictum adipiscing metus
                sed accumsan. Aenean facilisis justo lacus, et fringilla arcu luctus id. Nulla at
                tortor at erat sagittis pellentesque.</p>
              <p class="align-center">
                <a href="http://data.earthli.com/albums/oz/images/im000185.jpg"><img title="Ozzie in the garden" alt="Ozzie in the garden" src="http://data.earthli.com/albums/oz/images/im000185_tn.jpg"></a>
              </p>
            </td>
          </tr>
        </table>
      </div>
      <h2 id="videos">Videos</h2>
      <p>Videos can be embedded just as easily as images with the
        <span class="highlight">&lt;media&gt;</span> tag. All of the
        properties documented for <a href="#images">image</a> handling work exactly
        the same for videos. You can reference local videos using the "attachment"
        property or remote videos using a full url in the "src" property. The "href"
        property for videos refers to the page from which the video is being retrieved
        and is used to format the caption, as described in <a href="#attributes">standard attributes for all blocks</a>.</p>
      <table class="basic columns left-labels top">
        <tr>
          <th>src</th>
          <td>The url for the video itself. Use path/resource syntax or an absolute
            URL. It won't always be easy to find out the source URL for videos you want to embed, depending on the site.
            For YouTube, though, it's as easy as taking the URL for the video's page and replacing "watch?v=" with "v/".</td>
        </tr>
        <tr>
          <th>height</th>
          <td>Sets the height of the video using CSS units.</td>
        </tr>
        <tr>
          <th>args</th>
          <td>Arguments that are passed to the plugin. Needed by some non-standard video pages (e.g. Comedy Central).</td>
        </tr>
      </table>
      <div class="preview ">
        <h3 class="preview-title">Example</h3>
        <table class="basic">
          <tr>
            <td style="width: 50%">
              <span class="highlight">&lt;media author="mvonballmo" src="http://www.youtube.com/&#x200b;v/7ryCiS3RxQY" caption="A rainy day in Z&uuml;ri Oberland" href="http://www.youtube.com/&#x200b;watch?v=7ryCiS3RxQY" source="YouTube" width="280px" height="165px"&gt;</span>
            </td>
            <td style="font-size: 150%">&rarr;</td>
            <td>
              <span style="width: 280px; display: table"><span class="auto-content-inline"><embed src="http://www.youtube.com/v/7ryCiS3RxQY" type="application/x-shockwave-flash" style="width: 280px; height: 165px"></embed></span><span class="auto-content-caption"><a href="http://www.youtube.com/watch?v=7ryCiS3RxQY">A rainy day in Z&uuml;ri Oberland</a> by <cite>mvonballmo</cite> (<cite><a href="http://www.youtube.com/">YouTube</a></cite>)</span></span>
            </td>
          </tr>
        </table>
      </div>
      <h2 id="paths">Paths/resources</h2>
      <p>As you'll see below, you can add images and links to your text really easily.
        You don't have to use absolute urls though. You can base your url on a path
        defined in the WebCore application by specifying a base 'location' at the start
        of your url, like this:</p>
      <pre><code>{icons}file_types/file_32px</code></pre>
      <p>If you leave off the extension when referring to an icon file, the default
        application icon extension is applied, so you get:</p>
      <p><?php echo $Page->resolve_icon_as_html ('{icons}file_types/file', Thirty_two_px, ' '); ?></p>
      <p>If, at some point, you move your icons, your reference to the icons folder won't
        be broken. This works for other stuff as well, like attachments. You can refer to
        an attachment file like this:
      <pre><code>{att_link}my_file.zip</code></pre>
      <p>The path to the attachments folder for the current object will be prepended for
        you. If the attachment is an image, you can use that path as the 'src' attribute
        of an image and it will show up in the page. Or you can use <code>{att_thumb}</code>
        to show only the thumbnail for it.</p>
      <p>Supported locations (you can also add your own) are:
      <table class="basic columns left-labels top">
        <tr>
          <th>Alias</th>
          <th>Description</th>
        </tr>
        <tr>
          <th>application</th>
          <td>Root url for the current application</td>
        </tr>
        <tr>
          <th>icons</th>
          <td>Location of the application icons folder</td>
        </tr>
        <tr>
          <th>styles</th>
          <td>Location of the application styles folder</td>
        </tr>
        <tr>
          <th>scripts</th>
          <td>Location of the application scripts folder</td>
        </tr>
        <tr>
          <th>att_link</th>
          <td>Location of attachments for the current object</td>
        </tr>
        <tr>
          <th>att_thumb</th>
          <td>Converts the given attachment file to thumbnail name</td>
        </tr>
        <tr>
          <th>pic_image</th>
          <td>Location of images for the current album. Accepts a picture ID or file name. Only available in earthli Albums</td>
        </tr>
        <tr>
          <th>pic_thumb</th>
          <td>Same as "pic_image", but converts to the thumbnail name. Only available in earthli Albums</td>
        </tr>
      </table>
      <h1>Special</h1>
      <h2 id="pre_text">Pre-formatted text</h2>
      <p>Whitespace is interpreted differently by different output formats, so
        the <span class="highlight">&lt;pre&gt;</span> tag can be used to force
        the formatter to use the exact whitespace you have specified.</p>
      <div class="preview ">
        <h3 class="preview-title">Example</h3>
        <table class="basic">
          <tr>
            <td style="width: 50%">
              <pre>
    <span class="highlight">&lt;pre&gt;</span>Some text
         just
             needs
    to be
       formatted
                just...
                       ...so.<span class="highlight">&lt;/pre&gt;</span>
              </pre>
            </td>
            <td style="font-size: 150%">&rarr;</td>
            <td>
              <pre>
    Some text
         just
             needs
    to be
       formatted
                just...
                       ...so.
              </pre>
            </td>
          </tr>
        </table>
      </div>
      <h2 id="code_text">Source code</h2>
      <p>
        Use the <span class="highlight">&lt;code&gt;</span> tag to highlight source code.
        This tag acts much like the <span class="highlight">pre</span> tag but applies other
        styles. For inline code examples, use the <span class="highlight">&lt;c&gt;</span>
        tag. Text is still generally formatted the same as with the code tag, except
        that it doesn't force block formatting.
      </p>
      <div class="preview ">
        <h3 class="preview-title">Example 1</h3>
        <table class="basic">
          <tr>
            <td style="width: 50%">
              <span class="highlight">&lt;code&gt;</span><br>
              function _process($input, $tokenizer)<br>
              {<br>
              &nbsp;&nbsp;$tokenizer->set_input($input);<br>
              &nbsp;&nbsp;while ($tokenizer->available())<br>
              &nbsp;&nbsp;{<br>
              &nbsp;&nbsp;&nbsp;&nbsp;$tokenizer->read_next();<br>
              &nbsp;&nbsp;&nbsp;&nbsp;$token = $tokenizer->current();<br>
              &nbsp;&nbsp;&nbsp;&nbsp;$this->_process($token);<br>
              &nbsp;&nbsp;}<br>
              }<span class="highlight">&lt;/code&gt;</span>
            </td>
            <td style="font-size: 150%">&rarr;</td>
            <td>
              <pre><code>function _process($input, $tokenizer)
    {
      $tokenizer->set_input($input);
      while ($tokenizer->available())
      {
        $tokenizer->read_next();
        $token = $tokenizer->current();
        $this->_process($token);
      }
    }</code></pre>
            </td>
          </tr>
        </table>
      </div>
      <div class="preview ">
        <h3 class="preview-title">Example 2</h3>
        <table class="basic">
          <tr>
            <td style="width: 40%">
              This example highlights the <span class="highlight">&lt;c&gt;</span>RunProcess()<span class="highlight">&lt;/c&gt;</span> method.
            </td>
            <td style="font-size: 150%">&rarr;</td>
            <td>
              This example highlights the <code>RunProcess()</code> method.
            </td>
          </tr>
        </table>
      </div>
      <h2 id="quoting">Quoting</h2>
      <p>Often, you pull information from other sites. You can indicate this with
        the <span class="highlight">&lt;iq&gt;</span> (inline quote) and <span class="highlight">&lt;bq&gt;</span>
        (block quote) tags. The inline quote just applies formatting and coloring.
        The block quote will put the text in a separate block and indent it slightly,
        while also providing theme-specific coloring and formatting.</p>
      <p>The following attributes apply to <span class="highlight">&lt;bq&gt;</span> tags.</p>
      <table class="basic columns left-labels top">
        <tr>
          <th>quote-style</th>
          <td>
            <p>The default value is 'default'. This parameter applies to both the HTML and the plain-text renderer.</p>
            <table class="basic columns left-labels top">
              <tr>
                <th>multiple</th>
                <td>Add a quote mark at the very beginning of each each contained block/paragraph and quote mark at the
                  very of the quoted content (this is the literary quoting style).</td>
              </tr>
              <tr>
                <th>none</th>
                <td>Do not add any quote marks. Use this when surrounding quotes for lists or code examples otherwise
                  complex, quoted content just looks confusing.</td>
              </tr>
              <tr>
                <th>single</th>
                <td>Add a quote mark at the very beginning and very end of the quoted content.</td>
              </tr>
              <tr>
                <th>default</th>
                <td>A synonym for multiple.</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
      <div class="preview">
        <h3 class="preview-title">Example 1</h3>
        <table class="basic">
          <tr>
            <td style="width: 50%">
              As Mark Twain once said, <span class="highlight">&lt;iq&gt;</span>A banker is a fellow who lends you
              his umbrella when the sun is shining, but wants it back the minute it
              begins to rain.<span class="highlight">&lt;/iq&gt;</span>.
            </td>
            <td style="font-size: 150%">&rarr;</td>
            <td>
              As Mark Twain once said, <span class="quote-inline">"A banker is a fellow
              who lends you his umbrella when the sun is shining, but wants it back
              the minute it begins to rain."</span>
            </td>
          </tr>
        </table>
      </div>
      <div class="preview">
        <h3 class="preview-title">Example 2</h3>
        <table class="basic">
          <tr>
            <td style="width: 50%">
              As Mark Twain once said, <span class="highlight">&lt;bq&gt;</span>A banker is a fellow who lends you
              his umbrella when the sun is shining, but wants it back the minute it
              begins to rain.<span class="highlight">&lt;/bq&gt;</span>
            </td>
            <td style="font-size: 150%">&rarr;</td>
            <td>
              <p>As Mark Twain once said,</p>
              <div class="quote-block"><p>"A banker is a fellow who lends you his umbrella
                  when the sun is shining, but wants it back the minute it begins to rain."</p></div>
            </td>
          </tr>
        </table>
      </div>
      <div class="preview">
        <h3 class="preview-title">Example 3</h3>
        <table class="basic">
          <tr>
            <td style="width: 50%">
              <p>As Mark Twain once said, <span class="highlight">&lt;bq quote-style="multiple"&gt;</span>A banker is a fellow who</p>
              <p>lends you his umbrella when the sun is shining,</p>
              <p>but wants it back the minute it begins to rain.<span class="highlight">&lt;/bq&gt;</span></p>
            </td>
            <td style="font-size: 150%">&rarr;</td>
            <td>
              <p>As Mark Twain once said,</p>
              <div class="quote-block">
                <p>"A banker is a fellow who</p>
                <p>"lends you his umbrella when the sun is shining,</p>
                <p>"but wants it back the minute it begins to rain."</p>
              </div>
            </td>
          </tr>
        </table>
      </div>
      <h2 id="notes">Notes</h2>
      <p>The <span class="highlight">&lt;n&gt;</span>
        tag uses a smaller font and is generally in italics to indicate that
        the text is supplemental or tangential. In the HTML formatter, this
        translates to the 'notes' CSS style. It is ignored in the plain-text formatter.</p>
      <div class="preview">
        <h3 class="preview-title">Example</h3>
        <table class="basic">
          <tr>
            <td style="width: 50%">
              <p><span class="highlight">&lt;n&gt;</span>N.B. The following text is of draft quality.<span class="highlight">&lt;/n&gt;</span></p>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac
                leo accumsan, semper magna in, consectetur metus.</p>
            </td>
            <td style="font-size: 150%">&rarr;</td>
            <td>
              <p><span class="notes">N.B. The following text is of draft quality.</span></p>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac
                leo accumsan, semper magna in, consectetur metus.</p>
            </td>
          </tr>
        </table>
      </div>
      <h2 id="abstract">Abstract</h2>
      <p>Use the <span class="highlight">&lt;abstract&gt;</span>
        tag at the beginning of longer articles to provide a synopsis or a
        <abbr title="too long; didn't read">tl;dr</abbr>. In the HTML formatter, this
        translates to the 'abstract' CSS style. It is ignored in the plain-text formatter.</p>
      <p>The <span class="highlight">&lt;quote-style&gt;</span> attribute (see <a href="#quoting">quoting</a> above)
        applies this tag as well, but the default value is <span class="highlight">none</span>.</p>
      <div class="preview">
        <h3 class="preview-title">Example</h3>
        <table class="basic">
          <tr>
            <td style="width: 50%">
              <span class="highlight">&lt;abstract&gt;</span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac
              leo accumsan, semper magna in, consectetur metus. Mauris dictum adipiscing metus
              sed accumsan. Aenean facilisis justo lacus, et fringilla arcu luctus id. Nulla at
              tortor at erat sagittis pellentesque.<span class="highlight">&lt;/abstract&gt;</span>
            </td>
            <td style="font-size: 150%">&rarr;</td>
            <td>
              <p class="abstract">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac
                leo accumsan, semper magna in, consectetur metus. Mauris dictum adipiscing metus
                sed accumsan. Aenean facilisis justo lacus, et fringilla arcu luctus id. Nulla at
                tortor at erat sagittis pellentesque.
              </p>
            </td>
          </tr>
        </table>
      </div>
      <h2 id="pullquotes">Pull quotes</h2>
      <p>Use the <span class="highlight">&lt;pullquote&gt;</span>
        tag throughout longer articles to highlight . In the HTML formatter, this
        translates to the 'abstract' CSS style. It is ignored in the plain-text formatter.</p>
      <p>The <span class="highlight">&lt;quote-style&gt;</span> attribute (see <a href="#quoting">quoting</a> above)
        applies to this tag as well, but the default value is <span class="highlight">none</span>.</p>
      <div class="preview">
        <h3 class="preview-title">Example</h3>
        <table class="basic">
          <tr>
            <td style="width: 50%">
              <p><span class="highlight">&lt;pullquote width="150px" align="right"&gt;</span>Mauris dictum adipiscing metus
                sed accumsan<span class="highlight">&lt;/pullquote&gt;</span> Lorem ipsum dolor
                sit amet, consectetur adipiscing elit. Nunc ac
                leo accumsan, semper magna in, consectetur metus. Mauris dictum adipiscing metus
                sed accumsan. Aenean facilisis justo lacus, et fringilla arcu luctus id. Nulla at
                tortor at erat sagittis pellentesque.</p>
            </td>
            <td style="font-size: 150%">&rarr;</td>
            <td>
              <blockquote class="quote pullquote right align-right" style="width: 150px"><div>Mauris dictum adipiscing metus
                  sed accumsan</div></blockquote>
              <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac
                leo accumsan, semper magna in, consectetur metus. Mauris dictum adipiscing metus
                sed accumsan. Aenean facilisis justo lacus, et fringilla arcu luctus id. Nulla at
                tortor at erat sagittis pellentesque.
              </p>
            </td>
          </tr>
        </table>
      </div>
      <h2 id="boxes">Boxes</h2>
      <p>When formatting code or larger quoted samples, it's nice to be able box
        the content out. Use the <span class="highlight">&lt;box&gt;</span> tag
        for this, with the following (optional) attributes:</p>
      <table class="basic columns left-labels top">
        <tr>
          <th>title</th>
          <td>Float the image to the left,
            with text wrapped around to the right.</td>
        </tr>
        <tr>
          <th>align</th>
          <td>
            <p>The default value is 'none'. This parameter is ignored in the plain-text renderer.</p>
            <table class="basic columns left-labels top">
              <tr>
                <th>left</th>
                <td>Float the box to the left,
                  with text wrapped around to the right.</td>
              </tr>
              <tr>
                <th>right</th>
                <td>Float the box to the right,
                  with text wrapped around to the left.</td>
              </tr>
              <tr>
                <th>center</th>
                <td>Box is centered in its own block. Text is split, with the flow continuing after the image.</td>
              </tr>
              <tr>
                <th>none</th>
                <td>Box is formatted inline with the text.</td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <th>width</th>
          <td>If you specify a left or right alignment, you will probably want to
            specify a width as well. This attribute accepts all CSS values.</td>
        </tr>
        <tr>
          <th>class</th>
          <td>This will assign the specified CSS class to the innermost generated
            container (affecting the box's content).</td>
        </tr>
      </table>
      <div class="preview">
        <h3 class="preview-title">Example 1</h3>
        <table class="basic">
          <tr>
            <td style="width: 50%">
              <span class="highlight">&lt;box title="Listing One"&gt;&lt;code&gt;</span><br>
                function _process($input, $tokenizer)<br>
                {<br>
                &nbsp;&nbsp;$tokenizer->set_input($input);<br>
                &nbsp;&nbsp;while ($tokenizer->available())<br>
                &nbsp;&nbsp;{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;$tokenizer->read_next();<br>
                &nbsp;&nbsp;&nbsp;&nbsp;$token = $tokenizer->current();<br>
                &nbsp;&nbsp;&nbsp;&nbsp;$this->_process($token);<br>
                &nbsp;&nbsp;}<br>
                }<span class="highlight">&lt;/code&gt;&lt;box&gt;</span>
            </td>
            <td style="font-size: 150%">&rarr;</td>
            <td>
              <div class="chart"><div class="chart-title">Listing One</div>
                <div class="chart-body">
              <pre><code>function _process($input, $tokenizer)
    {
      $tokenizer->set_input($input);
      while ($tokenizer->available())
      {
        $tokenizer->read_next();
        $token = $tokenizer->current();
        $this->_process($token);
      }
    }</code></pre>
                </div>
              </div>
            </td>
          </tr>
        </table>
      </div>
      <h2 id="messages">Messages</h2>
      <p>Use the <span class="highlight">&lt;info&gt;</span>, <span class="highlight">&lt;warning&gt;</span> or
        <span class="highlight">&lt;error&gt;</span> tag to show a message box with some content.</p>
      <div class="preview">
        <h3 class="preview-title">Example</h3>
        <table class="basic">
          <tr>
            <td style="width: 50%">
              <p><span class="highlight">&lt;info&gt;</span>This is an info message.<span class="highlight">&lt;/info&gt;</span></p>
              <p><span class="highlight">&lt;warning&gt;</span>This is a warning message.<span class="highlight">&lt;/warning&gt;</span></p>
              <p><span class="highlight">&lt;error&gt;</span>This is an error message.<span class="highlight">&lt;/error&gt;</span></p>
            </td>
            <td style="font-size: 150%">&rarr;</td>
            <td>
              <?php $Page->show_message('This is an info message.', 'info')?>
              <?php $Page->show_message('This is a warning message.', 'warning')?>
              <?php $Page->show_message('This is an error message.', 'error')?>
            </td>
          </tr>
        </table>
      </div>
      <h2 id="footnotes">Footnotes</h2>
      <p>Longer texts can include footnotes and end-notes by placing markers with the
        <span class="highlight">&lt;fn&gt;</span> tag. These are replaced with integer markers (e.g. 1, 2, 3, etc.).
        Use the <span class="highlight">&lt;ft&gt;</span> tag to create a block that forms the body of the footnote.
        These are also numbered incrementally. Footnotes can be placed anywhere in the text, but are commonly
        included at the end, separated by a horizontal rule, as shown in the example below.</p>
      <div class="preview">
        <h3 class="preview-title">Example</h3>
        <table class="basic">
          <tr>
            <td style="width: 50%">
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac
                leo accumsan, semper magna in, consectetur metus. Mauris dictum adipiscing metus
                sed accumsan. Aenean facilisis justo lacus<span class="highlight">&lt;fn&gt;</span>,
                et fringilla arcu luctus id. Nulla at tortor at erat sagittis pellentesque.</p>
              <p><span class="highlight">&lt;hr&gt;</span></p>
              <p><span class="highlight">&lt;ft&gt;</span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac
                leo accumsan, semper magna in, consectetur metus. Mauris dictum adipiscing metus
                sed accumsan.<span class="highlight">&lt;/ft&gt;</span></p>
            </td>
            <td style="font-size: 150%">&rarr;</td>
            <td>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac
                leo accumsan, semper magna in, consectetur metus. Mauris dictum adipiscing metus
                sed accumsan. Aenean facilisis justo lacus
                <a href="#footnote_body" id="footnote_ref" class="footnote-number" title="Jump to footnote.">[1]</a>,
                et fringilla arcu luctus id. Nulla at
                tortor at erat sagittis pellentesque.
              </p>
              <hr>
              <div class="footnote-reference"><span id="footnote_body" class="footnote-number">[1]</span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac
                leo accumsan, semper magna in, consectetur metus. Mauris dictum adipiscing metus
                sed accumsan.<a href="#footnote_ref" class="footnote-return" title="Jump back to reference.">&#8617;</a></div>
            </td>
          </tr>
        </table>
      </div>
      <h2 id="clear">Clearing floats</h2>
      <p>Use a <span class="highlight">&lt;clear&gt;</span> tag to clear any floating element in the preceding text. You can
      also use the attribute <span class="highlight">clear="both"</span> on any block to do the same thing, but this tag lets
        you avoid surrounding a paragraph with <span class="highlight">&lt;div&gt;</span> tags, as shown in the example below.</p>
      <div class="preview">
        <h3 class="preview-title">Example</h3>
        <table class="basic">
          <tr>
            <td style="width: 50%">
              <p><span class="highlight">&lt;box align="right" width="100px"&gt;</span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac
                leo accumsan, semper magna in...<span class="highlight">&lt;/box&gt;</span></p>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
              <p><span class="highlight">&lt;clear&gt;</span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac
              leo accumsan, semper magna in...</p>
            </td>
            <td style="font-size: 150%">&rarr;</td>
            <td>
              <div class="chart align-right" style="width: 100px">
                <div class="chart-body">
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac
                leo accumsan, semper magna in...</p>
                </div>
              </div>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
              <p class="clear-both">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac
                leo accumsan, semper magna in...</p>
            </td>
          </tr>
        </table>
      </div>
      <h2 id="anchors">Anchors</h2>
      <p>Use an <span class="highlight">&lt;anchor&gt;</span> tag to provide an invisible anchor in the text to which you can link
        with an <span class="highlight">&lt;a&gt;</span> tag. You can also use the attribute
        <span class="highlight">id</span> on any other tag to do the same thing, but this tag lets
        you avoid adding unnecessary begin/end tags, as shown in the example below.</p>
      <div class="preview">
        <h3 class="preview-title">Example</h3>
        <table class="basic">
          <tr>
            <td style="width: 50%">
              <p><span class="highlight">&lt;a href="#anchor1"&gt;</span>Jump to the anchor<span class="highlight">&lt;/a&gt;</span></p>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac
                leo accumsan, semper magna in, consectetur metus. Mauris dictum adipiscing metus
                sed accumsan. Aenean facilisis justo lacus, et fringilla arcu luctus id. Nulla at
                tortor at erat sagittis pellentesque.<span class="highlight">&lt;anchor id="anchor1"&gt;</span></p>
            </td>
            <td style="font-size: 150%">&rarr;</td>
            <td>
              <p><a href="#anchor1">Jump to the anchor</a></p>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac
                leo accumsan, semper magna in, consectetur metus. Mauris dictum adipiscing metus
                sed accumsan. Aenean facilisis justo lacus, et fringilla arcu luctus id. Nulla at
                tortor at erat sagittis pellentesque.<span id="anchor1"></span></p>
            </td>
          </tr>
        </table>
      </div>
      <h2 id="rules">Rules</h2>
      <p>Use an <span class="highlight">&lt;hr&gt;</span> tag to introduce a break in the text.</p>
      <div class="preview">
        <h3 class="preview-title">Example</h3>
        <table class="basic">
          <tr>
            <td style="width: 50%">
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac
                leo accumsan, semper magna in...</p>
              <p><span class="highlight">&lt;hr&gt;</span></p>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac
                leo accumsan, semper magna in...</p>
            </td>
            <td style="font-size: 150%">&rarr;</td>
            <td>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac
                leo accumsan, semper magna in...
              </p>
              <hr>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac
                leo accumsan, semper magna in...</p>
            </td>
          </tr>
        </table>
      </div>
      <h1>Advanced</h1>
      <h2 id="macro">Controlling the formatter</h2>
      <p>As discussed in <a href="#characters">special characters</a>, you can control
        which types of character replacement occur within a block of text. The
        <span class="highlight">&lt;macro&gt;</span> tag applies options, which
        apply for the rest of the text or until they are changed by a subsequent
        macro.</p>
      <p>The following properties are supported:</p>
      <h3>convert</h3>
      <p>Pass in a comma-delimited list of converters, which should be active. Use
        a plus (+) sign to enable a converter and a minus (-) sign to disable it.
        If no sign is present, it is assumed to be enabling the converter. The
        converters in the list are applied in order and can overwrite one another.
        Choose from the following converters:</p>
      <table class="basic columns left-labels top">
        <tr>
          <th>punctuation</th>
          <td>If enabled, punctuation marks are replaced with their fancier HTML equivalents. <em>enabled by default</em></td>
        </tr>
        <tr>
          <th>ligature</th>
          <td>If enabled, ligatures are replaced with their fancier HTML equivalents. <em>disabled by default</em></td>
        </tr>
        <tr>
          <th>tags</th>
          <td>If enabled, special html characters are converted to
            avoid being interpreted as HTML. The keyword "all" does not apply to this
            converter; you must explicitly toggle it if you want to change the setting.
            <em>enabled by default</em></td>
        </tr>
        <tr>
          <th>highlight</th>
          <td>If enabled, keywords from a search are
            highlighted within the text. <em>enabled by default</em></td>
        </tr>
        <tr>
          <th>all</th>
          <td>Applies to all converters except for "tags"</td>
        </tr>
      </table>
      <p>The following examples enable ligatures:</p>
      <div class="preview">
        <h3 class="preview-title">Example 1</h3>
        <span class="highlight">&lt;macro convert="ligature"&gt;</span> or <span class="highlight">&lt;macro convert="+ligature"&gt;</span>
      </div>
      <p>The following examples turn off everything but highlighting:</p>
      <div class="preview">
        <h3 class="preview-title">Example 2</h3>
        <span class="highlight">&lt;macro convert="-ligature,-punctuation"&gt;</span>
        <p>or</p>
        <span class="highlight">&lt;macro convert="-all;+highlight"&gt;</span>
      </div>
      <p>The following example makes sure that all converters are turned on:</p>
      <div class="preview">
        <h3 class="preview-title">Example 3</h3>
        <span class="highlight">&lt;macro convert="+all"&gt;</span>
      </div>
    </div>
  </div>
</div>
<?php
  $Page->finish_display ();
?>
