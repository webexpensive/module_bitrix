<?
use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use \Bitrix\Main\ModuleManager;

Class Record_module extends CModule
{

    public $MODULE_ID = 'record.module';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;
    public $PARTNER_URI;

    protected $moduleInstallPath;
    protected $bitrixAdminPath;

    public function __construct()
    {
        $arModuleVersion = array();

        include(__DIR__ . '/version.php');

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->PARTNER_NAME = Loc::getMessage('ACU_RECORD_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = 'https://bitrix.webexpensive.ru/';

        $this->MODULE_NAME = Loc::getMessage('ACU_RECORD_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('ACU_RECORD_MODULE_DESCRIPTION');

        $this->moduleInstallPath = $_SERVER['DOCUMENT_ROOT'] . '/local/modules/' . $this->MODULE_ID . '/install';
        $this->bitrixPath = $_SERVER['DOCUMENT_ROOT'] . '/bitrix';
    }

    protected function GetInstallFiles()
    {
        return array(
            '/admin' => '/admin'
        );
    }

    public function InstallFiles()
    {

        foreach ($this->GetInstallFiles() as $source => $destination)
        {
            CopyDirFiles($this->moduleInstallPath . $source, $this->bitrixPath . $destination, true, true);
        }

        return true;
    }

    public function UnInstallFiles()
    {
        foreach ($this->GetInstallFiles() as $source => $destination)
        {

            if (is_dir($this->moduleInstallPath . $source))
            {
                $d = dir($this->moduleInstallPath . $source);

                while ($entry = $d->read())
                {
                    if ($entry == '.' || $entry == '..')
                    {
                        continue;
                    }

                    if (is_dir($this->bitrixPath . $destination . '/' . $entry))
                    {
                        DeleteDirFilesEx('bitrix' . $destination . '/' . $entry);
                    }
                    elseif (is_file($this->bitrixPath . $destination . '/' . $entry))
                    {
                        @unlink($this->bitrixPath . $destination . '/' . $entry);
                    }
                }
                $d->close();
            }

        }

        return true;
    }

    public function DoInstall()
    {
        $this->InstallFiles();
        RegisterModule($this->MODULE_ID);
        return true;
    }

    public function DoUninstall()
    {
        $this->UnInstallFiles();
        UnRegisterModule($this->MODULE_ID);
        return true;
    }

}