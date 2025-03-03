<?php

namespace Mellooh\DuelsX\commands\admin;

use Mellooh\DuelsX\commands\SubCommand;
use Mellooh\DuelsX\Main;
use pocketmine\command\CommandSender;


class DeleteArenaArgument implements SubCommand{

    public function execute(CommandSender $sender, array $args): void {
        $dataFolder = Main::getInstance()->getDataFolder() . "arenas" . DIRECTORY_SEPARATOR;
        $messages = Main::getInstance()->messagesConfig;

        if (!$sender->hasPermission("duels.arena")) {
            $sender->sendMessage($messages->getNested("messages.no_permission"));
            return;
        }

        if (count($args) < 1) {
            $sender->sendMessage($messages->getNested("messages.usage.delete"));
            return;
        }

        $arenaName = $args[0];
        $arenaFile = $dataFolder . $arenaName . ".yml";
        if (!file_exists($arenaFile)) {
            $message = str_replace("{arena}", $arenaName, $messages->getNested("messages.arena.not_exist"));
            $sender->sendMessage($message);
            return;
        }
        @unlink($arenaFile);
        $message = str_replace("{arena}", $arenaName, $messages->getNested("messages.arena.deleted"));
        $sender->sendMessage($message);
    }
}