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

  $Page->title->subject = "An error has occurred";
  $Page->template_options->title = "Exception";

  $class_name = $Page->final_class_name ('EXCEPTION_SIGNATURE', 'webcore/sys/exception_signature.php');
  /** @var $sig EXCEPTION_SIGNATURE */
  $sig = new $class_name ($Page);
  $sig->load_from_request ();

  $class_name = $Page->final_class_name ('SUBMIT_EXCEPTION_FORM', 'webcore/forms/submit_exception_form.php');
  /** @var $form SUBMIT_EXCEPTION_FORM */
  $form = new $class_name ($Page);

  $form->process_existing ($sig);
  if ($form->committed ())
  {
    $Env->redirect_local ('exception_submitted.php');
  }

  $Page->location->add_root_link ();
  $Page->location->append($Page->title->subject, '', '{icons}indicators/warning');

  $Page->start_display ();
?>
<div class="main-box">
  <div class="text-flow">
    <p>An error occurred while processing your last request.</p>
    <p>Please submit the prepared report below to help us address this issue.</p>
    <p>Or take a chance and try again. You never know.</p>
    <p>We apologize for the inconvenience and thank you in advance for your help,<br>
      the earthli Team.</p>
  </div>
  <?php
  $controls_renderer = $Page->make_controls_renderer ();
  $buttons [] = $controls_renderer->javascript_button_as_html ('Submit report', 'document.getElementById (\'update_form\').submit()', '{icons}buttons/send');
  $buttons [] = $controls_renderer->javascript_button_as_html ('Try again', 'document.getElementById (\'retry_form\').submit()', '{icons}buttons/restore');
  if ($Env->debug_enabled)
  {
    $buttons [] = $controls_renderer->javascript_button_as_html ('Debug', 'document.getElementById (\'debug_form\').submit()', '{icons}buttons/debug');
  }
  ?>
  <p>
    <?php
    $controls_renderer->draw_buttons ($buttons);
    ?>
  </p>
  <?php
    $Page->show_message('By default, the report includes some browser and web site data. See the full report details for more information.', 'info');

    $layer = $Page->make_layer ('exception_details');
  ?>
  <p class="detail"><?php $layer->draw_toggle (); ?> Toggle the arrow to see the full report details.</p>
  <?php

  $layer->start ();
  $class_name = $Page->final_class_name ('EXCEPTION_RENDERER', 'webcore/gui/exception_renderer.php');
  /** @var $renderer EXCEPTION_RENDERER */
  $renderer = new $class_name ($Page);
  /** @var $options EXCEPTION_RENDERER_OPTIONS */
  $options = $renderer->options ();
  $options->include_page_data = true;
  $options->include_browser_info = true;
  $renderer->display_as_html ($sig, $options);

  $retry_form_data = $sig->as_form (array(), 'retry_form');
  echo $retry_form_data;

  $debug_form_data = $sig->as_form (array ('debug' => 1), 'debug_form');
  echo $debug_form_data;

  $layer->finish ();
?>
  <div class="form-content">
<?php
  $form->display ();
?>
  </div>
</div>
<?php
  $Page->finish_display ();
?>