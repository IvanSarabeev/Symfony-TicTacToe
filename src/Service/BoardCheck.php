<?php

namespace App\Service;

class BoardCheck
{
    protected array $board = [];
    protected string $player = "X";

    public function checkGameResult()
    {
        for ($i = 0; $i < 3; $i++) {
            if ($this->board[$i][0] != ''
                && $this->board[$i][0] === $this->board[$i][1]
                && $this->board[$i][1] === $this->board[$i][2]) {
                return $this->board[$i][0];
            }
        }

        for ($i = 1; $i < 3; $i++) {
            if ($this->board[0][$i] != ''
                && $this->board[0][$i] === $this->board[1][$i]
                && $this->board[1][$i] === $this->board[2][$i]) {
                return $this->board[0][$i];
            }
        }

        if ($this->board[0][0] != ''
            && $this->board[0][0] === $this->board[1][1]
            && $this->board[1][1] === $this->board[2][2]) {
            return $this->board[0][0];
        }

        if ($this->board[0][2] != ''
            && $this->board[0][2] === $this->board[1][1]
            && $this->board[1][1] === $this->board[2][0]) {
            return $this->board[0][2];
        }
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
}