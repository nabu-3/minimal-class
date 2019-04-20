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

namespace nabu\data\traits;

use nabu\data\CNabuDataObject;

use nabu\data\interfaces\INabuDataWritable;

/**
 * Trait to manage a @see { INabuDataWritable } or a @see { INabuDataReadable } derived classes as a nested
 * or multilevel data.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.2
 * @version 3.0.2
 * @package \nabu\data\traits
 */
trait TNabuNestedData
{
    /**
     * Try to parse value as a JSON data descriptor. If success returns an array with parsed structure. If fails,
     * then returns the original value.
     * @param string $name Name of the value to get.
     * @return array|null Returns the array as result of decode the JSON, the original value if cannot be decoded,
     * or null if data value is empty or not exists.
     */
    public function getValueAsJSONDecoded(string $name)
    {
        $value = $this->getValue($name);

        if ($value !== null &&
            is_string($value) &&
            is_null($json = json_decode($value, true))
        ) {
            $json = array($value);
        }
        if (isset($json)) {
            $value = $json;
        }

        return $value;
    }

    /**
     * Stores a value as a JSON encoded string.
     * @param string $name Name of the value to set.
     * @param mixed|null $value Value to be setted.
     * @return CNabuDataObject Returns self pointer for convenience for cascade calls.
     */
    public function setValueAsJSONEncoded(string $name, $value = null): CNabuDataObject
    {
        if ($this instanceof INabuDataWritable && $this->isEditable()) {
            if ($value === null) {
                $this->setValue($name, null);
            } elseif (is_string($value)) {
                $this->setValue($name, $value);
            } elseif (is_array($value) || is_object($value)) {
                $this->setValue($name, json_encode($value));
            }
        } else {
            trigger_error(TRIGGER_ERROR_READ_ONLY_MODE, E_USER_ERROR);
        }

        return $this;
    }

    /**
     * Splits an string Path in dot style into an array with each level in order from top to down.
     * @param string $path String path to split.
     * @param array &$route If $path is splitted, overwrites $route with the array of levels.
     * @return int Returns the size or $route (number of elements). If $path cannot be splitted then returns 0.
     */
    private function splitPath(string $path, array &$route): int
    {
        $route = mb_strlen($path) > 0 ? preg_split('/\./', $path) : null;

        return is_array($route) ? count($route) : 0;
    }

    /**
     * Gets a value using a point style JSON path. Overrides parent method.
     * @param string $path The path of the value to locate.
     * @return mixed|null Returns the value in path if exists, or null otherwise.
     */
    public function getValue(string $path)
    {
        $retval = null;
        $route = array();

        if (!$this->isEmpty() && ($l = $this->splitPath($path, $route)) > 0) {
            $p = &$this->data;
            for ($i = 0; $i < $l; $i++) {
                if (is_array($p) && array_key_exists($route[$i], $p)) {
                    $p = &$p[$route[$i]];
                } else {
                    break;
                }
            }
            $retval = ($i === $l ? $p : null);
        }

        return $retval;
    }

    /**
     * Overrides @see { CNabuRODataObject::hasValue() } to check if a Path exists.
     * @param string $path Path to check.
     * @return bool Returns true if the path exists.
     */
    public function hasValue(string $path): bool
    {
        $retval = false;
        $route = array();

        if (!$this->isEmpty() && ($l = $this->splitPath($path, $route)) > 0) {
            $p = &$this->data;
            for ($i = 0; $i < $l; $i++) {
                if (is_array($p) && array_key_exists($route[$i], $p)) {
                    $p = &$p[$route[$i]];
                } else {
                    break;
                }
            }
            $retval = ($i === $l);
        }

        return $retval;
    }

    /**
     * Grant a Path creating missed nested levels.
     * Keep with caution because this method overwrites scalar data if their path needs to be converted
     * to an intermediate level when flag is setted with TRAIT_NESTED_REPLACE_EXISTING.
     * @param string $path Path to check.
     * @param bool $replace If true, forces to grant unexistent path replacing existing values if needed.
     * @return bool Returns true if the path is granted.
     */
    public function grantPath(string $path, bool $replace = true)
    {
        $retval = false;

        if ($this instanceof INabuDataWritable && $this->isEditable()) {
            $retval = $this->grantPathInternal($path, $replace);
        } else {
            trigger_error(TRIGGER_ERROR_READ_ONLY_MODE, E_USER_ERROR);
        }

        return $retval;
    }

    /**
     * Process the deepest loop to grant a Path creating missed nested levels.
     * This method is called internally by @see { grantPath() } method.
     * @param string $path Path to check.
     * @param bool $replace If true, forces to grant unexistent path replacing existing values if needed.
     * @return bool Returns true if the path is granted.
     */
    private function grantPathInternal(string $path, bool $replace)
    {
        $retval = false;
        $route = array();

        if (($l = $this->splitPath($path, $route)) > 0) {
            $p = &$this->data;
            $retval = true;
            for ($i = 0; $i < $l; $i++) {
                if (is_null($p) || (!is_array($p) && $replace)) {
                    $p = array($route[$i] => null);
                } elseif (!is_array($p)) {
                    $retval = false;
                    break;
                } elseif (!array_key_exists($route[$i], $p)) {
                    $p[$route[$i]] = null;
                }
                $p = &$p[$route[$i]];
            }
        }

        return $retval;
    }

    /**
     * Set a Nested data value.
     * @param string $path Path of the value to be setted.
     * @param mixed|null $value Value to be setted.
     * @param bool $replace If true, forces to grant unexistent path replacing existing values if needed.
     * @return INabuDataWritable Returns self pointer for convenience.
     */
    public function setValue(string $path, $value = null, bool $replace = true): INabuDataWritable
    {
        if ($this instanceof  INabuDataWritable && $this->isEditable()) {
            if ($this->grantPath($path, $replace)) {
                $route = array();
                $p = &$this->data;
                for ($i = 0, $l = $this->splitPath($path, $route); $i < $l; $i++) {
                    $p = &$p[$route[$i]];
                }
                $p = $value;
            }
        } else {
            trigger_error(TRIGGER_ERROR_READ_ONLY_MODE, E_USER_ERROR);
        }

        return $this;
    }
}
