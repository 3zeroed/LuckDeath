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
namespace Anders\LuckDeath;
class Main extends \pocketmine\plugin\PluginBase implements \pocketmine\event\Listener{
    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this ,$this);
        if(!is_dir($this->getDataFolder())) @mkdir($this->getDataFolder(), 0777, true);
        if(!file_exists($this->getDataFolder()."config.yml")) $this->saveResource("config.yml");
        $this->config = new \pocketmine\utils\config($this->getDataFolder()."config.yml", \pocketmine\utils\Config::YAML);
    }
    public function onDeath(\pocketmine\event\player\PlayerDeathEvent $event):void{
        if ($this->config->get("开启死亡不掉落")) $event->setKeepInventory(true);
        if ($this->config->get("开启死亡随机掉落物品")){
            foreach ($event->getPlayer()->getInventory()->getContents() as $item){
                if (mt_rand(0,100) >= $this->config->get("物品掉落几率")){
                    $event->getPlayer()->getInventory()->remove($item);
                    $event->getPlayer()->getLevel()->dropItem(new \pocketmine\math\Vector3($event->getPlayer()->getX(),$event->getPlayer()->getY(),$event->getPlayer()->getZ()), $item);
                    
                }
            }
        }
    }
}
?>