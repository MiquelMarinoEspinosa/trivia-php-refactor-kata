<?php

declare(strict_types=1);

namespace Tests;

use Game\Game;
use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;

final class GameTest extends TestCase
{
    public function testCreateGame(): void
    {
        ob_start();

        $aGame = new Game();
  
        $aGame->add("Player1");
        $aGame->add("Player2");
        $aGame->add("Player3");

        $aGame->roll(1);
        $aGame->wrongAnswer();

        $aGame->roll(2);
        $aGame->wrongAnswer();

        $aGame->roll(3);
        $aGame->wasCorrectlyAnswered();

        $aGame->roll(4);
        $aGame->wasCorrectlyAnswered();

        $aGame->roll(5);
        $aGame->wasCorrectlyAnswered();

        $aGame->roll(6);
        $aGame->wasCorrectlyAnswered();

        $aGame->roll(3);
        $aGame->wasCorrectlyAnswered();

        $aGame->roll(6);
        $aGame->wasCorrectlyAnswered();
        
        $aGame->roll(5);
        $aGame->wrongAnswer();

        $aGame->roll(4);
        $aGame->wasCorrectlyAnswered();

        $aGame->roll(3);
        $aGame->wrongAnswer();

        $aGame->roll(2);
        $aGame->wrongAnswer();

        $aGame->roll(1);
        $aGame->wasCorrectlyAnswered();

        $aGame->roll(5);
        $aGame->wrongAnswer();

        $aGame->roll(6);
        $aGame->wasCorrectlyAnswered();

        $aGame->roll(3);
        $aGame->wrongAnswer();

        $aGame->roll(5);
        $aGame->wrongAnswer();

        $aGame->roll(2);
        $aGame->wasCorrectlyAnswered();

        $aGame->roll(5);
        $aGame->wrongAnswer();

        $aGame->roll(6);
        $aGame->wasCorrectlyAnswered();

        $aGame->roll(3);
        $aGame->wrongAnswer();

        $aGame->roll(5);
        $aGame->wrongAnswer();

        $aGame->roll(2);
        $aGame->wasCorrectlyAnswered();

        $output = ob_get_clean();

        Approvals::verifyString($output);
    }

    public function testShouldTheGameBeNotPlayableWhenZeroPlayersHaveBeenAdded(): void 
    {
        $aGame = new Game();
        
        self::assertFalse($aGame->isPlayable());
    }

    public function testShouldTheGameBeNotPlayableWhenOnePlayersHaveBeenAdded(): void 
    {
        $aGame = new Game();
        $aGame->add("Chet");

        self::assertFalse($aGame->isPlayable());
    }

    public function testShouldTheGameBePlayableWhenTwoPlayersHaveBeenAdded(): void 
    {
        $aGame = new Game();
        $aGame->add("Chet");
        $aGame->add("Pat");
        
        self::assertTrue($aGame->isPlayable());
    }
}
