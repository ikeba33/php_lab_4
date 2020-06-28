<?php

require_once __DIR__ . '/core/main_functions.php';

use \core\Users;

checkPermissions([Users::ROLE_ADMIN, Users::ROLE_MANAGER]);

$user_o = new Users();

$available_filters = ['first_name', 'last_name', 'lang'];
$conditions = array_intersect_key($_GET, array_flip($available_filters));

if(!empty($conditions)) {

$user = getUser();

$users = $user_o->getUserList($conditions);

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

?>

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
<?php }
}