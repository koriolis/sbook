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
 * Path to the application's directory.
 */
define ('APP',         ROOT.'app'.DS);

/**
 * Path to the application's models directory.
 */
define ('MODELS',          APP.'models'.DS);

/**
 * Path to the application's actions directory.
 */
define ('ACTIONS',     APP.'actions'.DS);

/**
 * Path to the application's helpers directory.
 */
define ('PLUGINS',         APP.'plugins'.DS);

/**
 * Path to the application's templates directory.
 */
define ('TEMPLATES',         APP.'templates'.DS);

/**
 * Path to the application's templates directory.
 */
define ('CORETEMPLATES',         SBOOK.'templates'.DS);

/**
 * Path to the configuration files directory.
 */
define ('CONFIGS',     APP.'config'.DS);

/**
 * Path to the libs directory.
 */
define ('LIBS',        SBOOK.'libs'.DS);
define ('CORE',        SBOOK.'core'.DS);

/**
 * Path to the site libs directory.
 */
define ('APPLIBS',        APP.'libs'.DS);

/**
 * Path to the modules directory.
 */
define ('MODULES', SBOOK.'modules'.DS);

/**
 * Path to the site modules directory.
 */
define ('APPMODULES', APP.'modules'.DS);

/**
 * Path to the logs directory.
 */
define ('LOGS',        APP.'logs'.DS);

/**
 * Path to the Pear directory
 * The purporse is to make it easy porting Pear libs into Cake
 * without setting the include_path PHP variable.
 */
define ('PEAR',            MODULES.'pear'.DS);

/**
 *  Full url prefix
 */
define('FULL_BASE_URL', 'http://'.parse_url('http://'.$_SERVER['HTTP_HOST'],PHP_URL_HOST));

?>