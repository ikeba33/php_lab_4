<?php

require_once __DIR__ . '/core/main_functions.php';

use \core\Users;

checkPermissions([Users::ROLE_ADMIN]);

$lang = new \core\Language();

$lang->setCurrentLanguage(getLang());

$current_user = getUser();
$user_o = new Users();

if(isset($_GET['id'])) {
    $user = $user_o->fromID($_GET['id']);
}

$post = $_POST;
$mesage = '';
if(!empty($post['login']) || !empty($post['password']) || !empty($post['first_name']) || !empty($post['last_name']) || !empty($post['role'])) {
    if(!empty($user)) {
        $mesage = $user_o->update($user['id'], $post) ? 'Данные пользователя успешно обновлены!' : 'Возникла ошибка при обновлении пользователя';
        $user = $user_o->fromID($user['id']);
    } else {
        $users = $user_o->getUserList(['login' => $post['login']]);
        if(!empty($users)) {
            $mesage = 'Пользователь с таким логином уже существует!';
            $user = current($users);
        } else {
            $mesage = $user_o->create($post) ? 'Пользователь успешно создан!' : "Возникла ошибка при создании пользователя!";
        }
    }
}

$title = empty($user) ? 'Create user' : ('Edit user:' . $user['first_name'] . ' ' . $user['last_name']);

$roles = Users::getRoles();
$roles_options = [];
foreach($roles as $role => $role_name) {
    $roles_options[] = sprintf('<option value="%s" %s>%s</option>', $role, !empty($user['role']) && $user['role'] == $role ? 'selected' : '', $role_name);
}

?>

<!DOCTYPE html>
<html lang="<?=getLang()?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?=$title?></title>
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
            <h1><?= $title ?></h1>
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
                <div>
                    <label>Роль</label>
                    <select required name="role">
                       <?= implode('', $roles_options) ?>
                    </select>
                </div>
                <div class="btn-block">
                    <button type="submit">
                        Сохранить
                    </button>
                </div>
            </form>
       </div>
    </div>
    </body>
</html>