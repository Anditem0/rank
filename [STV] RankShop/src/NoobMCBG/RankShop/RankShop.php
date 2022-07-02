<?php

declare(strict_types=1);

namespace NoobMCBG\RankShop;

use pocketmine\player\Player;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use NoobMCBG\RankShop\commands\RankShopCommands;
use NoobMCBG\RankShop\task\RankTimeTask;

class RankShop extends PluginBase implements Listener {

	public static $instance;

	public static function getInstance() : self {
		return self::$instance;
	}


	public function onLoad() : void {
		$this->times = new Config($this->getDataFolder() . "times.yml", Config::YAML);
		self::$instance = $this;
	}

	public function onEnable() : void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig();
        $this->getServer()->getCommandMap()->register("/rankshop", new RankShopCommands($this));
        $this->pp = $this->getServer()->getPluginManager()->getPlugin("PurePerms");
        $this->times = new Config($this->getDataFolder() . "times.yml", Config::YAML);
        $this->getScheduler()->scheduleRepeatingTask(new RankTimeTask($this), 20);
	}

	public function getTime(){
		return $this->times;
	}

	public function PurePerms(){
		return $this->pp;
	}

	public function getTimeRank(Player $player){
		if($this->times->exists($player->getName())){
			if($this->times->get($player->getName()) == "Forever"){
				return "Vĩnh Viễn";
			}else{
            	return $this->times->get($player->getName());
            }
		}else{
			return 0;
		}
	}

	public function addTime(Player $player, float|int $time){
		if($this->times->exists($player->getName())){
			if($this->times->get($player->getName()) == "Forever") return;
			$this->times->set($player->getName(), $this->times->get($player->getName()) + $time);
			$this->times->save();
		}else{
			$this->times->set($player->getName(), $time);
			$this->times->save();
		}
	}

	public function reduceTime(Player $player, float|int $time){
		if($this->times->exists($player->getName())){
			if($this->times->get($player->getName()) == "Forever") return;
			$this->times->set($player->getName(), $this->times->get($player->getName()) - $time);
			$this->times->save();
		}
	}

	public function setForever(Player $player, bool $status = true){
		if($status == true){
			$this->times->set($player->getName(), "Forever");
			$this->times->save();
		}
	}
}