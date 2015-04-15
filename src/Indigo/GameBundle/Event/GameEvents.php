<?php

namespace Indigo\GameBundle\Event;

/**
 * Class GameEvents
 * @package Indigo\GameBundle\Event
 */
final class GameEvents
{
    const GAME_PLAYER_JOIN_EVENT = 'indigo_game.playerJoin';

    const GAME_FINISH = 'indigo_game.finish';

    const GAME_FINISH_ON_SCORE = 'indigo_game.finish_on_score';
}