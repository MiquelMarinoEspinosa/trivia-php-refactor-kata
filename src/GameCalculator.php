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
        $this->places = [0];
    }

    public function add(string $playerName): void
    {
        array_push($this->players, $playerName);
        $this->inPenaltyBox[$this->numPlayers()] = false;
        $this->purses[$this->numPlayers()] = 0;
        $this->places[$this->numPlayers()] = 0;
    }

    public function roll(int $roll): void
    {
        if ($this->isCurrentPlayerInPenaltyBox()) {
            $this->isGettingOutOfPenaltyBox = $roll % 2 != 0;
        }

        if ($this->isCurrentPlayerGettingOutOfPenaltyBox() === false) {
            return;
        }

        $this->places[$this->currentPlayer()] = $this->currentPlayerPlaces() + $roll;
        if ($this->currentPlayerPlaces() > 11) {
            $this->places[$this->currentPlayer()] = $this->currentPlayerPlaces() - 12;
        }
    }

    public function correctAnswer(): void
    {
        $player = $this->currentPlayer();

        $this->nextPlayer();

        if ($this->isCurrentPlayerGettingOutOfPenaltyBox() === false) {
            return;
        }

        $this->purses[$player]++;
    }

    public function wrongAnswer(): void
    {
        $this->inPenaltyBox[$this->currentPlayer()] = true;

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
        return ($this->numPlayers() >= 2);
    }

    public function isCurrentPlayerGettingOutOfPenaltyBox(): bool
    {
        if (!$this->isCurrentPlayerInPenaltyBox()) {
            return true;
        }

        return $this->isGettingOutOfPenaltyBox;
    }

    public function didPlayerWin(int $player): bool
    {
        return !($this->pursesBy($player) == 6);
    }

    public function numPlayers(): int
    {
        return count($this->players);
    }

    public function nameBy(int $player): string
    {
        return $this->players[$player];
    }

    private function isCurrentPlayerInPenaltyBox(): bool
    {
        return $this->inPenaltyBox[$this->currentPlayer()];
    }

    private function nextPlayer(): void
    {
        $this->currentPlayer++;
        if ($this->currentPlayer() == $this->numPlayers()) {
            $this->currentPlayer = 0;
        }
    }
}