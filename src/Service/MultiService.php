<?php

namespace App\Service;

use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class MultiService extends BoardCheck
{
    const SESSION_MULTIPLAYER = 'gameBot';

    /** When the service is initialized get the current session.
     * If it's not set, then set the session name and the needed property's
     * Else, initialize the service property's once again.
     * @param RequestStack $requestStack
     */
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

    /** Getting the 3x3 ASSOC array
     * @return array
     */
    public function getBoard(): array
    {
        return $this->board;
    }

    /** Setting the player move based on the chosen array index
     * @param $row
     * @param $col
     * @return void
     */
    public function setPlayerMove($row, $col): void
    {
        if ($this->board[$row][$col] === null) {
            $this->board[$row][$col] = $this->player;
        }
    }

    /** Method responsible for the bot moves on the table and setting the session
     * @param Request $request
     * @return void
     */
    #[NoReturn] public function setBotMoves(Request $request): void
    {
        // Getting the current session request
        $session = $this->requestStack->getCurrentRequest()->getSession();

        $emptyCells = [];

        // looping through the 3x3 array and get each row and col
        foreach ($this->board as $rowKeys => $row) {
            foreach ($row as $colKeys => $col) {
                if ($col === null) {
                    // set the empty array to get the row & column keys by changing them to type string
                    $emptyCells[][] = [
                        'row' => $rowKeys,
                        'col' => $colKeys,
                    ];
                }
            }
        }

        // Check if it's variable isn't empty
        if (!empty($emptyCells)) {
            // get random cell based
            $randIndex = array_rand($emptyCells);
            // get random cell index based on the row & column
            $randCell = $emptyCells[$randIndex];

            if ($request->get('row') !== 0) {
                if ($request->get('col') !== null) {
                    // set the corresponding row & column a player, that will be O
                    $this->board[$randCell['row']][$randCell['col']] = $this->player;
                }
                $this->player = $this->player === "X" ? "O" : "X";
            }
            // Determine winner
            $this->checkGameResult();

            // Set session to accept the following keys via the corresponding property's
            $session->set(self::SESSION_MULTIPLAYER, [
                'board' => $this->board,
                'player' => $this->player,
            ]);
        }
    }

    /** Remove current game session if it's started and it has the corresponding name
     * @return void
     */
    public function removeGameSession(): void
    {
        $removeSession = $this->requestStack->getCurrentRequest()->getSession();

        if ($removeSession->isStarted() && $removeSession->has(self::SESSION_MULTIPLAYER)) {
            $removeSession->remove(self::SESSION_MULTIPLAYER);
        }
    }
}