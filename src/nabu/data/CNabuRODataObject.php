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

namespace nabu\data;

use \nabu\min\CNabuObject;

/**
 * Abstract class to implement read only data objects of nabu-3.
 * This class can also be extended by third party classes to inherit his functionality.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @version 3.0.0 Surface
 * @package \nabu\data
 */
abstract class CNabuRODataObject extends CNabuObject
{
    /** @var array|null Data stored in the instance. Functions that applies to set data in this instance modifies this
     * array. */
    protected $data = null;

    public function __construct(array $data = null)
    {
        parent::__construct();

        $this->data = $data;
    }

    /**
     * Dumps data to an string using var_export. Data string is in PHP format.
     * @return string Returns data as string in PHP format.
     */
    public function dump()
    {
        return var_export($this->data, true);
    }

    /**
     * Get a value identified by his name.
     * @param string $name Name of the value to get.
     * @return mixed|null Returns the stored value if exists or null otherwise.
     */
    public function getValue(string $name)
    {
        return ($this->data == null || !array_key_exists($name, $this->data) ? null : $this->data[$name]);
    }

    /**
     * Splits the value using a token based in a regular expression (uses function preg_split) and returns the result
     * as an array. If the value cannot be splitted then returns their original value.
     * @param string $name Name of the value to get.
     * @param string $token Token used to split the value. By default, split uses comma with with backard and forward
     * separators.
     * @return array|null Returns the array as result of split the value or null if it is empty.
     */
    public function getValueAsList(string $name, string $token = "/(\s*,\s*)/")
    {
        $value = $this->getValue($name);

        if ($value != null && is_string($value)) {
            $value = preg_split($token, $value);
        }

        return $value;
    }

    /**
     * Check if a value name exists.
     * @param string $name Name of the value to check.
     * @return bool Returns true if the value name exists.
     */
    public function hasValue(string $name): bool
    {
        return ($this->data !== null && array_key_exists($name, $this->data));
    }

    /**
     * Check if none values are stored.
     * @return bool Returns true if data storage is empty.
     */
    public function isEmpty(): bool
    {
        return ($this->data === null) || (count($this->data) == 0);
    }

    /**
     * Check if a value is numeric.
     * @param string $name Name of the value to check.
     * @return bool Returns true if the value exists and is numeric.
     */
    public function isValueNumeric(string $name): bool
    {
        return $this->hasValue($name) && is_numeric($this->getValue($name));
    }

    /**
     * Check if a value is a float number.
     * @param string $name Name of the value to check.
     * @return bool Returns true if the value exists and is a float number.
     */
    public function isValueFloat(string $name): bool
    {
        $value = $this->getValue($name);

        return $this->hasValue($name) && (is_numeric($value) || is_float($value));
    }

    /**
     * CHeck if a value is boolean.
     * @param string $name Name of the value to check.
     * @return bool Returns true if the value exists and is a boolean.
     */
    public function isValueBool(string $name): bool
    {
        $value = $this->getValue($name);

        return $this->hasValue($name) && is_bool($value);
    }

    /**
     * Check if a value is an string.
     * @param string $name Name of the value to check.
     * @return bool Returns true if the value exists and is an string.
     */
    public function isValueString(string $name): bool
    {
        return $this->hasValue($name) && is_string($this->getValue($name));
    }

    /**
     * Check if a value is an empty string.
     * @param string $name Name of the value to check.
     * @return bool Returns true if the value exists and is an empty string.
     */
    public function isValueEmptyString(string $name): bool
    {
        $value = $this->getValue($name);

        return $this->hasValue($name) && is_string($value) && mb_strlen($value) === 0;
    }

    /**
     * Check if a value is an array.
     * @param string $name Name of the value to check.
     * @return bool Returns true if the value exists and is an empty string.
     */
    public function isValueArray(string $name): bool
    {
        $value = $this->getValue($name);

        return $this->hasValue($name) && is_array($value);
    }

    /**
     * Check if a value is an empty array.
     * @param string $name Name of the value to check.
     * @return bool Returns true if the value exists and is an empty array.
     */
    public function isValueEmptyArray(string $name): bool
    {
        $value = $this->getValue($name);

        return $this->hasValue($name) && is_array($value) && count($value) === 0;
    }

    /**
     * Check if a value is null.
     * @param string $name Name of the value to check.
     * @return bool Returns true if the value exists and is null.
     */
    public function isValueNull(string $name): bool
    {
        return $this->hasValue($name) && is_null($this->getValue($name));
    }

    /**
     * Check if a value is empty. An empty value is a value that exists and is null, or false, or 0, or an empty string.
     * @param string $name Name of the value to check.
     * @return bool Returns true if the value exists and is empty.
     */
    public function isValueEmpty(string $name): bool
    {
        $value = $this->getValue($name);

        return $this->hasValue($name) &&
               ($value === null ||
                $value === false ||
                $value === 0 ||
                (is_string($value) && mb_strlen($value) === 0) ||
                (is_array($value) && count($value) == 0)
               )
        ;
    }

    /**
     * Check if a value is a GUID.
     * @param string $name Name of the value to check.
     * @return bool Returns true if the value exists and is a well formed GUID.
     */
    public function isValueGUID(string $name): bool
    {
        return $this->hasValue($name) && nb_isValidGUID($this->getValue($name));
    }

    /**
     * Check if a value is strickly equal to a test value.
     * @param string $name Name of the value to check.
     * @param mixed $test Test value to compare.
     * @param bool $strict If true comparation is strict (uses === to match).
     * @return bool Returns true if both values are equals.
     */
    public function isValueEqualTo(string $name, $test, bool $strict = false): bool
    {
        $value = $this->getValue($name);

        return $this->hasValue($name) &&
               (($strict && $value === $test) || (!$strict && $value == $test))
        ;
    }

    /**
     * Check if a value of this instance matches another value in another instance.
     * @param CNabuRODataObject|null $object Object instance to match values.
     * @param string|null $source_name Source name of value to match.
     * @param string|null $target_name Target name in $object to match. If null, then uses $source_name.
     * @param bool $strict If true comparation is strict (uses === to match).
     * @return bool Returns true if both values exists and matchs.
     */
    public function matchValue(
        CNabuRODataObject $object = null, string $source_name = null, string $target_name = null, bool $strict = false
    ): bool {
        $retval = false;

        if ($object instanceof CNabuRODataObject) {
            $target_name = (is_string($target_name) ? $target_name : $source_name);
            $retval = $this->hasValue($source_name) &&
                      $object->hasValue($target_name) &&
                      $this->isValueEqualTo($source_name, $object->getValue($target_name), $strict);
        }

        return $retval;
    }
}
