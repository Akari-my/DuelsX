<?php

declare(strict_types=1);

namespace Mellooh\DuelsX\manager;

use Mellooh\DuelsX\task\DuelTask;
use pocketmine\world\Position;
use pocketmine\utils\Config;
use Mellooh\DuelsX\Main;

class DuelManager {

    /** @var array<string, string> */
    public static array $duelRequests = [];

    /** @var array<string, string> */
    public static array $activeDuels = [];

    /** @var array<string, string> */
    public static array $duelArenaMapping = [];

    /** @var array<string, bool> */
    public static array $occupiedArenas = [];


    public static function getAvailableArena() : ?array {
        $arenaFolder = Main::getInstance()->getDataFolder() . "arenas" . DIRECTORY_SEPARATOR;
        if(!is_dir($arenaFolder)){
            return null;
        }
        $files = scandir($arenaFolder);
        foreach($files as $file){
            if(substr($file, -4) !== ".yml"){
                continue;
            }
            $arenaName = substr($file, 0, -4);
            if(isset(self::$occupiedArenas[$arenaName])){
                continue;
            }
            $config = new Config($arenaFolder . $file, Config::YAML);
            $data = $config->getAll();
            if($data["pos1"] !== null && $data["pos2"] !== null){
                return ["name" => $arenaName, "data" => $data];
            }
        }
        return null;
    }

    public static function freeArena(string $arenaName) : void {
        unset(self::$occupiedArenas[$arenaName]);
    }

    public static function startDuel($player1, $player2) : void {
        $arenaInfo = self::getAvailableArena();
        $arenaName = null;
        $arenaPos1 = null;
        $arenaPos2 = null;
        if($arenaInfo !== null){
            $arenaName = $arenaInfo["name"];
            $data = $arenaInfo["data"];
            $world = Main::getInstance()->getServer()->getWorldManager()->getWorldByName($data["world"] ?? "");
            if($world !== null){
                $arenaPos1 = new Position((float)$data["pos1"]["x"], (float)$data["pos1"]["y"], (float)$data["pos1"]["z"], $world);
                $arenaPos2 = new Position((float)$data["pos2"]["x"], (float)$data["pos2"]["y"], (float)$data["pos2"]["z"], $world);
                self::$occupiedArenas[$arenaName] = true;
                self::$duelArenaMapping[$player1->getName()] = $arenaName;
                self::$duelArenaMapping[$player2->getName()] = $arenaName;
            }
        }
        if($arenaPos1 === null || $arenaPos2 === null){
            $arenaPos1 = Main::getInstance()->arenaPos1;
            $arenaPos2 = Main::getInstance()->arenaPos2;
        }
        self::$activeDuels[$player1->getName()] = $player2->getName();
        self::$activeDuels[$player2->getName()] = $player1->getName();

        $countdown = 5;
        $task = new DuelTask(Main::getInstance(), $player1, $player2, $arenaPos1, $arenaPos2, $countdown);
        $taskHandler = Main::getInstance()->getScheduler()->scheduleRepeatingTask($task, 20);
        $task->handler = $taskHandler;
    }
}
