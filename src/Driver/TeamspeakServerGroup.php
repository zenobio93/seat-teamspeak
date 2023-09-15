<?php

/*
 * This file is part of SeAT Teamspeak Connector.
 *
 * Copyright (C) 2019, 2020  Warlof Tutsimo <loic.leuilliot@gmail.com>
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

namespace Warlof\Seat\Connector\Drivers\Teamspeak\Driver;

use Warlof\Seat\Connector\Drivers\ISet;
use Warlof\Seat\Connector\Drivers\IUser;
use Warlof\Seat\Connector\Drivers\Teamspeak\Exceptions\TeamspeakException;
use Warlof\Seat\Connector\Exceptions\DriverException;

/**
 * Class TeamspeakServerGroup.
 *
 * @package Warlof\Seat\Connector\Drivers\Teamspeak\Driver
 */
class TeamspeakServerGroup implements ISet
{
    private string $id;

    private string $name;

    /**
     * @var \Warlof\Seat\Connector\Drivers\IUser[]
     */
    private \Illuminate\Support\Collection $members;

    /**
     * TeamspeakServerGroup constructor.
     */
    public function __construct(array $attributes = [])
    {
        $this->members = collect();
        $this->hydrate($attributes);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     * @throws \Warlof\Seat\Connector\Exceptions\DriverException
     */
    public function getMembers(): array
    {
        if ($this->members->isEmpty()) {
            try {
                $this->members = collect(TeamspeakClient::getInstance()->getServerGroupMembers($this));
            } catch (TeamspeakException $e) {
                logger()->error(sprintf('[seat-connector][teamspeak] %d : %s', $e->getCode(), $e->getMessage()));
                throw new DriverException($e->getMessage(), $e->getCode(), $e);
            }
        }

        return $this->members->toArray();
    }

    /**
     * @param \Warlof\Seat\Connector\Drivers\IUser $user
     * @throws \Warlof\Seat\Connector\Exceptions\DriverException
     */
    public function addMember(IUser $user): void
    {
        if (in_array($user, $this->getMembers()))
            return;

        try {
            TeamspeakClient::getInstance()->addSpeakerToServerGroup($user, $this);
        } catch (TeamspeakException $e) {
            logger()->error(sprintf('[seat-connector][teamspeak] %d : %s', $e->getCode(), $e->getMessage()));
            throw new DriverException($e->getMessage(), $e->getCode(), $e);
        }

        $this->members->put($user->getClientId(), $user);
    }

    /**
     * @param \Warlof\Seat\Connector\Drivers\IUser $user
     * @throws \Warlof\Seat\Connector\Exceptions\DriverException
     */
    public function removeMember(IUser $user): void
    {
        if (! in_array($user, $this->getMembers()))
            return;

        try {
            TeamspeakClient::getInstance()->removeSpeakerFromServerGroup($user, $this);
        } catch (TeamspeakException $e) {
            logger()->error(sprintf('[seat-connector][teamspeak] %d : %s', $e->getCode(), $e->getMessage()));
            throw new DriverException($e->getMessage(), $e->getCode(), $e);
        }

        $this->members->pull($user->getClientId());
    }

    /**
     * @return $this
     */
    public function hydrate(array $attributes): static
    {
        $this->id   = $attributes['sgid'];
        $this->name = $attributes['name'];

        return $this;
    }
}
