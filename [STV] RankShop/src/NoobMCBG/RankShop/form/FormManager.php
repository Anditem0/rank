<?php

declare(strict_types=1);

namespace NoobMCBG\RankShop\form;

use pocketmine\player\Player;
use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\ModalForm;
use YTBJero\AddSoundAPI\AddSoundAPI;
use onebone\coinapi\CoinAPI;
use NoobMCBG\RankShop\RankShop;

class FormManager {

    public function __construct(RankShop $plugin){
    	$this->plugin = $plugin;
        $this->title = ($this->plugin->getConfig()->get("form-title") != null) ? $this->plugin->getConfig()->get("form-title") : "§l§3•§2 Rank Shop §3•";
        $this->exit = ($this->plugin->getConfig()->get("exit-button") != null) ? $this->plugin->getConfig()->get("exit-button") : "§l§3•§2 Thoát Menu §3•";
        $this->back = ($this->plugin->getConfig()->get("back-button") != null) ? $this->plugin->getConfig()->get("back-button") : "§l§3•§2 Quay Lại §3•";
        $this->fifteen = ($this->plugin->getConfig()->get("fifteen-button") != null) ? $this->plugin->getConfig()->get("fifteen-button") : "§l§3•§2 Mua 15 Ngày §3•";
        $this->forever = ($this->plugin->getConfig()->get("forever-button") != null) ? $this->plugin->getConfig()->get("forever-button") : "§l§3•§2 Mua Vĩnh Viễn §3•";
    }

    public function menuRankShop(Player $player){
    	$form = new SimpleForm(function(Player $player, $data){
    		if($data == null){
    			return true;
    		}
    		switch($data){
    			case 1:
    			    $this->buyRank($player, "VipI");
                break;
                case 2:
                    $this->buyRank($player, "VipII");
                break;
                case 3:
                    $this->buyRank($player, "VipIII");
                break;
                case 4:
                    $this->buyRank($player, "VipIV");
                break;
                case 5:
                    $this->buyRank($player, "King");
                break;
    		}
    	});
        $form->setTitle($this->title);
        $rank = $this->plugin->PurePerms()->getUserDataMgr()->getData($player)["group"];
        $times = $this->plugin->getTimeRank($player);
        $coin = CoinAPI::getInstance()->myCoin($player);
        $form->setContent("§l§c•§e Rank Của Bạn:§a $rank\n§l§c•§e Thời Gian Ranks Của Bạn Còn:§a $times\n§l§c•§e Số Coin Của Bạn:§a $coin");
        $form->addButton($this->exit);
        $vip1 = ($this->plugin->getConfig()->getAll()["VipI"]["button"] != null) ? $this->plugin->getConfig()->getAll()["VipI"]["button"] : "§l§3•§2 VipI §3•";
        $form->addButton($vip1);
        $vip2 = ($this->plugin->getConfig()->getAll()["VipII"]["button"] != null) ? $this->plugin->getConfig()->getAll()["VipII"]["button"] : "§l§3•§2 VipII §3•";
        $form->addButton($vip2);
        $vip3 = ($this->plugin->getConfig()->getAll()["VipIII"]["button"] != null) ? $this->plugin->getConfig()->getAll()["VipIII"]["button"] : "§l§3•§2 VipIII §3•";
        $form->addButton($vip3);
        $vip4 = ($this->plugin->getConfig()->getAll()["VipIV"]["button"] != null) ? $this->plugin->getConfig()->getAll()["VipIV"]["button"] : "§l§3•§2 VipIV §3•";
        $form->addButton($vip4);
        $king = ($this->plugin->getConfig()->getAll()["King"]["button"] != null) ? $this->plugin->getConfig()->getAll()["King"]["button"] : "§l§3•§2 King §3•";
        $form->addButton($king);
        $form->sendToPlayer($player);
    }

    public function buyRank(Player $player, string $rank){
        $form = new SimpleForm(function(Player $player, $data) use ($rank) {
            if(!isset($data)){
                $this->menuRankShop($player);
                return true;
            }
            switch($data){
                case 0:
                    $this->menuRankShop($player);
                break;
                case 1:
                    $this->buyFifteenDay($player, $rank);
                break;
                case 2:
                    $this->buyForever($player, $rank);
                break;
            }
        });
        $form->setTitle($this->title);
        $group = $this->plugin->PurePerms()->getUserDataMgr()->getData($player)["group"];
        $times = $this->plugin->getTimeRank($player);
        $coin = CoinAPI::getInstance()->myCoin($player);
        $form->setContent("§l§c•§e Rank Của Bạn:§a $group\n§l§c•§e Thời Gian Ranks Của Bạn Còn:§a $times\n§l§c•§e Số Coin Của Bạn:§a $coin");
        $form->addButton($this->back);
        $fifteenday = ($coin >= $this->plugin->getConfig()->getAll()[$rank]["cost"]["fifteenday"]) ? "§l§2".$this->plugin->getConfig()->getAll()[$rank]["cost"]["fifteenday"]."/15 Ngày" : "§l§4".$this->plugin->getConfig()->getAll()[$rank]["cost"]["fifteenday"]." Coin/15 Ngày";
        $form->addButton($this->fifteen."\n".$fifteenday);
        $forever = ($coin >= $this->plugin->getConfig()->getAll()[$rank]["cost"]["forever"]) ? "§l§2".$this->plugin->getConfig()->getAll()[$rank]["cost"]["forever"]."/Vĩnh Viễn" : "§l§4".$this->plugin->getConfig()->getAll()[$rank]["cost"]["forever"]." Coin/Vĩnh Viễn";
        $form->addButton($this->forever."\n".$forever);
        $form->sendToPlayer($player);
    }

    public function buyFifteenDay(Player $player, string $rank){
        $form = new ModalForm(function(Player $player, $data) use ($rank) {
            if(!isset($data)){
                $this->menuRankShop($player);
                return true;
            }
            if($data == true){
                $cost = $this->getCost($rank);
                $group = $this->plugin->PurePerms()->getUserDataMgr()->getData($player)["group"];
                foreach($this->plugin->getConfig()->getAll()["rank-anti-buy"]["list"] as $anti){
                    if(strtolower($group) == strtolower($anti)){
                        $player->sendMessage($this->plugin->getConfig()->getAll()["rank-anti-buy"]["msg"]);
                        AddSoundAPI::sendSound($player, "random.explode");
                        return true;
                    }
                }
                switch(strtolower($rank)){
                    case "vipi": // VipI
                        if(CoinAPI::getInstance()->myCoin($player) >= $cost){
                            CoinAPI::getInstance()->reduceCoin($player, $cost);
                            if($group !== "VipI" or $group !== "VipII" or $group !== "VipIII" or $group !== "VipIV" or $group !== "King"){
                                $this->plugin->PurePerms()->setGroup($player, $this->plugin->PurePerms()->getGroup($rank));
                            }
                            $this->plugin->addTime($player, 15);
                            $this->successfully($player, $rank);
                        }else{
                            $this->fallied($player, $rank);
                        }
                    break;
                    case "vipii": // VipII
                        if(CoinAPI::getInstance()->myCoin($player) >= $cost){
                            CoinAPI::getInstance()->reduceCoin($player, $cost);
                            if($group !== "VipII" or $group !== "VipIII" or $group !== "VipIV" or $group !== "King"){
                                $this->plugin->PurePerms()->setGroup($player, $this->plugin->PurePerms()->getGroup($rank));
                            }
                            $this->plugin->addTime($player, 15);
                            $this->successfully($player, $rank);
                        }else{
                            $this->fallied($player, $rank);
                        }
                    break;
                    case "vipiii": // VipIII
                        if(CoinAPI::getInstance()->myCoin($player) >= $cost){
                            CoinAPI::getInstance()->reduceCoin($player, $cost);
                            if($group !== "VipIII" or $group !== "VipIV" or $group !== "King"){
                                $this->plugin->PurePerms()->setGroup($player, $this->plugin->PurePerms()->getGroup($rank));
                            }
                            $this->plugin->addTime($player, 15);
                            $this->successfully($player, $rank);
                        }else{
                            $this->fallied($player, $rank);
                        }
                    break;
                    case "vipiv": // VipIV
                        if(CoinAPI::getInstance()->myCoin($player) >= $cost){
                            CoinAPI::getInstance()->reduceCoin($player, $cost);
                            if($group !== "VipIV" or $group !== "King"){
                                $this->plugin->PurePerms()->setGroup($player, $this->plugin->PurePerms()->getGroup($rank));
                            }
                            $this->plugin->addTime($player, 15);
                            $this->successfully($player, $rank);
                        }else{
                            $this->fallied($player, $rank);
                        }
                    break;
                    case "king": // King
                        if(CoinAPI::getInstance()->myCoin($player) >= $cost){
                            CoinAPI::getInstance()->reduceCoin($player, $cost);
                            if($group !== "King"){
                                $this->plugin->PurePerms()->setGroup($player, $this->plugin->PurePerms()->getGroup($rank));
                            }
                            $this->plugin->addTime($player, 15);
                            $this->successfully($player, $rank);
                        }else{
                            $this->fallied($player, $rank);
                        }
                    break;
                }
            }
        });
        $form->setTitle($this->title);
        $cost = $this->getCost($rank);
        $form->setContent("§l§c•§e Bạn Đã Chắc Chắn Mua Rank§d $rank §eVới Giá§a $cost Coin/15 Ngày §eChưa ?");
        $form->setButton1("§l§3•§2 Mua §3•");
        $form->setButton2("§l§3•§4 Không Mua §3•");
        $form->sendToPlayer($player);
    }

    public function buyForever(Player $player, string $rank){
        $form = new ModalForm(function(Player $player, $data) use ($rank) {
            if(!isset($data)){
                $this->menuRankShop($player);
                return true;
            }
            if($data == true){
                $cost = $this->getCost($rank, true);
                $group = $this->plugin->PurePerms()->getUserDataMgr()->getData($player)["group"];
                foreach($this->plugin->getConfig()->getAll()["rank-anti-buy"]["list"] as $anti){
                    if(strtolower($group) == strtolower($anti)){
                        $player->sendMessage($this->plugin->getConfig()->getAll()["rank-anti-buy"]["msg"]);
                        AddSoundAPI::sendSound($player, "random.explode");
                        return true;
                    }
                }
                switch(strtolower($rank)){
                    case "vipi": // VipI
                        if(CoinAPI::getInstance()->myCoin($player) >= $cost){
                            CoinAPI::getInstance()->reduceCoin($player, $cost);
                            $this->plugin->PurePerms()->setGroup($player, $this->plugin->PurePerms()->getGroup($rank));
                            $this->plugin->setForever($player);
                            $this->successfully($player, $rank, true);
                        }else{
                            $this->fallied($player, $rank, true);
                        }
                    break;
                    case "vipii": // VipII
                        if(CoinAPI::getInstance()->myCoin($player) >= $cost){
                            CoinAPI::getInstance()->reduceCoin($player, $cost);
                            $this->plugin->PurePerms()->setGroup($player, $this->plugin->PurePerms()->getGroup($rank));
                            $this->plugin->setForever($player);
                            $this->successfully($player, $rank, true);
                        }else{
                            $this->fallied($player, $rank, true);
                        }
                    break;
                    case "vipiii": // VipIII
                        if(CoinAPI::getInstance()->myCoin($player) >= $cost){
                            CoinAPI::getInstance()->reduceCoin($player, $cost);
                            $this->plugin->PurePerms()->setGroup($player, $this->plugin->PurePerms()->getGroup($rank));
                            $this->plugin->setForever($player);
                            $this->successfully($player, $rank, true);
                        }else{
                            $this->fallied($player, $rank, true);
                        }
                    break;
                    case "vipiv": // VipIV
                        if(CoinAPI::getInstance()->myCoin($player) >= $cost){
                            CoinAPI::getInstance()->reduceCoin($player, $cost);
                            $this->plugin->PurePerms()->setGroup($player, $this->plugin->PurePerms()->getGroup($rank));
                            $this->plugin->setForever($player);
                            $this->successfully($player, $rank, true);
                        }else{
                            $this->fallied($player, $rank, true);
                        }
                    break;
                    case "king": // King
                        if(CoinAPI::getInstance()->myCoin($player) >= $cost){
                            CoinAPI::getInstance()->reduceCoin($player, $cost);
                            $this->plugin->PurePerms()->setGroup($player, $this->plugin->PurePerms()->getGroup($rank));
                            $this->plugin->setForever($player);
                            $this->successfully($player, $rank, true);
                        }else{
                            $this->fallied($player, $rank, true);
                        }
                    break;
                }
            }
        });
        $form->setTitle($this->title);
        $cost = $this->getCost($rank, true);
        $form->setContent("§l§c•§e Bạn Đã Chắc Chắn Mua Rank§d $rank §eVới Giá§a $cost Coin/Vĩnh Viễn §eChưa ?");
        $form->setButton1("§l§3•§2 Mua §3•");
        $form->setButton2("§l§3•§4 Không Mua §3•");
        $form->sendToPlayer($player);
    }

    public function getCost(string $rank, bool $forever = false){
        if($forever == false){
            return $this->plugin->getConfig()->getAll()[$rank]["cost"]["fifteenday"];
        }else{
            return $this->plugin->getConfig()->getAll()[$rank]["cost"]["forever"];
        }
    }

    public function successfully(Player $player, string $rank, bool $forever = false){
        if($forever == false){
            $msg = str_replace(["{player}", "{name}", "{line}", "{rank}", "{time}"], [$player->getName(), $player->getName(), "\n", $rank, "15"], $this->plugin->getConfig()->getAll()["broadcast"]["successfully"]);
            $this->plugin->getServer()->broadcastMessage($msg);
            AddSoundAPI::sendSound($player, "random.levelup");
        }else{
            $msg = str_replace(["{player}", "{name}", "{line}", "{rank}", "{time}"], [$player->getName(), $player->getName(), "\n", $rank, "Vĩnh Viễn"], $this->plugin->getConfig()->getAll()["broadcast"]["successfully"]);
            $this->plugin->getServer()->broadcastMessage($msg);
            AddSoundAPI::sendSound($player, "random.levelup");
        }
    }

    public function fallied(Player $player, string $rank, bool $forever = false){
        $cost = $this->getCost($rank, $forever);
        $msg = str_replace(["{player}", "{name}", "{line}", "{rank}", "{cost}"], [$player->getName(), $player->getName(), "\n", $rank, $cost], $this->plugin->getConfig()->get("msg-buy-fallied"));
        $player->sendMessage($msg);
        AddSoundAPI::sendSound($player, "random.explode");
    }
}