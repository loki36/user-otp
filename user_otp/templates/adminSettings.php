<form id='user_otp_form' method="POST" action="#">
    <div id="userotpSettings" class="personalblock">
        <ul>
            <?php foreach ($_['allTab'] as $tab): ?>
                <li><a href="#<?php p($tab['name']) ?>"><?php p($tab['label']) ?></a></li>
            <?php endforeach; ?>
        </ul>
        <?php foreach ($_['allTab'] as $tab): ?>
            <fieldset id="<?php p($tab['name']) ?>">
                <?php foreach ($_[$tab['arrayConf']] as $input): ?>
                    <p>
                        <?php p($input['label']); ?> : 
                        <?php if($input['type'] === "text") { ?>
                            <input type="text" name="<?php p($input['name']); ?>" value="<?php echo $_[$input['name']]?>">
                        <?php }else if ($input['type'] === "checkbox") { ?>
                            <input type="checkbox" name="<?php p($input['name']); ?>" id="<?php p($input['name']); ?>" <?php if ($_[$input['name']]) p(' checked'); ?>>
                        <?php } ?>
                    </p>
                <?php endforeach; ?>
            </fieldset>
        <?php endforeach; ?>
        <input type='submit' value='Save'>
    </div>
</form>
