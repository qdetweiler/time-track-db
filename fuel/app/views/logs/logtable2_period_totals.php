
  <div class='content_box'>
    
    <?php foreach ($users as $user): ?>

    <?php if(count($users) > 1):?>
    <div class='total_display_wrapper'>
    <?php endif?>
      <!--------------------- Header ---------------------->

      <!--    More than one user    -->
      <?php if (count($users) > 1): ?>

        <span class='pt_name'>
          <h3><?php echo $user['name'] ?></h3>
        </span>

      <?php endif?>

      <span class='pt_total_display'>
        <h3><?php echo $user['total']?></h3>
      </span>
    
    <?php if(count($users) > 1):?>
    </div>
    <?php endif?>
    
    <?php endforeach //end for each user?>
    
</div>

