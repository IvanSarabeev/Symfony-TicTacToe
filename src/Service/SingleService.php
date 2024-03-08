<?php

namespace App\Service;

use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\HttpFoundation\RequestStack;

class SingleService extends BoardCheck
{
    const SESSION_SINGLE_GAME = 'gameBoard';

    /** When the service is initialized ge the current session.
     * If it's not set, then set the session name and the needed property's
     * Else, initialize the service property's once again.
     * @param RequestStack $requestStack
     */
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

    /** Getting the 3x3 ASSOC array
     * @return array|bool
     */
    public function getBoard(): array|bool
    {
        return $this->board;
    }


    /** Setting the player's move based on the chosen array index, then we toggle between the value X and O
     *
     * @param $row
     * @param $col
     * @return void
     */
    #[NoReturn] public function setPlayerMoves($row, $col): void
    {
        // Getting the current session request
        $session = $this->requestStack->getCurrentRequest()->getSession();

        if ($this->board[$row][$col] == null) {
            $this->board[$row][$col] = $this->player;

            // Toggle between values
            $this->player = $this->player === "X" ? "O" : "X";
        }

        // Determine winner
        $this->checkGameResult();

        // Set session to accept the following keys via the corresponding property's
        $session->set(self::SESSION_SINGLE_GAME, [
            'board' => $this->board,
            'player' => $this->player
        ]);
    }


    /** Remove current game session if it's started and it has the corresponding name
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