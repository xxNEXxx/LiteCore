<?php namespace pocketmine\command;
use pocketmine\event\TextContainer;
use pocketmine\event\TimingsHandler;
use pocketmine\event\TranslationContainer;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
abstract class Command {
	private static $defaultDataTemplate = null;
	private $name;
	protected $commandData = null;
	private $nextLabel;
	private $label;
	private $aliases = [];
	private $activeAliases = [];
	private $commandMap = null;
	protected $description = "";
	protected $usageMessage;
	private $permissionMessage = null;
	public $timings;
	public function __construct($name, $description = "", $usageMessage = null, array $aliases = []){
		$this->commandData = self::generateDefaultData();
		$this->name = $this->nextLabel = $this->label = $name;
		$this->setDescription($description);
		$this->usageMessage = $usageMessage === null ? "/" . $name : $usageMessage;
		$this->setAliases($aliases);
		$this->timings = new TimingsHandler("** Команда: " . $name);
	}

	public function getDefaultCommandData() : \stdClass{
		return $this->commandData;
	}

	public function generateCustomCommandData(Player $player){
		$customData = clone $this->commandData;
		$customData->aliases = $this->getAliases();
		return $customData;
	}

	public function getOverloads() : \stdClass{
		return $this->commandData->overloads;
	}

	public abstract function execute(CommandSender $sender, $commandLabel, array $args);

	public function getName() : string{
		return $this->name;
	}

	public function getPermission(){
		return $this->commandData->pocketminePermission ?? null;
	}

	public function setPermission($permission){
		if($permission !== null){
			$this->commandData->pocketminePermission = $permission;
		}else{
			unset($this->commandData->pocketminePermission);
		}
	}

	public function testPermission(CommandSender $target){
		if($this->testPermissionSilent($target)){
			return true;
		}

		if($this->permissionMessage === null){
			$target->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.permission"));
		}elseif($this->permissionMessage !== ""){
			$target->sendMessage(str_replace("<разрешение(права)>", $this->getPermission(), $this->permissionMessage));
		}

		return false;
	}

	public function testPermissionSilent(CommandSender $target){
		if(($perm = $this->getPermission()) === null or $perm === ""){
			return true;
		}

		foreach(explode(";", $perm) as $permission){
			if($target->hasPermission($permission)){
				return true;
			}
		}

		return false;
	}

	public function getLabel(){
		return $this->label;
	}

	public function setLabel($name){
		$this->nextLabel = $name;
		if(!$this->isRegistered()){
			$this->timings = new TimingsHandler("** Команда: " . $name);
			$this->label = $name;

			return true;
		}

		return false;
	}

	public function register(CommandMap $commandMap){
		if($this->allowChangesFrom($commandMap)){
			$this->commandMap = $commandMap;

			return true;
		}

		return false;
	}

	public function unregister(CommandMap $commandMap){
		if($this->allowChangesFrom($commandMap)){
			$this->commandMap = null;
			$this->activeAliases = $this->commandData->aliases;
			$this->label = $this->nextLabel;

			return true;
		}

		return false;
	}

	private function allowChangesFrom(CommandMap $commandMap){
		return $this->commandMap === null or $this->commandMap === $commandMap;
	}

	public function isRegistered(){
		return $this->commandMap !== null;
	}

	public function getAliases(){
		return $this->activeAliases;
	}

	public function getPermissionMessage(){
		return $this->permissionMessage;
	}

	public function getDescription(){
		return $this->commandData->description;
	}

	public function getUsage(){
		return $this->usageMessage;
	}

	public function setAliases(array $aliases){
		$this->commandData->aliases = $aliases;
		if(!$this->isRegistered()){
			$this->activeAliases = (array) $aliases;
		}
	}

	public function setDescription($description){
		$this->commandData->description = $description;
	}

	public function setPermissionMessage($permissionMessage){
		$this->permissionMessage = $permissionMessage;
	}

	public function setUsage($usage){
		$this->usageMessage = $usage;
	}

	public static final function generateDefaultData() : \stdClass{
		if(self::$defaultDataTemplate === null){
			self::$defaultDataTemplate = json_decode(file_get_contents(Server::getInstance()->getFilePath() . "src/pocketmine/resources/command_default.json"));
		}
		return clone self::$defaultDataTemplate;
	}
	public static function broadcastCommandMessage(CommandSender $source, $message, $sendToSource = true){
		if($message instanceof TextContainer){
			$m = clone $message;
			$result = "[" . $source->getName() . ": " . ($source->getServer()->getLanguage()->get($m->getText()) !== $m->getText() ? "%" : "") . $m->getText() . "]";
			$users = $source->getServer()->getPluginManager()->getPermissionSubscriptions(Server::BROADCAST_CHANNEL_ADMINISTRATIVE);
			$colored = TextFormat::GRAY . TextFormat::ITALIC . $result;
			$m->setText($result);
			$result = clone $m;
			$m->setText($colored);
			$colored = clone $m;
		}else{
			$users = $source->getServer()->getPluginManager()->getPermissionSubscriptions(Server::BROADCAST_CHANNEL_ADMINISTRATIVE);
			$result = new TranslationContainer("chat.type.admin", [$source->getName(), $message]);
			$colored = new TranslationContainer(TextFormat::GRAY . TextFormat::ITALIC . "%chat.type.admin", [$source->getName(), $message]);
		}
		if($sendToSource === true and !($source instanceof ConsoleCommandSender)){
			$source->sendMessage($message);
		}
		foreach($users as $user){
			if($user instanceof CommandSender){
				if($user instanceof ConsoleCommandSender){
					$user->sendMessage($result);
				}elseif($user !== $source){
					$user->sendMessage($colored);
				}
			}
		}
	}
	public function __toString(){
		return $this->name;
	}
}
?>