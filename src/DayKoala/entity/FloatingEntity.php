<?php

namespace DayKoala\entity;

use pocketmine\entity\object\FallingBlock;

use pocketmine\entity\Location;
use pocketmine\entity\EntitySizeInfo;

use pocketmine\nbt\tag\CompoundTag;

use pocketmine\block\VanillaBlocks;

class FloatingEntity extends FallingBlock{

    protected int $maxDuration = -1;
    protected float $minDuration = 0;

    public function __construct(Location $location, ?CompoundTag $nbt = null){
        parent::__construct($location, VanillaBlocks::AIR(), $nbt);

        $this->setScale(0.01);
        $this->setNameTagAlwaysVisible();
        $this->setNoClientPredictions();
    }

    protected function getInitialSizeInfo() : EntitySizeInfo{ return new EntitySizeInfo(0, 0); }

    public function setMaxDuration(int $duration) : void{
        $this->maxDuration = $duration;
    }

    protected function entityBaseTick(int $tickDiff = 1): bool{
        if($this->closed or $this->isFlaggedForDespawn()){
            return false;
        }
        if($this->maxDuration > -1){
            if($this->maxDuration <= $this->minDuration){
                $this->flagForDespawn();
            }else{
                $this->minDuration++;
            }
        }
        return true;
    }

}