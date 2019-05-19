<?php

/*
  _
  | |                 _      ____                _    _
  | |    _   _   ___ | | __ |  _ \   ___   __ _ | |_ | |__
  | |   | | | | / __|| |/ / | | | | / _ \ / _` || __|| '_ \
  | |___| |_| || (__ |   <  | |_| ||  __/| (_| || |_ | | | |
  |_____|\__,_| \___||_|\_\ |____/  \___| \__,_| \__||_| |_|

  LuckDeath by Anders
  QQ: 480177664
  Mail: fox404@foxmail.com
  GitHub: https://github.com/Anders233

 */
declare(strict_types=1);

namespace Anders\LuckDeath;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use function is_dir;
use function mkdir;
use function file_exists;
use function mt_rand;

class Main extends PluginBase implements Listener {

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        if (!is_dir($this->getDataFolder())) {
            @mkdir($this->getDataFolder(), 0777, true);
        }
        if (!file_exists($this->getDataFolder() . "config.yml")) {
            $this->saveResource("config.yml");
        }
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
    }

    public function onDeath(PlayerDeathEvent $event): void {
        if ($this->config->get("开启死亡不掉落")) {
            $event->setKeepInventory(true);
        }
        if ($this->config->get("开启死亡随机掉落物品")) {
            $player = $event->getPlayer();
            foreach ($player->getInventory()->getContents() as $item) {
                if (mt_rand(0, 100) >= $this->config->get("物品掉落几率")) {
                    $player->getInventory()->remove($item);
                    $player->getLevel()->dropItem($player, $item);
                }
            }
        }
    }

}
