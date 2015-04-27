<?php

class ConnectDB {

    function __construct() {
        
    }
    
    public static function connectDB($filename_user, $project_root) {
        
        if (!file_exists($filename_user)) {

            // переадресация, если фаил не существует
            header("Refresh:10; url=install.php");
            exit("Параметры подключения к БД не заданы. Через 10 сек. Вы будете перенаправлены на страницу INSTALL.</br>
            Если автоматического перенаправления не происходит, нажмите <a href='install.php'>здесь</a>.");
        }

        // Подключение к БД
        if (!file_get_contents($filename_user)) {
            exit('Ошибка: неверный формат файла ' . $filename_user);
        }

        $user = unserialize(file_get_contents($filename_user));
        $u_name = $user['u_name'];
        $s_name = $user['s_name'];
        $pas = $user['pas'];
        $db_name = $user['db_name'];

        // Подключить DBSimple
        require_once $project_root . "/dbsimple/config.php";
        require_once "DbSimple/Generic.php";

        // Подключаемся к БД.
        $mysqli = DbSimple_Generic::connect("mysqli://$u_name:$pas@$s_name/$db_name");

        // Устанавливаем обработчик ошибок.
        $mysqli->setErrorHandler('databaseErrorHandler');
        $mysqli->setLogger('myLogger');

        // Проверка существования таблиц

        $tables = array();
        $tables = $mysqli->selectCol("SELECT table_name FROM information_schema.tables WHERE table_schema = ?", $db_name);

        if (!in_array('explanations', $tables) ||
                !in_array('categories_list', $tables) ||
                !in_array('cities_list', $tables)) {

            // Переадресация, если таблиц нет
            header("Refresh:10; url=install.php");
            exit("Нарушена структура или отсутствуют таблицы в БД. Через 10 сек. Вы будете перенаправлены на страницу INSTALL.</br>
            Если автоматического перенаправления не происходит, нажмите <a href='install.php'>здесь</a>.");
        }
        return $mysqli;
    }
     

}
