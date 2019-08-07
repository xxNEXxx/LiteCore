<?php namespace pocketmine\command;
interface CommandExecutor {
	public function onCommand(CommandSender $sender, Command $command, $label, array $args);
}
?>