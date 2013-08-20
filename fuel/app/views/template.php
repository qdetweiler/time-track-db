<?php
/**********************************************************************
 *                          - Template View -
 * 
 * The template.php view is the main template for the TimeTrack application
 * and is used to frame all pages in the system
 * 
 * @Author Dr. Kline
 * @Editor Quinn Detweiler
 **********************************************************************/
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
  "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php if(isset($title)) echo $title?></title>

    <?php echo Asset::css(array('defaultstyle.css', 'main.css')) ?>
    <?php if(isset($page_css)) echo Asset::css($page_css) ?>
    <?php if(isset($style)) echo $style ?>
    
  </head>

  <body>
    <div id="container">
      <div id="header"><?php echo render("header") ?></div>
      <div id="navigation"><?php echo render("links") ?></div>
      <div id="content"><!-- content -->

        <?php echo $content ?>

      </div><!-- content -->
    </div><!-- container -->

  </body>
  
  <?php echo Asset::js(array('jquery-1.9.1.min.js', 'jquery.form.min.js', 'jquery-ui-1.10.3.custom.min.js')) ?>
  <?php if(isset($page_js)) echo Asset::js($page_js) ?>
  <?php if(isset($script)) echo $script ?>
  
  
</html>