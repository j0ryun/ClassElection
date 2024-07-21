<?php

namespace ryun42680\classelection\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use ryun42680\classelection\ClassElection;
use ryun42680\classelection\form\VotingForm;

final class ElectionCommand extends Command {

    public function __construct(private readonly ClassElection $plugin) {
        parent::__construct('학급선거', '충주 중산 고등학교 - 정병륜');
        $this->setPermission(DefaultPermissions::ROOT_OPERATOR);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if ($this->testPermission($sender) and $sender instanceof Player) {
            $server = $this->plugin->getServer();
            switch (array_shift($args)) {
                case '출마':
                    if (!$this->plugin->isExisted($sender)) {
                        $this->plugin->addCandidate($sender);
                        $server->broadcastMessage(ClassElection::PREFIX_2 . $sender->getName() . '(이)가 선거에 출마했습니다.');
                    } else {
                        $sender->sendMessage(ClassElection::PREFIX_1 . '이미 선거에 출마한 상태입니다. 선거 마감을 기다려주세요.');
                    }
                    break;

                case '투표':
                    if (!$this->plugin->isAlreadyVoted($sender)) {
                        $sender->sendForm(new VotingForm($this->plugin));
                    } else {
                        $sender->sendMessage(ClassElection::PREFIX_1 . '이미 투표권을 사용했습니다.');
                    }
                    break;

                case '마감':
                    $result = [];
                    foreach ($this->plugin->getCandidates() as $candidate) {
                        $result [$candidate->getCandidateId()] = $candidate->getCount();
                    }
                    arsort($result);
                    $server->broadcastMessage(ClassElection::PREFIX_2 . '학급 선거가 마감되었습니다!');
                    $rank = 1;
                    foreach ($result as $candidateId => $count) {
                        $server->broadcastMessage('§l' . $rank . '위: §r' . $candidateId . ' (' . $count . '표)');
                    }
                    $this->plugin->clearAll();
                    break;

                default:
                    $sender->sendMessage(ClassElection::PREFIX_1 . '/학급선거 출마');
                    $sender->sendMessage(ClassElection::PREFIX_1 . '/학급선거 투표');
                    $sender->sendMessage(ClassElection::PREFIX_1 . '/학급선거 마감');
            }
        }
    }
}