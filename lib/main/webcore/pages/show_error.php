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

  $error_num = read_var ('error');

  switch ($error_num)
  {
    case 400:
      $title = 'Invalid request';
      break;
    case 401:
      $title = 'Unauthorized access';
      break;
    case 402:
      $title = 'Payment required';
      break;
    case 403:
      $title = 'Access denied';
      break;
    case 404:
      $title = 'Document not found';
      break;
    default:
      $title = '';
  }

  $Page->template_options->title = $error_num;
  $Page->title->subject = $title . ' (Error ' . $error_num . ')';

  $Page->start_display ();
?>
<div class="box">
  <div class="box-title">
    <?php echo $Page->resolve_icon_as_html ('{icons}indicators/error', 'Error', '20px'); ?>
    <?php echo $Page->title->subject; ?>
  </div>
  <div class="box-body">
  <p>
  <?php
    switch ($error_num)
    {
      case 400:
        echo 'The server did not understand the request (syntax error).';
        break;
      case 401:
        echo 'The requested document cannot be viewed without the proper authentication.';
        break;
      case 402:
        echo 'The document cannot be accessed without authenticated payment.';
        break;
      case 403:
        echo 'Access to the requested resource is forbidden under all circumstances.';
        break;
      case 404:
        echo 'The requested document was not found on this site.';
        break;
      default:
        echo 'An error occurred while processing the document request.';
    }
  ?>
  </p>
  <p class="error">
  <?php
    $page_url = $Page->url ();
    echo $page_url; 
  ?>
  </p>
  <p class="notes">Please see the <a href="http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html">HTTP 1.1 Status Code Definitions</a> for more information.</p>
  </div>
</div>
<?php
  $Page->finish_display ();
?>