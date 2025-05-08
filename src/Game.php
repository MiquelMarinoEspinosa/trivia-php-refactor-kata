<?php

namespace Game;

final class Game
{
    public private(set) array $popQuestions;
    public private(set) array $scienceQuestions;
    public private(set) array $sportsQuestions;
    public private(set) array $rockQuestions;

    private GameCalculator $gameCalculator;

    public function __construct()
    {
        $this->gameCalculator = new GameCalculator();

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
        $this->gameCalculator->add($playerName);

        $this->printAdd($playerName);

        return true;
    }

    public function roll(int $roll): void
    {
        $player = $this->gameCalculator->currentPlayer();

        $this->gameCalculator->roll($roll);

        $this->printRoll($roll, $player);
    }

    public function wasCorrectlyAnswered(): bool
    {
        $player = $this->gameCalculator->currentPlayer();

        $this->gameCalculator->correctAnswer();

        return $this->printAnswerCorrect($player);
    }

    public function wrongAnswer(): bool
    {
        $this->printWrongAnswer();
        $this->gameCalculator->wrongAnswer();
        return true;
    }

    public function isPlayable(): bool
    {
        return $this->gameCalculator->isPlayable();
    }

    private function createRockQuestion(int $index): string
    {
        return "Rock Question " . $index;
    }

    private function printAdd(string $playerName): void
    {
        $this->echoln($playerName . " was added");
        $this->echoln("They are player number " . $this->gameCalculator->numPlayers());
    }

    private function printRoll(int $roll, int $player): void
    {
        $this->echoln($this->gameCalculator->nameBy($player) . " is the current player");
        $this->echoln("They have rolled a " . $roll);

        $this->printPenaltyBoxMessage($roll, $player);

        if ($this->gameCalculator->isCurrentPlayerGettingOutOfPenaltyBox() === false) {
            return;
        }

        $this->echoln($this->gameCalculator->nameBy(
            $this->gameCalculator->currentPlayer()
        ) . "'s new location is "
          .$this->gameCalculator->currentPlayerPlaces());
        $this->echoln("The category is " . $this->currentCategory());
        $this->askQuestion();
    }

    private function printPenaltyBoxMessage(int $roll, int $player): void
    {
        if (!$this->gameCalculator->isPlayerInPenaltyBox($player)) {
            return;
        }

        $this->echoln($this->gameCalculator->nameBy($player) . $this->buildPenaltyBoxMessage($roll));
    }

    private function buildPenaltyBoxMessage(int $roll): string
    {
        if ($roll % 2 != 0) {
            return " is getting out of the penalty box";
        }

        return " is not getting out of the penalty box";
    }

    private function printAnswerCorrect(string $player): bool
    {
        if ($this->gameCalculator->isCurrentPlayerGettingOutOfPenaltyBox() === false) {
            return true;
        }

        $this->echoln("Answer was correct!!!!");
        $this->echoln($this->gameCalculator->nameBy($player)
                . " now has "
                . $this->gameCalculator->pursesBy($player)
                . " Gold Coins.");

        return $this->gameCalculator->didPlayerWin($player);
    }

    private function printWrongAnswer(): void
    {
        $this->echoln("Question was incorrectly answered");
        $this->echoln($this->gameCalculator->nameBy(
            $this->gameCalculator->currentPlayer()
        ) . " was sent to the penalty box");
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
        if ($this->gameCalculator->currentPlayerPlaces() == 0) {
            return "Pop";
        }
        if ($this->gameCalculator->currentPlayerPlaces() == 4) {
            return "Pop";
        }
        if ($this->gameCalculator->currentPlayerPlaces() == 8) {
            return "Pop";
        }
        if ($this->gameCalculator->currentPlayerPlaces() == 1) {
            return "Science";
        }
        if ($this->gameCalculator->currentPlayerPlaces() == 5) {
            return "Science";
        }
        if ($this->gameCalculator->currentPlayerPlaces() == 9) {
            return "Science";
        }
        if ($this->gameCalculator->currentPlayerPlaces() == 2) {
            return "Sports";
        }
        if ($this->gameCalculator->currentPlayerPlaces() == 6) {
            return "Sports";
        }
        if ($this->gameCalculator->currentPlayerPlaces() == 10) {
            return "Sports";
        }
        return "Rock";
    }

    private function echoln($string): void
    {
        echo $string."\n";
    }
}
