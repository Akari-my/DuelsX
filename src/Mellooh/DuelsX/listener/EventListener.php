<?php

namespace Mellooh\DuelsX\listener;

use Mellooh\DuelsX\Main;
use Mellooh\DuelsX\manager\DuelManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;


class EventListener implements Listener {

    public function onEntityDamage(EntityDamageByEntityEvent $event) : void {
        $damager = $event->getDamager();
        $entity = $event->getEntity();
        if($damager instanceof Player && $entity instanceof Player){
            if(!(isset(DuelManager::$activeDuels[$damager->getName()]) && isset(DuelManager::$activeDuels[$entity->getName()]))) {
                return;
            }
            if(DuelManager::$activeDuels[$damager->getName()] !== $entity->getName()){
                $event->cancel();
            }
        }
    }

    public function onPlayerDeath(PlayerDeathEvent $event) : void {
        $player = $event->getPlayer();
        $playerName = $player->getName();
        $messages = Main::getInstance()->messagesConfig;
        if(isset(DuelManager::$activeDuels[$playerName])){
            $event->setDeathMessage("");

            $opponentName = DuelManager::$activeDuels[$playerName];
            $opponent = Main::getInstance()->getServer()->getPlayerExact($opponentName);
            if(isset(DuelManager::$duelArenaMapping[$playerName])){
                $arenaName = DuelManager::$duelArenaMapping[$playerName];
                DuelManager::freeArena($arenaName);
                unset(DuelManager::$duelArenaMapping[$playerName]);
                unset(DuelManager::$duelArenaMapping[$opponentName]);
            }
            if($opponent !== null){
                $winMessage = str_replace("{player}", $playerName, $messages->getNested("messages.duel.win"));
                $opponent->sendMessage($winMessage);
                $opponent->getInventory()->clearAll();
                $opponent->getArmorInventory()->clearAll();
                $opponent->teleport(Main::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
            }
            $loseMessage = str_replace("{player}", $opponentName, $messages->getNested("messages.duel.lose"));
            $player->sendMessage($loseMessage);
            $player->getInventory()->clearAll();
            $player->getArmorInventory()->clearAll();
            Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player) {
                $player->respawn();
            }), 1);
            unset(DuelManager::$activeDuels[$playerName]);
            unset(DuelManager::$activeDuels[$opponentName]);
        }
    }
}