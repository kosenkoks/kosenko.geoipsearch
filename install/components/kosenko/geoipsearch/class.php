<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Highloadblock\HighloadBlockTable as HLtable;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Error;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Loader;
use Bitrix\Main\Mail\Event;
use Bitrix\Main\Web\HttpClient;

class GeoIPSearch extends CBitrixComponent implements Controllerable, Errorable
{
    const HLBLOCK_NAME = 'Geoipsearch';
    const API_URL = 'https://api.sypexgeo.net/json/';
    protected array $errorList;
    protected $errorCollection;

    public function onPrepareComponentParams($arParams)
	{
		$this->errorCollection = new ErrorCollection();
	}

    public function executeComponent()
    {
        $this->IncludeComponentTemplate();
    }

    public function configureActions()
    {
        return [];
    }

    /**
     *
     * AJAX событие search, реализует получение геоинформации по IP адресу
     *
     * @param $request
     * @return mixed|void
     */
    public function searchAction($ip)
    {

        // проверка подключения модуля
        if(!Loader::includeModule("highloadblock")) {
            $this->logErrors($ip, 'Не подключен модуль highloadblock');
            return;    
        }

        // валидация ip адреса
        $requestIp = $ip;
        $ip = filter_var($requestIp, FILTER_VALIDATE_IP);
        if (empty($ip)) {
            $this->logErrors($ip, 'Не валидный IP адрес');
            return;
        }

        // поиск нужнога HL блока
        $rsgeoipHlblock = HLtable::getList(array('filter' => array('NAME' => self::HLBLOCK_NAME)));
        if (!($geoipHlblock = $rsgeoipHlblock->fetch())) {
            $this->logErrors($ip, 'HL блок '.self::HLBLOCK_NAME.' не найден');
            return;
        }

        // получение информации из HL блока
        $geoipHlclass = HLtable::compileEntity($geoipHlblock)->getDataClass();
        $rsIpData = $geoipHlclass::getList([
            "select" => ['UF_JSON'],
            "filter" => ["UF_IP_ADRESS" => $ip]
        ]);

        // если информация найдена, то возращаем её
        if($arIpData = $rsIpData->fetch()){
            return json_decode($arIpData['UF_JSON']);
        }
        // если по ip не найдено записи, то делаем запрос по API и записываем результат в HL блок
        else {
            $client = new HttpClient();
            $response = $client->get(self::API_URL . $ip);
            if(!$response) {
                $this->logErrors($ip, 'Не получен ответ от API');
                return;
            }

            $result = json_decode($response);
            if( $result == null) {
                $this->logErrors($ip, 'Ошибка при обработке результата');
                return;   
            }
            
            $geoipHlclass::add([
                "UF_IP_ADRESS" => $ip,
                "UF_JSON" => $response,
            ]);
            return $result;
        }
    }

    protected function logErrors($ip, $text){
        Event::send(array(
            "EVENT_NAME" => "GEOIP_ERROR",
            "LID" => "s1",
            "C_FIELDS" => array(
                "IP" => $ip,
                "ERROR_TEXT" => $text
            ),
        )); 

        $this->errorCollection->add([new Error($text)]);
    }
    public function getErrors()
	{
		return $this->errorCollection->toArray();
	}

    public function getErrorByCode($code)
	{
		return $this->errorCollection->getErrorByCode($code);
	}
}
