<?php

declare(strict_types=1);

namespace Game;

final class GameCalculator
{
    private array $players;
    private int $currentPlayer;
    private bool $isGettingOutOfPenaltyBox;
    private array $inPenaltyBox;
    private array $purses;
    private array $places;

    public function __construct()
    {
        $this->players = [];
        $this->currentPlayer = 0;
        $this->isGettingOutOfPenaltyBox = true;
        $this->inPenaltyBox = [false];
        $this->purses  = [0];
        $this->places = [0];
    }

    public function add(string $playerName): void
    {
        array_push($this->players, $playerName);
        $this->inPenaltyBox[$this->howManyPlayers()] = false;
        $this->purses[$this->howManyPlayers()] = 0;
        $this->places[$this->howManyPlayers()] = 0;
    }

    public function roll(int $roll): void
    {
        if ($this->isCurrentPlayerInPenaltyBox()) {
            $this->setIsGettingOutOfPenaltyBox($roll % 2 != 0);
        }

        if ($this->isCurrentPlayerGettingOutOfPenaltyBox() === false) {
            return;
        }

        $this->increaseCurrentPlayerPlacesBy($roll);
    }

    public function correctAnswer(): void
    {
        $player = $this->currentPlayer();

        $this->nextPlayer();

        if ($this->isCurrentPlayerGettingOutOfPenaltyBox() === false) {
            return;
        }

        $this->increasePursesFor($player);
    }

    public function wrongAnswer(): void
    {
        $this->addCurrentPlayerToPenaltyBox();

        $this->nextPlayer();
    }

    public function currentPlayer(): int
    {
        return $this->currentPlayer;
    }

    public function isPlayerInPenaltyBox(int $player): bool
    {
        return $this->inPenaltyBox[$player];
    }

    public function pursesBy(int $player): int
    {
        return $this->purses[$player];
    }

    public function currentPlayerPlaces(): int
    {
        return $this->places[$this->currentPlayer()];
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

    public function didPlayerWin(int $player): bool
    {
        return !($this->pursesBy($player) == 6);
    }

    private function increaseCurrentPlayerPlacesBy(int $roll): void
    {
        $this->places[$this->currentPlayer()] = $this->currentPlayerPlaces() + $roll;
        if ($this->currentPlayerPlaces() > 11) {
            $this->places[$this->currentPlayer()] = $this->currentPlayerPlaces() - 12;
        }
    }

    private function increasePursesFor(int $player): void
    {
        $this->purses[$player]++;
    }

    private function addCurrentPlayerToPenaltyBox(): void
    {
        $this->inPenaltyBox[$this->currentPlayer()] = true;
    }

    private function isCurrentPlayerInPenaltyBox(): bool
    {
        return $this->inPenaltyBox[$this->currentPlayer()];
    }

    private function setIsGettingOutOfPenaltyBox(bool $value): void
    {
        $this->isGettingOutOfPenaltyBox = $value;
    }

    private function isCurrentPlayerNowGettingOutOfPenaltyBox(): bool
    {
        return $this->isGettingOutOfPenaltyBox;
    }

    private function nextPlayer(): void
    {
        $this->currentPlayer++;
        if ($this->currentPlayer() == $this->howManyPlayers()) {
            $this->currentPlayer = 0;
        }
    }

    private function howManyPlayers(): int
    {
        return count($this->players);
    }
}