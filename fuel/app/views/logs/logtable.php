<?php foreach($users as $user):?>
    <table>
        <thead>
            <?php if(count($users) > 1):?>
            <tr>
                <td id='user_info' colspan="3">
                    <?php echo $user['name']?>
                </td>
            </tr>
            <?php endif?>
            
            <?php if(($user['num_logs'] != 0) && $display_type != 'period_totals'):?>
            <tr>
                <td id='day'>Day</td>
                <?php if($display_type=='all'):?>
                    <td id='logs'>Log(s)</td>
                <?php endif ?>
                <td id='total'>Total</td>
            </tr>
            <?php endif?>
            
        </thead>
        
        <?php if($user['num_logs'] == 0):?>
        <tr>
            <td id="no_logs_msg">No logs to display for user.</td>
        </tr>
        <?php else:?>

            <?php if($display_type != 'period_totals'):?>
                <?php foreach($user['days'] as $day):?>
                <tr>
                    <td class='day'><?php echo $day['string']?></td>
                    <?php if($display_type=='all'):?>
                    <td class='logs'>
                        <?php if(count($day['logs'])):?>
                            <?php foreach($day['logs'] as $log):?>
                            <p><?php echo $log['start']." - ".$log['end']?></p>
                            <?php endforeach?>
                        <?php else:?>
                            <p>None</p>
                        <?php endif?>
                    </td>
                    <?php endif?>
                    <td class='total'><?php echo $day['total']?></td>
                </tr>
                <?php endforeach?>
            <?php endif ?>
            <tr>
                <td id='overall_total' colspan='3'><?php echo $user['total']?></td>
            </tr>
            
        <?php endif?>
    </table>
<?php endforeach?>


