<?php namespace pocketmine\command\defaults;
use pocketmine\network\protocol\TransferPacket;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
class TransferServerCommand extends VanillaCommand {
	public function __construct($name){
		parent::__construct("transferserver", "Отправка игрока на другой сервер");
		$this->setPermission("pocketmine.command.transfer");
	}
	public function execute(CommandSender $sender, $currentAlias, array $args){
		$address = null;
		$port = null;
		$player = null;
		if($sender instanceof Player){
			if(!$this->testPermission($sender)){
				return true;
			}
			if(count($args) <= 0){
				$sender->sendMessage("Использование: /transferserver <адрес> [порт]");
				return false;
			}
			$address = strtolower($args[0]);
			$port = (isset($args[1]) && is_numeric($args[1]) ? $args[1] : 19132);
			$pk = new TransferPacket();
			$pk->address = $address;
			$pk->port = $port;
			$sender->dataPacket($pk);
			return false;
		}
		if(count($args) <= 1){
			$sender->sendMessage("Использование: /transferserver <ник> <адрес> [порт]");
			return false;
		}
		if(!($player = Server::getInstance()->getPlayer($args[0])) instanceof Player){
			$sender->sendMessage("Указанный игрок не найден!");
			return false;
		}
		$address = strtolower($args[1]);
		$port = (isset($args[2]) && is_numeric($args[2]) ? $args[2] : 19132);
		$sender->sendMessage("Отправка " . $player->getName() . " на " . $address . ":" . $port);
		$pk = new TransferPacket();
		$pk->address = $address;
		$pk->port = $port;
		$player->dataPacket($pk);
	}
}
?>
