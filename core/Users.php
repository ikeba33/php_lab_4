<?php

namespace core;

class Users {

    const ROLE_ADMIN = 1;
    const ROLE_MANAGER = 2;
    const ROLE_CLIENT = 3;
    
    private $users = [];
    private $current_user = null;
    private $instance = null;

    private $_pdo = null;

    private static $roles = [];

    public function __construct(){

        self::$roles = [
            self::ROLE_ADMIN => 'admin',
            self::ROLE_MANAGER => 'manager',
            self::ROLE_CLIENT => 'client'
        ];

        $this->_pdo = \core\CustomPDO::getInstance();
    }

    // Получить пользователя по логину и паролю.
    public function getUserFromLoginAndPassword($login, $password) {
        $sql = "SELECT * FROM users WHERE login=:login AND password=:password";
        $query = $this->_pdo->prepare($sql);
        $query->execute([
            ':login' => $login,
            ':password' => $password
        ]);

        $user = $query->fetch(\PDO::FETCH_ASSOC);

        if(empty($user)) return null;

        // для каждой роли свой класс
        $class = '\\classes\\' . self::$roles[intval($user['role'])];

        // инициализация обьекта для конкретной роли
        $instance = new $class();
        $instance->current_user = $user;
        $this->instance = $instance;

        return $this->instance;;
    }

    // ПОлучить список из пользователей
    public function getUserList($condition = []) {
        $where = $whereValues = [];

        $filters = [
            'first_name' => '%s REGEXP :%s',
            'last_name' => '%s REGEXP :%s',
        ];

        foreach($condition as $key => $value) {
            if(!empty($value)) {
                $where[] = isset($filters[$key]) ? sprintf($filters[$key], $key, $key)  : "$key = :$key";
                $whereValues[':'.$key] = $value;
            }
        }

        $sql = "SELECT * FROM users " . (!empty($where) ? ' WHERE ' . implode(' AND ', $where) : '');

        $query = $this->_pdo->prepare($sql);
        if($query->execute($whereValues)) {
            return $query->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            return [];
        }
    }

    public function fromID($id) {
        $users = $this->getUserList(['id' => $id]);
        if(!empty($users)) {
            return current($users);
        } else {
            return null;
        }
    }

    // Создать пользователя
    public function create($data) {
        $sqlParams = [];
        foreach($data as $key => $value) {
            if(is_numeric($value)) {
            }
            $sqlParams[':'.$key] = $value;
        }

        $sql = "INSERT INTO users(login, password, first_name, last_name, lang, role, created, modified) VALUES(" . implode(',', array_keys($sqlParams)) . ", NOW(), NOW())";
        $query = $this->_pdo->prepare($sql);
        return $query->execute($sqlParams);
    }

    // Обновить пользователя
    public function update($user_id, $data) {
        $sqlParams = $sqlValues = [];
        foreach($data as $key => $value) {
            $sqlParams[] = $key . ' = :' . $key;
            $sqlValues[':'.$key] = $value;
        }

        $sql = "UPDATE users SET " . implode(', ', $sqlParams) . ' WHERE id = ' . $user_id;

        $query = $this->_pdo->prepare($sql);
        return $query->execute($sqlValues);
    }

    // Удалить пользователя
    public function delete($id) {
        $sql = "DELETE FROM users WHERE id=" . $id;
        $query = $this->_pdo->prepare($sql);
        return $query->execute();
    }

    public function getID() {
        return $this->current_user['id'];
    }

    public function getLogin() {
        return $this->current_user['login'];
    }

    public function getPassword() {
        return $this->current_user['password'];
    }

    public function getFirstName() {
        return $this->current_user['first_name'];
    }

    public function getLastName() {
        return $this->current_user['last_name'];
    }

    public function getFullName() {
        return $this->current_user['first_name'] . ' ' . $this->current_user['last_name'];
    }

    public function setLang($lang) {
        $this->current_user['lang'] = $lang;
    }

    public function getLang() {
        return isset($this->current_user['lang']) ? $this->current_user['lang'] : null;
    }

    public function getRoleID() {
        return $this->current_user['role'];
    }

    public function getRole() {
        return self::$roles[$this->current_user['role']];
    }

    public static function getRoles() {
        return self::$roles;
    }

    public function isClient() {
        return $this->current_user['role'] == self::ROLE_CLIENT;
    }

    public function isManager() {
        return $this->current_user['role'] == self::ROLE_MANAGER;
    }

    public function isAdmin() {
        return $this->current_user['role'] == self::ROLE_ADMIN;
    }

    public function getMessage() {
        return 'hello_message';
    }
}