<?php

declare(strict_types=1);

namespace NoobMCBG\RankShop\task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use NoobMCBG\RankShop\RankShop;

class RankTimeTask extends Task {

	private RankShop $plugin;

    public function __construct(RankShop $plugin){
    	$this->plugin = $plugin;
    }

    public function getOwningPlugin() : RankShop {
    	return $this->plugin;
    }

    public function onRun() : void {
    	if(count($this->getOwningPlugin()->times->getAll()) >= 1){
    		foreach($this->getOwningPlugin()->times->getAll() as $p => $times){
                if(is_numeric($times)){
                    if($times == 0){
    			     	$this->getOwningPlugin()->times->remove($p);
    			     	if($this->getOwningPlugin()->getServer()->getPlayerByPrefix($p) instanceof Player){
    			     		$this->getOwningPlugin()->getServer()->getPlayerByPrefix($p)->sendMessage("§l§c•§e Rank Của Bạn Đã Hết Ngày, Hãy Mua Thêm Bằng Cách Sử Dụng Lệnh§b /rankshop");
    			     		\YTBJero\AddSoundAPI\AddSoundAPI::sendSound($this->getOwningPlugin()->getServer()->getPlayerByPrefix($p), "mob.enderdragon.growl", 1, 1);
    			     	}
                    }
                }
                date_default_timezone_set('Asia/Ho_Chi_Minh');
                if(date("H:i:s") == "24:00:00"){
                    if(is_numeric($times)){
                        $this->getOwningPlugin()->times->set($p, $times - 1);
                        if($this->getOwningPlugin()->getServer()->getPlayerByPrefix($p) instanceof Player){
                            $this->getOwningPlugin()->getServer()->getPlayerByPrefix($p)->sendMessage("§l§c•§e Rank Của Bạn Còn§a $times Ngày §c•");
                            \YTBJero\AddSoundAPI\AddSoundAPI::sendSound($this->getOwningPlugin()->getServer()->getPlayerByPrefix($p), "random.click", 1, 1);
                        }
                    }
                }
    		}
            $this->getOwningPlugin()->times->save();
    	}
    }
}