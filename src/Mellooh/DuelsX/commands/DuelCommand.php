<?php

namespace Mellooh\DuelsX\commands;

use Mellooh\DuelsX\commands\admin\CreateArenaArgument;
use Mellooh\DuelsX\commands\admin\DeleteArenaArgument;
use Mellooh\DuelsX\commands\admin\SetposArgument;
use Mellooh\DuelsX\commands\player\AcceptDuelArgument;
use Mellooh\DuelsX\commands\player\DeclineDuelArgument;
use Mellooh\DuelsX\Main;
use Mellooh\DuelsX\manager\DuelManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

class DuelCommand extends Command implements PluginOwned {

    private Main $plugin;

    private CreateArenaArgument $createArenaArgument;
    private DeleteArenaArgument $deleteArenaArgument;
    private SetposArgument $setposArgument;
    private AcceptDuelArgument $acceptDuelArgument;
    private DeclineDuelArgument $declineDuelArgument;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;

        $this->createArenaArgument = new CreateArenaArgument();
        $this->deleteArenaArgument = new DeleteArenaArgument();
        $this->setposArgument = new SetposArgument();
        $this->acceptDuelArgument = new AcceptDuelArgument();
        $this->declineDuelArgument = new DeclineDuelArgument();

        parent::__construct("duel", "Management of duels and arenas");
        $this->setPermission("duels.arena");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage("§c✖ Only players can use this command!");
            return true;
        }

        if (count($args) < 1) {
            $sender->sendMessage("§l§5■§d━━━━━━━━━━ §5[ §d⚡ DuelsX ⚡ §5] §d━━━━━━━━━━§5■");
            $sender->sendMessage("§5➥ §dPlugin by: §eMellooh");
            $sender->sendMessage("§5➥ §dVersion: §e1.0-BETA");
            $sender->sendMessage("§a");
            $sender->sendMessage("§5➥ §dUse /duel help");
            $sender->sendMessage("§l§5■§d━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━§5■");
            return true;
        }

        $subCommand = strtolower(array_shift($args));

        switch ($subCommand) {
            case "create":
                $this->createArenaArgument->execute($sender, $args);
                break;
            case "delete":
                $this->deleteArenaArgument->execute($sender, $args);
                break;
            case "setpos":
                $this->setposArgument->execute($sender, $args);
                break;
            case "accept":
                $this->acceptDuelArgument->execute($sender, $args);
                break;
            case "decline":
                $this->declineDuelArgument->execute($sender, $args);
                break;
            case "help":
                $sender->sendMessage("§l§5■§d━━━━━━━━━━ §5[ §d⚡ DuelsX ⚡ §5] §d━━━━━━━━━━§5■");
                $sender->sendMessage("§5➥ §d/duel create <arena name> <nome world>");
                $sender->sendMessage("§5➥ §d/duel delete <arena name>");
                $sender->sendMessage("§5➥ §d/duel setpos 1/2 <arena name>");
                $sender->sendMessage("§5➥ §d/duel accept <player name>");
                $sender->sendMessage("§5➥ §d/duel decline <player name>");
                $sender->sendMessage("§5➥ §d/duel <player name>  (per inviare una richiesta di duello)");
                $sender->sendMessage("§l§5■§d━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━§5■");
                break;
            default:
                $targetName = $subCommand;
                $messages = Main::getInstance()->messagesConfig;

                if (strtolower($targetName) === strtolower($sender->getName())) {
                    $sender->sendMessage($messages->getNested("messages.duel.challenge_self"));
                    return true;
                }

                $target = Main::getInstance()->getServer()->getPlayerExact($targetName);
                if ($target === null) {
                    $offlineMessage = str_replace("{player}", $targetName, $messages->getNested("messages.duel.target_offline"));
                    $sender->sendMessage($offlineMessage);
                    return true;
                }

                if (isset(DuelManager::$activeDuels[strtolower($sender->getName())]) || isset(DuelManager::$activeDuels[strtolower($target->getName())])) {
                    $sender->sendMessage($messages->getNested("messages.duel.already_in_duel"));
                    return true;
                }

                DuelManager::$duelRequests[strtolower($target->getName())] = $sender->getName();

                $requestSentMessage = str_replace("{target}", $target->getName(), $messages->getNested("messages.duel.request_sent"));
                $sender->sendMessage($requestSentMessage);

                $challengeMessage = str_replace("{challenger}", $sender->getName(), $messages->getNested("messages.duel.challenge"));
                $target->sendMessage($challengeMessage);

                break;
        }
        return true;
    }

    public function getOwningPlugin(): Plugin {
        return $this->plugin;
    }
}
