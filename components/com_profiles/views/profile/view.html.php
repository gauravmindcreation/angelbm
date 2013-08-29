<?php
/**
 * @version     1.0.0
 * @package     com_profiles
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */

// No direct access
defined('_JEXEC') or die;

/**
 * HTML View class for the Profiles component
 */
class ProfilesViewProfile extends JViewLegacy
{
	protected $state;
	protected $item;

	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$params		= $app->getParams();

		// Get some data from the models
		$this->state	= $this->get('State');
		$this->family	= $this->get('Item');
		$this->gallery	= $this->get('Gallery');
		
		$this->data		= $this->family;
		
		$this->prepImages();
		
		$this->prepareDocument();
		
        parent::display($tpl);

	}
	
	protected function prepareDocument()
	{
		$title = $this->family->first_name;
		
		if (isset($this->family->spouse_name))
		{
			$title .= ' & ' . $this->family->spouse_name . ' are ';
		}
		else
		{
			$title .= ' is ';
		}

		$title .= 'hoping to adopt a baby.';
		$description = substr(strip_tags($this->family->dear_birthmother), 0, 160);
		$description = explode(' ', $description);
		array_pop($description);
		$description = implode(' ', $description) . '...';

		JFactory::getDocument()
			->setTitle($title)
			->setDescription($description);
	}
	
	public function addHit()
	{
		$this->getModel()->addHit();
	}
	
	public function prepImages()
	{
		$basepath = "http://www.angeladoptioninc.com/uploads/profiles/{$this->data->id}/";
		
		if(isset( $this->data->our_home_image )) {
			$this->our_home_image = "{$basepath}300_{$this->data->our_home_image}";
			$this->our_home_image_lb = $basepath.$this->data->our_home_image;
		}
		
		if(isset( $this->data->do_for_fun_image )) {
			$this->fun_image = "{$basepath}300_{$this->data->do_for_fun_image}";
			$this->fun_image_lb = $basepath.$this->data->do_for_fun_image;
		}
		
		if(isset( $this->data->ext_family_image )) {
			$this->our_extended_image = "{$basepath}300_{$this->data->ext_family_image}";
			$this->our_extended_image_lb = $basepath.$this->data->ext_family_image;
		}
		
		if(isset( $this->data->ext_family_image_spouse )) {
			$this->our_extended_image_spouse = "{$basepath}300_{$this->data->ext_family_image_spouse}";
			$this->our_extended_image_spouse_lb = $basepath.$this->data->ext_family_image_spouse;
		}
		
		if(isset( $this->data->about_us_image )) {
			$this->about_us_image = "{$basepath}300_{$this->data->about_us_image}";
			$this->about_us_image_lb = $basepath.$this->data->about_us_image;
		}
		
		if(isset( $this->data->adoption_story_image )) {
			$this->adoption_story_image = "{$basepath}300_{$this->data->adoption_story_image}";
			$this->adoption_story_image_lb = $basepath.$this->data->adoption_story_image;
		}
	}
}
