<?php

require_once __DIR__ . '/core/main_functions.php';

use \core\Users;

checkPermissions([Users::ROLE_ADMIN, Users::ROLE_MANAGER]);

$lang = new \core\Language();

$lang->setCurrentLanguage(getLang());

$user = getUser();

$user_o = new Users();
$users = $user_o->getUserList();

$columns = [
    'id' => 'ID',
    'login' => 'Login',
    'first_name' => 'First name',
    'last_name' => 'Last name',
    'lang' => 'Language',
    'role' => 'Role',
    'created' => 'Created',
    'modified' => 'Modified'
];

$head = '';
foreach($columns as $title) {
    $head .= '<th>' . $title . '</th>';
}

if($user->isAdmin()) {
    $head .= '<th>Actions</th>';
}

$message = getMessageFromSession();

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Client profile</title>
        <link rel="stylesheet" type="text/css" href="/css/styles.css">
        <script type="text/javascript" src="/js/jquery-3.5.1.min.js"></script>
    </head>
    <body>
    <header>
        <?= createNav($user) ?>
        <div class="logout-btn">
            <a href="/logout"><?=$lang->translate('logout') ?></a>
        </div>
        <?= getLangBlock() ?>
    </header>
    <div class="content">
        <div class="block">
            <form id="search-form">
                <div>
                    <label>Имя:</label>
                    <input type='text' name='first_name' value=''>
                </div>
                <div>
                    <label>Фамилия:</label>
                    <input type='text' name='last_name' value=''>
                </div>
                <div>
                    <label>Язык:</label>
                    <?= getLanguageSelect('Выберите язык для поиска', null) ?>
                </div>
                <div class="btn-block">
                    <button type="submit">
                        Искать
                    </button>
                </div>
            </form>
        </div>
        <div class="block">
            <h1><?=$lang->translate('user_list')?></h1>
            <?php if(!empty($message)) { ?>
                <div style="color: red; text-align: center; padding: 20px 0px;">
                        <?= $message ?>
                </div>
            <?php } ?>
            <table id="user-table">
                <thead>
                    <tr>
                        <?= $head ?>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $row) { ?>
                    <tr>
                        <?php foreach ($columns as $field => $title) { ?>
                            <td><?= $row[$field] ?></td>
                        <?php } ?>
                        <?php if($user->isAdmin()) { ?>
                            <td>
                                <a href="/edit-user?id=<?=$row['id']?>">Edit</a>
                                <?= $row['id'] !== $user->getID() ? '<a href="/remove-user?id=' . $row['id'] . '">Remove</a>' : '' ?>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
       </div>
    </div>
    <script>
        $(function(e) {
            // Выполнить функцию после полной загрузки ДОМ
            $('#search-form').submit(function(e) { // Выполнить поиск при отправки формы
                e.preventDefault();
                var form = $(this);
                $.ajax({
                    url: '/user-search?' + form.serialize(),
                    method: 'GET',
                    success: function(response) {
                        if(response) {
                            $('#user-table').find('tbody').html(response);
                        }
                    }, 
                    error: function(response)  {
                        alert('Error happend during user search!.');
                    }
                })
            })
        });
    </script>
    </body>
</html>