<div class="my_addresses">
    <div class="billing_address">
        <h2 class="billing_address_title"><?php _e( 'Billing address', TEXTDOMAIN );?></h2>
        <div class="form_element">
            <div style="display:none" id="save_billing_address_success_message" class="alert alert-success save_billing_address_success"></div>
            <div class="element_group">
                <div class="element_label">
                    <label><?php _e( 'Gender', TEXTDOMAIN );?></label><span>*</span>
                </div>
                <div class="element_value">
                    <select name="gender" id="gender">
                        <option value="Male" <?php if( $all_meta_for_user['gender'][0]=='Male' ){ ?> selected="selected"<?php } ?> ><?php _e( 'Male', TEXTDOMAIN );?></option>
                        <option value="Female" <?php if( $all_meta_for_user['gender'][0]=='Female' ){ ?> selected="selected"<?php } ?>><?php _e( 'Female', TEXTDOMAIN );?></option>
                    </select>
                </div>
            </div>
            <div style="display:none" id="gender_error_message">  </div>
            <div class="element_group">
                <div class="element_label">
                    <label><?php _e( 'First Name', TEXTDOMAIN );?></label><span>*</span>
                </div>
                <div class="element_value">
                    <input type="text" name="first_name" id="first_name" value="<?php echo $all_meta_for_user['first_name'][0]; ?>" id="first_name" />
                </div>
            </div>
            <div id="first_name_error_message">  </div>
            <div class="element_group">
                <div class="element_label">
                    <label><?php _e( 'Last Name', TEXTDOMAIN );?></label><span>*</span>
                </div>
                <div class="element_value">
                    <input type="text" name="last_name" id="last_name" value="<?php echo $all_meta_for_user['last_name'][0]; ?>" id="last_name" />
                </div>
            </div>
            <div class="element_group">
                <div class="element_save_button">
                    <p class="submit">
                        <button name="save_personal_data" id="save_personal_data" type="submit" class="btn btn-default button button-medium exclusive save_personal_data_btn">
                            <span>
                                <i class="icon-lock left"></i>
                                <?php _e( 'Save', TEXTDOMAIN );?>
                            </span>
                        </button>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="delivery_address">
        <h2 class="delivery_address_title"><?php _e( 'Delivery address', TEXTDOMAIN );?></h2>
        <div class="form_element">
            <div style="display:none" id="save_delivery_address_success_message" class="alert alert-success save_delivery_address_success"></div>
            <div style="display:none" id="save_delivery_address_error_message" class="alert alert-danger save_delivery_address_error"></div>
            <div class="element_group">
                <div class="element_label">
                    <label><?php _e( 'Old Password', TEXTDOMAIN );?></label><span>*</span>
                </div>
                <div class="element_value">
                    <input type="password" name="old_password" id="old_password" value="" id="old_password" />
                </div>
            </div>
            <div id="old_password_error_message">  </div>
            <div class="element_group">
                <div class="element_label">
                    <label><?php _e( 'New Password', TEXTDOMAIN );?></label><span>*</span>
                </div>
                <div class="element_value">
                    <input type="password" name="new_password" id="new_password" value="" id="new_password" />
                </div>
            </div>
            <div id="new_password_error_message">  </div>
            <div class="element_group">
                <div class="element_label">
                    <label><?php _e( 'Confirm Password', TEXTDOMAIN );?></label><span>*</span>
                </div>
                <div class="element_value">
                    <input type="password" name="confirm_password" id="confirm_password" value="" id="confirm_password" />
                </div>
            </div>
            <div class="element_group">
                <div class="element_save_button">
                    <p class="submit">
                        <button name="save_password_data" id="save_password_data" type="submit" class="btn btn-default button button-medium exclusive save_password_data_btn">
                            <span>
                                <i class="icon-lock left"></i>
                                <?php _e( 'Save', TEXTDOMAIN );?>
                            </span>
                        </button>
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>