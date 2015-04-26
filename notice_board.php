<?php

class Notice_board {

    public $board = array();
    private $mysqli;

    function __construct($mysqli) {
        $this->mysqli = $mysqli;
        //Загрузка объявлений
        $this->get_explanations_from_db();
    }

    function add_explanation_into_db($exp, $id) {
        $exp = $this->processingQuery($exp, $id);
        $this->board[$id] = new Explanation($exp);
        $this->mysqli->select("REPLACE INTO explanations (?#) VALUES (?a)", array_keys($exp), array_values($exp));
        $this->listOfExplanations = $this->getListOfExplanations();
    }

    function delete_explanation_from_db($id) {
        unset($this->board[$id]);
        $this->mysqli->select("delete from explanations where id = ?d", $id);
        $this->listOfExplanations = $this->getListOfExplanations();
    }

    // Массив для списка объявлений для вывода
    function getListOfExplanations() {

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

}
