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

  $page_name = read_array_index ($_GET, 'page_name');
  $file_name = url_to_file_name ($page_name);

  define ('Start_of_template', 'webcore/pages');
  define ('End_of_template', '.php');

  function draw_source ($source_text)
  {
    echo "<div class=\"log-box\">$source_text</div>\n";
  }

  $Page->title->subject = 'View source';
  $Page->template_options->title = 'Source';

  $Page->location->add_root_link ();
  $Page->location->append ('View source');
  $Page->add_style_sheet ($Env->logger_style_sheet);
  $Page->start_display ();
?>
<div class="box">
  <div class="box-title">
    View source
  </div>
  <div class="box-body">
    <div class="info-box-top">
      <div style="float: left; padding-bottom: .5em">
        <?php echo $Page->resolve_icon_as_html ('{icons}buttons/source', '', '50px'); ?>
      </div>
      <div style="margin-left: 60px">
        <p style="margin-top: 0em">The best way to learn how to use the <a href="http://earthli.com/software/webcore/">WebCore</a>
          is by example. See the <a href="http://earthli.com/software/webcore/documentation.php">documentation</a> for
          more information.</p>
        <p style="margin-bottom: 0em">The requested URL is always shown first; if a page uses one or more WebCore templates, those are shown afterwards.</p>
      </div>
      <div style="clear: both"></div>
    </div>
    <?php
      if (is_file ($file_name))
      {
        $class_name = $Page->final_class_name ('HIGHLIGHTER', 'webcore/util/highlighter.php');
        $highlighter = new $class_name ($Page);

        $page_text = $highlighter->file_as_html ($file_name);
        $template_texts = array ();

        $text_to_search = $page_text;
        $template_start = strpos ($text_to_search, Start_of_template);
        while ($template_start !== false)
        {
          $template_end = strpos ($text_to_search, End_of_template, $template_start);
          $template_name = substr ($text_to_search, $template_start, $template_end - $template_start + strlen (End_of_template));
          $template_file_name = join_paths ($Env->library_path, $template_name);

          if (@is_file ($template_file_name))
          {
            $text_to_search = $highlighter->file_as_html ($template_file_name);
            $template_texts [$template_name] = $text_to_search;
          }
          $template_start = strpos ($text_to_search, Start_of_template, $template_end);
        }

        if (sizeof ($template_texts))
        {
    ?>
      <p>Source for: <span class="field"><?php echo $page_name; ?></span></p>
      <p>Uses WebCore template(s):</p>
      <ul>
    <?php
          $idx = 1;
          foreach ($template_texts as $tname => $ttext)
          {
    ?>
        <li><a href="#template_source_<?php echo $idx; ?>"><?php echo $tname; ?></a></li>
    <?php
            $idx += 1;
          }
    ?>
      </ul>
    <?php
          draw_source ($page_text);

          $idx = 1;
          foreach ($template_texts as $tname => $ttext)
          {
    ?>
    <a id="template_source_<?php echo $idx; ?>"></a>
    <p>Source for template: <span class="field"><?php echo $tname; ?></span></p>
    <?php
            draw_source ($ttext);
            $idx += 1;
          }
        }
        else
        {
          draw_source ($page_text);
        }
      }
      else
      {
	      $page_name = htmlentities($page_name);
        echo "<div class=\"error\">[$page_name] is not a file.</div>";
      }
    ?>
  </div>
</div>
<?php $Page->finish_display (); ?>