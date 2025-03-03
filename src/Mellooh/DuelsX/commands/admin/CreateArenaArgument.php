<?php

namespace Mellooh\DuelsX\commands\admin;

use Mellooh\DuelsX\commands\SubCommand;
use Mellooh\DuelsX\Main;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;


class CreateArenaArgument implements SubCommand {


    public function execute(CommandSender $sender, array $args): void{
        $dataFolder = Main::getInstance()->getDataFolder() . "arenas" . DIRECTORY_SEPARATOR;
        $messages = Main::getInstance()->messagesConfig;

        if (!$sender->hasPermission("duels.arena")){
            $sender->sendMessage($messages->getNested("messages.no_permission"));
            return;
        }

        if(count($args) < 2){
            $sender->sendMessage($messages->getNested("messages.usage.create"));
            return;
        }

        if(!is_dir($dataFolder)){
            @mkdir($dataFolder, 0777, true);
        }

        $arenaName = $args[0];
        $worldName = $args[1];
        $arenaFile = $dataFolder . $arenaName . ".yml";
        if(file_exists($arenaFile)){
            $message = str_replace("{arena}", $arenaName, $messages->getNested("messages.arena.already_exists"));
            $sender->sendMessage($message);
            return;
        }
        $data = [
            "world" => $worldName,
            "pos1" => null,
            "pos2" => null
        ];
        $config = new Config($arenaFile, Config::YAML, $data);
        $config->save();
        $message = str_replace(["{arena}", "{world}"], [$arenaName, $worldName], $messages->getNested("messages.arena.created"));
        $sender->sendMessage($message);
    }
}