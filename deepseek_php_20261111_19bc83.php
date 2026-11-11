<?php
header('Content-Type: application/json; charset=utf-8');

$to       = 'ivan@example.com';   // ← ВПИШИТЕ Email Ивана
$fromName = 'Сайт Ремонт-Хабаровск';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Неверный метод']);
    exit;
}

function clean($v) { return htmlspecialchars(trim((string)$v), ENT_QUOTES, 'UTF-8'); }

$name    = clean($_POST['name']    ?? '');
$phone   = clean($_POST['phone']   ?? '');
$device  = clean($_POST['device']  ?? '');
$problem = clean($_POST['problem'] ?? '');

if ($name === '' || $phone === '' || $device === '') {
    echo json_encode(['status' => 'error', 'message' => 'Заполните имя, телефон и тип техники']);
    exit;
}

$subject = '=?UTF-8?B?' . base64_encode('Новая заявка с сайта — ' . $device) . '?=';
$body = "Новая заявка с сайта\n\n"
      . "Имя: $name\n"
      . "Телефон: $phone\n"
      . "Тип техники: $device\n"
      . "Проблема: " . ($problem !== '' ? $problem : 'не указана') . "\n\n"
      . "Дата: " . date('d.m.Y H:i') . "\n"
      . "IP: " . ($_SERVER['REMOTE_ADDR'] ?? '—') . "\n";

$headers = "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <no-reply@" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ">\r\n"
         . "Content-Type: text/plain; charset=UTF-8\r\n"
         . "MIME-Version: 1.0\r\n";

$sent = @mail($to, $subject, $body, $headers);

echo $sent
    ? json_encode(['status' => 'ok'])
    : json_encode(['status' => 'error', 'message' => 'Письмо не отправлено. Позвоните: 8 924 114-36-46']);