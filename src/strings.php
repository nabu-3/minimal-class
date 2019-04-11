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
 * Check if a string starts by another. The function allows to receive params with null value for convenience,
 * but in this case always returns false. This functions uses mbstring library.
 * @param string|null $haystack The string to search in.
 * @param string|null $needle The searched string.
 * @return bool True if success of false if not.
 */
function nb_strStartsWith(string $haystack = null, string $needle = null)
{
    $ln = mb_strlen($needle);

    return is_string($haystack) &&
           is_string($needle) &&
           $ln > 0 &&
           mb_strlen($haystack) >= $ln &&
           mb_substr($haystack, 0, $ln) === $needle
    ;
}

/**
 * Check if a string ends by another. The function allows to receive params with null value for convenience,
 *  but in this case always returns false. This functions uses mbstring library.
 * @param string $haystack The string to search in.
 * @param string $needle The searched string.
 * @return bool True if success of false if not.
 */
function nb_strEndsWith($haystack, $needle)
{
    $lh = mb_strlen($haystack);
    $ln = mb_strlen($needle);

    return is_string($haystack) &&
           is_string($needle) &&
           $ln > 0 &&
           $lh >= $ln &&
           mb_strrpos($haystack, $needle) === ($lh - $ln)
    ;
}
