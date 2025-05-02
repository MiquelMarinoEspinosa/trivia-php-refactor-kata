<?php

namespace Game;

class Game
{
    private array $players;
    private array $places;
    private array $purses;
    private array $inPenaltyBox;

    public private(set) array $popQuestions;
    public private(set) array $scienceQuestions;
    public private(set) array $sportsQuestions;
    public private(set) array $rockQuestions;

    private int $currentPlayer = 0;
    private bool $isGettingOutOfPenaltyBox;

    public function __construct()
    {

        $this->players = array();
        $this->places = array(0);
        $this->purses  = array(0);
        $this->inPenaltyBox  = array(0);

        $this->popQuestions = array();
        $this->scienceQuestions = array();
        $this->sportsQuestions = array();
        $this->rockQuestions = array();

        for ($i = 0; $i < 50; $i++) {
            array_push($this->popQuestions, "Pop Question " . $i);
            array_push($this->scienceQuestions, ("Science Question " . $i));
            array_push($this->sportsQuestions, ("Sports Question " . $i));
            array_push($this->rockQuestions, $this->createRockQuestion($i));
        }
    }

    private function createRockQuestion(int $index): string
    {
        return "Rock Question " . $index;
    }

    public function isPlayable(): bool
    {
        return ($this->howManyPlayers() >= 2);
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

    private function howManyPlayers(): int
    {
        return count($this->players);
    }

    public function roll(int $roll): void
    {
        $this->echoln($this->players[$this->currentPlayer] . " is the current player");
        $this->echoln("They have rolled a " . $roll);

        if ($this->inPenaltyBox[$this->currentPlayer]) {
            if ($roll % 2 != 0) {
                $this->isGettingOutOfPenaltyBox = true;

                $this->echoln($this->players[$this->currentPlayer] . " is getting out of the penalty box");
                $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] + $roll;
                if ($this->places[$this->currentPlayer] > 11) {
                    $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] - 12;
                }

                $this->echoln($this->players[$this->currentPlayer]
                        . "'s new location is "
                        .$this->places[$this->currentPlayer]);
                $this->echoln("The category is " . $this->currentCategory());
                $this->askQuestion();
            } else {
                $this->echoln($this->players[$this->currentPlayer] . " is not getting out of the penalty box");
                $this->isGettingOutOfPenaltyBox = false;
            }
        } else {
            $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] + $roll;
            if ($this->places[$this->currentPlayer] > 11) {
                $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] - 12;
            }

            $this->echoln($this->players[$this->currentPlayer]
                    . "'s new location is "
                    .$this->places[$this->currentPlayer]);
            $this->echoln("The category is " . $this->currentCategory());
            $this->askQuestion();
        }

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

    public function wasCorrectlyAnswered(): bool
    {
        if ($this->inPenaltyBox[$this->currentPlayer]) {
            if ($this->isGettingOutOfPenaltyBox) {
                $this->echoln("Answer was correct!!!!");
                $this->purses[$this->currentPlayer]++;
                $this->echoln($this->players[$this->currentPlayer]
                        . " now has "
                        .$this->purses[$this->currentPlayer]
                        . " Gold Coins.");

                $winner = $this->didPlayerWin();
                $this->currentPlayer++;
                if ($this->currentPlayer == count($this->players)) {
                    $this->currentPlayer = 0;
                }

                return $winner;
            } else {
                $this->currentPlayer++;
                if ($this->currentPlayer == count($this->players)) {
                    $this->currentPlayer = 0;
                }
                return true;
            }
        } else {
            $this->echoln("Answer was corrent!!!!");
            $this->purses[$this->currentPlayer]++;
            $this->echoln($this->players[$this->currentPlayer]
                    . " now has "
                    .$this->purses[$this->currentPlayer]
                    . " Gold Coins.");

            $winner = $this->didPlayerWin();
            $this->currentPlayer++;
            if ($this->currentPlayer == count($this->players)) {
                $this->currentPlayer = 0;
            }

            return $winner;
        }
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

    private function didPlayerWin(): bool
    {
        return !($this->purses[$this->currentPlayer] == 6);
    }

    private function echoln($string)
    {
        echo $string."\n";
    }
}
