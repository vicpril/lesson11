<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', 0);
header("Content-Type: text/html; charset=utf-8");

$project_root = __DIR__;
$filename_user = 'user.php';
$smarty_dir = $project_root . '/smarty/';

// put full path to Smarty.class.php
require($smarty_dir . '/libs/Smarty.class.php');
$smarty = new Smarty();

$smarty->compile_check = true;
$smarty->debugging = false;

$smarty->template_dir = $smarty_dir . 'templates';
$smarty->compile_dir = $smarty_dir . 'templates_c';
$smarty->cache_dir = $smarty_dir . 'cache';
$smarty->config_dir = $smarty_dir . 'configs';

// Подключаем библиотеку FirePHPCore
require_once ($project_root . '/FirePHPCore/FirePHP.class.php');

// Инициализируем класс FirePHP
$firePHP = FirePHP::getInstance(true);

// Устанавливаем активность. Если выключить (false), то отладочные сообщения не будут
// отображаться в FireBug
$firePHP->setEnabled(true);

//
// FUNCTION
//

// Код обработчика ошибок SQL.
function databaseErrorHandler($message, $info) {
    // Если использовалась @, ничего не делать.
    if (!error_reporting())
        return;
    // Выводим информацию об ошибке.
    $path = strstr($message, '/');
    // Сообщение об ошибке без пути
    echo "SQL Error: " . rtrim($message, 'at ' . $path);
    // Кнопка "назад"
    exit('<br><a href="install.php">Sing in database</a>');
}

 // Пишем лог в firePHP
function myLogger($db, $sql, $caller) {
    global $firePHP;
    if (isset($caller['file'])) {
        $firePHP->group("at " . @$caller['file'] . ' line ' . @$caller['line']);
    }
    $firePHP->log($sql);
    if (isset($caller['file'])) {
        $firePHP->groupEnd();
    }
}

//
// Main block
//

// Работа скрипта
include 'notice_board.php';
include 'explanation.php';


$board = new Notice_board($filename_user, $project_root);

$id = (isset($_GET['id'])) ? $_GET['id'] : '';

if (isset($_GET['delete'])) {
    $board->delete_explanation_from_db($_GET['delete']);
}

      
if (isset($_POST['button_add'])) {
    $board->add_explanation_into_db($_POST, $id);
}

if (isset($_GET['show']) && isset($board->board[$_GET['show']])) {
    $show = $_GET['show'];
    $name = $board->board[$show]->get();
    foreach ($name as &$value) {
        $value = htmlspecialchars($value);
    }
    $smarty->assign('header_tpl', 'header_exp');
    $smarty->assign('title', 'Объявление');
    $smarty->assign('show', $show);
    $smarty->assign('name', $name);
} else {
    $smarty->assign('header_tpl', 'header');
    $smarty->assign('title', 'Доска объявлений');
}

$smarty->assign('private_radios', array('0' => 'Частное лицо', '1' => 'Компания'));
$smarty->assign('cities', $board->getCitiesList());
$smarty->assign('categories', $board->getCategoriesList());
$smarty->assign('list', $board->readListOfExplanations());
$smarty->assign('tr', array('bgcolor="#ffffff"', 'bgcolor="#E7F5FE"'));

$smarty->display('index.tpl');