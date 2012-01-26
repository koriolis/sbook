<?php
/**
 * Spellbook: Web application framework
 *
 * Copyright Wiz Interactive (2006)
 *
 * Spellbook is yet another MVC web framework written in PHP. It is 
 * oriented on simplicity and speed. It has been used internally to build 
 * sites for several clients and as such it is perfectly usable in its 
 * current incarnation. Uses PEAR for library routines and Smarty templating.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, 
 * Boston, MA  02110-1301, USA.
 *
 * @author       Jorge Pena <jmgpena@gmail.com>
 * @version 3.0
 * @package sbook
 */

/**
  * Not empty.
  */
define('VALID_NOT_EMPTY', '/.+/');

/**
  * Numbers [0-9] only.
  */
define('VALID_NUMBER', '/^[0-9]+$/');

/**
  * A valid email address.
  */
define('VALID_EMAIL', '/^([a-z0-9][a-z0-9_\-\.\+]*)@([a-z0-9][a-z0-9\.\-]{0,63}\.[a-z]{2,3})$/i');

/**
  * A valid year (1000-2999).
  */
define('VALID_YEAR', '/^[12][0-9]{3}$/');

class validate
{
    function batch($vars,$validators) 
    {
        if( count($vars) != count($validators) )
        {
            return false;
        }
        foreach($vars as $var)
        {
            list(,$validator) = each($validators);
            $params = array_merge(array($var),array_slice($validator,1));
            $function = array('validate',$validator['type']);
            if(!call_user_func_array($function,$params))
            {
                return false;
            }
        }
        return true;
    }

    function string($var,$min = 0, $max = 0)
    {
        return (is_string($var)) and 
            (strlen($var)>=$min) and 
            (($max==0)?true:strlen($var)<=$max);
    }

    function number($var)
    {
        return preg_match(VALID_NUMBER,$var);
    }
}
?>
