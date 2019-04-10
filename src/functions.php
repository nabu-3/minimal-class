<?php

/** @license
 *  Copyright 2019-2011 Rafael Gutierrez Martinez
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
