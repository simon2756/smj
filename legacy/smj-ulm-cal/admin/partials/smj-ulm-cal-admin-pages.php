<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Smj_Ulm_Cal
 * @subpackage Smj_Ulm_Cal/admin/partials
 */


//------------------------------------------------------------------------------
//!
//! Function: 		smj_ulm_cal_options_page
//!
//! Description:	add options page for smj_ulm_page
//!
//! Parameter: 		None
//!
//! Return: 		None
//------------------------------------------------------------------------------
add_action( 'admin_menu', 'smj_ulm_cal_options_page' );
function smj_ulm_cal_options_page() {

	//The icon in Base64 format
	$icon_base64 = 'data:image/svg+xml;base64,PHN2ZyBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGZpbGwtcnVsZT0iZXZlbm9kZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIgc3Ryb2tlLW1pdGVybGltaXQ9IjIiIHZpZXdCb3g9IjAgMCAyMzMgMjU3IiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxwYXRoIGQ9Im0xNTEuODA1IDIzLjI3M2gtNzIuODMydi05LjMwOWMwLTcuNzA3LTYuMjU3LTEzLjk2NC0xMy45NjQtMTMuOTY0cy0xMy45NjMgNi4yNTctMTMuOTYzIDEzLjk2NHY5Ljc0M2MtMjguNzQ5IDMuNTIxLTUxLjA0NiAyOC4wNTEtNTEuMDQ2IDU3Ljc0OHYxMTYuMzYzYzAgMzIuMTEyIDI2LjA3MSA1OC4xODIgNTguMTgyIDU4LjE4MmgxMTYuMzY0YzMyLjExMSAwIDU4LjE4Mi0yNi4wNyA1OC4xODItNTguMTgydi0xMTYuMzYzYzAtMzAuMzY1LTIzLjMxMS01NS4zMjgtNTIuOTk2LTU3Ljk1NHYtOS41MzdjMC03LjcwNy02LjI1Ny0xMy45NjQtMTMuOTYzLTEzLjk2NC03LjcwNyAwLTEzLjk2NCA2LjI1Ny0xMy45NjQgMTMuOTY0em0tMTI5LjUwNyAxNzIuNjEzYzAgMjAuMzMzIDE2LjUwOCAzNi44NDIgMzYuODQxIDM2Ljg0MmgxMTIuNDk5YzIwLjMzNCAwIDM2Ljg0Mi0xNi41MDkgMzYuODQyLTM2Ljg0MnYtMTAyLjc5M2gtMTg2LjE4MnptMTQxLjM4OCAxNi4yNDZjMTUuMDY5IDAgMjkuMTg4LTcuNDY3IDI5LjE4OC0yMy4yMTUgMC0yNS42NTgtMzcuMDYyLTE5LjI3OC0zNy4wNjItMjcuOTY2IDAtMi40NDQgMi44NTEtNC43NTIgNy43MzgtNC43NTIgNC40OCAwIDcuODc0IDIuMDM3IDcuODc0IDUuNDMxdjEuMzU3aDIwLjA5MnYtMS4zNTdjMC0xMy4xNjktMTEuMjY3LTIyLjgwOC0yNy44My0yMi44MDgtMTYuNDI3IDAtMjcuOTY2IDkuNTAzLTI3Ljk2NiAyMy40ODcgMCAyNS4xMTUgMzYuOTI2IDE4LjE5MSAzNi45MjYgMjcuODMgMCAyLjg1MS0zLjM5NCA0LjYxNi04LjE0NSA0LjYxNi01LjE1OSAwLTkuNTAzLTIuMTcyLTkuNTAzLTYuMTA5di0xLjYzaC0yMC4yMjh2MS42M2MwIDEzLjU3NSAxMS41MzkgMjMuNDg2IDI4LjkxNiAyMy40ODZ6bS02My45NDIgMGMxOC4xOTEgMCAzMC4xMzgtMTEuNDA0IDMwLjEzOC0yOC43ODF2LTIuMDM2aC0yMC41djIuNTc5YzAgNi4yNDUtMy45MzYgOS45MS05LjYzOCA5LjkxLTcuMTk1IDAtMTAuNzI1LTQuMDcyLTEwLjcyNS0xMS44MTF2LTEzLjAzMmMwLTcuNzM5IDMuNjY1LTExLjgxMSAxMC43MjUtMTEuODExIDUuNzAyIDAgOS42MzggMy42NjUgOS42MzggOS45MXYyLjU3OWgyMC41di0yLjAzNmMwLTE3LjM3Ny0xMS45NDctMjguNzgxLTMwLjI3NC0yOC43ODEtMTkuMjc4IDAtMzAuOTUzIDExLjY3Ni0zMC45NTMgMzAuMDAzdjEzLjMwNGMwIDE4LjMyOCAxMS42NzUgMzAuMDAzIDMxLjA4OSAzMC4wMDN6bS02MC40MTMtMi4wMzdoMjAuMzY0di02OS4yMzZoLTIwLjM2NHptMC03Ni41NjdoMjAuMzY0di0xOS44MjFoLTIwLjM2NHoiIGZpbGw9IiNhN2FhYWQiLz48L3N2Zz4=';
			
	//The icon in the data URI scheme
	$icon_data_uri = 'data:image/svg+xml;base64,' . $icon_base64;

    add_menu_page(
        'SMJ Ulm Kalender', //$page_title
        'SMJ Ulm Kalender', //$menu_title
        'manage_options', // $capability
        'smj_ulm_cal_options', // $menu_slug
        'smj_ulm_cal_options_page_usage_html',  //$function
		$icon_data_uri, // $icon_url 
        20
    );

	//uses the same slug name as the main menu page
	add_submenu_page(
        'smj_ulm_cal_options', //$parent_slug
        'SMJ Ulm Kalender',		//$page_title
        'Benutzung',//$menu_title
        'manage_options',//$capability
        'smj_ulm_cal_options',//$menu_slug
        'smj_ulm_cal_options_page_usage_html',//$//$function 
    );

	add_submenu_page(
        'smj_ulm_cal_options', //$parent_slug
        'SMJ Ulm Kalender',		//$page_title
        'Einstellungen',//$menu_title
        'manage_options',//$capability
        'smj_ulm_cal_options__settings',//$menu_slug
        'smj_ulm_cal_options_page_settings_html',//$//$function 
    );

	add_submenu_page(
        'smj_ulm_cal_options', //$parent_slug
        'SMJ Ulm Kalender',		//$page_title
        'Statistik',//$menu_title
        'manage_options',//$capability
        'smj_ulm_cal_options__statistic',//$menu_slug
        'smj_ulm_cal_options_page_statistic_html',//$//$function 
    );
}

//------------------------------------------------------------------------------
//!
//! Function: 		smj_ulm_cal_options_page_settings_html
//!
//! Description:	returns html for the smj_ulm_cal settings page
//!
//! Parameter: 		None
//!
//! Return: 		None
//------------------------------------------------------------------------------
function smj_ulm_cal_options_page_settings_html() {
	?>
    <div class="wrap">

	<h1>SMJ Ulm Kalender: Einstellungen</h1>
	<p>Die hinterlegte URL des <strong>Hauptkalenders</strong> wird stündlich auf Änderungen überprüft.</p>
<p>Aus dem Hauptkalender werden die <strong>Abo-Kalender</strong> generiert. Die Abo-Kalender enthalten nur Termine mit den angegebenen Kategorien.</p>
<p>Es können mehrere Kategorien angegeben werden (durch Komma getrennt). Sind keine Kategorien angegeben, werden alle Termine verwendet.</p>
	<!--Settings From-->
	<div>
		<form action="options.php" method="post">
			<?php
			// output security fields for the registered setting "wporg_options"
			settings_fields( 'smj_ulm_cal_options' );
			// output setting sections and their fields
			// (sections are registered for "wporg", each field is registered to a specific section)
			do_settings_sections( 'smj_ulm_cal' );
			// output save settings button
			submit_button( 'Einstellungen speichern');
			?>
		</form>
	</div>
	<!--Settings From Ende-->

	<hr style="margin: 20px;"/>
	<p>Der Kalender wird jede Stunde aktualisiert. Eine manuelle Aktualisierung kann mit dem Button <i>"Aktualisiere Kalender"</i> durchgeführt werden.</p>

	<div class="log_file ">
		<div style="display: flex;   gap: 10px;">

		<form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
			<!-- form fields go here -->
			<input type="hidden" name="action" value="smj_ulm_cal_refresh_calender">
			<input class="button button-primary" type="submit" value="Aktualisiere Kalender">
		</form>
			
		<form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
			<!-- form fields go here -->
			<input type="hidden" name="action" value="smj_ulm_cal_delete_cache">
			<input class="button button-primary" type="submit" value="Lösche Kalender Cache">
		</form>

		<form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
			<!-- form fields go here -->
			<input type="hidden" name="action" value="smj_ulm_cal_delete_log">
			<input class="button button-primary" type="submit" value="Lösche Log Datei">
		</form>

		</div>	
	</div>


	</div>

		<!--Log Section-->
		<div class="log_file ">
			<h2> Log Datei:</h2>
			<?php
			$log_file_path = plugin_dir_path(__FILE__) ."../../data/logs.txt";
			if(file_exists($log_file_path)){
				$file = file($log_file_path);
				for ($i = max(0, count($file)-500); $i < count($file); $i++) {
					$splitted = explode(";",$file[$i]);

					$is_error_class = trim($splitted[0]) == "1" ? 'class="log-error"' : ''; 

					echo "<div $is_error_class><strong>".$splitted[1]."</strong>: " .$splitted[2] . "</div>";
				}
			}
			?>
		</div>
		<!--Log Section End-->
    <?php
}

//------------------------------------------------------------------------------
//!
//! Function: 		smj_ulm_cal_options_page_usage_html
//!
//! Description:	returns html for the smj_ulm_cal usage page
//!
//! Parameter: 		None
//!
//! Return: 		None
//------------------------------------------------------------------------------
function smj_ulm_cal_options_page_usage_html() {
	?>
	<!--Usage-->
	<div  class="wrap">
		<h1>SMJ Ulm Kalender: Benutzung</h1>
	

		<h2>Alle Termine </h2>
		<div class="sub-section">

			<div class="shortcode-title">Liste einfügen:</div>
			<div >
				<code class="shortcode" id="smj_full_list_copy">[smj-ulm-cal_fulllist]</code>
				<button class="button button-primary" style="vertical-align:middle;"  onclick="copyContent('smj_full_list_copy')">Shortcode in Zwischenablage kopieren</button>
			</div>


			<div class="shortcode-title">Liste einfügen mit Start und Enddatum:</div>
			<div> Das Datum muss im Format <em>YYYY-MM-DD</em> sein!</div>
			<div >
				<code class="shortcode" id="smj_full_list_dates_copy">[smj-ulm-cal_fulllist startDate="2024-01-01" endDate="2024-12-31"]</code>
				<button class="button button-primary" style="vertical-align:middle;"  onclick="copyContent('smj_full_list_dates_copy')">Shortcode in Zwischenablage kopieren</button>
			</div>

			<div class="shortcode-title">Liste einfügen und nach Kategorie filtern:</div>
			<div> Die Kategorien müssen mit ',' getrennt werden</div>
			<div >
				<code class="shortcode"  id="smj_full_list_categories_copy">[smj-ulm-cal_fulllist categories="Zeltlager,Abteilung"]</code>
				<button class="button button-primary" style="vertical-align:middle;"  onclick="copyContent('smj_full_list_categories_copy')">Shortcode in Zwischenablage kopieren</button>
			</div>

			<div class="shortcode-title">Liste einfügen und vergangene Termine ausblenden:</div>
			<div >
				<code class="shortcode"  id="smj_full_list_hide_past_events_copy">[smj-ulm-cal_fulllist hidePastEvents="yes"]</code>
				<button class="button button-primary" style="vertical-align:middle;"  onclick="copyContent('smj_full_list_hide_past_events_copy')">Shortcode in Zwischenablage kopieren</button>
			</div>
			
			<div class="shortcode-title" style="margin-top: 2em">Alle Optionen kombinieren:</div>
			<div >
				<code class="shortcode"  id="smj_full_list_all_copy">[smj-ulm-cal_fulllist hidePastEvents="yes"  categories="Zeltlager" startDate="2024-01-01" endDate="2024-12-31"]</code>
				<button class="button button-primary" style="vertical-align:middle;"  onclick="copyContent('smj_full_list_all_copy')">Shortcode in Zwischenablage kopieren</button>
			</div>
		</div>
		
		<h2>Nächste Termine: </h2>
		<div class="sub-section">

			<div class="shortcode-title">Liste einfügen:</div>
			<div> <em>num_max_events:</em> maximale Termine die angzeigt werden. <em>num_max_events</em> muss eine Zahl <em>>0</em> sein!</div>
			<div> <em>num_months:</em> maximale Anzahl an Monaten die anzeigt werden. <em>num_months</em> muss eine Zahl <em>>0</em> sein!</div>
			<div >
				<code class="shortcode"  id="smj_next_events_list_copy">[smj-ulm-cal_nextevents num_max_events=5 num_months=3]</code>
				<button class="button button-primary" style="vertical-align:middle;"  onclick="copyContent('smj_next_events_list_copy')">Shortcode in Zwischenablage kopieren</button>
			</div>


			<div class="shortcode-title">Liste einfügen und nach Kategorie filtern:</div>
			<div> Die Kategorien müssen mit ',' getrennt werden</div>
			<div >
				<code class="shortcode"  id="smj_events_list_categories_copy">[smj-ulm-cal_nextevents num_max_events=5 num_months=3 categories="Zeltlager,Abteilung"]</code>
				<button class="button button-primary" style="vertical-align:middle;"  onclick="copyContent('smj_events_list_categories_copy')">Shortcode in Zwischenablage kopieren</button>
			</div>
		</div>
		
		<p><i>(Füge den Shortcode auf deiner Seite/Beitrag ein)</i></p>

		<script>
			const copyContent = async (domElement) => {
				let text = document.getElementById(domElement).innerHTML;
				await navigator.clipboard.writeText(text);
			}
		</script>
	</div>
  	<!--Usage-->
	<?php
}


//------------------------------------------------------------------------------
//!
//! Function: 		smj_ulm_cal_options_page_statistic_html
//!
//! Description:	returns html for the smj_ulm_cal logs and statistic page
//!
//! Parameter: 		None
//!
//! Return: 		None
//------------------------------------------------------------------------------
function smj_ulm_cal_options_page_statistic_html() {
	?>

	<div class="wrap">

		<h1>SMJ Ulm Kalender: Statistiken</h1>

		<!--Categories Section-->
		<div class="abo_list ">
			<h2>Abo Kalender URLs:</h2>
			<?php

			$out_calendars_dir = plugin_dir_path(__FILE__) ."../../data/out_calendars/";
			$calendar_urls_file_path =$out_calendars_dir."calendar_urls.txt";

			if(file_exists($calendar_urls_file_path)){
				$file = file($calendar_urls_file_path);

				echo '<div class="calendars-container">';

				foreach($file as $line){
					$splitted_line = explode(";",$line);
					$calendar_name = $splitted_line[0];
					$calendar_url = $splitted_line[1];
					$calendar_categories = trim($splitted_line[2]);
					if(empty($calendar_categories)){
						$calendar_categories = "Alle Kategorien";
					}

					$calendar_events = array();
					//parse calendar events
					$calendar_stats_file = $out_calendars_dir.$calendar_name."_statistic.txt";
					if(file_exists($calendar_stats_file)){
						$stats_file = file($calendar_stats_file);
						foreach($stats_file as $stats_line){
							array_push($calendar_events, $stats_line);
						}
					}
	
					echo "<details>";

					//calendar name + url
					echo "<summary>";
					echo "<strong>".$calendar_name.": </strong>";
					echo "<a href=".$calendar_url.">".$calendar_url."</a>";
					echo "<em> (Anzahl Termine: ".count($calendar_events).",   Kategorien: ".$calendar_categories.")</em>"; 
					echo "</summary>";

					//display calendar events
					$calendar_stats_file = $out_calendars_dir.$calendar_name."_statistic.txt";

					echo "<ul>";
					foreach($calendar_events as $calendar_event){
						echo "<li>".$calendar_event."</li>";
					}
					echo "</ul>";
			

					echo "</details>";
				
				}

				echo "</div>";
			}
			?>
		</div>
		<!--Categories Section End-->




		<!--Categories Section-->
		<div class="log_file ">
			<h2>Termine nach Kategorien sortiert:</h2>
			<?php
			$statistic_file_path = plugin_dir_path(__FILE__) ."../../data/statistic.txt";

			if(file_exists($statistic_file_path)){
				$file = file($statistic_file_path);

				echo '<div class="statistic-container">';
				foreach($file as $line){
					$splitted_line = explode(";",$line);
					$category_name = $splitted_line[0];
					$num_events = count($splitted_line);
					echo '<details class="statistic-category">';

						echo "<summary><strong>&quot;$category_name&quot; (".($num_events-1)." Termine)</strong></summary>";
						echo '<ul>';
							foreach(array_slice($splitted_line, 1) as $event){
								echo "<li>". $event."</li>";
							}
						echo "</ul>";

					echo "</details>";
				}
				echo "</div>";
			}
			?>
		</div>
		<!--Categories Section End-->
	</div>
	<?php
}

?>
