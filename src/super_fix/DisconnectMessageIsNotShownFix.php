<?php namespace super_fix;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\network\protocol\BatchPacket;
use pocketmine\network\protocol\DisconnectPacket;
use pocketmine\utils\Binary;
class DisconnectMessageIsNotShownFix extends Task {
    private $trials = 0;
    private $player = null;
    private $message;
    private $reason;
    private $notify;
    private $interface;
	public static function hook_Player_close(Player $player, $player_connected, $player_closed, $interface, $message = "", $reason = "generic reason", $notify = true){
        $task = new DisconnectMessageIsNotShownFix();
        $task->player = $player;
        $task->message = $message;
		if($player_connected and !$player_closed){
            $task->reason = $reason;
		} else {
		    $task->reason = Server::getInstance()->getLanguage()->translateString("disconnectionScreen.noReason");
		}
        $task->notify = $notify;
        $task->interface = $interface;
		Server::getInstance()->getScheduler()->scheduleRepeatingTask($task, 10);
	}
	public function onRun($currentTick) {
        if ($this->trials < 5) {
            $this->trials++;
            $this->gentleAskPlayerToLeave();
        } else {
            $this->player->close_Original($this->message, $this->reason, $this->notify);
            Server::getInstance()->getScheduler()->cancelTask($this->getTaskId());
        }
    }
	public function gentleAskPlayerToLeave() {
        $pk = new DisconnectPacket;
        $pk->hideDisconnectionScreen = false;
        $pk->message = $this->reason;
        $pk->encode();
        $batch_pk = new BatchPacket();
        $batch_pk->buffer = Binary::writeUnsignedVarInt(strlen($pk->buffer)) . $pk->buffer;
        $identifier = $this->interface->putPacket($this->player, $pk, true, true);
	}
}
?>