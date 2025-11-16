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

/* ------------------------------------------------------------------------------------------------
 *
 * KCACHE (KOVAO CACHE)
 *
 * ------------------------------------------------------------------------------------------------ */

class Kcache
{
    protected $CI;

    public function __construct()
	{
        $this->CI =& get_instance();

        $this->CI->cache_loaded = FALSE;

        try 
        {
            $this->CI->load->driver('cache', array('adapter' => 'redis'));
        }
        catch (Exception $e)
        {
            return FALSE;
        }

        $this->CI->cache_loaded = TRUE;

		//
		// Defaults
		//

		$this->ttl          = 600;  // time to live
		$this->metadata_ttl = 6000; // metadata time to live

		//
		// Load metadata
		//

        $this->metadata = $this->CI->cache->get('metadata') ?: array();

        //
        // Category 
        //

        $categories = array('bienvenue', 'evaluations', 'corrections', 'blocs', 'questions', 'reponses', 'variables');
	}

	/* --------------------------------------------------------------------------------------------
	 *
	 * SAVE
	 *
	 * -------------------------------------------------------------------------------------------- */
	function save($key, $data, $category = 'general', $ttl = NULL)
    {
        if ( ! $this->_cache_enabled())
        {
            return FALSE;
        }

        if ($ttl == NULL || ! is_numeric($ttl))
        {
            $ttl = (int) ($this->CI->config->item('cache_ttl') ?: $this->ttl);
        }

        $skey = $this->_convert_key($key);

        //
        // Save data
        //

		if ( ! $this->CI->cache->save($skey, $data, $ttl))
        {
			return FALSE;
		}

        //
        // Save metadata
        //

		$this->_save_to_metadata($skey, $ttl, $category);

		return TRUE;
	}

	/* --------------------------------------------------------------------------------------------
	 *
	 * GET
	 *
	 * -------------------------------------------------------------------------------------------- */
	function get($key)
    {
        if ( ! $this->_cache_enabled())
		{
            return FALSE;
        }
        
		$this->_clear_expired();

        $skey = $this->_convert_key($key);

	  	if ($value = $this->CI->cache->get($skey))
        {
            $this->_increment($skey);

            return $value;
        }

        return FALSE;
	}

	/* --------------------------------------------------------------------------------------------
	 *
	 * REMOVE
	 *
	 * -------------------------------------------------------------------------------------------- */
	function remove($key)
	{
        if ( ! $this->_cache_enabled())
		{
            return FALSE;
        }

        $skey = $this->_convert_key($key);

		if ($this->CI->cache->delete($skey))
		{
			$this->_remove_from_metadata($skey);

            return TRUE;
		}

		return FALSE;
	}

	/* --------------------------------------------------------------------------------------------
	 *
	 * REMOVE_CATEGORY
	 *
	 * -------------------------------------------------------------------------------------------- */
    function remove_category($category, $user_id = NULL)
    {
        if ( ! $this->_cache_enabled())
		{
            return FALSE;
        }

        if ($user_id == NULL)
        {
            $user_id = isset($this->CI->enseignant_id) ? $this->CI->enseignant_id : 0;
        }

		foreach($this->metadata as $skey => $arr)
		{
			if ($arr['user_id'] == $user_id && $arr['category'] == $category)
            {
                if ($this->CI->cache->delete($skey))
                {
                    unset($this->metadata[$skey]);
                }
			}
		}
	
		$this->_save_metadata();

        return TRUE;
    }

	/* --------------------------------------------------------------------------------------------
	 *
	 * CLEAR_ALL
	 *
	 * -------------------------------------------------------------------------------------------- */
	function clear_all()
	{
        if ( ! $this->_cache_enabled())
		{
            return FALSE;
        }

		if ( ! $this->CI->cache->save('metadata', array(), $this->metadata_ttl))
		{
			return FALSE;
		}

		if ( ! $this->CI->cache->clean())
		{
			return FALSE;
		}

		return TRUE;
	}

    /* ------------------------------------------------------------------------
     *
     * CLEAR USER'S CACHE
     *  
     * ------------------------------------------------------------------------ */
	function clear_user()
	{
        if ( ! $this->_cache_enabled())
		{
            return FALSE;
        }

        $user_id = isset($this->CI->enseignant_id) ? $this->CI->enseignant_id : 0;

		foreach($this->metadata as $skey => $arr)
		{
			if ($arr['user_id'] == $user_id)
			{	
				if ($this->CI->cache->delete($skey))
				{
					unset($this->metadata[$skey]);	
				}
			}
		}

		$this->_clear_expired();

		return TRUE;
	}

	/* --------------------------------------------------------------------------------------------
	 *
	 * GET METADATA
	 *
	 * -------------------------------------------------------------------------------------------- */
	function get_metadata()
    {
		return $this->metadata;
	}

	/* --------------------------------------------------------------------------------------------
	 *
	 * CACHE INFO
	 *
	 * -------------------------------------------------------------------------------------------- */
	function cache_info()
	{
		return $this->CI->cache->cache_info();
	}


	/* --- P R I V A T E   F U N C T I O N S ------------------------------------------------------ */


	/* --------------------------------------------------------------------------------------------
	 *
	 * _CACHE_ENABLED
	 *
     * -------------------------------------------------------------------------------------------- 
     *
     * Verify that the cache is enabled in dynamic settings (database).
     *
     * -------------------------------------------------------------------------------------------- */
    function _cache_enabled()
    {
        if ( ! $this->CI->cache_loaded)
        {
            return FALSE;
        }

        if ($this->CI->config->item('is_DEV'))
        {
            if ( ! $this->CI->config->item('cache_actif_dev'))
            {
                return FALSE;
            }
        }
        else
        {
            if ( ! $this->CI->config->item('cache_actif'))
            {
                return FALSE;
            }
        }
    
        return TRUE;
    }

	/* --------------------------------------------------------------------------------------------
	 *
	 * _CONVERT KEY
	 *
	 * -------------------------------------------------------------------------------------------- */
	private function _convert_key($key)
	{
        //
		// Prepend session_id into the key
        //

        $session_id = session_id() ?: '';

        $md5_key = md5($session_id . $key);
    
        return $md5_key;
        // return $session_id . $key;
	}

	/* --------------------------------------------------------------------------------------------
	 *
	 * _INCREMENT
	 *
	 * -------------------------------------------------------------------------------------------- */
	private function _increment($skey)
	{
        if (array_key_exists($skey, $this->metadata))
        {
            $this->metadata[$skey]['access']++;

            $this->_save_metadata();

            return TRUE;
        }
        
        return FALSE;
    }

	/* --------------------------------------------------------------------------------------------
	 *
	 * _SAVE METADATA
	 *
	 * -------------------------------------------------------------------------------------------- */
	private function _save_metadata()
	{
		if ($this->CI->cache->save('metadata', $this->metadata, $this->metadata_ttl))
		{
			return TRUE;
		}

		return FALSE;
	}

	/* --------------------------------------------------------------------------------------------
	 *
	 * _SAVE TO METADATA 
	 *
	 * -------------------------------------------------------------------------------------------- */
	private function _save_to_metadata($skey, $ttl, $category)
	{
        $user_id = isset($this->CI->enseignant_id) ? $this->CI->enseignant_id : 0;

		//
		// Save a new key to the metadata store
		//

		$this->metadata[$skey] = array(
			'user_id'  => $user_id,
			'category' => $category,
            'access'   => 1,
			'expired'  => time() + $ttl,
            'expired_human' => date_humanize(time() + $ttl, TRUE)
		);

		$this->_save_metadata();

		return TRUE;
	}

	/* --------------------------------------------------------------------------------------------
	 *
	 * _REMOVE FROM METADATA
	 *
	 * -------------------------------------------------------------------------------------------- */
	private function _remove_from_metadata($skey)
	{
		if (array_key_exists($skey, $this->metadata))
		{
			unset($this->metadata[$skey]);

			$this->_save_metadata();
		}

		return TRUE;
	}

	/* --------------------------------------------------------------------------------------------
	 *
	 * _CLEAR EXPIRED
	 *
	 * -------------------------------------------------------------------------------------------- */
	private function _clear_expired()
	{
		//
		// Clear expired keys and save metadata
		//

		$now = time();

		foreach($this->metadata as $skey => $arr)
		{
			if ($arr['expired'] < $now)
			{
				unset($this->metadata[$skey]);
			}
		}

        $this->_save_metadata();

		return TRUE;
	}
}

/* End of file KCache.php */
/* Location: ./application/libraries/KCache.php */
