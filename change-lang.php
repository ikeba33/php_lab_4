<?php 

require_once __DIR__ . '/core/main_functions.php';

// Изменить язык для пользователя
if(count($_POST) === 1 && isset($_POST['lang'])) {
    setLang($_POST['lang']);
}

// Переадресация назад на страницу после смены языка.
exit(header('Location: ' . $_SERVER['HTTP_REFERER']));