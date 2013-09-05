<?php
$app = JFactory::getApplication();

$conf = JComponentHelper::getParams('com_profiles');
$options = array(
	'driver' => $conf->get('dbtype'),
	'host' => $conf->get('host'),
	'user' => $conf->get('user'),
	'password' => $conf->get('password'),
	'database' => $conf->get('db'),
	'prefix' => $conf->get('dbprefix')
);

try
{
	$db = JDatabaseDriver::getInstance($options);
}
catch (RuntimeException $e)
{
	if (!headers_sent())
	{
		header('HTTP/1.1 500 Internal Server Error');
	}

	jexit('Database Error: ' . $e->getMessage());
}

$atts = array('onchange' => 'this.form.submit();');
$selectedType = $app->getUserState('filter.family_type');
$typeOptions = array(
	(object) array(
		'value' => '',
		'text' => 'Select Family Type'
	),
	(object) array(
		'value' => 'single',
		'text' => 'Single'
	),
	(object) array(
		'value' => 'married',
		'text' => 'Married'
	)
);
$selectedReligion = $app->getUserState('filter.family_religion');

$query = $db->getQuery(true)
	->select('a.my_religion, a.spouse_religion')
	->from('#__profiles_families AS a');

$religionOptions = $db->setQuery($query)->loadObjectList();

$my_religion = JArrayHelper::getColumn($religionOptions, 'my_religion');
$spouse_religion = JArrayHelper::getColumn($religionOptions, 'spouse_religion');

$combined = array_merge($my_religion, $spouse_religion);
array_walk($combined, function(&$value, $key) {
	$value = rtrim($value);
});
$combined = array_unique($combined);
asort($combined);
$religionOptions = array();
foreach ($combined as $option)
{
	if (empty($option))
	{
		continue;
	}

	$religionOptions[] = (object) array(
		'value' => $option, 
		'text' => $option
	);
}
$first = (object) array(
	'value' => '',
	'text' => 'Select Family Religion'
);
array_unshift($religionOptions, $first);
?>
<div class="custom module-hearts-bg find-a-family">
<div class="row">
<div class="container">
<h2>Find the Right Family</h2>
<p class="hidden-phone italic script">We only work with adoptive parents who are ready to love and support a child. Browse through adoptive parent profiles and start searching for the family that is right for you.</p>
<form class="form-inline span10 offset1" method="post" action="">
<label>I'm Looking For:&nbsp;</label>
<?php
echo JHtml::_('select.genericlist', $typeOptions, 'filter_family_type', $atts, 'value', 'text', $selectedType);
echo JHtml::_('select.genericlist', $religionOptions, 'filter_family_religion', $atts, 'value', 'text', $selectedReligion);
?>
<button type="submit" class="btn btn-info">Find Families</button>
</form>
</div>
</div>
</div>