<?php

namespace ryun42680\classelection;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use ryun42680\classelection\command\ElectionCommand;
use ryun42680\lib\provider\DataProvider;
use ryun42680\lib\provider\ProviderHandler;

final class ClassElection extends PluginBase {

    public const PREFIX_1 = '§l학급 선거 | §r';
    public const PREFIX_2 = '§l§b선거 공지 | §r';

    private DataProvider $provider;

    protected function onEnable(): void {
        $this->getServer()->getCommandMap()->register(strtolower($this->getName()), new ElectionCommand($this));
    }

    protected function onDisable(): void {
        $this->provider->save();
    }

    protected function onLoad(): void {
        if (!ProviderHandler::isRegistered($this)) {
            ProviderHandler::register($this, Candidate::class);
            $this->provider = ProviderHandler::get($this);
        }
    }

    /** @return Candidate[] */
    public function getCandidates():array {
        return $this->provider->getAll();
    }

    public function isAlreadyVoted(Player $player): bool {
        /** @var Candidate $candidate */
        foreach ($this->provider->getAll() as $candidate)
            if ($candidate->isAlreadyVoted($player)) return true;
        return false;
    }

    public function isExisted(Player $player): bool {
        return !is_null($this->provider->getObject($player->getName()));
    }

    public function addCandidate(Player $player): void {
        $this->provider->setObject($player, new Candidate($player->getName(), []));
    }

    public function clearAll():void{
        foreach (array_keys($this->provider->getAll()) as $candidateId){
            $this->provider->deleteObject($candidateId);
        }
    }
}