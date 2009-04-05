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

  $Page->template_options->title = 'Support';
  $Page->title->subject = 'Support';

  $res = $Page->resources ();
  $options = $Page->template_options;

  $class_name = $Page->final_class_name ('SUBMIT_SUPPORT_FORM', 'webcore/forms/submit_support_form.php');
  $form = new $class_name ($Page);

  $browser = $Env->browser();
  
  $form->process ($browser);
  if ($form->committed ())
  {
    $Env->redirect_local ('exception_submitted.php');
  }

  $Page->location->add_root_link ();
  $Page->location->append ('Support');
  $Page->start_display ();
?>
<div class="box">
  <div class="box-title">Support</div>
  <div class="box-body">
    <p>If you're having trouble seeing something on this site, you should
      <a href="<?php echo $res->resolve_file ($options->browser_url); ?>">test 
      your browser</a>.</p>
    <p>If you have other questions about this site or the software running it, 
      fill out the form below and we'll get back to you as soon as possible.</p>
    <?php $form->display (); ?>
  </div>
</div>
<?php
  $Page->finish_display ();
?>