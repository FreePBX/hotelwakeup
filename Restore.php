<?php
namespace FreePBX\modules\Hotelwakeup;
use FreePBX\modules\Backup as Base;
class Restore Extends Base\RestoreBase{
	public function runRestore($jobid){
		$configs = $this->getConfigs();
		$this->processConfigs($configs);
	}
	public function processLegacy($pdo, $data, $tables, $unknownTables){
		$this->restoreLegacyDatabase($pdo);
		$this->restoreLegacyFeatureCodes($pdo);
	}
	public function processConfigs($configs){
		$this->importFeatureCodes($configs['features']);
		$this->FreePBX->Hotelwakeup->saveSetting($configs['config']);
		foreach ($configs['calls'] as $call) {
			out(sprintf(_("Restoring destination : %s"),$call['destination']));
			$this->FreePBX->Hotelwakeup->addWakeup($call['destination'], $call['timestamp'], $call['lang']);
		}
	}
}
