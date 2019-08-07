<?php namespace pocketmine\command\defaults;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\TranslationContainer;
class BanCidCommand extends VanillaCommand {
	public function __construct($name){
		parent::__construct("bancid", "Бан игрока по CID");
		$this->setPermission("pocketmine.command.bancid");
	}
	public function execute(CommandSender $sender, $label, array $args) {
        if($sender->hasPermission("pocketmine.command.bancid")) {
		    if(count($args) > 0) {
                $player = $sender->getServer()->getPlayer($args[0]);
			    unset($args[0]);
			    if($player !== null) {
 			        $reason3 = implode(" ", $args);
                    if(strlen($reason3) >= 1){
                        $sender->getServer()->getCIDBans()->addBan($player->getClientId(), $reason3, null, $sender->getName());
                        $player->close("","§eТебя забанил по CID админ: §6".$sender->getName()."\n§aПричина: §e".$reason3."");
                        $sender->getServer()->broadcastMessage(" §eИгрок §6".$player->getName()." §eбыл забанен по CID админом §a".$sender->getName()."\n§9Причина: §b".$reason3);
                    } else return $sender->sendMessage("§7(§6Система§7) §cУкажите причину!");
                } else return $sender->sendMessage("§7(§6Система§7) §cИгрок не онлайн или вы ввели не верный ник.");
		    } else return $sender->sendMessage("§7(§6Система§7) §eИспользуйте: §9/bancid <ник> <причина>");
		}
	}
}
?>