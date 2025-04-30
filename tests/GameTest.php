<?php

declare(strict_types=1);

namespace Tests;

use Game\Game;
use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;

final class GameTest extends TestCase
{
    public function testPlayGame(): void
    {
        ob_start();

        $aGame = new Game();
  
        $aGame->add("Player1");
        $aGame->add("Player2");

        $aGame->roll(1);
        self::assertTrue($aGame->wrongAnswer());

        $aGame->roll(2);
        self::assertTrue($aGame->wasCorrectlyAnswered());

        $aGame->roll(3);
        self::assertTrue($aGame->wasCorrectlyAnswered());

        $aGame->roll(4);
        self::assertTrue($aGame->wasCorrectlyAnswered());

        $aGame->roll(5);
        self::assertTrue($aGame->wasCorrectlyAnswered());

        $aGame->roll(6);
        self::assertTrue($aGame->wasCorrectlyAnswered());

        $aGame->roll(3);
        self::assertTrue($aGame->wasCorrectlyAnswered());

        $aGame->roll(6);
        self::assertTrue($aGame->wasCorrectlyAnswered());
        
        $aGame->roll(5);
        self::assertTrue($aGame->wrongAnswer());

        $aGame->roll(4);
        self::assertTrue($aGame->wasCorrectlyAnswered());

        $aGame->roll(3);
        self::assertTrue($aGame->wasCorrectlyAnswered());

        $aGame->roll(2);
        self::assertTrue($aGame->wrongAnswer());

        $aGame->roll(1);
        self::assertTrue($aGame->wasCorrectlyAnswered());

        $aGame->roll(5);
        self::assertFalse($aGame->wasCorrectlyAnswered());

        $aGame->roll(6);
        self::assertTrue($aGame->wasCorrectlyAnswered());

        $aGame->roll(3);
        self::assertTrue($aGame->wrongAnswer());

        $aGame->roll(5);
        self::assertFalse($aGame->wasCorrectlyAnswered());

        $aGame->roll(2);
        self::assertTrue($aGame->wasCorrectlyAnswered());

        $aGame->roll(5);
        self::assertTrue($aGame->wrongAnswer());

        $aGame->roll(6);
        self::assertTrue($aGame->wasCorrectlyAnswered());

        $output = ob_get_clean();

        Approvals::verifyString($output);
    }

    public function testShouldTheGameBeNotPlayableWhenZeroPlayersHaveBeenAdded(): void 
    {
        ob_start();

        $aGame = new Game();
        
        ob_get_clean();

        self::assertFalse($aGame->isPlayable());
    }

    public function testShouldTheGameBeNotPlayableWhenOnePlayersHaveBeenAdded(): void 
    {
        ob_start();

        $aGame = new Game();
        $aGame->add("Chet");

        ob_get_clean();

        self::assertFalse($aGame->isPlayable());
    }

    public function testShouldTheGameBePlayableWhenTwoPlayersHaveBeenAdded(): void 
    {
        ob_start();

        $aGame = new Game();
        $aGame->add("Chet");
        $aGame->add("Pat");

        ob_get_clean();
        
        self::assertTrue($aGame->isPlayable());
    }

    public function testShouldTheGameHasGotFiftyQuestionsForEachCategory(): void
    {
        $aGame = new Game();

        self::assertSame(50, count($aGame->popQuestions));
        self::assertSame(50, count($aGame->scienceQuestions));
        self::assertSame(50, count($aGame->sportsQuestions));
        self::assertSame(50, count($aGame->rockQuestions));
    }
}
