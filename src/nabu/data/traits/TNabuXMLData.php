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

/**
 * Trait to manage a TNabuDataObject as an XML object.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.2
 * @version 3.0.4
 * @package \nabu\data\traits
 */
trait TNabuXMLData
{
    /**
     * @deprecated since version 2.1
     */
    public function getXmlData($parent = null)
    {
        return null;
    }

    /**
     * @deprecated since version 2.1
     */
    public static function buildXmlObject($xmldata)
    {
        return null;
    }

    /**
     * @deprecated since version 2.1
     */
    protected function buildXmlElement($name, $field)
    {
        if ($this->contains($field)) {
            $value = $this->getValue($field);
            if ($value == null) {
                $xmldata = new \nabu\core\CNabuXmlElement("<$name/>");
            } else {
                $xmldata = new \nabu\core\CNabuXmlElement("<$name>$value</$name>");
            }
            return $xmldata;
        }

        return null;
    }

    /**
     * @deprecated since version 2.1
     */
    protected function buildXmlElementCDATA($name, $field)
    {
        if ($this->contains($field)) {
            $value = $this->getValue($field);
            if ($value == null) {
                $xmldata = new \nabu\core\CNabuXmlElement("<$name/>");
            } else {
                $xmldata = new \nabu\core\CNabuXmlElement(
                    "<$name><![CDATA[".htmlentities($value, ENT_COMPAT, 'UTF-8')."]]></$name>"
                );
            }
            return $xmldata;
        }

        return null;
    }

    /**
     * @deprecated since version 2.1
     */
    protected function addXmlAttribute($xml, $name, $field)
    {
        if ($xml instanceof \nabu\core\CNabuXmlElement && $name != null && $this->contains($field)) {
            $value = $this->getValue($field);
            if ($value !== null) {
                $xml->addAttribute($name, $value);

                return true;
            }
        }

        return false;
    }

    /**
     * @deprecated since version 2.1
     */
    protected function addXmlChild($xml, $name, $field = null)
    {
        if ($xml instanceof \nabu\core\CNabuXmlElement && $name != null) {
            if ($field == null) {
                return $xml->addChild($name);
            } elseif ($this->contains($field)) {
                $value = $this->getValue($field);
                if ($value !== null) {
                    return $xml->addChild($name, $value);
                } else {
                    return $xml->addChild($name);
                }
            }
        }

        return null;
    }

    /**
     * @deprecated since version 2.1
     */
    protected function addXmlChildCDATA($xml, $name, $field = null)
    {
        if ($xml instanceof \nabu\core\CNabuXmlElement && $name != null) {
            if ($field == null) {
                return $xml->addChild($name);
            } elseif ($this->contains($field)) {
                $value = $this->getValue($field);
                return $xml->addChildCDATA($name, $value);
            }
        }

        return null;
    }

    /**
     * @deprecated since version 2.1
     */
    protected function getXmlAttribute($xml, $attribute, $field, $defaultvalue = null, $mapvalue = null)
    {
        $value = $xml->getAttribute($attribute);
        if ($value !== false) {
            if (func_num_args() > 4 && $mapvalue != null) {
                if (isset($mapvalue[$value])) {
                    $value = $mapvalue[$value];
                    $this->setValue($field, $value);
                } else {
                    $value = $defaultvalue;
                    $this->setValue($field, $value);
                }
            } else {
                $this->setValue($field, $value);
            }
        } elseif (func_num_args() > 3) {
            $value = $defaultvalue;
            $this->setValue($field, $value);
        }

        return $value;
    }

    /**
     * @deprecated since version 2.1
     */
    protected function getXmlCDATA($xml, $field, $emptynull)
    {
        $value = $xml->getCDATA();
        if (mb_strlen($value) == 0 && $emptynull === true) {
            $value = null;
        }
        $this->setValue($field, $value);

        return $value;
    }

    /**
     * @deprecated since version 2.1
     */
    protected function getXmlText($xml, $field, $emptynull)
    {
        $value = $xml->getText();
        if (mb_strlen($value) == 0 && $emptynull === true) {
            $value = null;
        }
        $this->setValue($field, $value);

        return $value;
    }


}
