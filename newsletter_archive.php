<?php

add_action( 'init', 'create_post_type' );
function create_post_type() {
    register_post_type( 'newsletter',
        array(
            'labels' => array(
              'name' => __( 'Newsletters' ),
              'singular_name' => __( 'Newsletter' )
            ),
            'public' => true,
            'show_in_menu'=>true,
            'exclude_from_search'=>false,
            'rewrite' => array('slug' => 'newsletter')

        )
    );
}


function get_mailchimp_campaigns() {

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://us13.api.mailchimp.com/3.0/campaigns/?status=sent",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "authorization: apikey " . get_option('api_key'),
      "cache-control: no-cache",
      "postman-token: ddc78213-f4cb-b55d-ce57-e0bb99fa90fe"
    ),
  ));

  $response = curl_exec($curl);
  return json_decode($response)->campaigns;
}

function get_newsletters_from_mc() {

    $all_campaigns = get_mailchimp_campaigns();

    foreach($all_campaigns as $data){

        $args = array(
            'meta_key' => 'campaign_id',
            'meta_value' => $data->id,
            'post_type' => 'newsletter',
            'post_status' => 'any',
            'posts_per_page' => 1,
            'fields' => 'ids',
        );
        $posts = get_posts($args);

        if ( empty( $posts ) ) {

          $content_curl = curl_init();

          curl_setopt_array($content_curl, array(
            CURLOPT_URL => "https://us13.api.mailchimp.com/3.0/campaigns/".$data->id.'/content',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
              "authorization: apikey ". get_option('api_key'),
              "cache-control: no-cache"
            ),
          ));

          $content_response = curl_exec($content_curl);

          //parsing the messy mailchimp response
          $content = wpautop( json_decode($content_response)->plain_text, true );

          $content_without_hr = preg_replace('/(-)\1{9,}/', '<hr>', $content);

          $stripped_content = substr($content_without_hr, 0, strpos($content_without_hr, "============================================================"));

          $stripped_content_with_links = preg_replace('~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i', '<a href="$0" target="_blank" title="$0">$0</a>', $stripped_content);

          $stripped_content_with_links_and_headlines = preg_replace('/([\*]{2}\s{1})(.*?)(\<br \/\>)/', '<h2>$2</h2>', $stripped_content_with_links);

          $my_post = array(
              'post_title' => $data->settings->subject_line,
              'post_content' => $stripped_content_with_links_and_headlines,
              'post_status' => 'publish',
              'post_author' => 1,
              'post_type' => 'newsletter'
          );

          $post_id = wp_insert_post( $my_post );

          add_post_meta($post_id, 'campaign_id', $data->id);
        }
        wp_reset_postdata();
    }
}

// if admin_init check for sent newsletters
add_action( 'admin_init', 'check_for_nl' );
function check_for_nl() {
  $request = get_site_transient("mailchimp_request");
  if(empty( $request ) )   {
     get_newsletters_from_mc();
     set_site_transient("mailchimp_request", 'fetched', DAY_IN_SECONDS);
   }
}



add_filter( 'template_include', 'nl_page_template', 99 );

function nl_page_template( $template ) {

	if ( is_page( get_option('newsletter_archive_page' ))) {
		$page_template = dirname( __FILE__ ) . '/newsletter-template.php' ;
		if ( '' != $page_template ) {
			return $page_template ;
		}
	}

	return $template;
}
