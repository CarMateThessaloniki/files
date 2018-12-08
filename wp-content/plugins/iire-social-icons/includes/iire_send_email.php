<?php
$recipient = $_REQUEST['recipient'];
$cc = $_REQUEST['cc'];
$bcc = $_REQUEST['bcc'];
$fnln = $_REQUEST['fnln'];
$sender = $_REQUEST['sender'];
$subject = $_REQUEST['subject'];
$message = $_REQUEST['message'];

$msg="iiRe Social Icons Message

Name: ".$fnln."
Email: ".$recipient."

Message: ".$message."
";

$message = wordwrap($msg, 70);

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/plain; charset=UTF-8' . "\r\n";
if ($cc != '') {
	$headers .= 'CC: '.$cc.'' . "\r\n";
}
if ($bcc != '') {
	$headers .= 'BCC: '.$bcc.'' . "\r\n";
}									
$headers .= 'From: '.$sender.'' . "\r\n";
if (mail($recipient, $subject, $message, $headers)) {
	echo "Your message was sucessfully sent!";
} else {
	echo "There was a problem sending your message!";
}
?>