<?php

class ConnectDB {
    
    private $filename_user;
    private $project_root;
    private $user;
    private $u_name;
    private $s_name;
    private $pas;
    private $db_name;
    private $mysqli;
    
    function __construct($filename_user, $project_root) {
        $this->filename_user = $filename_user;
        $this->project_root = $project_root;
        
    }
    
    function connectDB() {
        if (!file_exists($this->filename_user)) {

            // переадресация, если фаил не существует
            header("Refresh:10; url=install.php");
            exit("Параметры подключения к БД не заданы. Через 10 сек. Вы будете перенаправлены на страницу INSTALL.</br>
            Если автоматического перенаправления не происходит, нажмите <a href='install.php'>здесь</a>.");
        }

        // Подключение к БД
        if (!file_get_contents($this->filename_user)) {
            exit('Ошибка: неверный формат файла ' . $this->filename_user);
        }

        $this->user = unserialize(file_get_contents($this->filename_user));
        $this->u_name = $this->user['u_name'];
        $this->s_name = $this->user['s_name'];
        $this->pas = $this->user['pas'];
        $this->db_name = $this->user['db_name'];

        // Подключить DBSimple
        require_once $this->project_root . "/dbsimple/config.php";
        require_once "DbSimple/Generic.php";

        // Подключаемся к БД.
        $this->mysqli = DbSimple_Generic::connect("mysqli://$this->u_name:$this->pas@$this->s_name/$this->db_name");

        // Устанавливаем обработчик ошибок.
        $this->mysqli->setErrorHandler('databaseErrorHandler');
        $this->mysqli->setLogger('myLogger');

        $this->message = "Соединение с БД установлено.<br>";

        // Проверка существования таблиц

        $tables = array();
        $tables = $this->mysqli->selectCol("SELECT table_name FROM information_schema.tables WHERE table_schema = ?", $this->db_name);

        if (!in_array('explanations', $tables) ||
                !in_array('categories_list', $tables) ||
                !in_array('cities_list', $tables)) {

            // Переадресация, если таблиц нет
            header("Refresh:10; url=install.php");
            exit("Нарушена структура или отсутствуют таблицы в БД. Через 10 сек. Вы будете перенаправлены на страницу INSTALL.</br>
            Если автоматического перенаправления не происходит, нажмите <a href='install.php'>здесь</a>.");
        }
        
        return $this->mysqli;
    }
     

}
