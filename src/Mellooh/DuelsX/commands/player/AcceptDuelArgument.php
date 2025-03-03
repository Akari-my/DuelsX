<?php

namespace Mellooh\DuelsX\commands\player;

use Mellooh\DuelsX\commands\SubCommand;
use Mellooh\DuelsX\Main;
use Mellooh\DuelsX\manager\DuelManager;
use pocketmine\command\CommandSender;


class AcceptDuelArgument implements SubCommand{

    public function execute(CommandSender $sender, array $args): void {
        $messages = Main::getInstance()->messagesConfig;

        if (count($args) < 1) {
            $sender->sendMessage($messages->getNested("messages.usage.decline"));
            return;
        }
        $challengerName = $args[0];
        if (!isset(DuelManager::$duelRequests[$sender->getName()]) || DuelManager::$duelRequests[$sender->getName()] !== $challengerName) {
            $message = str_replace("{player}", $challengerName, $messages->getNested("messages.duel.no_request"));
            $sender->sendMessage($message);
            return;
        }
        unset(DuelManager::$duelRequests[$sender->getName()]);
        $message = str_replace("{player}", $challengerName, $messages->getNested("messages.duel.declined"));
        $sender->sendMessage($message);
        $challenger = Main::getInstance()->getServer()->getPlayerExact($challengerName);
        if ($challenger !== null) {
            $message = str_replace("{player}", $sender->getName(), $messages->getNested("messages.duel.refused"));
            $challenger->sendMessage($message);
        }
    }
}