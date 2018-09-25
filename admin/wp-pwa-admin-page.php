<?php
	global $wp_pwa;

	$settings = get_option('wp_pwa_settings');
	//var_dump($settings);
	//delete_option('wp_pwa_settings');
	$current_user = wp_get_current_user();

	if (isset($settings["synced_with_wp_pwa"])) {
		$synced_with_wp_pwa = $settings["synced_with_wp_pwa"];
	} else {
		$synced_with_wp_pwa = false;
	}

	if (isset($settings["wp_pwa_status"])) {
		$wp_pwa_status = $settings["wp_pwa_status"];
	} else {
		$wp_pwa_status = 'disabled';
	}

	if (isset($settings["wp_pwa_amp"])) {
		$wp_pwa_amp = $settings["wp_pwa_amp"];
	} else {
		$wp_pwa_amp = 'disabled';
	}

	/* step & progress */
	$progress = 0;
	$step = 0;
	$wp_version = get_bloginfo('version');
	$rest_api_installed = $wp_pwa->rest_api_installed;
	$rest_api_active = $wp_pwa->rest_api_active;
	$rest_api_compatible = true;

	if (version_compare($wp_version, '4.7', '>=')) { //From WP 4.7, the REST API is already installed.
		$rest_api_installed = true;
		$rest_api_active = true;
	}

	if (version_compare($wp_version, '4.4', '<')) { //REST API Plugin is only compatible from WP 4.4 ahead
		$rest_api_compatible = false;
	} else if (!$rest_api_installed) {
		$step = 1;
	} else if ($rest_api_installed && !$rest_api_active) {
		$step = 2;
		$progress = 33;
	} else if ( $rest_api_installed && $rest_api_active && !$synced_with_wp_pwa) {
		$step = 3;
		$progress = 66;
	} else if ( $rest_api_installed && $rest_api_active && $synced_with_wp_pwa) {
		$step = 4;
		$progress = 100;
	}
?>
<div class="wrap">
	<p class="title is-2">WordPress PWA</p>
	<div class="columns">
		<div class="column is-half">
			<div class="box">
				<nav class="level">
					<div class="level-left">
						<p class="title is-5">1. Install REST API Plugin</p>
					</div>
					<div class="level-right">
						<?php echo ( $rest_api_installed ? '<span class="tag is-success">Installed&nbsp;&nbsp;<span class="icon is-small"><i class="fa fa-check-circle" aria-hidden="true"></i></span></span>':'');?>
						<?php echo ( !$rest_api_compatible ? '<span class="tag is-danger">Error&nbsp;&nbsp;<span class="icon is-small"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span></span>':'');?>
					</div>
				</nav>
				<?php if(!$rest_api_compatible):?>
		 		 <article class="message is-danger">
		 		   <div class="message-body">
		 		     <strong>Attention!</strong> The REST API Plugin requires WordPress 4.4 or higher, your WordPress version is <?php echo get_bloginfo('version');?>
		 		   </div>
		 		 </article>
		 		<?php elseif ($step==1): ?>
				<div class="content">
					<p>
						WP PWA uses the <a href="http://v2.wp-api.org/" target="_blank">REST API</a> plugin to send the content from your site to the App.
						<?php
							if($rest_api_installed) {
									$install_api_href ="#";
							} else {
								$install_api_href = get_site_url() . '/wp-admin/plugin-install.php?tab=plugin-information&plugin=rest-api';
							}
						?>
					</p>
					<p>
						<a href="<?php echo $install_api_href; ?>" class="button button-lg button-primary" <?php echo ($step<=1 ? 'style="color:white;"' : 'style="display:none;"'); ?>>Download Plugin</a>
					</p>
				</div>
				<?php endif;?>
			</div>

			<div class="box">
				<nav class="level">
					<div class="level-left">
						<p class="title is-5">2. Activate REST API Plugin</p>
					</div>
					<div class="level-right">
						<?php echo ( $rest_api_active  ? '<span class="tag is-success">Active&nbsp;&nbsp;<span class="icon is-small"><i class="fa fa-check-circle" aria-hidden="true"></i></span></span>':'');?>
					</div>
				</nav>
				<?php if ($step<=2 && $rest_api_compatible): ?>
				<div class="content">
					<p>
						Remember to activate the WP REST API Plugin
					</p>
					<p>
						<?php
							if($rest_api_installed || $rest_api_active ) {
									$activate_api_href =$wp_pwa->get_activate_wp_rest_api_plugin_url();
									$activate_class = "button button-lg button-primary";
							} else {
								$activate_api_href = "#";
								$activate_class = "button button-lg disabled";
							}
						?>
						<a href="<?php echo $activate_api_href; ?>" class="<?php echo $activate_class; ?>" style="color:white">Activate REST API Plugin</a>
					</p>
				</div>
				<?php endif;?>
			</div>

			<div class="box">
				<nav class="level">
					<div class="level-left">
						<p class="title is-5">3. Connect this WordPress with the dashboard</p>
					</div>
					<div id='label-created' class="level-right" <?php echo ( $step > 3 ? '':'style="display:none;"');?>>
						<span class="tag is-success">Connected&nbsp;&nbsp;<span class="icon is-small"><i class="fa fa-check-circle" aria-hidden="true"></i></span></span>
					</div>
				</nav>
					<?php if ($step==3): ?>
					<div class="content">
						<p>
							Copy the Site Id from the dashboard.
						</p>
						<?php
							/*
								Params accepted by https://dashboard.worona.org/register
									?name
									?email
									?siteURL
									?siteName
									?siteId
							*/
							$name = "";
							$email = "";
							$siteURL = get_site_url();
							$siteName = get_bloginfo( 'name' );
							$siteId = $settings["wp_pwa_siteid"];

							$current_user = wp_get_current_user();
							if ($current_user instanceof WP_User) {
									$name = $current_user->user_firstname;
									if($name == '') {
										$name = $current_user->display_name;
									}
									$email = $current_user->user_email;
							}
						?>
						<input id="param-name" type="hidden" value="<?php echo $name; ?>">
						<input id="param-email" type="hidden" value="<?php echo $email; ?>">
						<input id="param-siteURL" type="hidden" value="<?php echo $siteURL; ?>">
						<input id="param-siteName" type="hidden" value="<?php echo $siteName; ?>">
						<input id="param-siteId" type="hidden" value="<?php echo $siteId; ?>">

						<p id="label-create-buttons">
							<a href="#" target="_blank" style="color:white" class="open-change-siteid button button-lg button-primary button-hero">Insert a valid Site ID</a>
						</p>
					</div>

					<?php elseif($step<3):?>
					<div class="content">
						<p>
							Insert your Site ID from the Dashboard.
						</p>
						<p>
							<span style="text-decoration: underline;">Insert a PWA Site ID</span>
						</p>
					</div>
					<?php endif;?>
			</div>

			<div class="box">
				<nav class="level">
					<div class="level-left">
						<p class="title is-5">4. Configure your site</p>
					</div>
				</nav>
				<div class="content">
					<p>
						Go to the dashboard to preview your PWA and configure it.
					</p>
					<p>
						<?php
							$wp_pwa_dashboard_url = "https://dashboard.worona.org/site/" . $settings["wp_pwa_siteid"];

							if ($step==4) {
								$button_disabled = false;
							} else {
								$wp_pwa_dashboard_url = "#";
								$button_disabled = true;
							}
						?>
						<a id="dashboard-button" href="<?php echo $wp_pwa_dashboard_url ?>" target="_blank" style="color:white" class="button button-lg <?php echo ($button_disabled ? 'disabled button-hero' : 'button-primary button-hero'); ?>">Configure</a>
					</p>
				</div>
			</div>
	 </div><!-- column is-one-third -->
	 <div class="column">
	 </div>
	 <div class="column is-one-third">
		 <div id="wp-pwa-status-box" class="box" <?php echo (($step==4)?'':'style="display:none;"');?>>
			 <nav class="level">
				 <div class="level-left">
					 <p class="title is-5">
						 	Progressive Web App
						 	<span id="wp-pwa-status-enabled" class="icon is" <?php echo (($wp_pwa_status!='disabled')?'style="color:#97cd76;"':'style="display:none;color:#97cd76"');?>>
								<i class="fa fa-check-circle" aria-hidden="true"></i>
							</span>
							<span id="wp-pwa-status-saving" class="icon is" style="display:none;color:#808080">
								<i class="fa fa-clock-o" aria-hidden="true"></i>
							</span>
							<span id="wp-pwa-status-disabled" class="icon" <?php echo (($wp_pwa_status=='disabled')?'style="color:#ed6c63;"':'style="display:none;color:#ed6c63"');?>>
								<i class="fa fa-times-circle" aria-hidden="true"></i>
							</span>
					 </p>
				 </div>
				 <div class="level-right">
					 <div class="control">
						<div class="select">
								<select id="wp-pwa-status">
									<?php
										$options = array(array('mobile','Enabled'), array('disabled','Disabled')); // [value, Label]
										$output = '';
										for( $i=0; $i<count($options); $i++ ) {
											$output .= '<option value="' . $options[$i][0] . '"'
											. ( $wp_pwa_status == $options[$i][0] ? 'selected="selected"' : '' ) . '>'
											. $options[$i][1]
											. '</option>';
										}
										echo $output;
									?>
								</select>
							</div>
					 </div>
			 	</div>
			 </nav>
		 </div>
		 <div id="wp-pwa-amp-box" class="box" <?php echo (($step==4)?'':'style="display:none;"');?>>
			 <nav class="level">
				 <div class="level-left">
					 <p class="title is-5">
						 	Google AMP
						 	<span id="wp-pwa-amp-enabled" class="icon is" <?php echo (($wp_pwa_amp!='disabled')?'style="color:#97cd76;"':'style="display:none;color:#97cd76"');?>>
								<i class="fa fa-check-circle" aria-hidden="true"></i>
							</span>
						 	<span id="wp-pwa-amp-saving" class="icon is" style="display:none;color:#808080">
								<i class="fa fa-clock-o" aria-hidden="true"></i>
							</span>
							<span id="wp-pwa-amp-disabled" class="icon" <?php echo (($wp_pwa_amp=='disabled')?'style="color:#ed6c63;"':'style="display:none;color:#ed6c63"');?>>
								<i class="fa fa-times-circle" aria-hidden="true"></i>
							</span>
					 </p>
				 </div>
				 <div class="level-right">
					 <div class="control">
						<div class="select">
								<select id="wp-pwa-amp">
									<?php
										$options = array(array('posts','Enabled'), array('disabled','Disabled')); // [value, Label]
										$output = '';
										for( $i=0; $i<count($options); $i++ ) {
											$output .= '<option value="' . $options[$i][0] . '"'
											. ( $wp_pwa_amp == $options[$i][0] ? 'selected="selected"' : '' ) . '>'
											. $options[$i][1]
											. '</option>';
										}
										echo $output;
									?>
								</select>
							</div>
					 </div>
			 	</div>
			 </nav>
		 </div>
		 <article class="message is-info">
			<div class="message-header">
			  <?php if ($step == 4) { echo "Plugin settings"; } else { echo "Follow the 4 steps to configure the plugin";} ?>
			</div>
			<div id="#lateral-info-box"class="message-body">
				<?php if($step < 4):?>
					<progress class="progress is-info is-medium" value="<?php echo $progress;?>" max="100"></progress>
					<p id="step-message">
						You are on <strong>step <?php echo $step;?>/4.</strong>
					</p>
					<hr>
				<?php endif;?>
				<?php if ($rest_api_active):?>
				<p>
					<h2>REST API URL:</h2>
					<?php print(rest_url()); ?>
				</p>
			  <?php endif;?>
				<div id="wp-pwa-siteid-lateral" <?php echo ($synced_with_wp_pwa?'':'style="display:none;"');?>>
					<p>
						<hr>
						<h2>Site Id:</h2>
						<span id="wp-pwa-siteid-span"><?php echo $settings['wp_pwa_siteid'];?></span> <a class="open-change-siteid" style="text-decoration: underline; font-size: 10px;" href="#">change</a>
					</p>
				</div>
				<div id="wp-pwa-advanced-settings-lateral" style="text-align: right;" <?php echo ($synced_with_wp_pwa?'':'style="display:none;"');?>>
					<p>
						<hr>
						<a class="open-api-fields" href="#" style="font-size: 10px; text-decoration: underline;">WP API Fields</a>
						<span style="font-size: 10px; margin: 0 5px 0 5px;"> </span>
						<a class="open-excludes" href="#" style="font-size: 10px; text-decoration: underline;">PWA Excludes</a>
						<span style="font-size: 10px; margin: 0 5px 0 5px;"> </span>
						<a class="open-advanced-settings" href="#" style="font-size: 10px; text-decoration: underline;">Advanced settings</a>
					</p>
				</div>
			</div>
		 </article>
		 <article id="lateral-change-siteid" class="message is-warning" style="display:none;">
			 <div class="message-header">
					<nav class="level">
						<div class="level-left">
							<strong>Change Site Id</strong>
						</div>
						<div class="level-right">
							<a href="#" class="close-change-siteid" style="color:inherit"><i class="fa fa-times-circle" aria-hidden="true"></i></a>
						</div>
					</nav>
			  </div>
			  <div class="message-body">
					<p>
						<strong>Warning!</strong> Changing your Site Id can create conflicts with the Dashboard and the PWA.
					</p>
					<br>
					<p>
						<article id="lateral-error-siteid" class="message is-danger" style="display:none;">
							<div class="message-body">
								<nav class="level">
									<div id="siteid-error-message" class="level-left">
										The siteid is not valid
									</div>
									<div class="level-right">
										<a href="#" class="close-error-siteid" style="color:inherit"><strong>x</strong></a>
									</div>
								</nav>
							</div>
						</article>
					</p>
					<table class="form-table">
						<tr>
							<th scope="row">Site Id</th>
							<td>
									<fieldset>
											<label>
													<input type="text" id="wp-pwa-siteid" value="<?php echo ($settings['synced_with_wp_pwa']) ? $settings['wp_pwa_siteid'] : ''; ?>"/>
													<br />
													<span class="description">Enter a valid Site Id</span>
											</label>
									</fieldset>
							</td>
						</tr>
					</table>
					<p>
						<a href="#" id="change-siteid"class="button button-lg">Change</a>
					</p>
			  </div>
		 </article>
		 <article id="lateral-excludes" class="message is-warning" style="display:none;">
			 <div class="message-header">
					<nav class="level">
						<div class="level-left">
							<strong>Exclude URLs in the PWA</strong>
						</div>
						<div class="level-right">
							<a href="#" class="close-excludes" style="color:inherit"><i class="fa fa-times-circle" aria-hidden="true"></i></a>
						</div>
					</nav>
			  </div>
			  <div class="message-body">
					<p><strong>Important:</strong> Add only one URL per line.</p>
					<br>
					<p>
						<?php
							$wp_pwa_excludes = $settings['wp_pwa_excludes'];

							$excludes_output = '';
							for( $i=0; $i<count($wp_pwa_excludes); $i++ ) {
								$excludes_output .= $wp_pwa_excludes[$i];

								if($i + 1 < (count($wp_pwa_excludes))){
									$excludes_output .= "\n";
								}
							}
						?>
						<textarea id="excludes" class="textarea"><?php echo $excludes_output; ?></textarea>
					</p>
					<p><em>* You can use regular expressions.</em></p>
					<br>
					<p>
						<a href="#" id="save-excludes"class="button button-lg">Save</a>
					</p>
			  </div>
		 </article>
		 <article id="lateral-api-fields" class="message is-warning" style="display:none;">
			 <div class="message-header">
					<nav class="level">
						<div class="level-left">
							<strong>Filter WP API fields</strong>
						</div>
						<div class="level-right">
							<a href="#" class="close-api-fields" style="color:inherit"><i class="fa fa-times-circle" aria-hidden="true"></i></a>
						</div>
					</nav>
			  </div>
			  <div class="message-body">
					<p><strong>Important:</strong> Add only one field per line.</p>
					<br>
					<p>
						<?php
							$wp_pwa_api_fields = $settings['wp_pwa_api_fields'];

							$excludes_output = '';
							for( $i=0; $i<count($wp_pwa_api_fields); $i++ ) {
								$api_fields_output .= $wp_pwa_api_fields[$i];

								if($i + 1 < (count($wp_pwa_api_fields))){
									$api_fields_output .= "\n";
								}
							}
						?>
						<textarea id="api-fields" class="textarea"><?php echo $api_fields_output; ?></textarea>
					</p>
					<p><em>* Use dot notation for nested fields (i.e: title.rendered).</em></p>
					<br>
					<p>
						<a href="#" id="save-api-fields"class="button button-lg">Save</a>
					</p>
			  </div>
		 </article>
		 <article id="lateral-advanced-settings" class="message is-danger" style="display:none;" >
			 <div class="message-header">
					<nav class="level">
						<div class="level-left">
							<strong> Advanced Settings</strong>
						</div>
						<div class="level-right">
							<a href="#" class="close-advanced-settings" style="color:inherit"><i class="fa fa-times-circle" aria-hidden="true"></i></a>
						</div>
					</nav>
			  </div>
			  <div class="message-body">
					<p>
						<strong>Warning!</strong> Changing this settings can break your progressive web app.
					</p>
					<br>
					<p>
						<article id="lateral-error-advanced-settings" class="message is-danger" style="display:none;">
							<div class="message-body">
								<nav class="level">
									<div id="advanced-settings-error-message" class="level-left">
										Error modifying advanced settings.
									</div>
									<div class="level-right">
										<a href="#" class="close-error-advanced-settings" style="color:inherit"><strong>x</strong></a>
									</div>
								</nav>
							</div>
						</article>
					</p>
					<table class="form-table">
						<tr>
							<th scope="row">Enviroment</th>
							<td>
									<fieldset>
											<label>
													<select id="wp-pwa-env">
														<?php
															$options = array( 'pre', 'prod');
															$env = $settings['wp_pwa_env'];
															$output = '';
															for( $i=0; $i<count($options); $i++ ) {
																$output .= '<option '
					 											. ( $env == $options[$i] ? 'selected="selected"' : '' ) . '>'
					 											. $options[$i]
					 											. '</option>';
															}
															echo $output;
														?>
													</select>
											</label>
									</fieldset>
							</td>
						</tr>
						<tr>
							<th scope="row">SSR Server</th>
							<td>
									<fieldset>
											<label>
													<input type="text" id="wp-pwa-ssr" value="<?php echo $settings['wp_pwa_ssr']; ?>"/>
													<br />
													<span class="description">SSR URL</span>
											</label>
									</fieldset>
							</td>
						</tr>
						<tr>
							<th scope="row">Static Server</th>
							<td>
									<fieldset>
											<label>
													<input type="text" id="wp-pwa-static" value="<?php echo $settings['wp_pwa_static']; ?>"/>
													<br />
													<span class="description">Static URL</span>
											</label>
									</fieldset>
							</td>
						</tr>
						<tr>
							<th scope="row">AMP Server</th>
							<td>
									<fieldset>
											<label>
													<input type="text" id="wp-pwa-amp-server" value="<?php echo $settings['wp_pwa_amp_server']; ?>"/>
													<br />
													<span class="description">AMP Server</span>
											</label>
									</fieldset>
							</td>
						</tr>
						<tr>
							<th scope="row">Force FrontPage</th>
							<td>
									<fieldset>
											<label>
													<input type="checkbox" id="wp-pwa-force-frontpage" <?php echo ($settings['wp_pwa_force_frontpage'] === true ?'checked':''); ?>/>
													<br />
													<span class="description">Force FrontPage to retrieve Latest posts</span>
											</label>
									</fieldset>
							</td>
						</tr>
						<tr>
							<th scope="row">HtmlPurifier</th>
							<td>
									<fieldset>
											<label>
													<input type="button" id="wp-pwa-purge-htmlpurifier-cache" value="Purge cache"/>
											</label>
									</fieldset>
							</td>
						</tr>
					</table>
					<p>
						<a href="#" id="change-advanced-settings"class="button button-lg">Save changes</a>
					</p>
			  </div>
		 </article>
	 </div><!-- column one-third-->
	</div><!-- columns -->
</div><!-- wrap -->
