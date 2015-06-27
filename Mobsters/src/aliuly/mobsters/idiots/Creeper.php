<?php
namespace aliuly\mobsters\idiots;

use pocketmine\item\Item;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\Network;

use pocketmine\Player;
use pocketmine\entity\Monster;
use pocketmine\entity\Explosive;


class Creeper extends Monster implements Explosive{
	const NETWORK_ID = 33;

	public $width = 0.625;
	public $length = 1.4375;
	public $height = 1.25;

	public function getName(){
		return "Creeper";
	}

	public function spawnTo(Player $player){

		$pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = self::NETWORK_ID;
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = $this->motionX;
		$pk->speedY = $this->motionY;
		$pk->speedZ = $this->motionZ;
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk->setChannel(Network::CHANNEL_ENTITY_SPAWNING));
		parent::spawnTo($player);
	}

	public function getDrops(){
		$drops = [];
		$rand = mt_rand(0,2);
		if($rand){
			$drops[] = Item::get(Item::GUNPOWDER,0,$rand);
		}
		return $drops;
	}
	public function explode(){
		$this->server->getPluginManager()->callEvent($ev = new ExplosionPrimeEvent($this, 4));

		if(!$ev->isCancelled()){
			$explosion = new Explosion($this, $ev->getForce(), $this);
			if($ev->isBlockBreaking()){
				$explosion->explodeA();
			}
			$explosion->explodeB();
		}
	}

}
