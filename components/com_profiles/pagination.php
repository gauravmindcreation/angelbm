<?php

jimport('joomla.html.pagination');

class ProfilePagination extends JPagination
{
	public $data = null;
	
	public function getNextLink()
	{
		if (is_null($this->data))
		{
			$this->data = $this->_buildDataObject();
		}
		
		$next = $this->data->next;
		
		if (empty($next->link))
		{
			return '<span class="next-disabled">'.strtolower($next->text).' &rsaquo;</span>';
		}
		
		return  '<a class="link-next" title="Next" href="'.$next->link.'">'.strtolower($next->text).' &rsaquo;</a>';
	}

	public function loadMoreBtn($text = 'Load More')
	{
		if (is_null($this->data))
		{
			$this->data = $this->_buildDataObject();
		}
		
		$next = $this->data->next;
		
		if (empty($next->link))
		{
			return '<span class="next-disabled">'.$text.' &rsaquo;</span>';
		}
		
		return  '<a class="btn btn-info btn-block btn-large link-next" id="loadMore" href="'.$next->link.'">'.$text.'</a>';
	}
	
	public function getPrevLink()
	{
		if (is_null($this->data))
		{
			$this->data = $this->_buildDataObject();
		}
		
		$prev = $this->data->previous;
		
		if (empty($prev->link))
		{
			return '<span class="prev-disabled">&lsaquo; '.strtolower($prev->text).'</span>';
		}
		
		return  '<a class="link-prev" href="'.$prev->link.'">&lsaquo; '.strtolower($prev->text).'</a>';
	}
	
	public function getResultsCounter()
	{
		return str_replace(array('Results', ' - '), array('showing', '-'), parent::getResultsCounter());
	}
}