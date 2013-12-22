<?php
/**
 * File containing the RequestParserInterface class.
 *
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */
namespace BD\Bundle\XmlRpcBundle\XmlRpc;

interface RequestParserInterface
{
    /**
     * Loads an XML string for parsing
     * @param string $xmlString
     * @throws \UnexpectedValueException If the XML payload could not be parsed
     */
    public function fromXmlString( $xmlString );

    /**
     * @return array()
     */
    public function getParameters();

    /**
     * @return string
     */
    public function getMethodName();
}
