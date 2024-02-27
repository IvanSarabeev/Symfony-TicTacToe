<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class MultiPlayerRepository extends BoardCheck
{
    const SESSION_MULTIPLAYER = 'gameBot';

    public function __construct(private readonly RequestStack $requestStack)
    {
        if (
            $this->requestStack->getCurrentRequest()
            && $this->requestStack->getCurrentRequest()->getSession()
            && $this->requestStack->getCurrentRequest()->getSession()->has(self::SESSION_MULTIPLAYER)
            && $this->requestStack->getCurrentRequest()->getSession()->get(self::SESSION_MULTIPLAYER, $this->getBoard())
        ) {
            $boardData = $this->requestStack->getCurrentRequest()->getSession()->get(self::SESSION_MULTIPLAYER);

            $this->player = $boardData['player'];
            $this->board = $boardData['board'];
        } else {
            $this->player = "X";
            $this->board = array_fill(0,3,array_fill(0,3,null));
        }
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
        $session = $this->requestStack->getCurrentRequest()->getSession();

//        $this->getPlayerMove($row, $col);

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

        $session->set(self::SESSION_MULTIPLAYER, [
            'board' => $this->board,
            'player' => $this->player,
        ]);
    }

    // ?TODO: Create an method to remove local session state
    public function removeGameSession(): void
    {
        $removeSession = $this->requestStack->getCurrentRequest()->getSession();
        $removeSession->remove(self::SESSION_MULTIPLAYER);
    }
}