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

use nabu\data\interfaces\INabuDataReadable;
use nabu\data\interfaces\INabuDataWritable;

/**
 * Trait to manage a @see { INabuDataWritable } or a @see { INabuDataReadable } derived classes as a nested
 * or multilevel data.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.2
 * @version 3.0.3
 * @package \nabu\data\traits
 */
trait TNabuNestedData
{
    /** @var string|null Nested path to be used after call @see { with() } method. */
    private $with_preffix = null;

    /**
     * Splits an string Path in dot style into an array with each level in order from top to down.
     * @param string $path String path to split.
     * @param array &$route If $path is splitted, overwrites $route with the array of levels.
     * @return int Returns the size or $route (number of elements). If $path cannot be splitted then returns 0.
     */
    protected function splitPath(string $path, array &$route): int
    {
        $route = mb_strlen($path) > 0 ? preg_split('/\./', $path) : null;

        return is_array($route) ? count($route) : 0;
    }

    /**
     * Set a preffix path to be used in subsequent calls to affected methods in this trait.
     * @param string|null $preffix Preffix to be setted.
     * @return INabuDataReadable Returns the self pointer to grant fluent interfaces.
     */
    public function with(string $preffix = null): INabuDataReadable
    {
        if (is_string($preffix)) {
            $parts = preg_split('/\\./', $preffix);
            $preffix = implode('.', $parts);
        }
        $this->with_preffix = $preffix;

        return $this;
    }

    /**
     * Get current 'with' preffix.
     * @return string|null Returns the 'with' preffix if setted or null otherwise.
     */
    public function getWithPreffix(): ?string
    {
        return $this->with_preffix;
    }

    /**
     * Translate path into final path applying preffix if exists.
     * @param string|null $path Path to translate.
     * @return string|null If $path is a valid path returns the final path. Otherwise returns null.
     */
    protected function translatePath(?string $path): ?string
    {
        return is_string($this->with_preffix) && is_string($path) ? $this->with_preffix . '.' . $path : $path;
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
        $path = $this->translatePath($path);

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
        $path = $this->translatePath($path);

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
        $path = $this->translatePath($path);

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
            $real_path = $this->translatePath($path);
            if ($this->grantPath($path, $replace)) {
                $route = array();
                $p = &$this->data;
                for ($i = 0, $l = $this->splitPath($real_path, $route); $i < $l; $i++) {
                    $p = &$p[$route[$i]];
                }
                $p = $value;
            }
        } else {
            trigger_error(TRIGGER_ERROR_READ_ONLY_MODE, E_USER_ERROR);
        }

        return $this;
    }

    /**
     * Remove a Nested data value and all their children.
     * @param string $path Path of the value to be removed.
     * @return INabuDataWritable Returns the self pointer to grant fluent interface.
     */
    public function removeValue(string $path): INabuDataWritable
    {
        if ($this instanceof INabuDataWritable && $this->isEditable()) {
            if ($this->hasValue($path)) {
                $real_path = $this->translatePath($path);
                $route = array();
                $l = $this->splitPath($real_path, $route);
                if ($l === 1) {
                    parent::removeValue($route[0]);
                } elseif ($l > 1) {
                    $this->removeValueInternal($route);
                }
            }
        } else {
            trigger_error(TRIGGER_ERROR_READ_ONLY_MODE, E_USER_ERROR);
        }

        return $this;
    }

    /**
     * Internal subprocess to remove the data value in @see { removeValue() }.
     * @param array $route Route as array of the value to be removed.
     */
    private function removeValueInternal(array $route): void
    {
        $l = count($route);

        for ($i = 0, $p = &$this->data; $i < $l; $i++) {
            if (($i + 1) < $l) {
                $p = &$p[$route[$i]];
            } else {
                unset($p[$route[$i]]);
            }
        }
    }
}
