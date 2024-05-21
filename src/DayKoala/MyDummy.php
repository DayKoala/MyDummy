<?php

namespace DayKoala;

use pocketmine\plugin\PluginBase;

use pocketmine\entity\EntityFactory;
use pocketmine\entity\EntityDataHelper as Helper;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;

use pocketmine\world\World;

use pocketmine\nbt\tag\CompoundTag;

use DayKoala\entity\Dummy;
use DayKoala\entity\FloatingEntity;

use DayKoala\command\DummyCommand;

final class MyDummy extends PluginBase{

    private static $instance = null;

    public static function getInstance() : ?self{
        return self::$instance;
    }

    protected function onLoad() : void{
        EntityFactory::getInstance()->register(Dummy::class, function(World $world, CompoundTag $nbt) : Dummy{
            return new Dummy(Helper::parseLocation($nbt, $world), Dummy::parseSkinNBT($nbt), $nbt);
        }, ['Dummy']);
        EntityFactory::getInstance()->register(FloatingEntity::class, function(World $world, CompoundTag $nbt) : FloatingEntity{
            return new FloatingEntity(Helper::parseLocation($nbt, $world), $nbt);
        }, ['FloatingEntity']);

        self::$instance = $this;
    }

    protected function onEnable() : void{
        @mkdir($this->getDataFolder());
        foreach(['StrawManSkin.json', 'StrawManGeometry.json'] as $resource){
            $this->saveResource($resource);
        }
        $this->getServer()->getPluginManager()->registerEvents(new MyDummyListener($this), $this);
        $this->getServer()->getCommandMap()->register('MyDummy', new DummyCommand($this));
    }

    public function sendDummy(Location $location) : void{
        try{
            $skin = new Skin('Dummy', file_get_contents($this->getDataFolder() .'StrawManSkin.json'), '', 'geometry.strawman', file_get_contents($this->getDataFolder() .'StrawManGeometry.json'));
        }catch(\Exception $e){
            $this->getLogger()->critical('MyDummy skin error.');
            return;
        }
        $entity = new Dummy($location, $skin);
        $entity->setScale(1.5);
        $entity->spawnToAll();
    }
    
}