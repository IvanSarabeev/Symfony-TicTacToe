<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SinglePlayerRepository extends BoardCheck
{
    private SessionInterface $session;

    public function __construct($session)
    {
        $this->session = $session;

        if ($this->session->has('gameBoard')) {
            $gameBoard = unserialize($this->session->get('gameBoard'));

            $this->board = $gameBoard->board;
            $this->player = $gameBoard->player;
        } else {
            $this->player = "X";
            $this->board = array_fill(0,3,array_fill(0,3,null));
        }
    }

    public function getBoard(): array
    {
        return $this->board;
    }

    public function setPlayerMoves($row, $col): void
    {
        if ($this->board[$row][$col] == null) {
            $this->board[$row][$col] = $this->player;
            $this->player = $this->player === "X" ? "O" : "X";
        }

        $this->checkGameResult();
//        $this->renderWinner();
    }

    public function renderWinner(): void
    {
        if ($this->checkGameResult()) {
            echo "<h2 class='d-flex align-items-center justify-content-center mb-3'>The winner is
                        <strong class='pl-2 fs-3 d-flex align-items-center justify-content-center'>{$this->checkGameResult()}</strong>
                    </h2>";
        } else {
            echo "<p class='text-center fs-4 fw-medium'>The game is still running</p>";
        }
    }

    public function resetGame(): void
    {
        $this->session->remove('gameBoard');
    }

    public function saveGame(): void
    {
        $gameData = serialize(['board' => $this->board, 'player' => $this->player]);
        $this->session->set('gameBoard', $gameData);
    }

}