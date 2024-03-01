<?php

namespace App\Service;

use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class MultiPlayerRepository extends BoardCheck
{
    const SESSION_MULTIPLAYER = 'gameBot';

    //private readonly Request $request
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

    #[NoReturn] public function setBotMoves(Request $request): void
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();

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

            if ($request->get('row') !== 0) {
                if ($request->get('col') !== null) {
                    $this->board[$randRow['row']][$randRow['col']] = $this->player;
                }
                $this->player = $this->player === "X" ? "O" : "X";
            }
            $this->checkGameResult();

            $session->set(self::SESSION_MULTIPLAYER, [
                'board' => $this->board,
                'player' => $this->player,
            ]);
        }
    }

    public function removeGameSession(): void
    {
        $removeSession = $this->requestStack->getCurrentRequest()->getSession();

        if ($removeSession->isStarted() && $removeSession->has(self::SESSION_MULTIPLAYER)) {
            $removeSession->remove(self::SESSION_MULTIPLAYER);
        }
    }
}