<?php

namespace App\Service;

use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\HttpFoundation\RequestStack;

class SinglePlayerRepository extends BoardCheck
{
    const SESSION_SINGLE_GAME = 'gameBoard';

    public function __construct(private readonly RequestStack $requestStack)
    {
        if (
            $this->requestStack->getCurrentRequest()
            && $this->requestStack->getCurrentRequest()->getSession()
            && $this->requestStack->getCurrentRequest()->getSession()->has(self::SESSION_SINGLE_GAME)
            && $this->requestStack->getCurrentRequest()->getSession()->get(self::SESSION_SINGLE_GAME, $this->getBoard())
        ) {
            $gameData = $this->requestStack->getCurrentRequest()->getSession()->get(self::SESSION_SINGLE_GAME);

            $this->player = $gameData['player'];
            $this->board = $gameData['board'];
        } else {
            $this->board = array_fill(0, 3, array_fill(0, 3, null));
            $this->player = "X";
        }
    }

    public function getBoard(): array|bool
    {
        return $this->board;
    }


    #[NoReturn] public function setPlayerMoves($row, $col): void
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();

        if ($this->board[$row][$col] == null) {
            $this->board[$row][$col] = $this->player;

            $this->player = $this->player === "X" ? "O" : "X";
        }

        $this->checkGameResult();

        $session->set(self::SESSION_SINGLE_GAME, [
            'board' => $this->board,
            'player' => $this->player
        ]);
    }


    /** Remove existing session via it's name
     * @return void
     */
    public function removeGameSession(): void
    {
         $removeSession = $this->requestStack->getCurrentRequest()->getSession();

         if ($removeSession->isStarted() && $removeSession->has(self::SESSION_SINGLE_GAME)) {
            $removeSession->remove(self::SESSION_SINGLE_GAME);
         }
    }
}