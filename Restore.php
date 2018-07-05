<?php
namespace FreePBX\modules\__MODULENAME__;
use FreePBX\modules\Backup as Base;
class Restore Extends Base\RestoreBase{
  public function runRestore($jobid){
    $configs = $this->getConfig();
    $this->FreePBX->Hotelwakeup->saveSetting($configs['config']);
    foreach ($configs['calls'] as $call) {
        $this->FreePBX->Hotelwakeup->addWakeup($call['destination'], $call['time'], $call['lang']);
    }
  }
}