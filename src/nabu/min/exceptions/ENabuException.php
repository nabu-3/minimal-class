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

namespace nabu\min\exceptions;
use Exception;

/**
 * Base class for Nabu Exceptions mechanism
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.0
 * @version 3.0.0
 * @package nabu\min\exceptions
 */
abstract class ENabuException extends \Exception
{
    /**
     * Default constructor for exceptions. Optional values enhances the exception description depending on each
     * exception kind.
     * @param string $message Text message to notice in the exception.
     * @param int $code Code ID of the exception.
     * @param array|null $values Scalar or array values to complete notice description for the exception.
     * @param Exception|null $previous Previous exception raised before this to chain both.
     */
    public function __construct(string $message = "", int $code = 0, array $values = null, Exception $previous = null)
    {
        if (strlen($message) > 0 && is_array($values) && count($values) > 0) {
            $message = nb_vnsprintf($message, (is_array($values) || $values === null ? $values : array($values)));
        }
        parent::__construct($message, $code, $previous);
    }
}
