<?php

namespace Game;

final class Game
{
    private const int MAX_NUM_QUESTIONS = 50;
    private const string POP_CATEGORY = "Pop";
    private const string SCIENCE_CATEGORY = "Science";
    private const string SPORTS_CATEGORY = "Sports";
    private const string ROCK_CATEGORY = "Rock";
    /**
     * @var array<string>
     */
    private const array CATEGORIES = [
        self::POP_CATEGORY,
        self::SCIENCE_CATEGORY,
        self::SPORTS_CATEGORY,
        self::ROCK_CATEGORY
    ];

    /**
     * @var array<string>
     */
    private array $questions;

    private GameCalculator $gameCalculator;

    public function __construct()
    {
        $this->gameCalculator = new GameCalculator();

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

    public function totalNumQuestions(): int
    {
        $totalNumQuestions = 0;

        return array_reduce(
            $this->questions,
            static fn(int $totalNumQuestions, array $categoryQuestions)
                => $totalNumQuestions + count($categoryQuestions),
            $totalNumQuestions
        );
    }

    private function createQuestions(): void
    {
        $this->questions = [
            self::POP_CATEGORY => [],
            self::SCIENCE_CATEGORY => [],
            self::SPORTS_CATEGORY => [],
            self::ROCK_CATEGORY => []
        ];

        for ($numQuestion = 0; $numQuestion < self::MAX_NUM_QUESTIONS; $numQuestion++) {
            $this->createCategoryQuestionsBy($numQuestion);
        }
    }

    private function createCategoryQuestionsBy(int $numQuestion): void
    {
        foreach (self::CATEGORIES as $category) {
            array_push(
                $this->questions[$category],
                sprintf("%s Question %d", $category, $numQuestion)
            );
        }
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
        $this->echoln(
            array_shift(
                $this->questions[$this->currentCategory()]
            )
        );
    }

    private function currentCategory(): string
    {
        return match($this->gameCalculator->currentPlayerPlaces()) {
            0,4,8   => self::POP_CATEGORY,
            1,5,9   => self::SCIENCE_CATEGORY,
            2,6,10  => self::SPORTS_CATEGORY,
            default => self::ROCK_CATEGORY
        };
    }

    private function echoln($string): void
    {
        echo $string."\n";
    }
}
