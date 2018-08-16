<?php
namespace FreePBX\modules\Hotelwakeup;
use FreePBX\modules\Backup as Base;
class Restore Extends Base\RestoreBase{
  public function runRestore($jobid){
    $configs = reset($this->getConfigs());
    $this->processConfig($configs);
  }
  public function processLegacy($pdo, $data, $tables, $unknownTables, $tmpfiledir){
    $tables = array_flip($tables + $unknownTables);
    if (!isset($tables['hotelwakeup'])) {
      return $this;
    }
    $cb = $this->FreePBX->Hotelwakeup;
    $cb->setDatabase($pdo);
    $configs = [
      'config' => $cb->getSetting(),
      'calls' => $cb->getAllCalls(),
    ];
    $cb->resetDatabase();
    $this->processConfig($configs);
    return $this;
  }
  public function processConfigs($configs){
    $this->FreePBX->Hotelwakeup->saveSetting($configs['config']);
    foreach ($configs['calls'] as $call) {
      $this->FreePBX->Hotelwakeup->addWakeup($call['destination'], $call['time'], $call['lang']);
    }
  }
}
