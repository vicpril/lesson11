<?php /* Smarty version 2.6.28, created on 2015-04-14 18:22:55
         compiled from install_dump.tpl */ ?>
<!DOCTYPE HTML>

<HTML>
    <HEAD>
        <TITLE>Install database</TITLE>
    </HEAD>
    <BODY>
        <form  method="post" accept-charset="utf-8" action="install.php">
            <DIV>
                <label><b>В базе данных уже существуют необходимые таблицы!</b></label>
                <br>
                <label>Вы хотите восстановить данные таблиц из дампа? </label>
                <br>
                <label>ВНИМАНИЕ! СУЩЕСТВУЮЩИЕ ДАННЫЕ БУДУТ ПОТЕРЯНЫ!</label>
                <br>
                <input name="button_install" type="submit" value="Да" >
                <input name="button_install" type="submit" value="Нет" >
            </DIV>
        </form>
    </BODY>
</HTML>