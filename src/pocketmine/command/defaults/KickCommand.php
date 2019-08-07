<?php namespace pocketmine\command\defaults;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\TranslationContainer;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
class KickCommand extends VanillaCommand {
	public function __construct($name){
		parent::__construct("kick", "Выгнать игрока с сервера");
		$this->setPermission("pocketmine.command.kick");
	}
	public function execute(CommandSender $sender, $alias, array $args) {
        if($sender->hasPermission("pocketmine.command.kick")) {
            if(count($args) > 0) {
                $player = $sender->getServer()->getPlayer($args[0]);
                if($player != null) {
			        unset($args[0]);
		            $reason = implode(" ", $args);
                    if(strlen($reason) >= 1) {
                        $player->close("", "§eТебя кикнул с сервера §6".$sender->getName()."\n§eПричина: §6".$reason."");
                        $sender->getServer()->broadcastMessage(" §eИгрок §6".$player->getName()." §eбыл кикнут игроком §a".$sender->getName()."\n§9Причина: §b".$reason);
                    } else return $sender->sendMessage(" §cУкажите причину!");
	            } else return $sender->sendMessage(" §cИгрок не онлайн или вы ввели не верный ник.");
		    } else return $sender->sendMessage(" §eИспользуйте: §9/kick <ник> <причина>");
		}
	}
}
?>