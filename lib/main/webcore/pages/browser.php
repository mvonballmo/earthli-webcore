<?php

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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

  $Page->template_options->title = 'Browser';
  $Page->title->subject = 'Browser support';
  $Page->template_options->check_browser = false;

  $browser = $Env->browser ();

  $class_name = $Page->final_class_name ('SUBMIT_BROWSER_FORM', 'webcore/forms/submit_browser_form.php');
  $form = new $class_name ($Page);
  $form->process ($browser);
  if ($form->committed ())
  {
    $Env->redirect_local ('exception_submitted.php');
  }

  $Page->location->add_root_link ();
  $Page->location->append ('Browser support');
  $Page->start_display ();
?>
<div style="float: right; width: 19em">
  <div class="side-bar">
    <div class="side-bar-title" id="your_browser">
      Your browser
    </div>
    <div class="side-bar-body">
      <?php
        $class_name = $Page->final_class_name ('BROWSER_RENDERER', 'webcore/gui/browser_renderer.php');
        $renderer = new $class_name ($Page);
        echo $renderer->display_as_html ($browser);
      ?>
      <div class="horizontal-separator" style="margin: .5em"></div>
      <div class="notes">Problems with your browser? <a href="#report_problem">Report it</a>.</div>
    </div>
  </div>
  <br>
  <div class="side-bar">
    <div class="side-bar-title"id="downloads">
      <?php echo $Page->resolve_icon_as_html ('{icons}buttons/download_to_hd', '', '32px'); ?> Download
    </div>
    <div class="side-bar-body">
      <p>For a better browsing experience, try one of these:</p>
      <dl>
        <dt class="field">
          <?php echo $Page->resolve_icon_as_html ('{icons}logos/browsers/opera1_t', '', '32px'); ?> <a href="http://opera.com">Opera</a>
        </dt>
        <dd class="detail">
          Free (Mac/Windows/Linux)
        </dd>
        <dt class="field">
          <?php echo $Page->resolve_icon_as_html ('{icons}logos/browsers/firefox', '', '32px'); ?> <a href="http://mozilla.org/products/firefox/">Firefox</a>
        </dt>
        <dd class="detail">
          Free (Mac/Windows/Linux)
        </dd>
        <dt class="field">
          <?php echo $Page->resolve_icon_as_html ('{icons}logos/browsers/omniweb5', '', '32px'); ?> <a href="http://omnigroup.com/applications/omniweb/">Omniweb</a>
        </dt>
        <dd class="detail">
          30-day trial (Mac only)
        </dd>
        <dt class="field">
          <?php echo $Page->resolve_icon_as_html ('{icons}logos/browsers/safari', '', '32px'); ?> <a href="http://apple.com/safari">Safari</a>
        </dt>
        <dd class="detail">
          Free (Mac only)
        </dd>
        <dt class="field">
          <?php echo $Page->resolve_icon_as_html ('{icons}logos/browsers/camino', '', '32px'); ?> <a href="http://www.caminobrowser.org/">Camino</a>
        </dt>
        <dd class="detail">
          Free (Mac only)
        </dd>
        <dt class="field">
          <?php echo $Page->resolve_icon_as_html ('{icons}logos/browsers/shiira', '', '32px'); ?> <a href="http://hmdt-web.net/shiira/en">Shiira</a>
        </dt>
        <dd class="detail">
          Free (Mac only)
        </dd>
        <dt class="field">
          <?php echo $Page->resolve_icon_as_html ('{icons}logos/browsers/chrome', '', '32px'); ?> <a href="http://google.com/chrome/">Chrome</a>
        </dt>
        <dd class="detail">
          Free (Win32 only)
        </dd>
      </dl>
    </div>
  </div>
</div>
<div class="box" style="margin-right: 20em">
  <div class="box-title" id="recommend_browsers">Browser support</div>
  <div class="box-body">
    <p>Any modern browser will be able to display this web site nicely.</p>
    <p><a href="http://opera.com/download">Opera</a> and <a href="http://mozilla.org/products/firefox/">Firefox</a> are
      both highly recommended for all platforms. On Mac OS X,
      both <a href="http://apple.com/safari/">Safari</a> and
      <a href="http://omnigroup.com/applications/omniweb/">OmniWeb</a> also do a great job.</p>
    <p>The FAQ below answers these questions:</p>
    <ul>
      <li><a href="#unsupported_browser">Why do I see a warning at the top of the page?</a></li>
      <li><a href="#what_cant_i_do">What kind of stuff won't I be able to do?</a></li>
      <li><a href="#remove_warning">How do I get rid of the warning?</a></li>
      <li><a href="#technologies">What does my browser need?</a></li>
      <li><a href="#report_problem">How do I report a problem with browser detection?</a></li>
    </ul>
    <h2>FAQ</h2>
    <p><!-- An extra paragraph for IE ... you see what I mean? --></p>
    <div>
      <a id="unsupported_browser"></a>
      <div style="float: left">
        <?php echo $Page->resolve_icon_as_html ('{icons}indicators/question', '', '32px'); ?>
      </div>
      <div class="quote-block" style="margin-left: 36px">
        Why do I see a warning at the top of the page?
      </div>
      <p>If your browser's not on this list (yeah, I'm looking at you, Internet Explorer), you'll see
        this message at the top of the page.</p>
      <div class="caution">
        <div class="notes">
          <div style="float: left"><?php echo $Page->resolve_icon_as_html ('{icons}indicators/warning', '', '16px'); ?></div>
          <div style="margin-left: 20px">
            &quot;Your browser may have trouble rendering this site properly. Visit supported browsers
            for more information.&quot;
          </div>
        </div>
      </div>
      <p>While this site degrades gracefully under older browsers, it won't look as nice as it could.</p>
    </div>
    <div>
      <a id="what_cant_i_do"></a>
      <div style="float: left">
        <?php echo $Page->resolve_icon_as_html ('{icons}indicators/question', '', '32px'); ?>
      </div>
      <div class="quote-block" style="margin-left: 36px">
        What kind of stuff won't I be able to do?
      </div>
      <p>If your browser doesn't support alpha-PNGs (see <a href="#your_browser">chart</a> to the right), GIFs are used instead.
        This means that the icons will only look good on lighter backgrounds, so you won't be able
        to use all of the themes.</p>
      <p>If your browser doesn't have DOM/DHTML support, all content has to be rendered statically.
        This takes more space and makes for poorer usability. Lack of CSS2 support causes similar
        problems, resulting in less flexible layouts.</p>
      <p>The default theme has been carefully designed so that it looks nice even on non-compliant
        browsers.</p>
    </div>
    <div>
      <a id="remove_warning"></a>
      <div style="float: left">
        <?php echo $Page->resolve_icon_as_html ('{icons}indicators/question', '', '32px'); ?>
      </div>
      <div class="quote-block" style="margin-left: 36px">
        How do I get rid of the warning?
      </div>
      <p>The only way to get rid of the warning is to use one of the
        <a href="#recommend_browsers">browsers mentioned above</a>
        (see the <a href="#downloads">downloads sidebar</a>).</p>
    </div>
    <div>
      <a id="technologies"></a>
      <div style="float: left">
        <?php echo $Page->resolve_icon_as_html ('{icons}indicators/question', '', '32px'); ?>
      </div>
      <div class="quote-block" style="margin-left: 36px">
        What does my browser need?
      </div>
      <p><a href="http://earthli.com/software/webcore/">earthli WebCore</a> web sites make
        use of and are fully compliant with the following web standards:</p>
      <ul>
        <li><a href="http://www.w3.org/TR/html4/">HTML 4.01</a> (strict)</li>
        <li><a href="http://www.w3.org/TR/CSS21/">CSS 2.1</a></li>
        <li><a href="http://www.w3c.org/DOM/">DOM</a> (levels 1 and 2)</li>
        <li><a href="http://www.ecma-international.org/publications/standards/Ecma-262.htm">ECMAScript</a> (aka JavaScript)</li>
        <li><a href="http://www.libpng.org/pub/png/">PNG graphics</a> (with alpha blending)</li>
        <li><a href="http://www.faqs.org/rfcs/rfc2965.html">Cookies</a> must be enabled</li>
      </ul>
      <p>Your browser's capabilities are listed in the <a href="#your_browser">your browser</a> box above.</p>
    </div>
    <div>
      <a id="report_problem"></a>
      <div style="float: left">
        <?php echo $Page->resolve_icon_as_html ('{icons}indicators/question', '', '32px'); ?>
      </div>
      <div class="quote-block" style="margin-left: 36px">
        How do I report a problem with browser detection?
      </div>
      <p id="report_form">If you think you shouldn't be getting a warning (e.g. all technologies listed above
        are supported) or if there is some other problem, you can:</p>
        <ul>
          <li><a href="#" onclick="<?php echo $form->js_form_name (); ?>.submit(); return false;">send</a> just your browser information</li>
          <li>fill out the form below to provide more information</li>
        </ul>
      <?php
        $form->allow_focus = false;
        $form->action_anchor = 'report_form';
        $form->display ();
      ?>
    </div>
    <div>
      <div class="notes"><a name="report_problem"></a>Details courtesy of the
        <a href="http://earthli.com/software/browser_detector/">earthli Browser Detector</a>.</div>
    </div>
  </div>
</div>
<?php
  $Page->finish_display ();
?>