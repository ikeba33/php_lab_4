<?php

session_start();
define('ROOT', __DIR__ . '/../');

// Подключает все классы
spl_autoload_register(function ($class) {
    $file = ROOT . '/' . str_replace('\\', '/', $class) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

// Получить пользователя по логину и паролю из сессии
function getUser() {
    if(isset($_SESSION['user_id'])) {
        $user_o = new \core\Users();
        $user = $user_o->fromID($_SESSION['user_id']);
        // Init object
        return $user_o->getUserFromLoginAndPassword($user['login'], $user['password']);
    }
    return null;
}

// Проверка на то что пользователь гость
function isGuest() {
    return  !isset($_SESSION['user_id']);
}

// Проверить если пользователь имеет доступ
function checkPermissions($allowed_roles = []) {
    if(isGuest()) {
        exit(header('Location: /'));
    }
    $user = getUser();
    if(!in_array($user->getRoleID(), $allowed_roles)) {
        http_response_code(403);
        die('Forbidden');
    }
}

function logIn($user_id) {
    $_SESSION['user_id'] = $user_id;
}

function logOut() {
    session_destroy();
}

function getLangBlock() {
    return sprintf('<form class="lang-block" action="/change-lang" method="POST">%s<button type="submit">Save</button></form>', getLanguageSelect());
}

function getLanguageSelect($placeholder = false, $selected = null) {
    if($selected == null) $selected = getLang();
    $options = $placeholder ? '<option value="">Select language</option>' : '';
    foreach(\core\Language::getAllLanguages() as $abr => $lang) {
        $options .= sprintf('<option value="%s" %s>%s</option>', $abr, $abr == $selected ? 'selected' : '', $lang['name']);
    }
    
    return sprintf('<select name="lang">%s</select>', $options);
}

function createNav($user) {
    $items = [];
    switch($user->getRoleID()) {
        case  \core\Users::ROLE_ADMIN:
            $items['/admin'] = 'Admin profile';
            $items['/edit-user'] = 'Create user';
        case  \core\Users::ROLE_MANAGER:
            $items['/manager'] = 'Manager profile';
            $items['/users-list'] = 'Users list';
        case  \core\Users::ROLE_CLIENT:
            $items['/client'] = 'Client profile';
            break;
    }

    $urls = '';
    foreach($items as $url => $text) {
        $urls .= sprintf('<a href="%s">%s</a>', $url, $text);
    }

    return sprintf('<nav>%s</nav>', $urls);

}

function getLang() {
    return isset($_SESSION['lang']) ? $_SESSION['lang'] : null;
}

function setLang($lang) {
    $_SESSION['lang'] = $lang; 
}

function getMessageFromSession() {
    $message = '';
    if(isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
    }
    return $message;
}

function redirectToProfile($user) {
        // Определение ссылки для конкретной роли
        if($user->isAdmin()) {
            $location = '/admin';
        } elseif($user->isClient()) {
            $location = '/client';
        } elseif($user->isManager()) {
            $location = '/manager';
        } else {
            logOut();
            return 'Неизвестная роль';
        }
    
        if(isset($location)) { // Редирект на профайл
            exit(header('Location: ' . $location));
        }
}

// Перенаправить пользователя на страницу входа
function redirectToLogin() {
    exit(header('Location: /'));
}