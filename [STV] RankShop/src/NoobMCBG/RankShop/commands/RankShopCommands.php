<?php

declare(strict_types=1);

namespace NoobMCBG\RankShop\commands;

use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use NoobMCBG\RankShop\RankShop;
use NoobMCBG\RankShop\form\FormManager;

class RankShopCommands extends Command implements PluginOwned {

	private RankShop $plugin;

	public function __construct(RankShop $plugin){
		$this->plugin = $plugin;
		parent::__construct("rankshop", "Lệnh Để Mở Menu Mua Ranks", null, ["muarank", "muaranks", "buyrank", "buyranks"]);
	}
	
	public function execute(CommandSender $sender, string $label, array $args){
		if($sender instanceof Player){
			$form = new FormManager($this->getOwningPlugin());
			$form->menuRankShop($sender);
        }
	}

	public function getOwningPlugin() : RankShop {
		return $this->plugin;
	}
}