<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class MultiPlayerRepository extends BoardCheck
{
    private SessionInterface $session;

    public function __construct(private readonly RequestStack $requestStack)
    {
        $this->player = "X";
        $this->board = array_fill(0,3,array_fill(0,3,null));
    }

    public function getBoard(): array
    {
        return $this->board;
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

        $session = $this->requestStack->getSession();
        // TODO: I need to start the session inside the controller
        // Returns Session->start: false, Session->closed: false
        //dd($session);

        $this->checkGameResult();
        $this->renderWinner();

    }
}