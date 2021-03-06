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

$Page->template_options->title = 'Browser';
$Page->title->subject = 'Browser support';
$Page->template_options->check_browser = false;

$browser = $Env->browser();

$class_name = $Page->final_class_name('SUBMIT_BROWSER_FORM', 'webcore/forms/submit_browser_form.php');
/** @var SUBMIT_BROWSER_FORM $form */
$form = new $class_name ($Page);
$form->process($browser);
if ($form->committed()) {
  $Env->redirect_local('exception_submitted.php');
}

$Page->location->add_root_link();
$Page->location->append('Browser support');
$Page->start_display();
?>
<div class="main-box">
  <div class="columns text-flow">
    <div>
      <p>Any modern, standards-compliant browser will be able to display this web site nicely. See
        the sidebar for download links.</p>
      <p>The FAQ below answers these questions:</p>
      <ul>
        <li><a href="#unsupported_browser">Why do I see a warning at the top of the page?</a></li>
        <li><a href="#what_cant_i_do">What kind of stuff won't I be able to do?</a></li>
        <li><a href="#remove_warning">How do I get rid of the warning?</a></li>
        <li><a href="#technologies">What does my browser need?</a></li>
        <li><a href="#report_problem">How do I report a problem with browser detection?</a></li>
      </ul>
      <h2>FAQ</h2>
      <div>
        <p class="quote-block" id="unsupported_browser">
          Why do I see a warning at the top of the page?
        </p>
        <p>If your browser's not on this list (yeah, I'm looking at you, Internet Explorer), you'll see
          this message at the top of the page.</p>

        <?php
          $Page->show_message('Your browser may have trouble rendering this page. [...]', 'warning');
        ?>
        <p>While this site degrades gracefully under older browsers, it won't look as nice as it could.</p>
      </div>
      <div>
        <p class="quote-block" id="what_cant_i_do">
          What kind of stuff won't I be able to do?
        </p>
        <p>If your browser doesn't support alpha-PNGs (see <a href="#your_browser">chart</a> to the right), GIFs are
          used instead.
          This means that the icons will only look good on lighter backgrounds, so you won't be able
          to use all of the themes.</p>

        <p>If your browser doesn't have DOM/DHTML support, all content has to be rendered statically.
          This takes more space and makes for poorer usability. Lack of CSS2 support causes similar
          problems, resulting in less flexible layouts.</p>

        <p>The default theme has been carefully designed so that it looks nice even on non-compliant
          browsers.</p>
      </div>
      <div>
        <p class="quote-block" id="remove_warning">
          How do I get rid of the warning?
        </p>
        <p>You can get rid of the warning by using one of the
          <a href="#downloads">browsers</a> in the downloads sidebar. Or you can just check the "Do not show this message
          again." box.</p>
      </div>
      <div>
        <p class="quote-block" id="technologies">
          What does my browser need?
        </p>
        <p><a href="http://earthli.com/software/webcore/">earthli WebCore</a> web sites make
          use of and are fully compliant with the following web standards:</p>
        <ul>
          <li><a href="http://www.w3.org/TR/html4/">HTML 4.01</a> (strict)</li>
          <li><a href="http://www.w3.org/TR/CSS21/">CSS 2.1</a></li>
          <li><a href="http://www.w3.org/TR/css3-multicol/">CSS 3 columns</a></li>
          <li><a href="http://www.w3c.org/DOM/">DOM</a> (levels 1 and 2)</li>
          <li><a href="http://www.ecma-international.org/publications/standards/Ecma-262.htm">ECMAScript</a> (aka
            JavaScript)
          </li>
          <li><a href="http://www.libpng.org/pub/png/">PNG graphics</a> (with alpha blending)</li>
          <li><a href="http://www.faqs.org/rfcs/rfc2965.html">Cookies</a> must be enabled</li>
        </ul>
        <p>Your browser's capabilities are listed in the <a href="#your_browser">your browser</a> box above.</p>
      </div>
      <div>
        <p class="quote-block" id="report_problem">
          How do I report a problem with browser detection?
        </p>
        <p id="report_form">If you think you shouldn't be getting a warning (e.g. all technologies listed above
          are supported) or if there is some other problem, you can:</p>
        <ul>
          <li><a href="#" onclick="<?php echo $form->js_form_name(); ?>.submit(); return false;">send</a> just your
            browser information
          </li>
          <li>fill out the form below to provide more information</li>
        </ul>
        <?php
        $form->allow_focus = false;
        $form->action_anchor = 'report_form';
        $form->display();
        ?>
      </div>
    </div>
    <div class="right-sidebar">
      <h2 id="your_browser">Your browser</h2>
      <?php
      $class_name = $Page->final_class_name('BROWSER_RENDERER', 'webcore/gui/browser_renderer.php');
      /** @var BROWSER_RENDERER $renderer */
      $renderer = new $class_name ($Page);
      $renderer->display_as_html($browser);
      ?>
      <div class="info-box-bottom">
        <p>Calculated by the <a href="http://earthli.com/software/browser_detector/">earthli Browser Detector</a>.</p>
        <p>Something look wrong? <a href="#report_problem">Report it</a>.</p>
      </div>
      <h2 id="downloads">
        <?php echo $Page->get_icon_with_text('{icons}buttons/download_to_hd', Thirty_two_px, 'Download'); ?>
      </h2>
      <p>For a better browsing experience, try one of these:</p>
      <?php
      $Page->start_icon_container('{icons}logos/browsers/opera1_t', Thirty_two_px);
      ?>
      <h3><a href="http://opera.com">Opera</a></h3>
      <p class="detail">Free (Mac/Windows/Linux)</p>
      <?php
      $Page->finish_icon_container();
      ?>
      <?php
      $Page->start_icon_container('{icons}logos/browsers/chrome', Thirty_two_px);
      ?>
      <h3><a href="http://google.com/chrome/">Chrome</a></h3>
      <p class="detail">Free (Mac/Windows/Linux)</p>
      <?php
      $Page->finish_icon_container();
      ?>
      <?php
      $Page->start_icon_container('{icons}logos/browsers/firefox', Thirty_two_px);
      ?>
      <h3><a href="http://mozilla.org/products/firefox/">Firefox</a></h3>
      <p class="detail">Free (Mac/Windows/Linux)</p>
      <?php
      $Page->finish_icon_container();
      ?>
      <?php
      $Page->start_icon_container('{icons}logos/browsers/safari', Thirty_two_px);
      ?>
      <h3><a href="http://apple.com/safari">Safari</a></h3>
      <p class="detail">Free (Mac)</p>
      <?php
      $Page->finish_icon_container();
      ?>
    </div>
  </div>
</div>
<?php
$Page->finish_display();
?>