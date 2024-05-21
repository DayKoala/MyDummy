<?php

namespace DayKoala;

use pocketmine\event\Listener;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

use pocketmine\player\Player;

use DayKoala\entity\Dummy;

use DayKoala\entity\FloatingEntity;

final class MyDummyListener implements Listener{

    private static $settings = [];

    public static function hasSettings(Player|string $player) : bool{
        return isset(self::$settings[$player instanceof Player ? $player->getName() : $player]);
    }

    public static function addSettings(Player $player) : void{
        self::$settings[$player->getName()] = true;
    }

    public static function removeSettings(Player|string $player) : void{
        if($player instanceof Player){
            $player = $player->getName();
        }
        if(isset(self::$settings[$player])) unset(self::$settings[$player]);
    }

    public function __construct(
        private MyDummy $plugin
    ){}

    public function onDamage(EntityDamageEvent $event){
        if(!$event instanceof EntityDamageByEntityEvent){
            return;
        }
        $entity = $event->getEntity();
        if(!$entity instanceof Dummy){
            return;
        }
        $damager = $event->getDamager();
        if(!$damager instanceof Player){
            return;
        }
        if(self::hasSettings($damager)){
            self::removeSettings($damager);
            $entity->close();
            return;
        }
        $pos = $entity->getLocation();

        $pos->x = $pos->getFloorX() + 0.5 + (mt_rand(-3, 3) / 10);
        $pos->y = $pos->getFloorY() + $entity->getScale() + 0.5 + (mt_rand(1, 5) / 10);
        $pos->z = $pos->getFloorZ() + 0.5 + (mt_rand(-3, 3) / 10);

        $entity = new FloatingEntity($pos);

        $entity->setNameTag(($event->isApplicable(EntityDamageEvent::MODIFIER_CRITICAL) ? "§c" : "§e") . $event->getFinalDamage());
        $entity->setMaxDuration(25);

        $entity->spawnTo($damager);
    }

}