<?php
namespace FreePBX\modules\Hotelwakeup;
use FreePBX\modules\Backup as Base;
class Backup Extends Base\BackupBase{
  public function runBackup($id,$transaction){
    $configs = [
        'config' => $this->FreePBX->Hotelwakeup->getSetting(),
        'calls' => $this->FreePBX->Hotelwakeup->getAllCalls(),
    ];
    $this->addDependency('ivr');
    $this->addConfigs($configs);
  }
}
