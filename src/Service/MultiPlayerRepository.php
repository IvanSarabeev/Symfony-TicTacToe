<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class MultiPlayerRepository extends BoardCheck
{
    private SessionInterface $session;

    public function __construct($session)
    {
        $this->session = $session;

        if ($this->session->has('gameBot')) {
            $gameBot = unserialize($this->session->get('gameBot'));

            $this->board = $gameBot['board'];
            $this->player = $gameBot['player'];
        } else {
            $this->player = "X";
            $this->board = array_fill(0, 3, array_fill(0, 3, null));
        }
    }

    public function getPlayerMove($row, $col): void
    {
        if ($this->board[$row][$col] === null) {
            $this->board[$row][$col] = $this->player;
            $this->player = $this->player === "X" ? "O" : "X";
        }
    }

    public function setBotMoves(): void
    {
        $emptyCells = [];

        foreach ($this->board as $rowKeys => $row) {
            foreach ($row as $colKeys => $col) {
                if ($col === null) {
                    $emptyCells[] = [
                        'row' => $rowKeys,
                        'col' => $colKeys,
                    ];
                }
            }
        }

        if (!empty($emptyCells)) {
            $randIndex = array_rand($emptyCells);

            $randRow = $emptyCells[$randIndex];
            $this->board[$randRow['row']][$randRow['col']] = $this->player;

            $this->player = $this->player === "X" ? "O" : "X";
        }

        $this->checkGameResult();
        $this->saveGame();
    }

    public function getBoard(): array
    {
        return $this->board;
    }

    public function renderWinner(): string
    {
        if ($this->checkGameResult()) {
            return "<h2 class='d-flex align-items-center justify-content-center mb-3'>The winner is
                        <strong class='pl-2 fs-3 d-flex align-items-center justify-content-center'>{$this->checkGameResult()}</strong>
                    </h2>";
        } else {
            return "<p class='text-center fs-4 fw-medium'>The game is still running</p>";
        }
    }

    public function resetBot(): void
    {
        $this->session->remove('gameBot');
    }

    public function saveGame(): void
    {
        $gameData = serialize(['board' => $this->board, 'player' => $this->player]);
        $this->session->set('gameBot', $gameData);
    }
}
