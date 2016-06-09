<?php
// php -q /home/kilaiet0/public_html/mail.php
$to_address = "jhtsai@hotmail.com";
$subject = "This goes in the subject line of the email!";
$message = "This is the body of the email.\n\n";
$message .= "More body: probably a variable.\n";
$headers = "From: kent.tsai@kilaiet.net\r\n";
mail("$to_address","$subject","$message","$headers");
echo "Mail Sent.";
?>