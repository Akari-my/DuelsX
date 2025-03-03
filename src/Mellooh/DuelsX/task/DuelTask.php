<?php

namespace Mellooh\DuelsX\task;

use pocketmine\player\GameMode;
use pocketmine\scheduler\Task;
use pocketmine\player\Player;
use pocketmine\scheduler\TaskHandler;
use pocketmine\world\Position;

use pocketmine\item\VanillaItems;
use Mellooh\DuelsX\Main;

class DuelTask extends Task{

    public ?TaskHandler $handler = null;

    private Main $plugin;
    private Player $p1;
    private Player $p2;
    private Position $aPos1;
    private Position $aPos2;
    private int $counter;

    public function __construct(Main $plugin, Player $p1, Player $p2, Position $aPos1, Position $aPos2, int $counter){
        $this->plugin = $plugin;
        $this->p1 = $p1;
        $this->p2 = $p2;
        $this->aPos1 = $aPos1;
        $this->aPos2 = $aPos2;
        $this->counter = $counter;
    }

    public function onRun() : void {
        $messages = Main::getInstance()->messagesConfig;
        if($this->counter > 0){
            $countdownMessage = str_replace("{seconds}", (string)$this->counter, $messages->getNested("messages.duel.countdown"));
            $this->p1->sendMessage($countdownMessage);
            $this->p2->sendMessage($countdownMessage);
            $this->counter--;
        } else {
            $startMessage = $messages->getNested("messages.duel.start");
            $this->p1->sendTitle($startMessage);
            $this->p2->sendTitle($startMessage);

            $this->p1->getInventory()->clearAll();
            $this->p2->getInventory()->clearAll();

            $this->p1->setGamemode(GameMode::SURVIVAL);
            $this->p2->setGamemode(GameMode::SURVIVAL);

            $this->p1->setHealth(20);
            $this->p2->setHealth(20);

            $this->p1->getHungerManager()->setFood(20);
            $this->p2->getHungerManager()->setFood(20);

            $this->p1->getInventory()->addItem(VanillaItems::DIAMOND_SWORD());
            $this->p2->getInventory()->addItem(VanillaItems::DIAMOND_SWORD());

            $this->p1->getArmorInventory()->setHelmet(VanillaItems::DIAMOND_SWORD());
            $this->p1->getArmorInventory()->setChestplate(VanillaItems::DIAMOND_CHESTPLATE());
            $this->p1->getArmorInventory()->setLeggings(VanillaItems::DIAMOND_LEGGINGS());
            $this->p1->getArmorInventory()->setBoots(VanillaItems::DIAMOND_BOOTS());

            $this->p2->getArmorInventory()->setHelmet(VanillaItems::DIAMOND_SWORD());
            $this->p2->getArmorInventory()->setChestplate(VanillaItems::DIAMOND_CHESTPLATE());
            $this->p2->getArmorInventory()->setLeggings(VanillaItems::DIAMOND_LEGGINGS());
            $this->p2->getArmorInventory()->setBoots(VanillaItems::DIAMOND_BOOTS());

            $this->p1->teleport($this->aPos1);
            $this->p2->teleport($this->aPos2);

            if($this->handler !== null){
                $this->handler->cancel();
            }
        }
    }
}