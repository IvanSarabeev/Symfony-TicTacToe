<?php

namespace App\Service;

class SinglePlayer extends  BoardCheck
{
    public function __construct()
    {
        if (!empty($_SESSION['gameBoard'])) {
            $gameBoard = unserialize($_SESSION['gameBoard']);

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

    public function setMultiPlayerMoves($row, $col)
    {
        if ($this->board[$row][$col] == null) {
            $this->board[$row][$col] = $this->player;
            $this->player = $this->player === "X" ? "O" : "X";
        }

        $this->gameStatus();
        $this->renderWinner();
    }

    public function renderWinner(): void
    {
        if ($this->gameStatus()) {
            echo "<h2 class='d-flex align-items-center justify-content-center mb-3'>The winner is
                        <strong class='pl-2 fs-3 d-flex align-items-center justify-content-center'>{$this->gameStatus()}</strong>
                    </h2>";
        } else {
            echo "<p class='text-center fs-4 fw-medium'>The game is still running</p>";
        }
    }

    public function resetGame(): void
    {
        unset($_SESSION['gameBoard']);
    }

}