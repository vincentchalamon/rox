<script type="text/javascript" src="script/fieldset.js"></script>
<script type="text/javascript" src="script/blog_suggest.js"></script>
<div>
<?php
        //$User = A PP_User::login();
        $member = $this->model->getLoggedInMember();

        $Model = new Blog;

        $callback = $this->getCallbackOutput('BlogController', 'createProcess');

        // get the saved post vars
        // todo: grab from page model
        $vars = array();
        // get current request
        $request = PRequest::get()->request;
        
        $errors = array();
        $lang = array();
        $words = new MOD_words();

        $catIt = $Model->getCategoryArray(false, $member);
        $tripIt = $Model->getTripFromUserIt($member->id);
        $google_conf = PVars::getObj('config_google');
        //$defaultVis = A PP_User::getSetting($User->getId(), 'APP_blog_defaultVis');
        // defaults to public then
        $defaultVis = false;
        
        if (!isset($vars['errors']) || !is_array($vars['errors'])) {
            $vars['errors'] = array();
        }
        
        if (!isset($request[2]) || $request[2] != 'finish') {
            $actionUrl = implode('/', $request);
            $submitName = '';
            $submitValue = $words->getSilent('BlogCreateSubmit');
        } else {
            echo '<p>'.$words->get('BlogCreateFinishText')."</p>\n";
            echo '<p>'.$words->get('BlogCreateFinishInfo')."</p>\n";
        }

/**
 * edit and create form template controller // mostly copied from Blog Application
 * for documentation look at build/blog/editcreateform.php
 * @author: Michael Dettbarn (bw: lupochen)
 */
if (!$member) {
    echo '<p class="error">'.$words->get('BlogErrors_not_logged_in').'</p>';
    return false;
}
$words = new MOD_words();
?>



<form method="post" action="<?=$actionUrl?>" class="def-form" id="blog-create-form">

<?php
if (in_array('inserror', $vars['errors'])) {
    echo '<p class="error">'.$words->get('BlogErrors_inserror').'</p>';
}
if (in_array('upderror', $vars['errors'])) {
    echo '<p class="error">'.$words->get('BlogErrors_upderror').'</p>';
}

// Setting variables:
if (!isset($vars['trip_id_foreign']) && isset($trip->trip_id)) $vars['trip_id_foreign'] = $trip->trip_id;
?>

<fieldset id="blog-trip"><legend><?=$words->get('BlogCreate_LabelTrips')?></legend>
    <div class="row">
        <div class="subcolumns" id="profile_subcolumns">

          <div class="c50l" >
            <div class="subcl" >
                
                <div class="row">
                <label for="create-title"><?=$words->get('BlogCreateLabelTitle')?>:</label><br/>
                    <input type="text" id="create-title" name="t" class="long" <?php
                    // the title may be set
                    echo isset($vars['t']) ? 'value="'.htmlentities($vars['t'], ENT_COMPAT, 'utf-8').'" ' : '';
                    ?>/>
                    <div id="bcreate-title" class="statbtn"></div>
                    <?php
                    if (in_array('title', $vars['errors'])) {
                        echo '<span class="error">'.$words->get('BlogErrors_title').'</span>';
                    }
                    ?>
                    <p class="desc"></p>
                </div>
                <div class="row">
                    <label for="create-txt"><?=$words->get('BlogCreateLabelText')?>:</label><br/>
                    <textarea id="create-txt" name="txt" rows="10" cols="50"><?php
                    // the content may be set
                    echo isset($vars['txt']) ? htmlentities($vars['txt'], ENT_COMPAT, 'utf-8') : '';
                    ?></textarea>
                    <div id="bcreate-c" class="statbtn"></div>
                    <?php
                    if (in_array('text', $vars['errors'])) {
                        echo '<span class="error">'.$words->get('BlogErrors_text').'</span>';
                    }
                    ?>
                    <p class="desc"></p>
                </div>
                
            </div> <!-- subcl -->
          </div> <!-- c50l -->
          <div class="c50r" >
            <div class="subcr" >
                
                <label for="create-sty"><?=$words->get('BlogCreateTrips_LabelStartdate')?>:</label><br />
                <div class="floatbox">
                    <input type="text" id="create-date" name="date" class="date" maxlength="10" style="width:9em" <?php
                    echo isset($vars['date']) ? 'value="'.htmlentities($vars['date'], ENT_COMPAT, 'utf-8').'" ' : '';
                    ?> />
                	<script type="text/javascript">
                		/*<[CDATA[*/
                		var datepicker	= new DatePicker({
                		relative	: 'create-date',
                		language	: '<?=isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en'?>',
                		current_date : '', 
                		topOffset   : '25',
                		relativeAppend : true
                		});
                		/*]]>*/
                	</script>
                </div>
                    <?php
                    if (in_array('startdate', $vars['errors'])) {
                        echo '<span class="error">'.$words->get('BlogErrors_startdate').'</span>';
                    } elseif (in_array('duration', $vars['errors'])) {
                        echo '<span class="error">'.$words->get('BlogErrors_duration').'</span>';
                    }
                    ?>
                    <p class="desc"><?=$words->get('BlogCreateTrips_SublineStartdate')?></p>
                    
                    <input id="create-trip" name="tr" type="hidden" value="<?=$vars['trip_id_foreign'] ? $vars['trip_id_foreign'] : ''?>">
                    <?php
                    if (in_array('trip', $vars['errors'])) {
                        echo '<span class="error">'.$words->get('BlogErrors_trip').'</span>';
                    }
                    ?>
                    <p class="desc"></p>

            </div> <!-- subcr -->
          </div> <!-- c50r -->

        </div> <!-- subcolumns -->
    <div class="row">
<?php

if (isset($vars['latitude']) && isset($vars['longitude']) && $vars['latitude'] && $vars['longitude']) {
	// store latitude and logitude into hidden fields (in order to get the values in registermform3.js)
	echo '<input type="hidden" id="markerLatitude" name="markerLatitude" value="'.$vars['latitude'].'"/>';
	echo '<input type="hidden" id="markerLongitude" name="markerLongitude" value="'.$vars['longitude'].'"/>';
	if (isset($vars['geonamename']) && isset($vars['geonamecountry'])) {
		$markerDescription = "'".$vars['geonamename'].", ".$vars['geonamecountry']."'";
		echo '<input type="hidden" id="markerDescription" name="markerDescription" value="'.$markerDescription.'"/>';
	}
} else {
	echo '<input type="hidden" id="markerLatitude" name="markerLatitude" value="0"/>';
	echo '<input type="hidden" id="markerLongitude" name="markerLongitude" value="0"/>';
}

?>
    <input type="hidden" name="geonameid" id="geonameid" value="<?php
            echo isset($vars['geonameid']) ? htmlentities($vars['geonameid'], ENT_COMPAT, 'utf-8') : '';
        ?>" />
    <input type="hidden" name="latitude" id="latitude" value="<?php
            echo isset($vars['latitude']) ? htmlentities($vars['latitude'], ENT_COMPAT, 'utf-8') : '';
        ?>" />
    <input type="hidden" name="longitude" id="longitude" value="<?php
            echo isset($vars['longitude']) ? htmlentities($vars['longitude'], ENT_COMPAT, 'utf-8') : '';
        ?>" />
    <input type="hidden" name="geonamename" id="geonamename" value="<?php
            echo isset($vars['geonamename']) ? htmlentities($vars['geonamename'], ENT_COMPAT, 'utf-8') : '';
        ?>" />
    <input type="hidden" name="geonamecountrycode" id="geonamecountrycode" value="<?php
            echo isset($vars['geonamecountrycode']) ? htmlentities($vars['geonamecountrycode'], ENT_COMPAT, 'utf-8') : '';
        ?>" />
    <input type="hidden" name="admincode" id="admincode" value="<?php
            echo isset($vars['admincode']) ? htmlentities($vars['admincode'], ENT_COMPAT, 'utf-8') : '';
        ?>" />
</div>
    <label for="create-location"><?=$words->get('BlogCreateTrips_LabelLocation')?>:</label><br />
    <input type="text" name="create-location" id="create-location" value="" /> <input type="button" id="btn-create-location" class="button" value="<?=$words->get('label_search_location')?>" />
    <p class="desc"><?=$words->get('BlogCreateTrips_SublineLocation')?></p>
    <div class="subcolumns">
      <div class="c50l">
        <div class="subcl">
          <div id="location-suggestion" class></div>
        </div>
      </div>
      <div class="c50r">
        <div class="subcr">
          <div id="spaf_map" style="width:240px; height:180px; border: 2px solid #333; display:none;">
          </div>
        </div>
      </div>
    </div>
    <p>
        <input type="submit" value="<?=$submitValue?>" class="submit"<?php
        echo ((isset($submitName) && !empty($submitName))?' name="'.$submitName.'"':'');
        ?> />
    </p>
</fieldset>

<fieldset id="blog-text">
<legend><?=$words->get('BlogCreateLabelText')?></legend>
    
    <div class="row">
        <label for="create-cat"><?=$words->get('BlogCreateLabelCategories')?>:</label><br />
        <select id="create-cat" name="cat">
            <option value="">-- <?=$words->get('BlogCreateNoCategories')?> --</option>
        <?php
            foreach ($catIt as $c) {
                echo "<option value=\"".$c->blog_category_id."\" ";
                if (isset($vars['cat']) && $c->blog_category_id == $vars['cat']) echo ' selected';
                echo ">".htmlentities($c->name, ENT_COMPAT, 'utf-8')."</option>\n";
            }
        ?>
        </select>
        <?php
        if (in_array('category', $vars['errors'])) {
            echo '<span class="error">'.$words->get('BlogErrors_category').'</span>';
        }
        ?>
        <p class="desc"></p>
    </div>
    <div class="row">
        <label for="create-tags"><?=$words->get('BlogCreateLabelCreateTags')?>:</label><br />
        <textarea id="create-tags" name="tags" cols="40" rows="1"><?php
        // the tags may be set
            echo isset($vars['tags']) ? htmlentities($vars['tags'], ENT_COMPAT, 'utf-8') : '';
        ?></textarea>
        <div id="suggestion"></div>
        <p class="desc"><?=$words->get('BlogCreateLabelSublineTags')?></p>
    </div>

    <?php echo $callback; ?>
<?php
if (isset($vars['id']) && $vars['id']) {
?>
        <input type="hidden" name="id" value="<?=(int)$vars['id']?>"/>
<?php
}
?>

    <legend><?=$words->get('BlogCreate_LabelSettings')?></legend>
    <?php
    /* removed pending deletiong
    if ($User->hasRight('write_sticky@blog')) {
    ?>
        <div class="row">
            <input type="checkbox" id="create-flag-sticky" name="flag-sticky"<?php
            if (isset($vars['flag-sticky']) && (int)$vars['flag-sticky']) {
                echo ' checked="checked"';
            }
            ?>/>
            <label for="create-flag-sticky"> <?=$words->get('BlogCreateSettings_LabelSticky')?></label>
        </div>
    <?php
    }
    */
    ?>
    <label><?=$words->get('label_vis')?></label>
    <div class="row">
        <input type="radio" name="vis" value="pub" id="create-vis-pub"<?php
        if (
            (isset($vars['vis']) && $vars['vis'] == 'pub')
            || (!isset($vars['vis']) && (!$defaultVis || ($defaultVis && $defaultVis->valueint == 2)))
        ) {
            echo ' checked="checked"';
        }
        ?>/> <label for="create-vis-pub"><?=$words->get('BlogCreateSettings_LabelVispublic')?></label>
        <p class="desc"><?=$words->get('BlogCreateSettings_DescriptionVispublic')?></p>
    </div>
    <div class="row">
        <input type="radio" name="vis" value="prt" id="create-vis-prt"<?php
        if (
            (isset($vars['vis']) && $vars['vis'] == 'prt')
            || (!isset($vars['vis']) && $defaultVis && $defaultVis->valueint == 1)
        ) {
            echo ' checked="checked"';
        }
        ?>/> <label for="create-vis-prt"><?=$words->get('BlogCreateSettings_LabelVisprotected')?></label>
        <p class="desc"><?=$words->get('BlogCreateSettings_DescriptionVisprotected')?></p>
    </div>
    <div class="row">
        <input type="radio" name="vis" value="pri" id="create-vis-pri"<?php
        if (
            (isset($vars['vis']) && $vars['vis'] != 'prt' && $vars['vis'] != 'pub')
            || (!isset($vars['vis']) && $defaultVis && $defaultVis->valueint == 0)
        ) {
            echo ' checked="checked"';
        }
        ?>/> <label for="create-vis-pri"><?=$words->get('BlogCreateSettings_LabelVisprivate')?></label>
        <p class="desc"><?=$words->get('BlogCreateSettings_DescriptionVisprivate')?></p>
    </div>
<p>
        <input type="submit" value="<?=$submitValue?>" class="submit"<?php
        echo ((isset($submitName) && !empty($submitName))?' name="'.$submitName.'"':'');
        ?> />
    </p>
</fieldset>





</form>
<script type="text/javascript">//<!--
new FieldsetMenu('blog-create-form', {
    active: "blog-trip"
});
BlogSuggest.initialize('blog-create-form');

jQuery(function() {
    $('spaf_map').style.display = 'block';
    initOsmMapBlogEdit();
});

//-->
</script>        
        
</div>
