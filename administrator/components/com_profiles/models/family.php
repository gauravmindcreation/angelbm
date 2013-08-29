<?php
/**
 * @version     1.0.0
 * @package     com_profiles
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Profiles model.
 */
class ProfilesModelfamily extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_PROFILES';


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Family', $prefix = 'ProfilesTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_profiles.family', 'family', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_profiles.edit.family.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	1.6
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {

			//Do any procesing on fields here if needed

		}

		return $item;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since	1.6
	 */
	protected function prepareTable(&$table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id)) {

			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__profiles_families');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}

		}
	}
	
	public function getCount()
	{
		if(empty($this->_count))
		{
			$this->getFamilyIDs();
			$this->_count = count($this->_family_ids);
		}
		return $this->_count;
	}
	
	public function getFamilyIDs()
	{
		if(empty($this->_family_ids))
		{
			// Create a new query object.
			$db		= $this->getDbo();
			$query	= $db->getQuery(true);
	
			// Select the required fields from the table.
			$query->select('id');
			$query->from('`#__profiles_families`');
			
			$db->setQuery((string)$query);
			$this->_family_ids = $db->loadObjectList();
		}
		return $this->_family_ids;
	}
	
	public function saveOrder($pks = null, $order = null)
	{
		// Initialise variables.
		$table = $this->getTable();
		$conditions = array();

		if (empty($pks))
		{
			return JError::raiseWarning(500, JText::_($this->text_prefix . '_ERROR_NO_ITEMS_SELECTED'));
		}

		// update ordering values
		foreach ($pks as $i => $pk)
		{
			$table->load((int) $pk);

			// Access checks.
			if (!$this->canEditState($table))
			{
				// Prune items that you can't change.
				unset($pks[$i]);
				JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
			elseif ($table->ordering != $order[$i])
			{
				$table->ordering = $order[$i];

				if (!$table->store())
				{
					$this->setError($table->getError());
					return false;
				}

				// Remember to reorder within position and client_id
				$condition = $this->getReorderConditions($table);
				$found = false;

				foreach ($conditions as $cond)
				{
					if ($cond[1] == $condition)
					{
						$found = true;
						break;
					}
				}

				if (!$found)
				{
					$key = $table->getKeyName();
					$conditions[] = array($table->$key, $condition);
				}
			}
		}

		// Execute reorder for each category.
		foreach ($conditions as $cond)
		{
			$table->load($cond[0]);
			$table->reorder($cond[1]);
		}

		// Clear the component's cache
		$this->cleanCache();

		return $table;
	}
	
	public function fixFields()
	{
		return true;
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query
			->select('a.do_for_fun, a.do_for_fun_image, a.id')
			->from('j25_profiles_families AS a');
			
		$rows = $db->setQuery($query)->loadObjectList();
		
		foreach ($rows as $row) {
			$db->setQuery($query->clear()->update('j25_profiles_families')->set(
				array(
					'adoption_story = '.$db->quote($row->do_for_fun),
					'adoption_story_image = '.$db->quote($row->do_for_fun_image)
				)
			)->where("id = '{$row->id}'"))->execute();
		}
		
		return true;
	}
	
	public function importFamilies()
	{
		$db		= JFactory::getDbo();
		$db->setDebug(true);
		JError::$legacy = true;
		$query	= $db->getQuery(true);
		
		$db->setQuery("TRUNCATE TABLE j25_profiles_families")->execute();
		$db->setQuery("TRUNCATE TABLE j25_profiles_photos")->execute();
		
		$query->select('*')->from('jos_family')->order('id ASC');
		
		$families	= $db->setQuery($query)->loadObjectList();
		
		$i=0;
		foreach ($families as $family) {
		
			foreach($family as $key => $value) {
				$family->$key = $db->escape($value);
			}
			
			$username = strtolower("family-{$i}");
			
			$query->clear()
			->insert('j25_profiles_families')
			->set(array(
				"id = '{$family->id}'",
				"username = '{$username}'",
				"my_sport = '{$family->sport_hus}'",
				"spouse_sport = '{$family->sport_wi}'",
				"my_food = '{$family->food_hus}'",
				"spouse_food = '{$family->food_wi}'",
				"my_hobby = '{$family->hobby_hus}'",
				"spouse_hobby = '{$family->hobby_wi}'",
				"my_tradition = '{$family->tradition_hus}'",
				"spouse_tradition = '{$family->tradition_wi}'",
				"my_music_group = '{$family->musical_group_hus}'",
				"spouse_music_group = '{$family->musical_group_wi}'",
				"my_movie = '{$family->movie_hus}'",
				"spouse_movie = '{$family->movie_wi}'",
				"my_vacation_spot = '{$family->dream_vacation_hus}'",
				"spouse_vacation_spot = '{$family->dream_vacation_wi}'",
				"my_tv_show = '{$family->tv_show_hus}'",
				"spouse_tv_show = '{$family->tv_show_wi}'",
				"my_subject_in_school = '{$family->subject_school_hus}'",
				"spouse_subject_in_school = '{$family->subject_school_wi}'",
				"my_book = '{$family->book_hus}'",
				"spouse_book = '{$family->book_wi}'",
				"my_animal = '{$family->animal_hus}'",
				"spouse_animal = '{$family->animal_wi}'",
				"my_occupation = '{$family->occ_hus}'",
				"spouse_occupation = '{$family->occ_wi}'",
				"my_religion = '{$family->religion_hus}'",
				"spouse_religion = '{$family->religion_wi}'",
				"my_education = '{$family->edu_hus}'",
				"spouse_education = '{$family->edu_wi}'",
				"my_memory_w_spouse = '{$family->memory_width_spouse_hus}'",
				"spouse_memory_w_spouse = '{$family->memory_width_spouse_wi}'",
				"my_family_activity = '{$family->family_activity_hus}'",
				"spouse_family_activity = '{$family->family_activity_hwi}'",
				"my_childhood_memory = '{$family->childhood_memory_hus}'",
				"spouse_childhood_memory = '{$family->childhood_memory_wi}'",
				"my_holiday = '{$family->holiday_hus}'",
				"spouse_holiday = '{$family->holiday_wi}'",
				"my_childhood_toy = '{$family->childhood_toy_hus}'",
				"spouse_childhood_toy = '{$family->childhood_toy_wi}'",
				"description = '{$family->des}'",
				"do_for_fun = '{$family->fun}'",
				"do_for_fun_image = '{$family->fun_image}'",
				"gallery = '{$family->image}'",
				"spouse_race = '{$family->race_hus}'",
				"my_race = '{$family->race_wi}'",
				"first_name = '{$family->family_name}'",
				"dear_birthmother = '{$family->intro}'",
				"about_us = '{$family->about_us}'",
				"about_us_image = '{$family->about_us_image}'",
				"our_home = '{$family->our_home}'",
				"our_home_image = '{$family->our_home_image}'",
				"ext_family = '{$family->our_extended}'",
				"ext_family_image = '{$family->our_extended_image}'",
				"profile_status = '{$family->status}'",
				"state = '{$family->published}'",
				"ordering = '{$family->ordering}'",
				"adopt_race = '{$family->race_interest}'",
				"video = '{$family->video_link}'"
			));
			if ($db->setQuery($query)->execute()) {
				$i++;
			}
		}
		$this->fixRace();
		$this->fixNames();
		$this->fixUserNames();
		$this->moveImages();
		$this->moveGallery();
		
		return $i;
	}
	
	public function fixNames()
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$families = $db->setQuery($query->select('*')->from('j25_profiles_families')->where('spouse_name = "" '))->loadObjectList();
		
		foreach ($families as $family) {
			$names = explode('&', $family->first_name);
			if(count($names) == 2) {
				$names[0] = trim(str_replace('amp;', '', $names[0]));
				$names[1] = trim(str_replace('amp;', '', $names[1]));
			} else {
				$names[0] = trim(str_replace('amp;', '', $family->first_name));
				$names[1] = trim(str_replace('amp;', '', $family->spouse_name));
			}
			$db->setQuery($query->clear()->update('j25_profiles_families')->set(array("first_name = '{$names[0]}'", "spouse_name = '{$names[1]}'"))->where("id = '{$family->id}'"))->execute();
		}
	}
	
	public function fixRace()
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$families = $db->setQuery($query->select('*')->from('j25_profiles_families')->where('adopt_race != "" '))->loadObjectList();
		
		foreach ($families as $family) {
			$races = explode(', ', $family->adopt_race);
			$registry = new JRegistry();
			$registry->loadArray($races);
			$races = (string)$registry;
			
			$db->setQuery($query->clear()->update('j25_profiles_families')->set("adopt_race = '{$races}'")->where("id = '{$family->id}'"))->execute();
		}
	}
	
	public function fixUserNames()
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$families = $db->setQuery($query->select('*')->from('j25_profiles_families'))->loadObjectList();
		
		$i=0;
		foreach ($families as $family) {
			$username = strtolower(trim($family->first_name));
			if(!empty($family->spouse_name)) {
				$username .= '-'.strtolower(trim($family->spouse_name));
			}
			$result = $db->setQuery($query->clear()->select('id')->from('j25_profiles_families')->where("username = '{$username}'"))->loadObject();
			if ($result->id) {
				$username .= '-'.$i;
			}
			
			$db->setQuery($query->clear()->update('j25_profiles_families')->set("username = '{$username}'")->where("id = '{$family->id}'"))->execute();
			$i++;
		}
	}
	
	public function moveGallery()
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->select(array('id', 'gallery'))
			->from('j25_profiles_families');
		
		$families = $db->setQuery($query)->loadObjectList();
		foreach ($families as $gallery) {
			$photos = unserialize($gallery->gallery);
			if (is_array($photos)) {
				foreach ($photos as $photo) {
					if (empty($photo)) continue;
					$origpath = realpath(JPATH_SITE."/../components/com_family/assets/upload/{$photo}");
					$destpath = JPATH_SITE."/uploads/profiles/{$gallery->id}/";
					JFolder::create($destpath);
				
					if (!JFile::exists($origpath)) continue;
					
					$info = pathinfo($origpath);
					$name = $info['filename'].'.'.strtolower($info['extension']);
					
					if (!JFile::exists($destpath.$name)) {
						JFile::copy($origpath, $destpath.$name);
					}
					
					ProfilesHelper::resizeImage($destpath, $name);
					$db->setQuery($query->clear()->insert('j25_profiles_photos')->set(array(
						"path = '{$name}'",
						"family_id = {$gallery->id}"
					)))->execute();
				}
			}
		}
	}
	
	public function moveImages()
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->select(array('id', 'about_us_image', 'our_home_image', 'ext_family_image', 'family_traditions_image', 'adoption_story_image', 'do_for_fun_image'))
			->from('j25_profiles_families');
		
		$families = $db->setQuery($query)->loadObjectList();
		
		foreach ($families as $photos) {
			foreach ($photos as $key => $photo) {
				if($key === 'id') continue;
				$origpath = realpath(JPATH_SITE."/../components/com_family/assets/upload/{$photo}");
				$destpath = JPATH_SITE."/uploads/profiles/{$photos->id}/";
				JFolder::create($destpath);
				
				if (!JFile::exists($origpath)) continue;
				
				$info = pathinfo($origpath);
				$name = $info['filename'].'.'.strtolower($info['extension']);
				
				if (!JFile::exists($destpath.$name)) {
					JFile::copy($origpath, $destpath.$name);
				}
				
				ProfilesHelper::resizeImage($destpath, $name);
				$db->setQuery($query->clear()->update('j25_profiles_families')->set("{$key} = '{$name}'")->where("id = '{$photos->id}'"))->execute();
			}
		}
	}
	
	public function resetHits()
	{
		return JFactory::getDbo()->setQuery('TRUNCATE TABLE #__profiles_stats')->execute();
	}

}