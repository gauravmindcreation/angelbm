<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.protostar
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Getting params from template
$params = JFactory::getApplication()->getTemplate(true)->params;

$app = JFactory::getApplication();

$this->setBase($this->baseurl);

// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = $app->getCfg('sitename');

if($task == "edit" || $layout == "form" )
{
	$fullWidth = 1;
}
else
{
	$fullWidth = 0;
}

// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');
$this->addScript('templates/' .$this->template. '/js/template.js');

// Add Stylesheets
$this->addStyleSheet('templates/'.$this->template.'/css/template.css');

// Load optional RTL Bootstrap CSS
JHtml::_('bootstrap.loadCss', false, $this->direction);

// Add current user information
$user = JFactory::getUser();

// Adjusting content width
if ($this->countModules('position-7') && $this->countModules('position-8'))
{
	$span = "span6";
}
elseif ($this->countModules('position-7') && !$this->countModules('position-8'))
{
	$span = "span9";
}
elseif (!$this->countModules('position-7') && $this->countModules('position-8'))
{
	$span = "span9";
}
else
{
	$span = "span12";
}

// Logo file or site title param
if ($this->params->get('logoFile'))
{
	$logo = '<img src="'. JUri::root() . $this->params->get('logoFile') .'" alt="'. $sitename .'" />';
}
elseif ($this->params->get('sitetitle'))
{
	$logo = '<span class="site-title" title="'. $sitename .'">'. htmlspecialchars($this->params->get('sitetitle')) .'</span>';
}
else
{
	$logo = '<span class="site-title" title="'. $sitename .'">'. $sitename .'</span>';
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0" />
	<jdoc:include type="head" />
	<?php
	// Use of Google Font
	if ($this->params->get('googleFont'))
	{
		$fullName = $this->params->get('googleFontName');
		list($fontFamily, $sizes) = explode(':', $fullName);
	?>
		<link href='http://fonts.googleapis.com/css?family=<?php echo $fullName; ?>' rel='stylesheet' type='text/css' />
		<style type="text/css">
			h1,h2,h3,h4,h5,h6,.site-title{
				font-family: '<?php echo str_replace('+', ' ', $fontFamily);?>', sans-serif;
			}
		</style>
	<?php
	}
	?>
	<!--[if lt IE 9]>
		<script src="<?php echo $this->baseurl ?>/media/jui/js/html5.js"></script>
	<![endif]-->
</head>

<body class="site <?php echo $option
	. ' view-' . $view
	. ($layout ? ' layout-' . $layout : ' no-layout')
	. ($task ? ' task-' . $task : ' no-task')
	. ($itemid ? ' itemid-' . $itemid : '')
	. ($params->get('fluidContainer') ? ' fluid' : '');
?>">
	<!-- Header -->
	<header class="header" role="banner">
		<div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : '');?>">
			<div class="header-inner clearfix">
				<a class="brand pull-left" href="<?php echo $this->baseurl; ?>">
					<?php echo $logo;?> <?php if ($this->params->get('sitedescription')) { echo '<div class="site-description">'. htmlspecialchars($this->params->get('sitedescription')) .'</div>'; } ?>
				</a>
				<div class="header-search pull-right">
					<jdoc:include type="modules" name="position-0" style="none" />
				</div>
			</div>
		</div>
	</header>
	<?php if ($this->countModules('position-1')) : ?>
	<nav class="navigation" role="navigation">
		<div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : '');?>">
			<jdoc:include type="modules" name="position-1" style="none" />
		</div>
	</nav>
	<?php endif; ?>
	<?php if ($this->countModules('banner')) : ?>
	<jdoc:include type="modules" name="banner" style="xhtml" />
	<?php endif; ?>
	<div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : '');?>">
		<div class="row<?php echo ($params->get('fluidContainer') ? '-fluid' : '');?>">
			<?php if ($this->countModules('position-8')) : ?>
			<!-- Begin Sidebar -->
			<div id="sidebar" class="span3">
				<div class="sidebar-nav">
					<jdoc:include type="modules" name="position-8" style="xhtml" />
				</div>
			</div>
			<!-- End Sidebar -->
			<?php endif; ?>
			<main id="content" role="main" class="<?php echo $span;?>">
				<!-- Begin Content -->
				<jdoc:include type="modules" name="position-3" style="xhtml" />
				<jdoc:include type="message" />
				<jdoc:include type="component" />
				<jdoc:include type="modules" name="position-2" style="none" />
				<!-- End Content -->
			</main>
			<?php if ($this->countModules('position-7')) : ?>
			<div id="aside" class="span3">
				<!-- Begin Right Sidebar -->
				<jdoc:include type="modules" name="position-7" style="well" />
				<!-- End Right Sidebar -->
			</div>
			<?php endif; ?>
		</div>
	</div>
	<!-- Footer -->
	<footer class="footer" role="contentinfo">
		<jdoc:include type="modules" name="footer" style="none" />
		<div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : '');?>">
			<hr />
			<p class="pull-right"><a href="#top" id="back-top"><?php echo JText::_('TPL_GIVE_BACKTOTOP'); ?></a></p>
			<p>&copy; <?php echo $sitename; ?> <?php echo date('Y');?></p>
		</div>
	</footer>
	<jdoc:include type="modules" name="debug" style="none" />
</body>
</html>
