<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<?php 
if(isset($seo)) {
?>
<title><?php echo $seo['pagetitle']; ?></title>
<meta name="keywords" content="<?php echo $seo['metadescription']; ?>" />
<meta name="description" content="<?php echo $seo['metakeywords']; ?>" />
<?php
}
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/reset.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/master.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/admin.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/forms.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/galleriffic.css" />
<?php echo showJS(); ?>
</head>
<body>
