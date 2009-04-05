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

  $Page->title->subject = "Exception";
  $Page->template_options->title = "Exception";

  $class_name = $Page->final_class_name ('EXCEPTION_SIGNATURE', 'webcore/sys/exception_signature.php');
  $sig = new $class_name ($Page);
  $sig->load_from_request ();

  $class_name = $Page->final_class_name ('SUBMIT_EXCEPTION_FORM', 'webcore/forms/submit_exception_form.php');
  $form = new $class_name ($Page);

  $form->process_existing ($sig);
  if ($form->committed ())
  {
    $Env->redirect_local ('exception_submitted.php');
  }

  $Page->location->add_root_link ();
  $Page->start_display ();
?>
<div class="box">
  <div class="box-title">
    <?php echo $Page->resolve_icon_as_html ('{icons}indicators/error', ' ', '20px'); ?> We apologize for the inconvenience
  </div>
  <div class="box-body">
  <?php
    if (! $form->previewing ())
    {
      $show_details = read_var ('details');
  ?>
    <div style="width: 35em">
      <div class="chart">
        <div class="chart-body">
        <?php
          $ctrls_renderer = $Page->make_controls_renderer ();
          if ($show_details)
          {
            $class_name = $Page->final_class_name ('EXCEPTION_RENDERER', 'webcore/gui/exception_renderer.php');
            $renderer = new $class_name ($Page);
            $options = $renderer->options ();
            $options->show_details = false;
            $options->include_page_data = true;
            $options->include_browser_info = true;
            $renderer->display_as_html ($sig, $options);

            $retry_form_data = $sig->as_form (array(), 'retry_form');
            echo $retry_form_data;

            $debug_form_data = $sig->as_form (array ('debug' => 1), 'debug_form');
            echo $debug_form_data;
            $buttons [] = $ctrls_renderer->javascript_button_as_html ('Send', 'document.getElementById (\'update_form\').submit()', '{icons}buttons/send');
            $buttons [] = $ctrls_renderer->javascript_button_as_html ('Try again', 'document.getElementById (\'retry_form\').submit()', '{icons}buttons/restore');
            if ($Env->debug_enabled)
            {
              $buttons [] = $ctrls_renderer->javascript_button_as_html ('Debug', 'document.getElementById (\'debug_form\').submit()', '{icons}buttons/debug');
            }
          }
          else
          {
            $url = new URL ($Env->url (Url_part_no_host_path));
            $details_url = $url->as_text ();
            $url->replace_argument ('error_message', urlencode ($sig->message));
            $url->add_argument ('details', 1);
            $details_url = str_replace ("'", "\\'", $url->as_text (true));
        ?>
        <p class="error" style="text-align: center">An unexpected error has occurred.</p>
        <?php
            $buttons [] = $ctrls_renderer->javascript_button_as_html ('Send', 'document.getElementById (\'update_form\').submit()', '{icons}buttons/send');
            $buttons [] = $ctrls_renderer->button_as_html ('Details', $details_url, '{icons}buttons/view');
          }
        ?>
        <div style="margin-top: 1em">
        <?php
            $ctrls_renderer->draw_buttons_in_row ($buttons);
        ?>
        </div>
      </div>
    </div>
    <p><span class="field">Send</span> lets us know something went wrong.
    <?php
      if ($show_details)
    {
    ?>
    <br><span class="field">Try again</span> gives it another try. You never know.
      <br><span class="field">Debug</span> investigates further.
      <?php
        }
      else
    {
    ?>
    <br><span class="field">Details</span> shows you exactly 
    what happened.
    <?php
      }
    ?>
    </p>
    <div>
      <p>Thanks very much for your help,<br>earthli Support</p>
      <?php echo $Page->resolve_icon_as_html ('{icons}indicators/warning', 'Warning', '16px', 'float: left'); ?>
      <p class="notes" style="margin-left: 24px">
        <span class="field">Sending</span> submits the form below, which
        includes browser and web site data by default. You can preview the email below
        and decide which information will be included.</p>
    </div>
    <?php
      }
      $form->display ();
    ?>
    </div>
  </div>
</div>
<?php
  $Page->finish_display ();
?>