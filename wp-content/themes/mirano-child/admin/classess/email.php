$subject="!NMID: ".$email_data["email_subject"]." CaseNo(".$group_details[0]['group_id'] ."_".$individual_data['product_interest_id'] .")\n\n";
					$message  = "<html><body>"."\n";
					$message .="<table cellpadding='0' cellspacing='0' bgcolor=#319d00 width='100%' style='margin:0 auto'><tr style='font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; color: rgb(255,255,255); line-height: 140%;'><td width='23'></td><td><span>!NMID: A Business Aggregator</span></td></tr></table>"."\n\n";
					$message .="<p>Dear Customer &nbsp;".$dear_text.",</p>"."\n";
					$message .="<p>".$email_data["email_message_to_interest_grp"]."</p>"."\n";

					if( $email_data["same_price_to_all"] || !intval( $individual_data['interest_unit_price'] )){
						if( $email_data["same_price_to_all"] ){
							$same_price_to_all = 1;
						}
						$message .="<p>A Price List is following for your interest:</p>"."\n";
						$message .="<table cellpadding='5' cellspacing='2' bgcolor=#ffffff width='100%' style='margin:0 auto'><tr style='font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; color: 333333; line-height: 140%;'>
						<td><span style='font-weight:bold;'>No Of Sells</span></td>
						<td><span style='font-weight:bold;'>Unit Price</span></td>
						<td><span style='font-weight:bold;'>Shipping Price</span></td>
						</tr>"."\n\n";
						$message .= $group_price_list_text . "</table>"."\n\n";
					}else{
						$message .="<p>The Unit Price For Your Interest Is:" ."&nbsp;".get_currency().":".$individual_data['interest_unit_price']."</p>\n";
					}
					$message .="<p>Your Interest Details:</p>"."\n";
					$message .="<p><b>Product Name: </b><a href=".get_site_url()."/my-interest-list/?action=edit&product_interest_id=".$individual_data['product_interest_id'] ."&product_name=".$individual_data['post_name'] ." >".$individual_data['product_name'] ."</a></p>\n";
					$message .="<p><b>Qty: </b>".$individual_data['interest_qty'] ."</p>\n";
					$message .="<p><b>Interest Start Date: </b>".$interest_start_date."</p>\n";
					$message .="<p><b>Interest End Date: </b>".$interest_end_date."</p>\n";
					if( $email_data['confirmation_within'] ){
						$message .="<p>You Have&nbsp;".$email_data['confirmation_within']."Hours to confirm that you are still want to purchase this product for the above Details</p>\n";
					}
					$message .="<p>To confirm Please click on Yes: <a href=".get_site_url()."/my-lists/?action=interest_confirmed&product_interest_id=".$individual_data['product_interest_id'] ." >Yes</a><a href=".get_site_url()."/my-lists/?action=interest_notconfirmed&product_interest_id=".$individual_data['product_interest_id'] ." >No</a>
					</p>\n";
					$message .="<table cellpadding='0' cellspacing='0' bgcolor=#319d00 width='100%' style='margin:0 auto'><tr style='font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; color: rgb(255,255,255); line-height: 140%;'><td width='23'></td><td><span>!NMID: A Business Aggregator</span></td></tr></table>"."\n\n";
					$message .= "</body></html>\n";
					$uid = md5(uniqid(time()));
					$header  = "From: !NMID <".$current_user->user_email.">\r\n";
					$header .= "Reply-To:".$current_user->user_email."\r\n";
					$header .= "MIME-Version: 1.0\r\n";
					$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
					$header .= "This is a multi-part message in MIME format.\r\n";
					$header .= "--".$uid."\r\n";
					$header .= "Content-type:text/html; charset=iso-8859-1\r\n";
					$header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
					$header .= $message."\r\n\r\n";
					$header .= "--".$uid."\r\n";
					//$header .= "Content-Type: application/octet-stream; name=\"".$attachment_name."\"\r\n"; // use different content types here
					$header .= "Content-Transfer-Encoding: base64\r\n";
					//$header .= "Content-Disposition: attachment; filename=\"".$attachment_name."\"\r\n\r\n";
					//$header .= $attachedfile."\r\n\r\n";
					$header .= "--".$uid."--";
					$attachments ="";
					$messages = "";