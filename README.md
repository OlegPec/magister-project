<p>Диплом магистра.</p> 

<p>Тема "Разработка и исследование прототипа системы администрирования базы данных при помощи Telegram bot".</p>
<p>Актуальность: Необходимость удаленного администрирования базы данных, отслеживания ошибок и их быстрого устранения администратором баз данных с минимизацией временных издержек. Для выполнения удаленного администрирования базы данных из любого места администратор должен иметь возможность использования легковесного решения на базе мобильного устройства.</p>

Код реализации находится в файле OracleMonitoringBot.php

<p>Для хранения данных о подключение к БД Oracle была использована база данных MySQL.</p>
<p>Что хранится в таблице с настройками:</p>
<ul>
<li>ORACLE_SID – SID базы данных.</li>
<li>HOST – Хост сервера где установлен Oracle.</li>
<li>PORT – Порт базы.</li>
</ul>

<p>Файлы с пользователями онлайн и с общим списком, хранят массив id в формате JSON. 
Первый файл с онлайн списком(user.json) хранит идентификаторы пользователей, у которых запущен бот. 
Второй файл(globuser.json) хранит общий список идентификаторов пользователей, которые запускали этого бота, для того, чтобы не искать его в базе, и при старте бота определять новый это пользователь или нет.
Также в MySQL создана таблица с пользователями, которая хранит идентификаторы пользователей, их права для доступа к командам, которых может быть всего 3: Администратор, младший администратор и новый пользователь. 
Также в таблицу записывается для определенного пользователя используемая им в данный момент команда, на которую требуется отследить ответ от пользователя. 
Для доступа к БД Oracle хранится логин и пароль, чтобы каждый пользователь для выполнения SQL запроса подключался под своей учетной записью Oracle.</p>
