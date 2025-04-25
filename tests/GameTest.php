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

        //$argv = ["", "30"];
        //include(__DIR__ . '/../src/GameRunner.php');
        $aGame = new Game();
  
        $aGame->add("Chet");
        $aGame->add("Pat");
        $aGame->add("Sue");

        $aGame->roll(1);
        $aGame->wasCorrectlyAnswered();

        $output = ob_get_clean();

        Approvals::verifyString($output);
    }
}
