<?php

namespace Game;

final class Game
{
    private array $players;
    private array $places;
    private array $purses;
    private array $inPenaltyBox;

    public private(set) array $popQuestions;
    public private(set) array $scienceQuestions;
    public private(set) array $sportsQuestions;
    public private(set) array $rockQuestions;

    private int $currentPlayer;
    private bool $isGettingOutOfPenaltyBox;

    public function __construct()
    {
        $this->currentPlayer = 0;
        $this->players = [];
        $this->places = [0];
        $this->purses  = [0];
        $this->inPenaltyBox  = [0];
        
        $this->popQuestions = [];
        $this->scienceQuestions = [];
        $this->sportsQuestions = [];
        $this->rockQuestions = [];

        for ($i = 0; $i < 50; $i++) {
            array_push($this->popQuestions, "Pop Question " . $i);
            array_push($this->scienceQuestions, ("Science Question " . $i));
            array_push($this->sportsQuestions, ("Sports Question " . $i));
            array_push($this->rockQuestions, $this->createRockQuestion($i));
        }
    }

    public function add(string $playerName): bool
    {
        array_push($this->players, $playerName);
        $this->places[$this->howManyPlayers()] = 0;
        $this->purses[$this->howManyPlayers()] = 0;
        $this->inPenaltyBox[$this->howManyPlayers()] = false;

        $this->echoln($playerName . " was added");
        $this->echoln("They are player number " . count($this->players));
        return true;
    }

    public function roll(int $roll): void
    {
        $this->printPreRollMessage($roll);

        $this->processRoll($roll);

        $this->printPostRollMessage();
    }

    public function wasCorrectlyAnswered(): bool
    {
        $player = $this->currentPlayer;

        if ($this->inPenaltyBox[$this->currentPlayer] && !$this->isGettingOutOfPenaltyBox) {
            $this->currentPlayer++;
            if ($this->currentPlayer == count($this->players)) {
                $this->currentPlayer = 0;
            }
        }

        if ($this->inPenaltyBox[$this->currentPlayer] && !$this->isGettingOutOfPenaltyBox) {
            return true;
        }

        $this->purses[$this->currentPlayer]++;

        $this->currentPlayer++;
        if ($this->currentPlayer == count($this->players)) {
            $this->currentPlayer = 0;
        }
        
        return $this->printAnswerCorrect($player);
    }

    public function wrongAnswer(): bool
    {
        $this->echoln("Question was incorrectly answered");
        $this->echoln($this->players[$this->currentPlayer] . " was sent to the penalty box");
        $this->inPenaltyBox[$this->currentPlayer] = true;

        $this->currentPlayer++;
        if ($this->currentPlayer == count($this->players)) {
            $this->currentPlayer = 0;
        }
        return true;
    }

    public function isPlayable(): bool
    {
        return ($this->howManyPlayers() >= 2);
    }

    private function createRockQuestion(int $index): string
    {
        return "Rock Question " . $index;
    }

    private function howManyPlayers(): int
    {
        return count($this->players);
    }

    private function printPreRollMessage(int $roll): void
    {
        $this->echoln($this->players[$this->currentPlayer] . " is the current player");
        $this->echoln("They have rolled a " . $roll);

        $this->printPenaltyBoxMessage($roll);
    }

    private function processRoll(int $roll): void
    {
        if ($this->inPenaltyBox[$this->currentPlayer]) {
            $this->isGettingOutOfPenaltyBox = $roll % 2 != 0;
        }

        if ($this->isCurrentPlayerGettingOutOfPenaltyBox() === false) {
            return;
        }

        $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] + $roll;
        if ($this->places[$this->currentPlayer] > 11) {
            $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] - 12;
        }
    }

    private function isCurrentPlayerGettingOutOfPenaltyBox(): bool
    {
        if (!$this->inPenaltyBox[$this->currentPlayer]) {
            return true;
        }

        return $this->isGettingOutOfPenaltyBox === true;
    }

    private function printPostRollMessage(): void
    {
        if ($this->isCurrentPlayerGettingOutOfPenaltyBox() === false) {
            return;
        }

        $this->echoln($this->players[$this->currentPlayer]
                    . "'s new location is "
                    .$this->places[$this->currentPlayer]);
        $this->echoln("The category is " . $this->currentCategory());
        $this->askQuestion();
    }

    private function printAnswerCorrect(string $player): bool
    {
        $this->echoln("Answer was correct!!!!");
        $this->echoln($this->players[$player]
                . " now has "
                . $this->purses[$player]
                . " Gold Coins.");

        return $this->didPlayerWin($player);
    }

    private function printPenaltyBoxMessage(int $roll): void 
    {
        if (!$this->inPenaltyBox[$this->currentPlayer]) {
            return;
        }

        $this->echoln($this->players[$this->currentPlayer] . $this->buildPenaltyBoxMessage($roll));
    }

    private function buildPenaltyBoxMessage(int $roll): string
    {
        if ($roll % 2 != 0) {
            return " is getting out of the penalty box";
        }

        return " is not getting out of the penalty box";
    }

    private function askQuestion(): void
    {
        if ($this->currentCategory() == "Pop") {
            $this->echoln(array_shift($this->popQuestions));
        }
        if ($this->currentCategory() == "Science") {
            $this->echoln(array_shift($this->scienceQuestions));
        }
        if ($this->currentCategory() == "Sports") {
            $this->echoln(array_shift($this->sportsQuestions));
        }
        if ($this->currentCategory() == "Rock") {
            $this->echoln(array_shift($this->rockQuestions));
        }
    }

    private function currentCategory(): string
    {
        if ($this->places[$this->currentPlayer] == 0) {
            return "Pop";
        }
        if ($this->places[$this->currentPlayer] == 4) {
            return "Pop";
        }
        if ($this->places[$this->currentPlayer] == 8) {
            return "Pop";
        }
        if ($this->places[$this->currentPlayer] == 1) {
            return "Science";
        }
        if ($this->places[$this->currentPlayer] == 5) {
            return "Science";
        }
        if ($this->places[$this->currentPlayer] == 9) {
            return "Science";
        }
        if ($this->places[$this->currentPlayer] == 2) {
            return "Sports";
        }
        if ($this->places[$this->currentPlayer] == 6) {
            return "Sports";
        }
        if ($this->places[$this->currentPlayer] == 10) {
            return "Sports";
        }
        return "Rock";
    }

    private function didPlayerWin(string $player): bool
    {
        return !($this->purses[$player] == 6);
    }

    private function echoln($string): void
    {
        echo $string."\n";
    }
}
