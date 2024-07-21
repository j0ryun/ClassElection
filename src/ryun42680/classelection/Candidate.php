<?php

namespace ryun42680\classelection;

use pocketmine\player\Player;

final class Candidate implements \JsonSerializable {

    public function __construct(private readonly string $candidateId, protected array $voters) { }

    public function getCandidateId(): string {
        return $this->candidateId;
    }

    public function getCount(): int {
        return count($this->voters);
    }

    public function isAlreadyVoted(Player $player): bool {
        return in_array($player->getName(), $this->voters);
    }

    public function addVoter(Player $player): void {
        $this->voters [] = $player->getName();
    }

    public function jsonSerialize(): array {
        return [
            $this->candidateId,
            $this->voters
        ];
    }

    public static function jsonDeserialize(array $data): self {
        return new self (... $data);
    }
}