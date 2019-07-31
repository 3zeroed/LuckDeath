<?php

/*
 _
 | |                 _      ____                _    _
 | |    _   _   ___ | | __ |  _ \   ___   __ _ | |_ | |__
 | |   | | | | / __|| |/ / | | | | / _ \ / _` || __|| '_ \
 | |___| |_| || (__ |   <  | |_| ||  __/| (_| || |_ | | | |
 |_____|\__,_| \___||_|\_\ |____/  \___| \__,_| \__||_| |_|
 
 LuckDeath by Anders
 GitHub: https://github.com/Anders233
 
 */
declare(strict_types=1);

namespace Anders\LuckDeath;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use onebone\economyapi\EconomyAPI;
use function is_dir;
use function mkdir;
use function file_exists;
use function mt_rand;

class Main extends PluginBase implements Listener {
    
    public $EconomyAPI = true;
    
    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        if (!is_dir($this->getDataFolder())) {
            @mkdir($this->getDataFolder(), 0777, true);
        }
        if (!file_exists($this->getDataFolder() . "config.yml")) {
            $this->saveResource("config.yml");
        }
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        if ($this->config->get("配置文件版本") == NULL OR $this->config->get("配置文件版本") !== 2){
            $this->getServer()->getLogger()->WARNING(TextFormat::BOLD . TextFormat::AQUA . "LuckDeath" . TextFormat::RESET . TextFormat::YELLOW . "配置文件版本过期，请删除旧配置文件，然后重新启动");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
        if ($this->config->get("死亡随机扣钱") and $this->getServer()->getPluginManager()->getPlugin("EconomyAPI") == NULL){
            $this->getServer()->getLogger()->WARNING(TextFormat::YELLOW . "未检测到" . TextFormat::BOLD . TextFormat::AQUA . "EconomyAPI" . TextFormat::RESET . TextFormat::YELLOW . "插件，已自动关闭死亡扣钱功能！");
            $this->EconomyAPI = FALSE;// 如果没有检测到经济核心，就把这个全局变量设置为false，方便下边事件调用。
//             $this->config->set("死亡随机扣钱", false);
//             $this->config->save();// 保存之后配置文件的注释会消失，弃用！
        }
    }
    
    public function onPlayerDeath(PlayerDeathEvent $event): void {
        if ($this->config->get("开启死亡不掉落")) {
            $event->setKeepInventory(true);
        }
        if ($this->config->get("开启死亡随机掉落物品")) {
            $player = $event->getPlayer();
            if (in_array($player->getLevel()->getFolderName(), $this->config->get("死亡随机掉落物品世界")) OR $this->config->get("死亡随机掉落物品世界") == NULL){
                foreach ($player->getInventory()->getContents() as $item) {
                    if (mt_rand(0, 100) >= $this->config->get("死亡随机掉落物品几率")) {
                        $player->getInventory()->remove($item);
                        $player->getLevel()->dropItem($player, $item);
                    }
                }
                if(isset($this->plugin->EconomyAPI)){
                    if ($this->config->get("死亡随机扣钱")){
                        $数量 = $this->config->get("死亡随机扣钱数量");
                        $浮动 = $this->config->get("死亡随机扣钱浮动");
                        $扣除钱数 = mt_rand($数量 - $浮动, $数量 + $浮动);
                        if (($数量 - $浮动) <= 0){
                            $扣除钱数 = mt_rand(0, $数量 + $浮动);
                        }
                        EconomyAPI::getInstance()->reduceMoney($player->getName(), $扣除钱数);
                    }
                }
            }
        }
    }
    
}
