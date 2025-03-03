<?php

namespace Mellooh\DuelsX\commands\admin;

use Mellooh\DuelsX\commands\SubCommand;
use Mellooh\DuelsX\Main;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;


class SetposArgument implements SubCommand{

    public function execute(CommandSender $sender, array $args): void {
        $dataFolder = Main::getInstance()->getDataFolder() . "arenas" . DIRECTORY_SEPARATOR;
        $messages = Main::getInstance()->messagesConfig;

        if (!$sender->hasPermission("duels.arena")){
            $sender->sendMessage($messages->getNested("messages.no_permission"));
            return;
        }

        if (count($args) < 2) {
            $sender->sendMessage($messages->getNested("messages.usage.setpos"));
            return;
        }

        $posSlot = $args[0];
        if ($posSlot !== "1" && $posSlot !== "2") {
            $sender->sendMessage($messages->getNested("messages.usage.invalid_pos"));
            return;
        }
        $arenaName = $args[1];
        $arenaFile = $dataFolder . $arenaName . ".yml";
        if (!file_exists($arenaFile)) {
            $message = str_replace("{arena}", $arenaName, $messages->getNested("messages.arena.not_exist"));
            $sender->sendMessage($message);
            return;
        }
        $config = new Config($arenaFile, Config::YAML);
        $levelName = $sender->getWorld()->getFolderName();
        $pos = $sender->getPosition();
        $positionData = [
            "level" => $levelName,
            "x" => $pos->getX(),
            "y" => $pos->getY(),
            "z" => $pos->getZ()
        ];
        $config->set("pos" . $posSlot, $positionData);
        $config->save();
        $message = str_replace(["{pos}", "{arena}"], [$posSlot, $arenaName], $messages->getNested("messages.arena.pos_set"));
        $sender->sendMessage($message);
    }
}