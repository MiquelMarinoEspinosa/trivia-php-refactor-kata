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
  
        $aGame->add("Chet");
        $aGame->add("Pat");
        $aGame->add("Sue");

        $aGame->roll(1);
        $aGame->wrongAnswer();

        $aGame->roll(2);
        $aGame->wrongAnswer();

        $aGame->roll(3);
        $aGame->wasCorrectlyAnswered();

        $output = ob_get_clean();

        Approvals::verifyString($output);
    }
}
