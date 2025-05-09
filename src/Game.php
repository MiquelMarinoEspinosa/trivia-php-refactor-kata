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
     * @var array<array<string>>
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
                sprintf("%s Question %u", $category, $numQuestion)
            );
        }
    }

    private function printAdd(string $playerName): void
    {
        $this->echoln(sprintf("%s was added", $playerName));
        $this->echoln(sprintf("They are player number %s", $this->gameCalculator->numPlayers()));
    }

    private function printRoll(int $roll, int $player): void
    {
        $this->echoln(sprintf("%s is the current player", $this->gameCalculator->nameBy($player)));
        $this->echoln(sprintf("They have rolled a %u", $roll));

        $this->printPenaltyBoxMessage($roll, $player);

        if ($this->gameCalculator->isCurrentPlayerGettingOutOfPenaltyBox() === false) {
            return;
        }

        $this->echoln(
            sprintf(
                "%s's new location is %u",
                $this->currentPlayerName(),
                $this->gameCalculator->currentPlayerPlaces())
        );
        $this->echoln(sprintf("The category is %s", $this->currentCategory()));
        $this->askQuestion();
    }

    private function currentPlayerName(): string
    {
        return $this->gameCalculator->nameBy(
            $this->gameCalculator->currentPlayer()
        );
    }

    private function printPenaltyBoxMessage(int $roll, int $player): void
    {
        if (!$this->gameCalculator->isPlayerInPenaltyBox($player)) {
            return;
        }

        $this->echoln(
            sprintf(
                "%s %s getting out of the penalty box",
                $this->gameCalculator->nameBy($player),
                $this->gameCalculator->isGettingOutOfPenaltyBoxBy($roll)
                    ? "is" : "is not"
            )
        );
    }

    private function printAnswerCorrect(string $player): bool
    {
        if ($this->gameCalculator->isCurrentPlayerGettingOutOfPenaltyBox() === false) {
            return true;
        }

        $this->echoln("Answer was correct!!!!");
        $this->echoln(
            sprintf(
                "%s now has %u Gold Coins.",
                $this->gameCalculator->nameBy($player),
                $this->gameCalculator->pursesBy($player),
            )
        );

        return $this->gameCalculator->didPlayerWin($player);
    }

    private function printWrongAnswer(): void
    {
        $this->echoln("Question was incorrectly answered");
        $this->echoln(
            sprintf(
                "%s was sent to the penalty box",
                $this->currentPlayerName()
            )
        );
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

    private function echoln(string $text): void
    {
        echo sprintf("%s\n", $text);
    }
}
