<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable as HLtable;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Errorable;
use Bitrix\Main\Mail\Event;

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

    public function searchAction($request)
    {
        if(!Loader::includeModule("highloadblock")) {
            $this->logErrors($ip, 'Не подключен модуль highloadblock');
            return;    
        }
        
        $ip = filter_var($request, FILTER_VALIDATE_IP);
        if (empty($ip)) {
            $this->logErrors($request, 'Не валидный IP адрес');
            return;
        }

        $rsgeoipHlblock = HLtable::getList(array('filter' => array('NAME' => self::HLBLOCK_NAME)));
        if (!($geoipHlblock = $rsgeoipHlblock->fetch())) {
            $this->logErrors($ip, 'HL блок '.self::HLBLOCK_NAME.' не найден');
            return;
        }

        $geoipHlclass = HLtable::compileEntity($geoipHlblock)->getDataClass();
        $rsIpData = $geoipHlclass::getList([
            "select" => ['UF_JSON'],
            "filter" => ["UF_IP_ADRESS" => $ip]
        ]);
        if($arIpData = $rsIpData->fetch()){
            return json_decode($arIpData['UF_JSON']);
        } else {
            $client = new HttpClient();
            $response = $client->get(self::API_URL . $ip);
            if($response == false) {
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