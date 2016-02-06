<?php
/*
Plugin Name: My Awesome Email Plugin
Plugin URI: http://myawesomewebsite.com
Description: I created this plugin to rule the world via awesome WordPress email goodness
Version: 1.0
Author: Me
Author URI: http://myself.me
*/
add_filter ("wp_mail_content_type", "my_awesome_mail_content_type");
function my_awesome_mail_content_type() {
    return "text/html";
}

add_filter ("wp_mail_from", "my_awesome_mail_from");
function my_awesome_mail_from() {
    return "info@logic-coder.info";
}

add_filter ("wp_mail_from_name", "my_awesome_mail_from_name");
function my_awesome_email_from_name() {
    return "Bestbuy-bestsell";
}
function wp_new_user_notification($user_id, $plaintext_pass)
{
    $user = new WP_User($user_id);

    $user_login = stripslashes($user->user_login);
    $user_email = stripslashes($user->user_email);

    $email_subject = "Welcome to MyAwesomeSite " . $user_login . "!";

    ob_start();

    include("email_header.php");

    ?>

    <p>A very special welcome to you, <?php echo $user_login ?>. Thank you for joining MyAwesomeSite.com!</p>

    <p>
        Your password is <strong style="color:orange"><?php echo $plaintext_pass ?></strong> <br>
        Please keep it secret and keep it safe!
    </p>

    <p>
        We hope you enjoy your stay at MyAwesomeSite.com. If you have any problems, questions, opinions, praise,
        comments, suggestions, please feel free to contact us at any time
    </p>


    <?php
    include("email_footer.php");

    $message = ob_get_contents();
    ob_end_clean();
    //echo $message; exit;
    wp_mail($user_email, $email_subject, $message);
}
?>


