<?php

namespace Mellooh\DuelsX\commands;

use pocketmine\command\CommandSender;

interface SubCommand{
    public function execute(CommandSender $sender, array $args): void;

}