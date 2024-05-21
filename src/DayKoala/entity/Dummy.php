<?php

namespace DayKoala\entity;

use pocketmine\entity\Human;
use pocketmine\entity\EntitySizeInfo;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

use pocketmine\player\Player;

use pocketmine\entity\animation\HurtAnimation;

use pocketmine\world\sound\BlockBreakSound;

use pocketmine\block\VanillaBlocks;

class Dummy extends Human{
    
    protected function getInitialSizeInfo() : EntitySizeInfo{ return new EntitySizeInfo(1, 1); }

    public function attack(EntityDamageEvent $source) : void{
        if(!$source instanceof EntityDamageByEntityEvent){
            return;
        }
        $damager = $source->getDamager();
        if($damager instanceof Player){
            $this->broadcastAnimation(new HurtAnimation($this), [$damager]);
            $this->broadcastSound(new BlockBreakSound(VanillaBlocks::WHEAT()), [$damager]);
        }
        $source->setKnockBack(0);
        $source->call();
    }

}