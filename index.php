<?php
/*
  *  Имя базы данных : test_db
     Есть 3 таблицы (связь : many-to-many):
    user:                        projects:                  user_proj
    id  | name  |  id_proj |     id | proj                  userid | projid
    -----------------------      ----------                 ----------------
    -test data for tb-user-      -test data for tb-proj-    -test data for tb-user-proj-

  * SQL в конце файла
*/
?>
<?php
// Нужно изменить на свои параметры подключ. к DB (в конструкторе класса DB)
// т.к. нет задачи для создания config или реестра

$query = 'SELECT user.id, user.name, count(up.projectid)
            FROM user 
            LEFT JOIN userproject up 
            ON (user.id = up.userid) 
            GROUP BY user.id;' ;
try {
    $db = DB::getDB();
    $rez = $db->query($query);
} catch (Exception $e) {
    echo $e->getMessage() . ':(';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Test</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
<div class="container">

    <div class="page-header">
        <h1>Тест (сетка Bootstrap):</h1>
        <p>* SQL - в конце файла index.php</p>
    </div>
    <div>
        <table class="table table-bordered">
                <tr class="warning">
                    <td>id</td>
                    <td>Имя</td>
                    <td>Количество проектов</td>
                </tr>
                <?php
                    foreach ($rez as $k => $v){
                        if($k % 2 == 0)echo '<tr class="success">';
                        else echo '<tr>';
                        foreach ($v as $key => $value) {
                            echo '<td>' . $value . '</td>';
                        }
                        echo '</tr>';
                    }
                ?>
        </table>
    </div>
</div> <!-- /container -->
</body>
</html>
<?php
final class DB{

    private $connection;
    private static $db = null;

    private function __construct() {
        $this->connection = new mysqli('localhost', 'root', 'falcons', 'test_db');

        $this->query("SET NAMES UTF8");

        if( !$this->connection ) {
            throw new Exception('Could not connect to DB ');
        }
    }

    // cтандартный singelton
    public static function getDB() {
        if (self::$db == null) self::$db = new DB();
        return self::$db;
    }

    public function query($sql){
        if ( !$this->connection ){
            return false;
        }

        $result = $this->connection->query($sql);

        if ( mysqli_error($this->connection) ){
            throw new Exception(mysqli_error($this->connection));
        }

        if ( is_bool($result) ){
            return $result;
        }

        $data = array();
        while( $row = mysqli_fetch_assoc($result) ){
            $data[] = $row;
        }

        mysqli_free_result($result);

        return $data;
    }

    public function escape($str){
        return mysqli_escape_string($this->connection, $str);
    }

    public function __destruct() {
        if ($this->connection) $this->connection->close();
    }
}
/* SQL for create table
-----------------------------------------

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `project`;
CREATE TABLE `project` (
  `id` int(55) NOT NULL AUTO_INCREMENT,
  `proj` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_mysql500_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `project` (`id`, `proj`) VALUES
(1,	'Project 1'),
(2,	'Project 2'),
(3,	'Project 3');

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(55) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user` (`id`, `name`) VALUES
(1,	'Александр'),
(2,	'Максим'),
(3,	'Галя'),
(4,	'Олег'),
(5,	'Герасим'),
(6,	'Александр');

DROP TABLE IF EXISTS `userproject`;
CREATE TABLE `userproject` (
  `userid` int(55) NOT NULL,
  `projectid` int(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `userproject` (`userid`, `projectid`) VALUES
(1,	2),
(2,	2),
(1,	3),
(4,	3);

-------------
*/
?>