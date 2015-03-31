<?php
// Mail relay via sympatico to bypass crappy classaxe mail server

  define ("smtp_host","smtp3.sympatico.ca");
  extract($_REQUEST);		// Extracts all request variables (GET and POST) into global scope.

  require("class.phpmailer.php");

/*
  print(	"<p>\n"
		."\$fromEmail=$fromEmail<br>\n"
		."\$fromName=$fromName<br>\n"
		."\$toEmail=$toEmail<br>\n"
		."\$toName=$toName<br>\n"
		."\$toCCEmail=$toCCEmail<br>\n"
		."\$toCCName=$toCCName<br>\n"
		."\$toBCCEmail=$toBCCName<br>\n"
		."\$toBCCEmail=$toBCCEmail<br>\n"
		."\$replyToEmail=$replyToEmail<br>\n"
		."\$replyToName=$replyToName<br>\n"
		."Host=".smtp_host."<br>\n"
		."\$subject=$subject<br>\n"
		."\$body=$body</p>\n"
	);
*/

  $mail = new PHPMailer();
  $mail->From =		$fromEmail;
  $mail->FromName =	$fromName;
  $mail->Host =		smtp_host;
  $mail->Mailer =	"smtp";
  $mail->AddReplyTo(    stripslashes($replyToEmail), stripslashes($replyToName));

  $mail->AddAddress(	stripslashes($toEmail),	     stripslashes($toName));
  $mail->AddCC(		stripslashes($toCCEmail),    stripslashes($toCCName));                  // name is optional
  $mail->AddBCC(	stripslashes($toBCCEmail),   stripslashes($toBCCName));
  $mail->Subject = 	stripslashes($subject);
  $mail->Body    = 	stripslashes($body);
  print("Result: ".$mail->Send());
?>
