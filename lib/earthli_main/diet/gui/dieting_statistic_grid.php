<?php
	require_once ('webcore/gui/list_grid.php');

  class DIETING_STATISTIC_GRID extends LIST_GRID
  {
    function DIETING_STATISTIC_GRID (&$context)
    {
      LIST_GRID::LIST_GRID ($context);

      $this->append_column ('');  // delete button
      $this->append_column ('');  // title
      $this->append_column ('Weight', 'right');
      $this->append_column ('');  // low/high indicator
      $this->append_column ('Body fat', 'right');
      $this->append_column ('');  // low/high indicator
    }

    function _draw (&$objs)
    {
      $this->lowest_body_fat_stat = new DIETING_STATISTIC ($this->context);
      $this->lowest_body_fat_stat->body_fat = 100;
      $this->lowest_weight_stat = new DIETING_STATISTIC ($this->page);
      $this->lowest_weight_stat->weight = 300;

      $this->highest_body_fat_stat = new DIETING_STATISTIC ($this->context);
      $this->highest_body_fat_stat->body_fat = 0;
      $this->highest_weight_stat = new DIETING_STATISTIC ($this->context);
      $this->highest_weight_stat->weight = 0;

      $total_weight = 0;
      $total_body_fat = 0;

      foreach ($objs as $stat)
      {
        if ($stat->body_fat < $this->lowest_body_fat_stat->body_fat)
          $this->lowest_body_fat_stat = $stat;

        if ($stat->body_fat > $this->highest_body_fat_stat->body_fat)
          $this->highest_body_fat_stat = $stat;

        if ($stat->weight < $this->lowest_weight_stat->weight)
          $this->lowest_weight_stat = $stat;

        if ($stat->weight > $this->highest_weight_stat->weight)
          $this->highest_weight_stat = $stat;

        $total_weight += $stat->weight;
        $total_body_fat += $stat->body_fat;
      }

      $average = new DIETING_STATISTIC ($this->context);
      $average->weight = sprintf ("%.1f", $total_weight / sizeof ($objs));
      $average->body_fat = sprintf ("%.1f", $total_body_fat / sizeof ($objs));

      $highest = new DIETING_STATISTIC ($this->context);
      $highest->weight = $this->highest_weight_stat->weight;
      $highest->body_fat = $this->highest_body_fat_stat->body_fat;

      $lowest = new DIETING_STATISTIC ($this->context);
      $lowest->weight = $this->lowest_weight_stat->weight;
      $lowest->body_fat = $this->lowest_body_fat_stat->body_fat;

      /* Make a copy. */
      $objs_to_display = $objs;
      
      array_unshift ($objs_to_display, new DIETING_STATISTIC ($this->context));
      array_unshift ($objs_to_display, $highest);
      array_unshift ($objs_to_display, $lowest);
      array_unshift ($objs_to_display, $average);

      $this->row_index = 0;

      parent::_draw ($objs_to_display);
    }

    function _start_row (&$obj)
    {
      $this->row_index++;
      parent::_start_row ($obj);
    }

    function _draw_column_contents (&$obj, $index)
    {
      // Rows appear like this:
      // TITLE
      // AVERAGE
      // LOWEST
      // HIGHEST
      // [BLANK] ... thus, ignore the fourth row
      
      if ($this->row_index != 4)
      {
        switch ($index)
        {
        case 0:
          if ($this->row_index > 4)
            $this->_draw_menu_for ($obj, Menu_size_toolbar);
          break;
        case 1:
          {
            switch ($this->row_index)
            {
            case 1:
              $title = 'Average';
              break;
            case 2:
              $title = 'Lowest';
              break;
            case 3:
              $title = 'Highest';
              break;
            default:
              $df = $obj->date->formatter ();
              $df->type = Date_time_format_short_date;
              $title = $obj->date->format ($df);
            }
            echo '<strong>' . $title . '</strong>';
          }
          break;
        case 2:
          echo sprintf ("%.1f", $obj->weight) . '&nbsp;lbs';
          break;
        case 3:
          if ($obj->equals ($this->lowest_weight_stat))
            echo $this->context->resolve_icon_as_html ('{icons}indicators/low_green', '', '16px');
          else if ($obj->equals ($this->highest_weight_stat))
            echo $this->context->resolve_icon_as_html ('{icons}indicators/high_red', '', '16px');
          break;
        case 4:
          echo sprintf ("%.1f", $obj->body_fat) . '%';
          break;
        case 5:
          if ($obj->equals ($this->lowest_body_fat_stat))
            echo $this->context->resolve_icon_as_html ('{icons}indicators/low_green', '', '16px');
          else if ($obj->equals ($this->highest_body_fat_stat))
            echo $this->context->resolve_icon_as_html ('{icons}indicators/high_red', '', '16px');
          break;
        }
      }
    }

    function _draw_empty_grid ()
    {
?>
      <p class="caution">
        <?php echo $this->context->resolve_icon_as_html ('{icons}indicators/warning', '', '16px'); ?>
        There are no statistics for this month.
      </p>
<?php
    }
  }
?>