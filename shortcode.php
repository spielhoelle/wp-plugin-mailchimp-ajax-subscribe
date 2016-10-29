<?php

add_shortcode('newsletter-form', 'form');
function form() {
  if(get_option('opt_in') === "1"){
      $status = 'pending';
  } else {
      $status = 'subscribed';
  }
  if(!get_option('api_key') || !get_option('list_id')){
    if(is_user_logged_in()){
      $html = '<h2>'.__("No Mailchimp api_key or list_id in options defined.", 'tommy-mailchimp-ajax').'</h2>';
      $html .= ' <a href="'.admin_url('options-general.php?page=tommy-mailchimp-ajax%2Foptions.php').'" target="_blank">';
      $html .= __("Go to options ->", 'tommy-mailchimp-ajax').'</a>';
    }
  } else {
    $html = '
        <form class="tmcajax '.$status.'">
          <label for="mce-EMAIL">Email</label><input required="required" type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL" placeholder="E-Mail">
          <input type="submit" value="'.__('Subscribe me!', 'tommy-mailchimp-ajax').'" class="header-button-two"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw" style="visibility: hidden"></i>
      </form>
      ';
  }
  return $html;
}


add_shortcode('newsletter-link', 'display_nl_link');
function display_nl_link() {
  $html = '<a href="' . get_the_permalink(get_option('newsletter_archive_page')) .'">Newsletter Archiv</a>';
  return $html;
}
