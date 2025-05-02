<?php

declare(strict_types=1);

namespace Tests;

use Game\Game;
use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;

final class GameTest extends TestCase
{
    private const int INITAL_NUMBER_QUESTIONS = 50;

    private Game $aGame;

    protected function setUp(): void
    {
        $this->aGame = new Game();
    }

    public function testPlayGame(): void
    {
        ob_start();
  
        self::assertTrue($this->aGame->add("Player1"));
        self::assertTrue($this->aGame->add("Player2"));

        $this->aGame->roll(1);
        self::assertTrue($this->aGame->wrongAnswer());

        $this->aGame->roll(2);
        self::assertTrue($this->aGame->wasCorrectlyAnswered());

        $this->aGame->roll(3);
        self::assertTrue($this->aGame->wasCorrectlyAnswered());

        $this->aGame->roll(4);
        self::assertTrue($this->aGame->wasCorrectlyAnswered());

        $this->aGame->roll(5);
        self::assertTrue($this->aGame->wasCorrectlyAnswered());

        $this->aGame->roll(6);
        self::assertTrue($this->aGame->wasCorrectlyAnswered());

        $this->aGame->roll(3);
        self::assertTrue($this->aGame->wasCorrectlyAnswered());

        $this->aGame->roll(6);
        self::assertTrue($this->aGame->wasCorrectlyAnswered());
        
        $this->aGame->roll(5);
        self::assertTrue($this->aGame->wrongAnswer());

        $this->aGame->roll(4);
        self::assertTrue($this->aGame->wasCorrectlyAnswered());

        $this->aGame->roll(3);
        self::assertTrue($this->aGame->wasCorrectlyAnswered());

        $this->aGame->roll(2);
        self::assertTrue($this->aGame->wrongAnswer());

        $this->aGame->roll(1);
        self::assertTrue($this->aGame->wasCorrectlyAnswered());

        $this->aGame->roll(5);
        self::assertFalse($this->aGame->wasCorrectlyAnswered());

        $this->aGame->roll(6);
        self::assertTrue($this->aGame->wasCorrectlyAnswered());

        $this->aGame->roll(3);
        self::assertTrue($this->aGame->wrongAnswer());

        $this->aGame->roll(5);
        self::assertFalse($this->aGame->wasCorrectlyAnswered());

        $this->aGame->roll(2);
        self::assertTrue($this->aGame->wasCorrectlyAnswered());

        $this->aGame->roll(5);
        self::assertTrue($this->aGame->wrongAnswer());

        $this->aGame->roll(6);
        self::assertTrue($this->aGame->wasCorrectlyAnswered());

        $output = ob_get_clean();

        Approvals::verifyString($output);
    }

    public function testGivenThreePlayersWhenPlayGameItIsRightPlayerTurn(): void
    {
        ob_start();
  
        self::assertTrue($this->aGame->add("Player1"));
        self::assertTrue($this->aGame->add("Player2"));
        self::assertTrue($this->aGame->add("Player3"));

        $this->aGame->roll(1);
        self::assertTrue($this->aGame->wrongAnswer());

        $this->aGame->roll(2);
        self::assertTrue($this->aGame->wasCorrectlyAnswered());

        $this->aGame->roll(3);
        self::assertTrue($this->aGame->wasCorrectlyAnswered());

        $output = ob_get_clean();

        Approvals::verifyString($output);
    }

    public function testShouldTheGameBeNotPlayableWhenZeroPlayersHaveBeenAdded(): void 
    {
        self::assertFalse($this->aGame->isPlayable());
    }

    public function testShouldTheGameBeNotPlayableWhenOnePlayersHaveBeenAdded(): void 
    {
        ob_start();

        self::assertTrue($this->aGame->add("Player1"));

        ob_get_clean();

        self::assertFalse($this->aGame->isPlayable());
    }

    public function testShouldTheGameBePlayableWhenTwoPlayersHaveBeenAdded(): void 
    {
        ob_start();

        self::assertTrue($this->aGame->add("Player1"));
        self::assertTrue($this->aGame->add("Player2"));

        ob_get_clean();
        
        self::assertTrue($this->aGame->isPlayable());
    }

    public function testShouldTheGameHasGotFiftyQuestionsForEachCategory(): void
    {
        self::assertSame(
            self::INITAL_NUMBER_QUESTIONS,
            count($this->aGame->popQuestions)
        );
        self::assertSame(
            self::INITAL_NUMBER_QUESTIONS,
            count($this->aGame->scienceQuestions)
        );
        self::assertSame(
            self::INITAL_NUMBER_QUESTIONS,
            count($this->aGame->sportsQuestions)
        );
        self::assertSame(
            self::INITAL_NUMBER_QUESTIONS,
            count($this->aGame->rockQuestions)
        );
    }
}
