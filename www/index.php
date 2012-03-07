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

/**#@+
 * Constants
 */
/**
 * Directory separator for portability between OSes 
 */
define ('DS', DIRECTORY_SEPARATOR);
/**
 * The site root directory
 */
define ('ROOT', dirname(__FILE__).DS);
/**
 * Get spellbook root directory 
 */
define ('SBOOK', ROOT.'sbook'.DS);

/**
 * Include the needed spellbook core files 
 */
require_once(SBOOK.'core/paths.php');
require_once(SBOOK.'core/basics.php');
require_once(SBOOK.'core/sbook.php');

/**
 * Call the dispatch method to do the heavy lifting 
 */
sBook::Dispatch();

?>