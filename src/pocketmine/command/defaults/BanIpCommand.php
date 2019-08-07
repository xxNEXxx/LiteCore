<?php namespace pocketmine\command\defaults;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\TranslationContainer;
use pocketmine\Player;
class BanIpCommand extends VanillaCommand {
	public function __construct($name){
		parent::__construct("ban-ip", "Бан игрока по IP адресу", "§7(§6Система§7) §eИспользуйте:§9/ban-ip <ник> <причина>");
		$this->setPermission("pocketmine.command.ban.ip");
	}
	public function execute(CommandSender $sender, $label, array $args) {
        if($sender->hasPermission("pocketmine.command.ban.ip")) {
		    if(count($args) > 0) {
                $player = $sender->getServer()->getPlayer($args[0]);
			    unset($args[0]);
			    $reason2 = implode(" ", $args);
			    if($player !== null) {
                    if(strlen($reason2) >= 1){
                        $sender->getServer()->getIPBans()->addBan($player->getAddress(), $reason2, null, $sender->getName());
                        $player->close("","§eТебя забанил по IP §6".$sender->getName()."\n§aПричина: §e".$reason2."");
                        $sender->getServer()->broadcastMessage("§8(§6Система§8) §eИгрок §6".$player->getName()." §eбыл забанен по IP админом §a".$sender->getName()."\n§9Причина: §b".$reason2);
                    } else return $sender->sendMessage("§7(§6Система§7) §cУкажите причину!");
                }else return $sender->sendMessage("§7(§6Система§7) §cИгрок не онлайн или вы ввели не верный ник.");
			} else return $sender->sendMessage("§7(§6Система§7) §eИспользуйте: §9/ban-ip <ник> <причина>");
		}
  	}
}
?>