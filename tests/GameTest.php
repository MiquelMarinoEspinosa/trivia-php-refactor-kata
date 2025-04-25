<?php

declare(strict_types=1);

namespace Tests;

use Game\Game;
use PHPUnit\Framework\TestCase;

final class GameTest extends TestCase
{
    public function testCreateGame(): void
    {
        $game = new Game();

        self::assertInstanceOf(Game::class, $game);
    }
}
