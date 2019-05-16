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

use nabu\data\interfaces\INabuDataList;

/**
 * Trait to manage classes implementing interfaces @see { INabuDataList } or a @see { INabuDataIndexedList }
 * as JSON data.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.4
 * @version 3.0.4
 * @package \nabu\data\traits
 */
trait TNabuJSONDataList
{
    /**
     * Creates a new instance of a class implementing @see { INabuDataList } or @see { InabuDataIndexedList }
     * from a JSON file.
     * @param string $filename File name to be loaded.
     * @param string|null $index_field Main index field to index data.
     * @return INabuDataList Returns the new instance of class used to call this method.
     */
    public static function createFromJSONFile(string $filename, ?string $index_field = null): INabuDataList {
        $called_class = get_called_class();

        if (strlen($filename) > 0 &&
            is_string($realname = realpath($filename)) &&
            file_exists($realname) &&
            is_file($realname) &&
            (
                mime_content_type($realname) !== 'text/plain' ||
                ($raw = file_get_contents($realname)) === false ||
                !($json = json_decode($raw, JSON_OBJECT_AS_ARRAY))
            )
        ) {
            trigger_error(sprintf(TRIGGER_ERROR_INVALID_FILE, $filename), E_USER_ERROR);
        }

        return new $called_class($index_field, $json);
    }
}
