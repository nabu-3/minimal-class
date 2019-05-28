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

/**
 * Overrides sprintf standard function to get values from an associative array.
 * @param string $format Format string.
 * @param array $data Data associative array.
 * @return string Returns the formatted string.
 */
function nb_vnsprintf(string $format, array $data) : string
{
    preg_match_all(
        '/ (?<!%) % ( (?: [[:alpha:]_-][[:alnum:]_-]* | ([-+])? [0-9]+ (?(2) ' .
        '(?:\.[0-9]+)? | \.[0-9]+ ) ) ) \$ [-+]? \'? .? -? [0-9]* (\.[0-9]+)? \w/x',
        $format,
        $match,
        PREG_SET_ORDER | PREG_OFFSET_CAPTURE
    );
    $offset = 0;
    $keys = array_keys($data);
    foreach ($match as &$value) {
        if (($key = array_search($value[1][0], $keys, true) ) !== false ||
            (is_numeric($value[1][0]) && ($key = array_search((int) $value[1][0], $keys, true)) !== false)
        ) {
            $len = strlen($value[1][0]);
            $format = substr_replace($format, 1 + $key, $offset + $value[1][1], $len);
            $offset -= $len - strlen(1 + $key);
        }
    }

    return vsprintf($format, $data);
}

/**
 * Generates a GUID or UUID v4.
 * Courtesy of http://guid.us/GUID/PHP
 * @return string Returns the GUID in string format
 */
function nb_generateGUID()
{
    $guid = false;

    if (function_exists('com_create_guid')) {
        $guid = com_create_guid();
    } else {
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $guid = chr(123)// "{"
            .substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid, 12, 4).$hyphen
            .substr($charid, 16, 4).$hyphen
            .substr($charid, 20, 12)
            .chr(125);// "}"
    }

    return $guid;
}

/**
 * Check if a guid is valid
 * @param string $guid
 * @return bool Returns true if $guid is valid
 */
function nb_isValidGUID($guid)
{
    return is_string($guid) &&
           strlen($guid) === 38 &&
           preg_match('/^\{[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}\}$/', $guid) === 1
    ;
}

/**
 * Inspect $object to get a field value or a direct value.
 * If $object is an instance derived from {@see \nabu\data\CNabuAbstractDataObject} and, if $type
 * is setted, an instance is derived also from $type, search in this instance
 * for a field named $field and returns its value. If value not found returns false.
 * Otherwise, if $object is a number or a string then returns its value directly.
 * @param mixed $object Object instance or scalar variable to locate the mixed value.
 * @param string $field Field name to search in $object.
 * @param string|null $type Class name to force type cast or null for default.
 * @return mixed|null Return the value of the field if $object is an instance and $field
 * exists inside it, or $object directly if is an scalar value.
 */
function nb_getMixedValue($object, string $field, ?string $type = null)
{
    $value = null;

    if (($object instanceof \nabu\data\interfaces\INabuDataReadable) &&
        (is_null($type) || ($object instanceof $type))
    ) {
        $value = $object->getValue($field);
    } elseif (is_scalar($object)) {
        $value = $object;
    }

    return $value;
}
