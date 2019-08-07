<?php namespace pocketmine\command\defaults;
use pocketmine\command\CommandSender;
use pocketmine\event\TranslationContainer;
use pocketmine\Player;
class TitleCommand extends VanillaCommand {
	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.title.description",
			"%pocketmine.command.title.usage"
		);
		$this->setPermission("pocketmine.command.title");
	}
	public function execute(CommandSender $sender, $currentAlias, array $args){
		if($sender instanceof Player){
			if(!$this->testPermission($sender)){
				return true;
			}
			if(count($args) <= 0){
				$sender->sendMessage(new TranslationContainer("%pocketmine.command.title.usage"));
				return false;
			}
		}
	}
}
?>