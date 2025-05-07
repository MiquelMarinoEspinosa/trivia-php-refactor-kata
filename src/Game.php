<?php

namespace Game;

final class Game
{
    private array $players;

    public private(set) array $popQuestions;
    public private(set) array $scienceQuestions;
    public private(set) array $sportsQuestions;
    public private(set) array $rockQuestions;

    private object $gameCalculator;

    public function __construct()
    {
        $this->gameCalculator = new class(){
            private array $playersProcess;
            private int $currentPlayer;
            private bool $isGettingOutOfPenaltyBox;
            private array $inPenaltyBox;
            private array $purses;
            private array $places;

            public function __construct()
            {
                $this->playersProcess = [];
                $this->currentPlayer = 0;
                $this->isGettingOutOfPenaltyBox = true;
                $this->inPenaltyBox = [0];
                $this->purses  = [0];
                $this->places = [0];
            }

            public function processAdd(string $playerName): void
            {
                array_push($this->playersProcess, $playerName);
                $this->inPenaltyBox[$this->howManyPlayers()] = false;
                $this->purses[$this->howManyPlayers()] = 0;
                $this->places[$this->howManyPlayers()] = 0;
            }

            public function howManyPlayers(): int
            {
                return count($this->playersProcess);
            }

            public function currentPlayer(): int
            {
                return $this->currentPlayer;
            }

            public function nextPlayer(): void
            {
                $this->currentPlayer++;
                if ($this->currentPlayer() == $this->howManyPlayers()) {
                    $this->currentPlayer = 0;
                }
            }

            public function isCurrentPlayerNowGettingOutOfPenaltyBox(): bool
            {
                return $this->isGettingOutOfPenaltyBox;
            }

            public function setIsGettingOutOfPenaltyBox(bool $value): void
            {
                $this->isGettingOutOfPenaltyBox = $value;
            }

            public function isCurrentPlayerInPenaltyBox(): bool
            {
                return $this->inPenaltyBox[$this->currentPlayer()];
            }

            public function isPlayerInPenaltyBox(int $player): bool
            {
                return $this->inPenaltyBox[$player];
            }

            public function addCurrentPlayerToPenaltyBox(): void
            {
                $this->inPenaltyBox[$this->currentPlayer()] = true;
            }

            public function pursesBy(int $player): int
            {
                return $this->purses[$player];
            }

            public function increasePursesFor(int $player): void
            {
                $this->purses[$player]++;
            }

            public function currentPlayerPlaces(): int
            {
                return $this->places[$this->currentPlayer()];        
            }

            public function increaseCurrentPlayerPlacesBy(int $roll): void
            {
                $this->places[$this->currentPlayer()] = $this->currentPlayerPlaces() + $roll;
                if ($this->currentPlayerPlaces() > 11) {
                    $this->places[$this->currentPlayer()] = $this->currentPlayerPlaces() - 12;
                }
            }

            public function isPlayable(): bool
            {
                return ($this->howManyPlayers() >= 2);
            }

            public function isCurrentPlayerGettingOutOfPenaltyBox(): bool
            {
                if (!$this->isCurrentPlayerInPenaltyBox()) {
                    return true;
                }

                return $this->isCurrentPlayerNowGettingOutOfPenaltyBox();
            }

            public function processRoll(int $roll): void
            {
                if ($this->isCurrentPlayerInPenaltyBox()) {
                    $this->setIsGettingOutOfPenaltyBox($roll % 2 != 0);
                }
        
                if ($this->isCurrentPlayerGettingOutOfPenaltyBox() === false) {
                    return;
                }
        
                $this->increaseCurrentPlayerPlacesBy($roll);
            }

            public function processCorrectAnswer(): void
            {
                $player = $this->currentPlayer();

                $this->nextPlayer();

                if ($this->isCurrentPlayerGettingOutOfPenaltyBox() === false) {
                    return;
                }

                $this->increasePursesFor($player);
            }
        };

        $this->players = [];
        
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
        $this->gameCalculator->processAdd($playerName);

        $this->printAdd($playerName);

        return true;
    }

    public function roll(int $roll): void
    {
        $player = $this->gameCalculator->currentPlayer();

        $this->gameCalculator->processRoll($roll);

        $this->printRoll($roll, $player);
    }

    public function wasCorrectlyAnswered(): bool
    {
        $player = $this->gameCalculator->currentPlayer();

        $this->gameCalculator->processCorrectAnswer();
        
        return $this->printAnswerCorrect($player);
    }

    public function wrongAnswer(): bool
    {
        $this->printWrongAnswer();
        $this->processWrongAnswer();
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

    private function processWrongAnswer(): void
    {
        $this->gameCalculator->addCurrentPlayerToPenaltyBox();

        $this->gameCalculator->nextPlayer();
    }

    private function didPlayerWin(int $player): bool
    {
        return !($this->gameCalculator->pursesBy($player) == 6);
    }

    private function printAdd(string $playerName): void
    {
        array_push($this->players, $playerName);
        $this->echoln($playerName . " was added");
        $this->echoln("They are player number " . count($this->players));
    }

    private function printRoll(int $roll, int $player): void
    {
        $this->echoln($this->players[$player] . " is the current player");
        $this->echoln("They have rolled a " . $roll);

        $this->printPenaltyBoxMessage($roll, $player);

        if ($this->gameCalculator->isCurrentPlayerGettingOutOfPenaltyBox() === false) {
            return;
        }

        $this->echoln($this->players[$this->gameCalculator->currentPlayer()]
                    . "'s new location is "
                    .$this->gameCalculator->currentPlayerPlaces());
        $this->echoln("The category is " . $this->currentCategory());
        $this->askQuestion();        
    }

    private function printPenaltyBoxMessage(int $roll, int $player): void 
    {
        if (!$this->gameCalculator->isPlayerInPenaltyBox($player)) {
            return;
        }

        $this->echoln($this->players[$player] . $this->buildPenaltyBoxMessage($roll));
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
        $this->echoln($this->players[$player]
                . " now has "
                . $this->gameCalculator->pursesBy($player)
                . " Gold Coins.");

        return $this->didPlayerWin($player);
    }

    private function printWrongAnswer(): void 
    {
        $this->echoln("Question was incorrectly answered");
        $this->echoln($this->players[$this->gameCalculator->currentPlayer()] . " was sent to the penalty box");
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
