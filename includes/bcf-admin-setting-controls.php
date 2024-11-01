<?php
global $wpdb; 
$table_name =  $wpdb->prefix.'basic_contact_form';
global $updated;
if (isset($_POST['submit']))
{

	$bcf_checkbox = sanitize_text_field($_POST['checkdata']);
	$bcf_email = sanitize_email($_POST['email']);
	$bcf_sitekey = sanitize_text_field($_POST['sitekey']);
	$bcf_secret = sanitize_text_field($_POST['secret']);
  $check_recaptcha = sanitize_text_field($_POST['check_recaptcha']);
	
  $bcf_data = array (
    'check_count' => $bcf_checkbox ,
		'email_to' => $bcf_email ,
		'site_key' => $bcf_sitekey ,
		'secret_key' => $bcf_secret ,
    'check_recaptcha' => $check_recaptcha ,

    );
    $wherecondition = array(
                             'id' => 1
    );
    $updated = $wpdb->update ($table_name , $bcf_data , $wherecondition);
   }
	
   $results = $wpdb->get_results( "SELECT * FROM $table_name");

   ?>
	

<section id="form_integration_controls" >

 <div class="card" id="forms_card_control">
	 <h2>Forms Integration Controls</h2></br>
    <form method="post" action="<?php the_permalink(); ?>">
     <input type="hidden" name="checkdata" value="0" />
       <p><label class="label_styled" >  Show Message Length:  </label> <input type="checkbox" id="isChecked" name="checkdata" value="1" size="40" 
       <?php  
         foreach ( $results as $print ) {
	       if($print-> check_count == 1){
          echo esc_html( "checked");
	       }else {
          echo esc_html("unchecked");
	      }
       }
      ?> ></p>
      <p><label class="label_styled" > Email To:</label> <input type="input" name="email" value="<?php  
      foreach ( $results as $print ) {
        echo esc_html( $print-> email_to );
      }
     ?>" class="email_height" ></p></br>	
     <h2 class="title">reCAPTCHA</h2>
     <input type="hidden" name="check_recaptcha" value="0" />
     <p><label class="label_styled" > Display reCAPTCHA:</label> <input type="checkbox" id="isChecked" name="check_recaptcha" value="1" size="40" 
     <?php  
      foreach ( $results as $print ) {
      	if($print-> check_recaptcha == 1){
          echo esc_html( "checked");
	          }else {
              echo esc_html( "unchecked");
	          }
         }
     ?> ></p>
 
   <div class="inside">
    <p>reCAPTCHA protects you against spam and other types of automated abuse. With WPCF Forms reCAPTCHA integration module, you can block abusive form submissions by spam bots. For details, see <a href="https://developers.google.com/recaptcha">reCAPTCHA (v2)</a>.</p>
    <input type="hidden" id="_wpnonce" name="_wpnonce" value="e3e709a10e"><input type="hidden" name="_wp_http_referer" value="">
    <p  class="secret_key_margin" ><label for="sitekey" class="label_styled" >Site Key:</label>
    <input type="text" aria-required="true" value="<?php  
    foreach ( $results as $print ) {
   	 echo esc_html( $print-> site_key) ;
     }
    ?>" id="sitekey" name="sitekey"   class="regular-text code email_height_site"></p>
    <p class="secret_key_margin"><label for="secret" class="label_styled" >Secret Key:</label>
    <input type="text" aria-required="true" value="<?php  
    foreach ( $results as $print ) {
	    echo esc_html( $print-> secret_key );
       }
     ?>" id="secret" name="secret" class="regular-text code email_height_secret" ></p>
  
    <p class="submit">
	   <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
    </p>
 
  </div> 
 </form>
 <?php  

	if($updated == true) {
    ?>
		<h1 class='data_save' ><?php echo esc_html( "Data Save Sucessfully"); ?></h1>
    <?php
	   }	  
?>
</div>
  
<div class="card_shortcode">
	<h3>Copy Shortcode</h3>
   <div>
	  <label>Copy this shortcode and paste it into your post, page, or text widget content:</label>
	  <p class="simple_shortcode_form">[bcf_form]</p>
   </div>
  <div>
	  <label>Copy this shortcode and paste it into your post, page, or text widget content:</label>
	  <p  class="simple_shortcode_form" >echo do_shortcode( '[bcf_form]' );</p>
   </div>
 </div>

</section>	


