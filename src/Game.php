<?php

namespace Game;

final class Game
{
    private const int MAX_NUM_QUESTIONS = 50;

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

        $this->createQuestions();
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

    private function createQuestions(): void
    {
        for ($numQuestion = 0; $numQuestion < self::MAX_NUM_QUESTIONS; $numQuestion++) {
            $this->createCategoryQuestionsBy($numQuestion);
        }
    }

    private function createCategoryQuestionsBy(int $numQuestion): void
    {
        array_push($this->popQuestions, "Pop Question " . $numQuestion);
        array_push($this->scienceQuestions, "Science Question " . $numQuestion);
        array_push($this->sportsQuestions, "Sports Question " . $numQuestion);
        array_push($this->rockQuestions, "Rock Question " . $numQuestion);
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
        if ($this->gameCalculator->isGettingOutOfPenaltyBoxBy($roll)) {
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
        $this->echoln($this->currentQuestion());
    }

    private function currentQuestion(): string
    {
        $question = match($this->currentCategory()) {
            "Pop"       => array_shift($this->popQuestions),
            "Science"   => array_shift($this->scienceQuestions),
            default     => ""
        };

        if ($this->currentCategory() == "Sports") {
            $question = array_shift($this->sportsQuestions);
        }
        if ($this->currentCategory() == "Rock") {
            $question = array_shift($this->rockQuestions);
        }

        return $question;
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
