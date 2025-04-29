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
        $aGame->wrongAnswer();

        $aGame->roll(2);
        $aGame->wasCorrectlyAnswered();

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
        $aGame->wasCorrectlyAnswered();

        $aGame->roll(2);
        $aGame->wrongAnswer();

        $aGame->roll(1);
        $aGame->wasCorrectlyAnswered();

        $aGame->roll(5);
        $aGame->wasCorrectlyAnswered();

        $aGame->roll(6);
        $aGame->wasCorrectlyAnswered();

        $aGame->roll(3);
        $aGame->wrongAnswer();

        $aGame->roll(5);
        $aGame->wasCorrectlyAnswered();

        $aGame->roll(2);
        $aGame->wasCorrectlyAnswered();

        $aGame->roll(5);
        $aGame->wrongAnswer();

        $aGame->roll(6);
        $aGame->wasCorrectlyAnswered();

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
