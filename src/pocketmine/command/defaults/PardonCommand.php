<?php namespace pocketmine\command\defaults;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\TranslationContainer;
class PardonCommand extends VanillaCommand {
	public function __construct($name){
		parent::__construct("pardon", "Разбанить игрока по нику");
		$this->setPermission("pocketmine.command.unban.player");
	}
	public function execute(CommandSender $sender, $alias, array $args) {
        if($sender->hasPermission("pocketmine.command.unban.player")) {
	        if(count($args) > 0) {
		        $player = $args[0];
		        unset($args[0]);
		        $reason4 = implode(" ", $args);
			    if(strlen($reason4) >= 1) {
				    $sender->getServer()->getNameBans()->remove($player);
				    $sender->getServer()->broadcastMessage(" §eИгрок §6".$player." §eбыл разбанен §a".$sender->getName()."\n§9Причина разбана: §b".$reason4);
				} else return $sender->sendMessage("§7(§6Система§7) §cУкажите причину разбана!");
			} else return $sender->sendMessage("§7(§6Система§7) §eИспользуйте: §9/pardon <ник> <причина разбана>");
		}
	}
}
?>