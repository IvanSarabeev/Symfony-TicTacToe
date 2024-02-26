<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class SinglePlayerRepository extends BoardCheck
{
    public function __construct(private RequestStack $requestStack)
    {
        $this->player = "X";
        $this->board = array_fill(0, 3, array_fill(0, 3, null));
    }

    public function getBoard(): array
    {
        return $this->board;
    }

    public function setPlayerMoves($row, $col): void
    {
        if ($this->board[$row][$col] == null) {
            $this->board[$row][$col] = $this->player;
            $session = $this->requestStack->getSession();
            $this->player = $this->player === "X" ? "O" : "X";
        }

        $this->checkGameResult();
        $this->renderWinner();
    }
}