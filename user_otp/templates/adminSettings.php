<?php
/**
 * ownCloud - One Time Password plugin
 *
 * @package user_otp
 * @author Frank Bongrand
 * @copyright 2013 Frank Bongrand fbongrand@free.fr
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU AFFERO GENERAL PUBLIC
 * License along with this library. If not, see <http://www.gnu.org/licenses/>.
 * Displays <a href="http://opensource.org/licenses/AGPL-3.0">GNU AFFERO GENERAL PUBLIC LICENSE</a>
 * @license http://opensource.org/licenses/AGPL-3.0 GNU AFFERO GENERAL PUBLIC LICENSE
 *
 */
?>
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
                        <?php print_unescaped($input['label']); ?> : 
                        <?php if($input['type'] === "text") { ?>
                            <input type="text" name="<?php p($input['name']); ?>" value="<?php echo $_[$input['name']]?>">
                        <?php }else if ($input['type'] === "checkbox") { ?>
                            <input type="checkbox" name="<?php p($input['name']); ?>" id="<?php p($input['name']); ?>" <?php if ($_[$input['name']]) p(' checked'); ?>>
                        <?php }else if ($input['type'] === "radio") { ?>
                            <?php $name=$input['name']; ?>
                            <br/>
                            <?php foreach ($input['values'] as $radio): ?>
                            <input type="radio" name="<?php p($name); ?>" value="<?php p($radio['value'])?>" <?php if ($_[$input['name']]===$radio['value']) p(' checked'); ?>> <?php p($radio['label'])?><br/>
                            <?php endforeach; ?>
                        <?php }else if ($input['type'] === "select") { ?>
							<?php $name=$input['name']; ?>
							<select name="<?php p($name); ?>">
							<?php foreach ($input['values'] as $select): ?>
                            <option value="<?php p($select['value'])?>" <?php if ($_[$input['name']]===$select['value']) p(' selected'); ?>> <?php p($select['label'])?></option>
                            <?php endforeach; ?>
                            </select>
						<?php } ?>
                    </p>
                <?php endforeach; ?>
            </fieldset>
        <?php endforeach; ?>
        <input type="hidden" name="requesttoken" value="<?php p($_['requesttoken']);?>" />
        <input type='submit' value='<?php p($l->t('Save'));?>'>
    </div>
</form>
