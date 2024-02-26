<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class MultiPlayerRepository extends BoardCheck
{
    private $requestStack;
    const SESSION_MULTIPLAYER = 'gameBot';

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;

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

        $session = $this->requestStack->getSession();

        if (empty($session->has(self::SESSION_MULTIPLAYER))) {
            $gameSession = $session->get(self::SESSION_MULTIPLAYER, $this->getBoard());
            dump($gameSession);
        }

        if ($this->requestStack->getSession()->isStarted()) {
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
        }

//        $session->set(self::SESSION_MULTIPLAYER, $gameSession);
//        dump($session);

        // Get session
        $session->get(self::SESSION_MULTIPLAYER);
    }

    // ?TODO: Create an method to remove local session state
    public function removeGameSession(): void
    {
        $removeSession = $this->requestStack->getSession();
        $removeSession->remove(self::SESSION_MULTIPLAYER);
    }
}