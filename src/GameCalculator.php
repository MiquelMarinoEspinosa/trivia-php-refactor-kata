<?php

declare(strict_types=1);

namespace Game;

final class GameCalculator
{
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
        $this->inPenaltyBox = [false];
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

    public function processWrongAnswer(): void
    {
        $this->addCurrentPlayerToPenaltyBox();

        $this->nextPlayer();
    }

    public function didPlayerWin(int $player): bool
    {
        return !($this->pursesBy($player) == 6);
    }
}