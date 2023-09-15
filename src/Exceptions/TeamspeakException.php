<?php
/**
 * This file is part of SeAT Teamspeak Connector.
 *
 * Copyright (C) 2019  Warlof Tutsimo <loic.leuilliot@gmail.com>
 *
 * SeAT Teamspeak Connector  is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * SeAT Teamspeak Connector is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Warlof\Seat\Connector\Drivers\Teamspeak\Exceptions;

use Exception;
use Throwable;

/**
 * Class TeamspeakException.
 *
 * @package Warlof\Seat\Connector\Drivers\Teamspeak\Exceptions
 */
abstract class TeamspeakException extends Exception
{
    /**
     * TeamspeakException constructor.
     *
     * @param \Throwable|null $previous
     */
    public function __construct(string $error, int $code = 0, Throwable $previous = null)
    {
        parent::__construct($error, $code, $previous);
    }
}
