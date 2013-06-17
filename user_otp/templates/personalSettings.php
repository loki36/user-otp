<form id="otp_personal_form">
    <div id="userotpSettings" class="personalblock">
        <legend><strong>TOTP Configuration</strong></legend>
        <?php if($_['UserExists']) { ?>
            <p>
                User Token Seed : <?php p($_['UserTokenSeed']); ?> 
                <?php if($_['UserPrefixPin']){ ?>
                    / User Pin : <?php p($_['UserPin']); ?>
                <?php } ?>
                <?php if($_['UserLocked']){ ?>
                    / <strong>User is locked</strong>
                <?php } ?>
            </p>
            <p>
                User Algorithm : <?php p($_['UserAlgorithm']); ?> 
                / 
                User Token Time Interval Or Last Event : <?php p($_['UserTokenTimeIntervalOrLastEvent']); ?>
            </p>
            <p>
                Token Url Link : <a href="<?php p($_['UserTokenUrlLink']); ?>"><?php p($_['UserTokenUrlLink']); ?></a>
            </p>
            <p>
                UserTokenQrCode : <img src="<?php p($_['UserTokenQrCode']); ?>">
            </p>
            <input type="hidden" id="otp_action" name="otp_action" value="delete_otp">
            <input id="otp_submit_action" type='button' value='Delete'>
        <?php }else{ ?>
            <p>
                User Token Seed : <input type="text" name="UserTokenSeed" value="">
                <?php if($_['UserPrefixPin']){ ?>
                    / User Pin :  <input type="text" name="UserPin" value="">
                <?php } ?>
                <input type="hidden" name="otp_action" value="create_otp">
                <input id="otp_submit_action" type='submit' value='Create'>
            </p>
        <?php } ?>
    </div>
</form>
