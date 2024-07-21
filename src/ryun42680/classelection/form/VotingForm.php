<?php

namespace ryun42680\classelection\form;

use pocketmine\form\Form;
use pocketmine\player\Player;
use ryun42680\classelection\Candidate;
use ryun42680\classelection\ClassElection;

final class VotingForm implements Form {

    private array $candidates;

    public function __construct(ClassElection $plugin) {
        $this->candidates = $plugin->getCandidates();
    }

    public function jsonSerialize(): array {
        return [
            'type' => 'form',
            'title' => '§l멀티버스 학급 선거',
            'content' => " \n> 투표를 원하는 사람을 클릭해주세요!. \n ",
            'buttons' => array_map(function (Candidate $candidate): array {
                return [
                    'text' => '§l§0> ' . $candidate->getCandidateId() . ' <'
                ];
            }, array_values($this->candidates))
        ];
    }

    public function handleResponse(Player $player, $data): void {
        if (is_numeric($data)) {
            $candidate = array_values($this->candidates) [$data] ?? null;
            if ($candidate instanceof Candidate) {
                $candidate->addVoter($player);
                $player->sendMessage(ClassElection::PREFIX_1 . $candidate->getCandidateId() . '에게 투표권을 사용했습니다.');
            } else {
                $player->sendMessage(ClassElection::PREFIX_1 . '알 수 없는 오류가 발생했습니다.');
            }
        }
    }
}