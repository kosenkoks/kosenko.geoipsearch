# Модуль kosenko.geoipsearch

## Системные требования

- Битрикс редакции Старт с версией ядра не менее 19 (тестировалось на 23.300.100)
- Установленный модуль [sprint.migrations](https://marketplace.1c-bitrix.ru/solutions/sprint.migration/) для установки миграций HL блока и почтового шаблона
- Тестировалось на PHP 8.1

## Установка

- Склонировать репозиторий в папку local/modules
- Установить через админку Bitrix /bitrix/admin/partner_modules.php
- Для тестирования работы перейти по url: <ВашДомен>/geoip/


## Тестовое задание

### Задача
 Сделать форму GeoIP поиска. Поиск будет осуществляться с помощью запроса в публичный веб-сервис (sypexgeo.net, geoip.top, ipstack.com на выбор).

### Сценарий
Пользователь вводит валидный IP, отправляется запрос в HL блок, если в HL блоке присутствует запись с данным IP, то данные отображаются из базы, если в базе нет нужного ip, то запрос отправляется на один из сервисов, пользователю показываются данные из сервиса и записываются в базу.

### Требования
 -	Оформить в виде компонента Битрикс используя D7;
 -	Использовать стандартный http или soap клиент битрикс;
 -	Использовать базу данных в качестве хранилища данных;
 -	Валидация должна присутствовать как минимум на серверной стороне;
 -	Обработка ошибок и исключений;
 -	Оформить страницу презентабельно (можно использовать инструменты типа Bootstrap);
 -	Производить комментирование кода.

#### Дополнительно
 •	Выполнить задание, используя ajax-запросы;
 •	В случае возникновения исключений отправлять на почту сообщение с данными об ошибке используя инструменты Битрикс.