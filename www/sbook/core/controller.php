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
 * Controller 
 *
 * This class implements the controller in the MVC model used in spellbook
 * TODO Rest of documentation
 * 
 * @package sbook
 * @copyright 2004-2006 Wiz Interactive
 * @author Jorge Pena <jmgpena@gmail.com> 
 */
class Controller
{
    
	var $tpl;
	
	function Controller(){
		
		modules('class.template');
		$this->tpl = new Template();
		$this->tpl->template_dir = TEMPLATES;
		$this->tpl->assign("baseuri",BASEURI);	
	}


	function DefaultAction($params) 
	{
        $this->tpl->template_dir = CORETEMPLATES;
        $this->tpl->display('error_no_site.tpl');
        exit;
	}

	function Initialize()
	{
        // runs at the beginning of every request
	}

	function Finalize()
	{
        // runs at the end of every request
	}

	function _redirect($url,$prefix = BASEURI)
	{
		header('Location: '.$prefix.$url);
		exit;
	}

    function _error_404()
    {
    	
        $this->tpl->template_dir = CORETEMPLATES;
        $this->tpl->display('error_404.tpl');
        exit;
    }
}
?>
