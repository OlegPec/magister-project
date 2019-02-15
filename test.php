
<?php

/**
* URL-адрес бота и его маркер.
*/



/**
* Зададим основные переменные.
*/


$file = file_get_contents('./user.json'); // Прочитаем файл с администраторами
$AdminsDBlist = json_decode($file, TRUE); // Получим список администраторов
$chat_id = 456155438;

var_dump($AdminsDBlist);
if (in_array($chat_id, $AdminsDBlist)) {
    echo "Нашел id";
}else{
    echo "Не нашел id";
}
$key = array_search($chat_id, $AdminsDBlist);
echo $key;

$fileconfig = file_get_contents('./config.json');
$config = json_decode($fileconfig, TRUE);
var_dump($config);
echo "<br>";
echo $config[0]['ORACLE_SID'];
echo "<br>";
echo "<br>";
echo "<br>";
$inline_button1 = array("text"=>"Изменить", "callback_data"=>'/test');
$inline_button2 = array("text"=>"Изменить2", "callback_data"=>'/plz');
$inline_keyboard = [[$inline_button1,$inline_button2]];
$keyboard = array("inline_keyboard"=>$inline_keyboard);
print_r($keyboard);
echo "<br>";
echo "<br>";
echo "<br>";
$keyboard2 = [
            'inline_keyboard' => [
                ['text'=>'Изменить'], // Первый ряд кнопок
                ['text'=>'Изменить2',"callback_data"=>'/plz']
            ],
        ];
        
print_r($keyboard2);
echo "<br>";
echo "<br>";
echo "<br>";
echo "<br>";
echo "<br>";
echo "<br>";
$connectstr_dbhost = '';
$connectstr_dbname = 'Monitoring';
$connectstr_dbusername = '';
$connectstr_dbpassword = '';

foreach ($_SERVER as $key => $value) {
	if (strpos($key, "MYSQLCONNSTR_localdb") !== 0) {
		continue;
	}
	
	$connectstr_dbhost = preg_replace("/^.*Data Source=(.+?);.*$/", "\\1", $value);
	//$connectstr_dbname = preg_replace("/^.*Database=(.+?);.*$/", "\\1", $value);
	$connectstr_dbusername = preg_replace("/^.*User Id=(.+?);.*$/", "\\1", $value);
	$connectstr_dbpassword = preg_replace("/^.*Password=(.+?)$/", "\\1", $value);
}
$res = selectData($chat_id, 'access');
var_dump($res);
echo "<br>";
echo $res['access'];

function selectData($chat_id, $table, $add_table = null){
    $link = mysqli_connect($GLOBALS['connectstr_dbhost'], $GLOBALS['connectstr_dbusername'],
        $GLOBALS['connectstr_dbpassword'], $GLOBALS['connectstr_dbname']);
    $tables = ['access', 'command', 'bd_login', 'pass'];
    //$result = 'adadad';
    //switch($table) {
        //case 'access':
    //if($add_table == null){
        //$sql = "SELECT * FROM Users WHERE chat_id=?";
    //}elseif(in_array($add_table, $tables)){
        //$sql = "SELECT {$table},{$add_table} FROM Users WHERE chat_id=?";
    //} 
    //if(in_array($table, $tables)) {
        if ($stmt = mysqli_prepare($link, "SELECT * FROM Users WHERE chat_id=?")) {
            /* связываем параметры с метками */
            mysqli_stmt_bind_param($stmt, "s", $chat_id);

            /* запускаем запрос */
            mysqli_stmt_execute($stmt);

            /* связываем переменные с результатами запроса */
            //mysqli_stmt_bind_result($stmt, $result, $comm);
            
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

            /* получаем значения */
            //while ($row = mysqli_fetch_assoc($result)) {
                //printf ("%s (%s)\n", $row["access"], $row["command"]);
            //}
            //$result = mysqli_fetch_array($stmt, MYSQLI_ASSOC);
            //mysqli_stmt_fetch($stmt);
            //while (mysqli_stmt_fetch($stmt)) {
                //printf("%s %s\n", $result, $com);
                //$results[] = $result;
            //}

            //$access = $res;
            /* закрываем запрос */
            mysqli_stmt_close($stmt);
        }
    //}
    mysqli_close($link);
    //var_dump($row);
    return $row;

}
//if(!$AdminsDBlist[0]){
//	echo "test";
//}
//$AdminsDBlists[] = array('id'=>'121212', 'status'=>'Admin');    // Представить новую переменную как элемент массива, в формате 'ключ'=>'имя переменной'      
//print_r(json_encode($AdminsDBlists));             
//file_put_contents('./user.json', json_encode($AdminsDBlists));  // Перекодировать в формат и записать в файл.

?>