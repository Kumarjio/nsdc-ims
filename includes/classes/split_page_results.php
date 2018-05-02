<?php
  class splitPageResults {
    var $sql_query, $number_of_rows, $current_page_number, $number_of_pages, $number_of_rows_per_page, $page_name;

/* class constructor */
    function splitPageResults($query, $max_rows, $count_key = '*', $page_holder = 'page') {
      global $_GET, $_POST;

      $this->sql_query = $query;
      $this->page_name = $page_holder;

      if (isset($_GET[$page_holder])) {
        $page = $_GET[$page_holder];
      } elseif (isset($_POST[$page_holder])) {
        $page = $_POST[$page_holder];
      } else {
        $page = '';
      }

      if (empty($page) || !is_numeric($page)) $page = 1;
      $this->current_page_number = $page;

      $this->number_of_rows_per_page = $max_rows;

      $pos_to = strlen($this->sql_query);
      $pos_from = strpos($this->sql_query, ' from', 0);

      $pos_group_by = strpos($this->sql_query, ' group by', $pos_from);
      if (($pos_group_by < $pos_to) && ($pos_group_by != false)) $pos_to = $pos_group_by;

      $pos_having = strpos($this->sql_query, ' having', $pos_from);
      if (($pos_having < $pos_to) && ($pos_having != false)) $pos_to = $pos_having;

      $pos_order_by = strpos($this->sql_query, ' order by', $pos_from);
      if (($pos_order_by < $pos_to) && ($pos_order_by != false)) $pos_to = $pos_order_by;

      if (strpos($this->sql_query, 'distinct') || strpos($this->sql_query, 'group by')) {
        $count_string = 'distinct ' . tep_db_input($count_key);
      } else {
        $count_string = tep_db_input($count_key);
      }

      $count_query = tep_db_query("select count(" . $count_string . ") as total " . substr($this->sql_query, $pos_from, ($pos_to - $pos_from)));
      $count = tep_db_fetch_array($count_query);

      $this->number_of_rows = $count['total'];

      $this->number_of_pages = ceil($this->number_of_rows / $this->number_of_rows_per_page);

      if ($this->current_page_number > $this->number_of_pages) {
        $this->current_page_number = $this->number_of_pages;
      }

      $offset = ($this->number_of_rows_per_page * ($this->current_page_number - 1));
	  $this->offset = $offset;

	  $this->sql_query .= " limit " . ($offset <= 0 ? "0" : $offset) . ", " . $this->number_of_rows_per_page;
	}


 function list_display_links($max_page_links, $parameters = '') {
      //global $tep_SELF, $request_type;
	  global  $request_type;
      $display_links_string = '';

      if (tep_not_null($parameters) && (substr($parameters, -1) != '&')) $parameters .= '&';

// previous button - not displayed on first page
      if ($this->current_page_number > 1) $display_links_string .= '<a href="' . tep_href_link(basename($_SERVER['tep_SELF']), $parameters . $this->page_name . '=' . ($this->current_page_number - 1), $request_type) . '" class="Treb13Smooth" ><< Previous</a>&nbsp;&nbsp;';

// check if number_of_pages > $max_page_links
      $cur_window_num = intval($this->current_page_number / $max_page_links);
      if ($this->current_page_number % $max_page_links) $cur_window_num++;

      $max_window_num = intval($this->number_of_pages / $max_page_links);
      if ($this->number_of_pages % $max_page_links) $max_window_num++;

// previous window of pages
      if ($cur_window_num > 1) $display_links_string .= '<a href="' . tep_href_link(basename($_SERVER['tep_SELF']), $parameters . $this->page_name . '=' . (($cur_window_num - 1) * $max_page_links), $request_type) . '" class="Treb13Smooth">...</a>';

// page nn button
      for ($jump_to_page = 1 + (($cur_window_num - 1) * $max_page_links); ($jump_to_page <= ($cur_window_num * $max_page_links)) && ($jump_to_page <= $this->number_of_pages); $jump_to_page++) {
        if ($jump_to_page == $this->current_page_number) {
          $display_links_string .= '&nbsp;<b>' . $jump_to_page . '</b>&nbsp;';
        } else {
          $display_links_string .= '&nbsp;<a href="' . tep_href_link(basename($_SERVER['tep_SELF']), $parameters . $this->page_name . '=' . $jump_to_page, $request_type) . '" class="Treb13Smooth" >' . $jump_to_page . '</a>&nbsp;';
        }
      }

// next window of pages
      if ($cur_window_num < $max_window_num) $display_links_string .= '<a href="' . tep_href_link(basename($_SERVER['tep_SELF']), $parameters . $this->page_name . '=' . (($cur_window_num) * $max_page_links + 1), $request_type) . '" class="Treb13Smooth" >...</a>&nbsp;';

// next button
      if (($this->current_page_number < $this->number_of_pages) && ($this->number_of_pages != 1)) $display_links_string .= '&nbsp;<a href="' . tep_href_link(basename($_SERVER['tep_SELF']), $parameters . 'page=' . ($this->current_page_number + 1), $request_type) . '" class="Treb13Smooth" >Next >></a>&nbsp;';

      return $display_links_string;
    }


function list_display_images($max_page_links, $parameters = '') {
      //global $tep_SELF, $request_type;
	  global  $request_type;
      $display_links_string = '';

      if (tep_not_null($parameters) && (substr($parameters, -1) != '&')) $parameters .= '&';

	  if ($this->current_page_number > 1) $display_links_string .= '&nbsp;<a href="' . tep_href_link(basename($_SERVER['tep_SELF']), $parameters . 'page=1', $request_type) . '"><img src="'. DIR_WS_IMAGES .'previous.jpg" align="absmiddle" border="0"></a>&nbsp;';

// previous button - not displayed on first page
      if ($this->current_page_number > 1) $display_links_string .= '<a href="' . tep_href_link(basename($_SERVER['tep_SELF']), $parameters . $this->page_name . '=' . ($this->current_page_number - 1), $request_type) . '"><img src="'. DIR_WS_IMAGES .'back.jpg" align="absmiddle" border="0"></a>&nbsp;&nbsp;';

// check if number_of_pages > $max_page_links
      $cur_window_num = intval($this->current_page_number / $max_page_links);
      if ($this->current_page_number % $max_page_links) $cur_window_num++;

      $max_window_num = intval($this->number_of_pages / $max_page_links);
      if ($this->number_of_pages % $max_page_links) $max_window_num++;

	  $max_offset = $this->offset + $this->number_of_rows_per_page;
	  $max_offset = ( $max_offset > $this->number_of_rows ? $this->number_of_rows : $max_offset );

	  if($this->number_of_rows != 0)
	  $display_links_string .= '('. ( $this->offset + 1) .'-'. ( $max_offset ) .' of '. $this->number_of_rows . ')';

// next button
      if (($this->current_page_number < $this->number_of_pages) && ($this->number_of_pages != 1)) $display_links_string .= '&nbsp;<a href="' . tep_href_link(basename($_SERVER['tep_SELF']), $parameters . 'page=' . ($this->current_page_number + 1), $request_type) . '"><img src="'. DIR_WS_IMAGES .'next.jpg" align="absmiddle" border="0"></a>&nbsp;';

	  if (($this->current_page_number < $this->number_of_pages) && ($this->number_of_pages != 1)) $display_links_string .= '&nbsp;<a href="' . tep_href_link(basename($_SERVER['tep_SELF']), $parameters . 'page=' . ($this->number_of_pages), $request_type) . '"><img src="'. DIR_WS_IMAGES .'forward.jpg" align="absmiddle" border="0"></a>&nbsp;';

      return $display_links_string;
    }



 function preview_mail_display_links($max_page_links, $parameters = '',$formNm = '') {
     // global $tep_SELF, $request_type;
		global $request_type;
      $display_links_string = '';

      if (tep_not_null($parameters) && (substr($parameters, -1) != '&')) $parameters .= '&';

// previous button - not displayed on first page
// $this->current_page_number - 1)
      if ($this->current_page_number > 1) $display_links_string .= '<a href="javascript: SubmitNavigation(document.'. $formNm .',\''. ($this->current_page_number - 1) .'\')" class="pageResults" >Previous</a>';

// check if number_of_pages > $max_page_links
      $cur_window_num = intval($this->current_page_number / $max_page_links);
      if ($this->current_page_number % $max_page_links) $cur_window_num++;
		
      $max_window_num = intval($this->number_of_pages / $max_page_links);
      if ($this->number_of_pages % $max_page_links) $max_window_num++;

	  if($this->current_page_number != "1")	{
		  	if ($cur_window_num < $max_window_num) $display_links_string .= '&nbsp;|&nbsp;';
	  }

// next button
// ($this->current_page_number + 1)
      if (($this->current_page_number < $this->number_of_pages) && ($this->number_of_pages != 1)) $display_links_string .= '<a href="javascript: SubmitNavigation(document.'. $formNm .',\''. ($this->current_page_number + 1) .'\') " class="pageResults" >Next</a>';

      return $display_links_string;
    }

	 function display_links($max_page_links, $parameters = '') {
      global $request_type;

      $display_links_string = '';

      if (tep_not_null($parameters) && (substr($parameters, -1) != '&')) $parameters .= '&';

// previous button - not displayed on first page
      if ($this->current_page_number > 1) $display_links_string .= '<a href="' . tep_href_link(basename($_SERVER['tep_SELF']), $parameters . $this->page_name . '=' . ($this->current_page_number - 1), $request_type) . '" class="pageResults" ><< Previous</a>&nbsp;&nbsp;';

// check if number_of_pages > $max_page_links
      $cur_window_num = intval($this->current_page_number / $max_page_links);
      if ($this->current_page_number % $max_page_links) $cur_window_num++;

      $max_window_num = intval($this->number_of_pages / $max_page_links);
      if ($this->number_of_pages % $max_page_links) $max_window_num++;

// previous window of pages
      if ($cur_window_num > 1) $display_links_string .= '<a href="' . tep_href_link(basename($_SERVER['tep_SELF']), $parameters . $this->page_name . '=' . (($cur_window_num - 1) * $max_page_links), $request_type) . '" class="pageResults">...</a>';

// page nn button
      for ($jump_to_page = 1 + (($cur_window_num - 1) * $max_page_links); ($jump_to_page <= ($cur_window_num * $max_page_links)) && ($jump_to_page <= $this->number_of_pages); $jump_to_page++) {
        if ($jump_to_page == $this->current_page_number) {
          $display_links_string .= '&nbsp;<b>' . $jump_to_page . '</b>&nbsp;';
        } else {
          $display_links_string .= '&nbsp;<a href="' . tep_href_link(basename($_SERVER['tep_SELF']), $parameters . $this->page_name . '=' . $jump_to_page, $request_type) . '" class="pageResults" >' . $jump_to_page . '</a>&nbsp;';
        }
      }

// next window of pages
      if ($cur_window_num < $max_window_num) $display_links_string .= '<a href="' . tep_href_link(basename($_SERVER['tep_SELF']), $parameters . $this->page_name . '=' . (($cur_window_num) * $max_page_links + 1), $request_type) . '" class="pageResults" >...</a>&nbsp;';

// next button
      if (($this->current_page_number < $this->number_of_pages) && ($this->number_of_pages != 1)) $display_links_string .= '&nbsp;<a href="' . tep_href_link(basename($_SERVER['tep_SELF']), $parameters . 'page=' . ($this->current_page_number + 1), $request_type) . '" class="pageResults" >Next >></a>&nbsp;';

      return $display_links_string;
    }

/* class functions */

// display split-page-number-links
    function display_links_gallery($max_page_links, $parameters = '') {
      global $tep_SELF, $request_type;

      $display_links_string = '';

      if (tep_not_null($parameters) && (substr($parameters, -1) != '&')) $parameters .= '&';

// previous button - not displayed on first page
      if ($this->current_page_number > 1) $display_links_string .= '<a href="' . tep_href_link(basename($_SERVER['tep_SELF']), $parameters . $this->page_name . '=' . ($this->current_page_number - 1), $request_type) . '" class="link1" title=" ' . PREVNEXT_TITLE_PREVIOUS_PAGE . ' ">' . strtolower(PREVNEXT_BUTTON_GALLERY_PREV) . '</a>&nbsp;';

// check if number_of_pages > $max_page_links
      $cur_window_num = intval($this->current_page_number / $max_page_links);
      if ($this->current_page_number % $max_page_links) $cur_window_num++;

      $max_window_num = intval($this->number_of_pages / $max_page_links);
      if ($this->number_of_pages % $max_page_links) $max_window_num++;

// previous window of pages
      if ($cur_window_num > 1) $display_links_string .= '<span class="blackmedium"><b>'. GO_TO_PAGE .'</b></span>&nbsp;<a href="' . tep_href_link(basename($_SERVER['tep_SELF']), $parameters . $this->page_name . '=' . (($cur_window_num - 1) * $max_page_links), $request_type) . '" class="" title=" ' . sprintf(PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE, $max_page_links) . ' ">...</a>';

// page nn button
      for ($jump_to_page = 1 + (($cur_window_num - 1) * $max_page_links); ($jump_to_page <= ($cur_window_num * $max_page_links)) && ($jump_to_page <= $this->number_of_pages); $jump_to_page++) {
		if ($jump_to_page == $this->current_page_number) {
          if($jump_to_page == '1')	{
			$display_links_string .= '&nbsp;<b>'. GO_TO_PAGE .' '. $jump_to_page . '</b>&nbsp;';
		  }else	{
			$display_links_string .= '&nbsp;<b>' . $jump_to_page . '</b>&nbsp;';
		  }

        } else {
		 if($jump_to_page == '1')	{
			 $display_links_string .= '<span class="blackmedium"><b>'. GO_TO_PAGE .'</b></span>&nbsp;<a href="' . tep_href_link(basename($_SERVER['tep_SELF']), $parameters . $this->page_name . '=' . $jump_to_page, $request_type) . '" class="link1" title=" ' . sprintf(PREVNEXT_TITLE_PAGE_NO, $jump_to_page) . ' ">'. $jump_to_page . '</a>&nbsp;';
			}else	{
				$display_links_string .= '&nbsp;<a href="' . tep_href_link(basename($_SERVER['tep_SELF']), $parameters . $this->page_name . '=' . $jump_to_page, $request_type) . '" class="link1" title=" ' . sprintf(PREVNEXT_TITLE_PAGE_NO, $jump_to_page) . ' ">' . $jump_to_page . '</a>&nbsp;';
			}
		}
      }

// next window of pages
      if ($cur_window_num < $max_window_num) $display_links_string .= '<a href="' . tep_href_link(basename($_SERVER['tep_SELF']), $parameters . $this->page_name . '=' . (($cur_window_num) * $max_page_links + 1), $request_type) . '" class="" title=" ' . sprintf(PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE, $max_page_links) . ' ">...</a>&nbsp;';

// next button
      if (($this->current_page_number < $this->number_of_pages) && ($this->number_of_pages != 1)) $display_links_string .= '&nbsp;<a href="' . tep_href_link(basename($_SERVER['tep_SELF']), $parameters . 'page=' . ($this->current_page_number + 1), $request_type) . '" class="link1" title=" ' . PREVNEXT_TITLE_NEXT_PAGE . ' ">' . strtolower(PREVNEXT_BUTTON_GALLERY_NEXT) . '</a>&nbsp;';

      return $display_links_string;
    }

// display number of total products found
    function display_count($text_output) {
      $to_num = ($this->number_of_rows_per_page * $this->current_page_number);
      if ($to_num > $this->number_of_rows) $to_num = $this->number_of_rows;

      $from_num = ($this->number_of_rows_per_page * ($this->current_page_number - 1));

      if ($to_num == 0) {
        $from_num = 0;
      } else {
        $from_num++;
      }

      return sprintf($text_output, $from_num, $to_num, $this->number_of_rows);
    }
  }
?>
