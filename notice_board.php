<?php

class Notice_board {

    public $board = array();
    private $filename_user;
    private $project_root;
    private $user;
    private $u_name;
    private $s_name;
    private $pas;
    private $db_name;
    private $mysqli;
    private $listOfExplanations;

    function __construct($filename_user, $project_root) {
        $this->filename_user = $filename_user;
        $this->project_root = $project_root;

        $this->message = $this->connectDB();
        //Загрузка объявлений
        $this->get_explanations_from_db();
        $this->listOfExplanations = $this->getListOfExplanations();
    }

    function add_explanation_into_db($exp, $id) {
        $exp = $this->processingQuery($exp, $id);
        $this->board[$id] = new Explanation($exp);
        $this->mysqli->select("REPLACEn INTO explanations (?#) VALUES (?a)", array_keys($exp), array_values($exp));
        $this->listOfExplanations = $this->getListOfExplanations();
    }

    function delete_explanation_from_db($id) {
        unset($this->board[$id]);
        $this->mysqli->select("delete from explanations where id = ?d", $id);
        $this->listOfExplanations = $this->getListOfExplanations();
    }

    // Массив для списка объявлений для вывода
    private function getListOfExplanations() {

        $list = array();
        if (count($this->board) > 0) {
            foreach ($this->board as $key => $exp) {
                $list[] = '<a href="index.php?show=' . $key . '">' . $exp->get()['title'] . '</a>';
                $list[] = $exp->get()['price'];
                $list[] = $exp->get()['seller_name'];
                $list[] = '<a href="index.php?delete=' . $key . '">Удалить</a>';
            }
        }
        return $list;
    }

    function readListOfExplanations() {
        return $this->listOfExplanations;
    }

    // обработка входящего запроса
    private function processingQuery($exp, $id) {
        $exp['id'] = $id;
        if (isset($exp['button_add'])) {
            unset($exp['button_add']);
        }
        foreach ($exp as $key => &$value) {
            $query[$key] = strip_tags($value);
        }
        $query['price'] = (float) $query['price'];
        return $query;
    }

    // Запрос списка городов для формы
    function getCitiesList() {
        $cities = $this->mysqli->selectCol("SELECT `index` AS ARRAY_KEY, city FROM cities_list");
        return $cities;
    }

    // Запрос списка категорий для формы
    function getCategoriesList() {
        $result = $this->mysqli->select("SELECT t2.index, t2.category AS cat, t1.category AS groupe
                        FROM categories_list AS t1
                        LEFT JOIN categories_list AS t2 ON t2.parent_id = t1.index
                        WHERE t2.parent_id is not null");
        foreach ($result as $row) {
            $categories [$row['groupe']][$row['index']] = $row['cat'];
        }
        return $categories;
    }

    // импорт объявлений из БД в хранилище класса
    private function get_explanations_from_db() {
        $explanations = $this->mysqli->select("SELECT id AS ARRAY_KEY, private, seller_name, "
                . "email, allow_mails, phone, location_id, category_id, title, description, "
                . "price FROM explanations ORDER BY id");
        if (isset($explanations)) {
            foreach ($explanations as $id => $exp) {
                $this->board[$id] = new Explanation($exp);
            }
        }
    }

    // соединение с БД
    private function connectDB() {
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

//        $mysql_dir = $project_root;
//        include($mysql_dir . '/mysql.php');
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
    }
}
