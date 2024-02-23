<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class MultiPlayerRepository extends BoardCheck
{
    public function __construct(private RequestStack $requestStack)
    {
        if (!empty($_SESSION['gameBot'])) {

            $gameBot = unserialize($_SESSION['gameBot']);

            $this->board = $gameBot->board;
            $this->player = $gameBot->player;
        } else {
            $this->board = array_fill(0, 3, array_fill(0, 3, null));
            $this->player = "X";
        }
    }

    public function getPlayerMove($row, $col): void
    {
        if ($this->board[$row][$col] === null) {
            $this->board[$row][$col] = $this->player;
        }
    }

    public function setBotMoves(): void
    {
        $emptyCells = [];

        foreach ($this->board as $rowKeys => $row) {
            foreach ($row as $colKeys => $col) {
                if ($col === null) {
                    $emptyCells[][] = [
                        'row' => $rowKeys,
                        'col' => $colKeys,
                    ];
                }
            }
        }


        if (!empty($emptyCells)) {
            $randIndex = array_rand((array)$emptyCells);

            $randRow = $emptyCells[$randIndex];
            if (isset($_POST['row'])) {
                if (isset($_POST['col'])) {
                    $this->board[$randRow['row']][$randRow['col']] = $this->player;
                }
            }

            $this->player = $this->player === "X" ? "O" : "X";
        }

        $this->checkGameResult();
//        $this->renderWinner();

        $_SESSION['gameBot'] = serialize($this);
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

    public function getBoard(): array
    {
        return $this->board;
    }

    public function resetBot(): void
    {
        unset($_SESSION['gameBot']);
    }
}