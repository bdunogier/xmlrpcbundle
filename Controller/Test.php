<?php
/**
 * File containing the Test class.
 *
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */ 
namespace BD\Bundle\XmlRpcBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class Test extends Controller
{
    public function test()
    {
        return new Response( __METHOD__ );
    }
}
