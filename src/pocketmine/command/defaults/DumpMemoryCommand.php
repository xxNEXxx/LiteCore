<?php namespace pocketmine\command\defaults;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
class DumpMemoryCommand extends VanillaCommand {
	public function __construct($name){
		parent::__construct(
			$name,
			"Сбросить память сервера",
			"/$name [path]"
		);
		$this->setPermission("pocketmine.command.dumpmemory");
	}
	public function execute(CommandSender $sender, $currentAlias, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		Command::broadcastCommandMessage($sender, "Сброс памяти сервера");
		$sender->getServer()->getMemoryManager()->dumpServerMemory(isset($args[0]) ? $args[0] : $sender->getServer()->getDataPath() . "/memory_dumps/memoryDump_" . date("D_M_j-H.i.s-T_Y", time()), 48, 80);
		return true;
	}
}
?>