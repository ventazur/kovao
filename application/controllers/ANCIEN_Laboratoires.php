<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This file is part of Kovao - http://kovao.com/
//
// Kovao is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, version 3 of the License.
//
// Kovao is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Kovao.  If not, see <http://www.gnu.org/licenses/>.

/* ============================================================================
 *
 * LABORATOIRES
 *
 * ----------------------------------------------------------------------------
 *
 * Ceci est un projet prototype pour permettre aux etudiants de remplir
 * un rapport de laboratoire pendant la realisation de l'experience au labo.
 *
 * ============================================================================ */

class Laboratoires extends MY_Controller 
{
	public function __construct()
    {
        parent::__construct();
    }

    /* ------------------------------------------------------------------------
     *
     * Index
     *
     * ------------------------------------------------------------------------ */
	public function index()
    {
    }

    /* ------------------------------------------------------------------------
     *
     * Affichage
     *
     * ------------------------------------------------------------------------ */
    public function _affichage()
    {
        $this->load->view('commons/header', $this->data);
        $this->load->view('laboratoires/skel');
        $this->load->view('commons/footer', $this->data);
    }
}
