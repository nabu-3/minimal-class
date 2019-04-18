<?php

/** @license
 *  Copyright 2009-2011 Rafael Gutierrez Martinez
 *  Copyright 2012-2013 Welma WEB MKT LABS, S.L.
 *  Copyright 2014-2016 Where Ideas Simply Come True, S.L.
 *  Copyright 2017 nabu-3 Group
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace nabu\min;

/**
 * Base class for all classes. Implements basic functionalities of classes.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.0
 * @version 3.0.0
 * @package \nabu\min
 */
class CNabuObject
{
    /**
     * Timestamp of instance creation
     * @var int
     */
    private $timestamp;

    /**
     * Hash to identify an instance across the entire collection in your class
     * @var string
     */
    private $hash = false;

    /**
     * Default constructor. Assign current timestamp to $timestamp
     */
    public function __construct()
    {
        $this->timestamp = time();
    }

    /**
     * Get creation timestamp
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Check if an instance is of type Built-in.
     * @return bool Returns true if the instance is of type BuiltIn
     */
    public function isBuiltIn()
    {
        return false;
    }

    /**
     * Create a new hash for an instance
     * @return string Return the hash created.
     */
    public function createHash()
    {
        $this->hash = nb_generateGUID();

        return $this->hash;
    }

    /**
     * Gets the current hash and, if none exists, then creates it.
     * @return string Retuns a valid unique hash (GUID) to identify this instance.
     */
    public function getHash()
    {
        return ($this->hash ? $this->hash : $this->createHash());
    }

}
