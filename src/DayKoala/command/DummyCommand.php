<?php

namespace DayKoala\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\plugin\PluginOwned;

use pocketmine\player\Player;

use DayKoala\MyDummy;
use DayKoala\MyDummyListener;

final class DummyCommand extends Command implements PluginOwned{

    private const PREFIX = "§l§eDUMMY §r§e";

    public function __construct(
        private $plugin
    ){
        parent::__construct('dummy', 'MyDummy main command', '/dummy');

        $this->setPermission('mydummy.command.main');
    }

    public function getOwningPlugin(): MyDummy{
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if(!$this->testPermission($sender)){
            return false;
        }
        if($sender instanceof Player){
            switch(strtolower((string) array_shift($args))){
                case 'spawn':
                    $this->plugin->sendDummy($sender->getLocation());
                    $sender->sendMessage(self::PREFIX ."A new dummy generated.");
                    return true;
                case 'kill':
                    if(MyDummyListener::hasSettings($sender)){
                        MyDummyListener::removeSettings($sender);
                        $message = "Disabled dummy removal.";
                    }else{
                        MyDummyListener::addSettings($sender);
                        $message = "Enabled dummy removal, hit him.";
                    }
                    $sender->sendMessage(self::PREFIX . $message);
                    return true;
                default:
                $sender->sendMessage(
                    self::PREFIX ."/dummy spawn\n".
                    self::PREFIX ."/dummy kill"
                );
                return true;
            }
        }
        $sender->sendMessage(self::PREFIX ."In game only.");
        return false;
    }

}