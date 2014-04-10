
  <div class='content_box'>
    
    <?php foreach ($users as $user): ?>

    <?php if(count($users) > 1):?>
    <div class='total_display_wrapper'>
    <?php endif?>
      <!--------------------- Header ---------------------->

      <!--    More than one user    -->
      <?php if (count($users) > 1): ?>

        <div class='pt_name'>
          <h3 class='pt_name_h3'><?php echo $user['name'] ?></h3>
        </div>

      <?php endif?>

      <div class='pt_total_display'>
        <?php foreach ($user['period_totals'] as $label => $total): ?>
            <div class='group_display'>  
                <span class='group_label'><?php echo $label ?>:</span>
                <span class='group_total'> <?php echo $total ?></span>
            </div>
        <?php endforeach ?>
        <?php if (count($user['period_totals']) > 1): ?>
            <div id='break_div'><hr id='break'></div>
            <div class='group_display'>
                <span class='group_label'>Total:</span>
                <span class='group_total'><?php echo $user['total'] ?></span>
            </div>
        <?php endif ?>
        <?php if(count($user['period_totals']) == 0):?>
            <p>None</p>
        <?php endif?>
      </div>
    <?php if(count($users) > 1):?>  
    </div>
    <?php endif?>
    
    <?php endforeach //end for each user?>
    
</div>

