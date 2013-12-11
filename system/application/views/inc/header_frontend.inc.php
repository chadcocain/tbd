<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<?php
if(isset($seo) && key_exists('metadescription', $seo) && key_exists('metakeywords', $seo)) {
?>
<meta name="description" content="<?php echo $seo['metadescription']; ?>" />
<meta name="keywords" content="<?php echo $seo['metakeywords']; ?>" />
<meta name="robots" content="index,follow" />
<meta name="revisit-after" content="2 days" />
<meta name="google-site-verification" content="VP1Hrk8DuL7IuzE5vogU5iSpyXUvbrFCLnclLsdqFK0" />
<?php
}
if(isset($seo) && key_exists('pagetitle', $seo)) {
?>
<title><?php echo $seo['pagetitle']; ?></title>
<?php
}
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/reset.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/front_master.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/navigation.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/rating.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/srm.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/front_forms.css" />
<!--[if IE 6]><link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/front_ie6.css" /><![endif]-->
<?php 
// check if array to pass is set
if(isset($array_showJS)) {
	echo showJS($array_showJS);
} else {
	echo showJS();
}
?>
</head>
<body>
