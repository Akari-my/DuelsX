<?php

declare(strict_types=1);

namespace Mellooh\DuelsX;

use Mellooh\DuelsX\listener\EventListener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

use Mellooh\DuelsX\commands\DuelCommand;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;

class Main extends PluginBase {

    /** @var Main|null */
    private static ?Main $instance = null;

    public Position $arenaPos1;
    public Position $arenaPos2;
    public Config $messagesConfig;

    public static function getInstance() : Main {
        return self::$instance;
    }

    public function onEnable() : void {
        self::$instance = $this;

        $this->saveDefaultConfig();

        $this->saveResource("messages.yml");
        $this->messagesConfig = new Config($this->getDataFolder() . "messages.yml", Config::YAML);

        $this->arenaPos1 = $this->getServer()->getWorldManager()->getDefaultWorld()->getSpawnLocation();
        $this->arenaPos2 = $this->getServer()->getWorldManager()->getDefaultWorld()->getSpawnLocation();

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);

        $this->getServer()->getCommandMap()->register("duel", new DuelCommand($this));
        
        $this->getLogger()->info(TextFormat::LIGHT_PURPLE . "
#    _____             _    __   __
#   |  __ \           | |   \ \ / /
#   | |  | |_   _  ___| |___ \ V / 
#   | |  | | | | |/ _ | / __| > <  
#   | |__| | |_| |  __| \__ \/ . \ 
#   |_____/ \__,_|\___|_|___/_/ \_\
#  
# Successfully enabled
# version: 1.0.0-BETA");
    }
}
