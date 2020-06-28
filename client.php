<?php

require_once __DIR__ . '/core/main_functions.php';

use \core\Users;

checkPermissions([Users::ROLE_ADMIN, Users::ROLE_CLIENT, Users::ROLE_MANAGER]);

$lang = new \core\Language();

$lang->setCurrentLanguage(getLang());

$user_o = new Users();
$current_user = getUser();

$user = $user_o->fromID($current_user->getID());

$post = $_POST;
$mesage = '';
if(!empty($post['login']) || !empty($post['password']) || !empty($post['first_name']) || !empty($post['last_name']) || !empty($post['role'])) {
    $mesage = $user_o->update($user['id'], $post) ? 'Данные пользователя успешно обновлены!' : 'Возникла ошибка при обновлении пользователя';
    $user = $user_o->fromID($user['id']);
}

?>

<!DOCTYPE html>
<html lang="<?=getLang()?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Профайл клиента</title>
        <link rel="stylesheet" type="text/css" href="/css/styles.css">
    </head>
    <body>
    <header>
        <?= createNav($current_user) ?>
        <div class="logout-btn">
            <a href="/logout"><?=$lang->translate('logout') ?></a>
        </div>
        <?= getLangBlock() ?>
    </header>
    <div class="content">
        <div class="block">
            <?php if($current_user->getRoleID() == Users::ROLE_CLIENT) { ?>
            <h1>Изменить данные</h1>
            <form method="POST">
                <div style="color: red; text-align: center;">
                    <?= $mesage ?>
                </div>
                <div>
                    <label>Логин:</label>
                    <input type='text' name='login' required value='<?=!empty($user['login']) ? $user['login'] : ''?>'>
                </div>
                <div>
                    <label>Пароль:</label>
                    <input type='text' name='password' required value='<?=!empty($user['password']) ? $user['password'] : ''?>'>
                </div>
                <div>
                    <label>Имя:</label>
                    <input type='text' name='first_name' required value='<?=!empty($user['first_name']) ? $user['first_name'] : ''?>'>
                </div>
                <div>
                    <label>Фамилия:</label>
                    <input type='text' name='last_name' required value='<?=!empty($user['last_name']) ? $user['last_name'] : ''?>'>
                </div>
                <div>
                    <label>Язык:</label>
                    <?= getLanguageSelect('Выберите язык', !empty($user['lang']) ? $user['lang'] : null) ?>
                </div>
                <div class="btn-block">
                    <button type="submit">
                        Сохранить
                    </button>
                </div>
            </form>
            <?php } else { ?>
                <h1>Client profile</h1>
                <h3>
                    <?= $lang->translate($current_user->getMessage(), ['{role}' => $lang->translate($current_user->getRole()), '{name}' => $current_user->getFullName()]) ?>
                </h3>
                <p>
                    Только клиент может менят свои данные на этой странице!
                </p>
            <?php } ?>
       </div>
    </div>
    </body>
</html>