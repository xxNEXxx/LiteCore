<?php namespace pocketmine\command\defaults;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\TranslationContainer;
use pocketmine\Player;
class BanCommand extends VanillaCommand {
	public function __construct($name){
		parent::__construct("ban", "Бан игрока по нику");
		$this->setPermission("pocketmine.command.ban.player");
	}
	public function execute(CommandSender $sender, $label, array $args) {
        if($sender->hasPermission("pocketmine.command.ban.player")) {
		    if(count($args) > 0) {
                $player = $sender->getServer()->getPlayer($args[0]);
			    if($player != null) {
		            unset($args[0]);
 		            $reason1 = implode(" ", $args);
                    if(strlen($reason1) >= 1){
                        $sender->getServer()->getNameBans()->addBan($player->getName(), $reason1, null, $sender->getName());
                        $player->close("","§eТебя забанил игрок §6".$sender->getName()."\n§eПричина: §e".$reason1."");
                        $sender->getServer()->broadcastMessage(" §eИгрок §6".$player->getName()." §eбыл забанен админом §a".$sender->getName()."\n§9Причина: §b".$reason1);
                    } else return $sender->sendMessage("§7(§6Система§7) §cУкажите причину!");
			    } else return $sender->sendMessage("§7(§6Система§7) §cИгрок не онлайн или вы ввели не верный ник.");
	        } else return $sender->sendMessage("§7(§6Система§7) §eИспользуйте: §9/ban <ник> <причина>");
		}
	}
}
?>