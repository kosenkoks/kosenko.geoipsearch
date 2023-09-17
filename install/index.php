<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-18);
@include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));
IncludeModuleLangFile($strPath2Lang."/install/index.php");

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Application;
use Bitrix\Main\SystemException;
use Sprint\Migration\Installer;

Loc::loadMessages(__FILE__);

class kosenko_geoipsearch extends CModule {
 
	public $MODULE_ID = 'kosenko.geoipsearch';
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	/**
	* Инициализация модуля для страницы "Управление модулями"
	*/
	function __construct() {
		include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/{$this->MODULE_ID}/install/version.php");
		$this->MODULE_NAME       	= GetMessage( 'GEOIPSEARCH_EMPTY_MODULNAME' );
		$this->MODULE_DESCRIPTION	= GetMessage( 'GEOIPSEARCH_EMPTY_DESC' );
		$this->MODULE_VERSION		= $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE	= $arModuleVersion['VERSION_DATE'];
		$this->PARTNER_NAME			= GetMessage("GEOIPSEARCH_OPTIONS_PARTNER_NAME");
		$this->PARTNER_URI			= GetMessage("GEOIPSEARCH_OPTIONS_PARTNER_URI");
	}
	
	
	
	/**
	* Устанавливаем модуль
	*/
	public function doInstall() {
	//  if( !$this->InstallDB() || !$this->InstallEvents() || !$this->InstallFiles() ) {
	//      return;
	//  }
	
		// if(!$this->InstallDB()) {
		//       return;
		// }
		// CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/{$this->MODULE_ID}/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
		global $APPLICATION;

		if (!CheckVersion(ModuleManager::getVersion("main"), "14.00.00")) {
			$APPLICATION->ThrowException(
				"Версия ядра меньше 14, установка невозможна"
			);
		}

		$this->installFiles();
		$this->installMigrations();
		


		ModuleManager::registerModule( $this->MODULE_ID );
	}
	
	/**
	* Удаляем модуль
	*/
	public function DoUninstall() {
		// удаление файлов не предусмотрено
		// откатываем миграции и снимаем модуль с регистрации

		$this->uninstallMigrations();

		ModuleManager::unRegisterModule( $this->MODULE_ID );
	}
	
	public function installFiles()
	{
		CopyDirFiles(__DIR__ . "/components", Application::getDocumentRoot() . "/local/components", true, true);
		CopyDirFiles(__DIR__ . "/files", Application::getDocumentRoot(), true, true);
	}

	public function installMigrations()
	{
		if (CModule::IncludeModule('sprint.migration')) {
            try {
                (new Installer(
                    [
                        'migration_dir' => __DIR__ . '/migrations/',
                        'migration_dir_absolute' => true,
                    ]
                ))->up();
            } catch (Exception $e) {
                throw new SystemException($e->getMessage());
            }
        } else {
			throw new SystemException("Не установлен модуль sprint.migration");
		}
	}

	public function uninstallMigrations()
	{
		if (CModule::IncludeModule('sprint.migration')) {
            try {
                (new Installer(
                    [
                        'migration_dir' => __DIR__ . '/migrations/',
                        'migration_dir_absolute' => true,
                    ]
                ))->down();
            } catch (Exception $e) {
                throw new SystemException($e->getMessage());
            }
        } else {
			throw new SystemException("Не установлен модуль sprint.migration");
		}
	}
}