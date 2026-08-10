<?php
// Honeypot: bots fill this hidden field, humans don't
if (!empty($_POST['company'])) {
    header('Location: /index.html?sent=1#kontakt');
    exit;
}

function clean($v) {
    return trim(str_replace(["\r", "\n"], '', $v));
}

$reasonLabels = [
    'allgemein'  => 'Allgemeine Anfrage',
    'einfuehrung'=> 'Einführungsflug',
    'gutschein'  => 'Rundflug-Gutschein',
    'event'      => 'Veranstaltung / Betriebsfeier',
    'mitglied'   => 'Mitglied werden',
];

$reason  = clean($_POST['reason'] ?? 'allgemein');
$name    = clean($_POST['name'] ?? '');
$email   = clean($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: /index.html?sent=0#kontakt');
    exit;
}

$to = 'kontakt@flugplatz-plaetzer.de';
$label = $reasonLabels[$reason] ?? 'Allgemeine Anfrage';
$subject = '=?UTF-8?B?' . base64_encode('Anfrage über die Website: ' . $label) . '?=';

$body  = "Neue Anfrage über das Kontaktformular\n";
$body .= "Grund: $label\n";
$body .= "Name: $name\n";
$body .= "E-Mail: $email\n\n";
$body .= "Nachricht:\n" . $message . "\n";

$headers  = "From: Website <kontakt@flugplatz-plaetzer.de>\r\n";
$headers .= "Reply-To: " . $name . " <" . $email . ">\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$sent = @mail($to, $subject, $body, $headers);

header('Location: /index.html?sent=' . ($sent ? '1' : '0') . '#kontakt');
exit;
