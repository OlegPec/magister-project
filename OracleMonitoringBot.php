<?php

/**
* URL-адрес бота и его маркер.
*/

$access_token = 'Токен бота';

$api = 'https://api.telegram.org/bot' . $access_token;

/**
* Зададим основные переменные.
*/
$output = json_decode(file_get_contents('php://input'), TRUE); // Получим то, что передано скрипту ботом в POST-сообщении и распарсим
$outputs = file_get_contents('php://input');

$file = file_get_contents('./user.json'); // Прочитаем файл с администраторами
$AdminDbListOnline = json_decode($file,TRUE); // Получим список пользователей(администраторов) которые в чате

$file2 = file_get_contents('./globuser.json'); // Прочитаем файл с администраторами
$AdminDbGlobList = json_decode($file2,TRUE); // Получим общий список пользователей //администраторов

$chat_id = $output['message']['chat']['id']; // Выделим идентификатор чата
$message = $output['message']['text']; // Выделим сообщение пользователя\

$callback_query = $output['callback_query'];
$query_id = $output['callback_query']['id'];
$data = $callback_query['data'];
$chat_id_call = $callback_query['message']['chat']['id'];

$connectstr_dbhost = '';
$connectstr_dbname = '';
$connectstr_dbusername = '';
$connectstr_dbpassword = '';

/**
* Получим значения для подключения к БД MySQL.
**/
foreach ($_SERVER as $key => $value) {
	if (strpos($key, "MYSQLCONNSTR_localdb") !== 0) {
		continue;
	}
	
	$connectstr_dbhost = preg_replace("/^.*Data Source=(.+?);.*$/", "\\1", $value);
	$connectstr_dbname = preg_replace("/^.*Database=(.+?);.*$/", "\\1", $value);
	$connectstr_dbusername = preg_replace("/^.*User Id=(.+?);.*$/", "\\1", $value);
	$connectstr_dbpassword = preg_replace("/^.*Password=(.+?)$/", "\\1", $value);
}
$select = selectData($chat_id);
$access = $select['access'];
$config = selectConfig();
/**
* Получим команды от пользователя.
**/
if($data){
    switch ($data){
        case '/sid':
            bot('answerCallbackQuery', [
                'callback_query_id' => $query_id ]);
            sendMessage($chat_id_call, 'Введите ORACLE_SID');
            updatesDBUsers($chat_id_call, 'command', 'ORACLE_SID');
                break;
        case '/host':
            bot('answerCallbackQuery', [
                'callback_query_id' => $query_id ]);
            sendMessage($chat_id_call, 'Введите HOST');
            updatesDBUsers($chat_id_call, 'command', 'HOST');
            break;
        case '/port':
            bot('answerCallbackQuery', [
                'callback_query_id' => $query_id ]);
            sendMessage($chat_id_call, 'Введите PORT');
            updatesDBUsers($chat_id_call, 'command', 'PORT');
            break;
        case '/close':
            bot('answerCallbackQuery', [
                'callback_query_id' => $query_id ]);
            bot('deleteMessage', [
                'chat_id' => $chat_id_call,
                'message_id' => $callback_query['message']['message_id']
            ]);
    }
}
if($select['command']){
    switch ($select['command']) {
        case ('SEND_SQL'):
            if($access == 'Admin' || $access == 'JuniorAdmin') {
                $text = '';
                if($config['HOST'] != '' && $config['PORT'] != '' && $config['ORACLE_SID'] != '') {
                    $db = '(DESCRIPTION=
                    (ADDRESS=(PROTOCOL=TCP)
                        (HOST='.$config['HOST'].')
                        (PORT='.$config['PORT'].'))
                    (CONNECT_DATA=
                        (SID='.$config['ORACLE_SID'].')
                    ))';
                    $conn = oci_connect('login', 'password', $db);
                    if (!$conn) {
                        $e = oci_error();
                        $text = json_encode($e);
                    } else {
                        $query = oci_parse($conn, $message);
                        oci_execute($query);
                        $row = oci_fetch_array($query, OCI_ASSOC+OCI_RETURN_NULLS);
                        $text = "Команда отправлена\n".json_encode($row);
                    }
                }
                else{
                    $text = "Введены не все данные в настройках";
                }
                updatesDBUsers($chat_id, 'command', '');
                $keyboard = keyboards($select['access']);
                bot('sendMessage', [
                    'chat_id' => $chat_id,
                    'text' => $text."\nВозвращаю клавиатуру",
                    'reply_markup' => json_encode($keyboard)
                ]);
                sendMessage($chat_id, $times);
            }
        break;
        case ('ORACLE_SID'):
            updateConfig('ORACLE_SID', $message);
            break;
        case ('HOST'):
            updateConfig('HOST', $message);
            break;
        case ('PORT'):
            updateConfig('PORT', $message);
            break;
    }
}
elseif(in_array($chat_id, $AdminDbListOnline)) {
    switch ($message) {

        case ('Справка'):
            sendMessage($chat_id, 'Текст спарвки');
            break;

        case ('/start'):
            startButton($chat_id, $AdminDbListOnline, $AdminDbGlobList, $select['access']);
            break;

        case ('/stop'):
            stopButton($chat_id, $AdminDbListOnline);
            break;

        case ('Настройки подключения'):
            settings($chat_id, $config);
            sendMessage($chat_id, $times);
            break;

        case ('Посмотреть свой id'):
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => $chat_id,
                'reply_markup' => json_encode(['remove_keyboard' => true])
            ]);
            break;

        case ('Отправить SQL команду'):
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => 'Введите команду',
                'reply_markup' => json_encode(['remove_keyboard' => true])
            ]);
            updatesDBUsers($chat_id, 'command', 'SEND_SQL');
            break;
        default:

    sendMessage($chat_id, 'Такой команды я не знаю. Воспользуйтесь командой /help');
    break;

    }
}else{
    switch ($message) {
        case ('/start'):
            startButton($chat_id, $AdminDbListOnline, $AdminDbGlobList, $select['access']);
            break;

        default:
            sendMessage($chat_id, 'Бот остановлен. Используйте команду /start');
            break;
    }
}


function bot($method,$datas=[]){
    $url = $GLOBALS['api']."/".$method;
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL             => $url,
        CURLOPT_POST                => TRUE,
        CURLOPT_RETURNTRANSFER  => TRUE,
        CURLOPT_FOLLOWLOCATION  => FALSE,
        CURLOPT_HEADER          => FALSE,
        CURLOPT_TIMEOUT         => 10,
        CURLOPT_HTTPHEADER      => array('Accept-Language: ru,en-us'),
        CURLOPT_POSTFIELDS      => $datas
 
    ));
    curl_exec($ch);
}

function startButton($chat_id, $AdminDbListOnline, $AdminDbGlobList, $access){
	if(count($AdminDbGlobList)==0){ //Сработает только 1 раз при начальном старте бота
		$AdminDbListOnline[] = $chat_id;   // Представить новую переменную как элемент массива, в формате 'ключ'=>'имя переменной'
		file_put_contents('./user.json',json_encode($AdminDbListOnline));  // Перекодировать в формат и записать в файл.

        $AdminDbGlobList[] = $chat_id;   // Представить новую переменную как элемент массива, в формате 'ключ'=>'имя переменной'
        file_put_contents('./globuser.json',json_encode($AdminDbGlobList));  // Перекодировать в формат и записать в файл.
		
		$access = 'Admin';

        /* создаем подготавливаемый запрос */
		insertNewUser($chat_id, $access);

        $keyboard = keyboards($access);

        bot('sendMessage',[
            'chat_id'=> $chat_id,
            'text'=> "Добро пожаловать.\nЭто бот для администрирования бд oracle, пожалуйста настройте подключение нажав кнопу 'Настройки подключения'.\nБолее подробная информация в 'Справка'.",
            'reply_markup' => json_encode($keyboard)
        ]);

	}
	elseif (in_array($chat_id, $AdminDbListOnline)){
        sendMessage($chat_id,"Вы уже вошли");
    }else{
	    if(in_array($chat_id, $AdminDbGlobList)){
            $AdminDbListOnline[] = $chat_id;
            file_put_contents('./user.json',json_encode($AdminDbListOnline));
            $keyboard = keyboards($access); //Предоставить клавиатуру
            bot('sendMessage',[
                'chat_id'=> $chat_id,
                'text'=> 'С возвращением.',
                'reply_markup' => json_encode($keyboard)
            ]);
        }else{
            $AdminDbListOnline[] = $chat_id;
            file_put_contents('./user.json', json_encode($AdminDbListOnline));
            $AdminDbGlobList[] = $chat_id;   // Представить новую переменную как элемент массива, в формате 'ключ'=>'имя переменной'
            file_put_contents('./globuser.json',json_encode($AdminDbGlobList));  // Перекодировать в формат и записать в файл.
            $access = 'New';
            insertNewUser($chat_id, $access);

            $keyboard = keyboards($access);

            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => 'Приветствую, Вы можете только посмотреть свой id, отправьте этот id администратору, чтобы он дал вам права для доступа к функциям бота.',
                'reply_markup' => json_encode($keyboard)
            ]);
        }
    }

}

function stopButton($chat_id, $AdminDbListOnline){
    $message = 'Бот уже остановлен!';
    //удалить из масссива элемент
    $key = array_search($chat_id, $AdminDbListOnline);
    if ($key !== false)
    {
        unset($AdminDbListOnline[$key]);
        file_put_contents('./user.json',json_encode($AdminDbListOnline));  // Перекодировать в формат и записать в файл.
        $message = 'Вы остановили бота. Вы не будете получать уведомления и не сможете использовать команды.';
    }

    bot('sendMessage',[
        'chat_id'=> $chat_id,
        'text'=> $message,
        'reply_markup' => json_encode(['remove_keyboard' => true])
    ]);
}



function settings($chat_id, $config){
    $inline_button1 = array("text"=>"SID","callback_data"=>'/sid');
    $inline_button2 = array("text"=>"HOST","callback_data"=>'/host');
    $inline_button3 = array("text"=>"PORT","callback_data"=>'/port');
    $inline_button4 = array("text"=>"Х Закрыть","callback_data"=>'/close');
    $inline_keyboard = [[$inline_button1,$inline_button2, $inline_button3],[$inline_button4]];
    $keyboard = array("inline_keyboard"=>$inline_keyboard);
    bot('sendMessage',[
        'chat_id'=> $chat_id,
        'text'=> "Настройки:\nORACLE_SID=".$config['ORACLE_SID'].
            "\nHOST=".$config['HOST'].
            "\nPORT=".$config['PORT'].
            "\n\nВыберете что хотите изменить:",
        'reply_markup' => json_encode($keyboard)
    ]);
}

function selectData($chat_id){
    $link = mysqli_connect($GLOBALS['connectstr_dbhost'], $GLOBALS['connectstr_dbusername'],
        $GLOBALS['connectstr_dbpassword'], $GLOBALS['connectstr_dbname']);
   
    if ($stmt = mysqli_prepare($link, "SELECT * FROM Users WHERE chat_id=?")) {
        /* связываем параметры с метками */
        mysqli_stmt_bind_param($stmt, "s", $chat_id);

        /* запускаем запрос */
        mysqli_stmt_execute($stmt);

        /* получаем результат из подготовленного запроса */
        $result = mysqli_stmt_get_result($stmt);

        /*Результаты запроса помещаем в ассоциативный массив*/
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

        /* закрываем запрос */
        mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
    return $row;
}


function updatesDBUsers($chat_id, $column, $value){
    $link = mysqli_connect($GLOBALS['connectstr_dbhost'], $GLOBALS['connectstr_dbusername'],
        $GLOBALS['connectstr_dbpassword'], $GLOBALS['connectstr_dbname']);
    if($stmt = mysqli_prepare($link, "UPDATE Users SET {$column}=? WHERE chat_id = ?")){

        mysqli_stmt_bind_param($stmt, "si",  $value,$chat_id);

        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
}

function updateConfig($column, $value){
  $link = mysqli_connect($GLOBALS['connectstr_dbhost'], $GLOBALS['connectstr_dbusername'],
        $GLOBALS['connectstr_dbpassword'], $GLOBALS['connectstr_dbname']);
    if($stmt = mysqli_prepare($link, "UPDATE Configs SET {$column}=?")){

        mysqli_stmt_bind_param($stmt, "s",  $value);

        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
}

}

function insertNewUser($chat_id, $access){
    $link = mysqli_connect($GLOBALS['connectstr_dbhost'], $GLOBALS['connectstr_dbusername'],
        $GLOBALS['connectstr_dbpassword'], $GLOBALS['connectstr_dbname']);
    if ($stmt = mysqli_prepare($link, 'INSERT INTO Users (chat_id, access) VALUES (?, ?)')) {
        //sendMessage($chat_id, 'err1-'.mysqli_error($link));
        /* связываем параметры с метками */
        mysqli_stmt_bind_param($stmt, "is", $chat_id, $access);
        //sendMessage($chat_id, 'err2-'.mysqli_error($link));
        /* запускаем запрос */
        mysqli_stmt_execute($stmt);
        //sendMessage($chat_id, 'err3-'.mysqli_error($link));
        /* закрываем запрос */
        mysqli_stmt_close($stmt);
    }

    mysqli_close($link);    /* закрываем соединение */
}

/**11
* Функция отправки сообщения в чат sendMessage().
*/
function keyboards($access){
    switch($access) {
        case ('Admin'):
            $keyboard = [
                'keyboard' => [
                    ['Войти'], // Первый ряд кнопок
                    ['Отправить SQL команду'], // Второй ряд кнопок
                    ['Настройки подключения'], // Третий ряд кнопок
                    ['Справка'] // Четвертый ряд кнопок
                ],
                'resize_keyboard' => true
            ];
            break;
        case ('New'):
            $keyboard = [
                'keyboard' => [
                    ['Посмотреть свой id'] //
                ],
                'resize_keyboard' => true
            ];
            break;
        case ('JuniorAdmin'):
            $keyboard = [
                'keyboard' => [
                    ['Войти'], // Первый ряд кнопок
                    ['Отправить SQL команду'], // Второй ряд кнопок
                    ['Справка'] // Третий ряд кнопок
                ],
                'resize_keyboard' => true
            ];
            break;
    }
    return $keyboard;
}

function sendMessage($chat_id, $message) {

file_get_contents($GLOBALS['api'] . '/sendMessage?chat_id=' . $chat_id . '&text=' . urlencode($message));

}
