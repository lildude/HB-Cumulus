<?php
/**
 *
 * Copyright 2009 Colin Seymour - http://www.lildude.co.uk/projects/hb-cumulus
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

/**
 * HB-Cumulus is a Flash-based tag cloud for Habari.
 * HB-Cumulus is a port of the very popular Wordpress version (WP-Cumulus) written by Roy Tanck.
 * 
 * @package HbCumulus
 * @version 0.6-1.3r61
 * @author Colin Seymour - http://www.colinseymour.co.uk
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0 (unless otherwise stated)
 * @link http://www.lildude.co.uk/projects/hb-cumulus
 */

class HbCumulus extends Plugin
{
    private $options = array();
    private $version = '0.6-1.3r61';
    const OPTNAME = 'hb-cumulus__options';

    /**
     * The help message - it provides a larger explanation of what this plugin
     * does
     *
     * @return string
     */
    public function help()
    {
	return _t( '<p>HB-Cumulus is a Flash-based tag cloud for Habari that displays your tag cloud in a rotating sphere.</p>
		    <p>HB-Cumulus is a port of the brilliant <a href="http://wordpress.org/extend/plugins/wp-cumulus/">WP-Cumulus</a> by <a href="http://www.roytanck.com/">Roy Tanck</a>.</p>
		    <br /><strong>Usage:</strong><br />
		    <p>There are two ways you can use HB-Cumulus:
		    <ol>
		    <li>In ANY page or post:<br />
		    <p>You can show the cloud in any page or post by putting the following code into the post/page content:<br />
		    <code>&lt;!-- hb-cumulus --&gt;</code>
		    <br /></p>
		    <p>This tag is NOT case sensitive, so don\'t worry too much about the case or spacing. So long as you have all of the above characters in that order, it should display.</p></li>

		    <li>In ANY theme file:<br />
		    <p>You can show the cloud anywhere on your site within your theme files, for example in the sidebar using:<br />
		    <code>$theme-&gt;hbcumulus();</code>
		    <br /></p>
		    <p>This IS case sensitive, so you\'ll need to be sure you get it 100% correct.</p></li>
		    </ol></p>' );
    }

    /**
     * Beacon Support for Update checking
     *
     * @access public
     * @return void
     **/
    public function action_update_check()
    {
		Update::add( 'HB-Cumulus', 'F7A0CCFC-C5DF-11DD-A399-37B955D89593', $this->version );
    }

    /**
     * Plugin activation
     *
     * @access public
     * @param string $file
     * @return void
     */
    public function action_plugin_activation( $file )
    {
	if( Plugins::id_from_file( $file ) == Plugins::id_from_file( __FILE__ ) ) {
            $defOptions = array(
                'width' => '250',
                'height' => '250',
                'tcolor' => 'FFFFFF',
                'tcolor2' => 'FFFFFF',
                'hicolor' => 'FFFFFF',
                'bgcolor' => '333333',
                'speed' => '100',
                'trans' => FALSE,
                'distr' => FALSE,
                'hide' => '',
                'mode' => 'tags',
                'minfont' => '8',
                'maxfont' => '25',
                'number' => '30',
		'compat' => FALSE
            );

            $this->options = Options::get( self::OPTNAME );

		if ( empty( $this->options ) ) {
		    Options::set( self::OPTNAME, $defOptions );
		}
		else if ( count( $this->options ) != count( $defOptions ) ) {
		    Options::set( self::OPTNAME, array_merge( $defOptions, $this->options ) );
		}
		else {
		    Session::notice( _t( 'Using previous HB-Cumulus options' ) );
		}
	    }
	}

    /**
     * Plugin De-activation
     *
     * @access public
     * @param string $file
     * @return void
     */
    public function action_plugin_deactivation( $file )
    {
        if ( realpath( $file ) == __FILE__ ) {
            //Options::delete(self::OPTNAME);
        }
    }

    /**
     * Add the Configure option for the plugin
     *
     * @access public
     * @param array $actions
     * @param string $plugin_id
     * @return array
     */
    public function filter_plugin_config( $actions, $plugin_id )
    {
	if ( $plugin_id == $this->plugin_id() ) {
	    $actions[]= _t( 'Configure' );
	}
	return $actions;
    }


    /**
     * Plugin UI
     *
     * @access public
     * @param string $plugin_id
     * @param string $action
     * @return void
     */
    public function action_plugin_ui( $plugin_id, $action )
    {
	if ( $plugin_id == $this->plugin_id() ) {
	    switch ( $action ) {
		case _t( 'Configure' ):
		$this->options = Options::get( self::OPTNAME );
		    $ui= new FormUI( strtolower( get_class( $this ) ) );
		    $ui->append( 'hidden', 'option_mode', 'null:null' );
			$ui->option_mode->value = $this->options['mode'];
		    $ui->append( 'text', 'options_width', 'null:null', _t( 'Width of Flash Tag Cloud (px)' ), 'optionscontrol_text' );
			$ui->options_width->value = $this->options['width'];
			$ui->options_width->add_validator( 'validate_heightWidth' );
		    $ui->append( 'text', 'options_height', 'null:null', _t( 'Height of Flash Tag Cloud (px)' ), 'optionscontrol_text' );
			$ui->options_height->value = $this->options['height'];
			$ui->options_height->add_validator( 'validate_heightWidth' );
		    $ui->append( 'text', 'options_tcolor', 'null:null', _t( 'Color of the Tags' ), 'optionscontrol_text' );
			$ui->options_tcolor->value = $this->options['tcolor'];
			$ui->options_tcolor->add_validator( 'validate_color' );
		    $ui->append( 'text', 'options_tcolor2', 'null:null', _t( 'Second Color for Gradient (opt)' ), 'optionscontrol_text' );
			$ui->options_tcolor2->value = $this->options['tcolor2'];
		    $ui->append( 'text', 'options_hicolor', 'null:null', _t( 'Highlight Color (opt)' ), 'optionscontrol_text' );
			$ui->options_hicolor->value = $this->options['hicolor'];
		    $ui->append( 'text', 'options_bgcolor', 'null:null', _t( 'Background Color' ), 'optionscontrol_text' );
			$ui->options_bgcolor->value = $this->options['bgcolor'];
			$ui->options_bgcolor->add_validator( 'validate_color' );
		    $ui->append( 'text', 'options_speed', 'null:null', _t( 'Rotation Speed' ), 'optionscontrol_text' );
			$ui->options_speed->value = $this->options['speed'];
		    $ui->append( 'text', 'options_hide', 'null:null', _t( 'Tag(s) to be Hidden' ), 'optionscontrol_text' );
			$ui->options_hide->value = $this->options['hide'];
		    $ui->append( 'text', 'options_minfont', 'null:null', _t( 'Minimum Font Size (pt)' ), 'optionscontrol_text' );
			$ui->options_minfont->value = $this->options['minfont'];
		    $ui->append( 'text', 'options_maxfont', 'null:null', _t( 'Maximum Font Size (pt)' ), 'optionscontrol_text' );
			$ui->options_maxfont->value = $this->options['maxfont'];
		    $ui->append( 'text', 'options_number', 'null:null', _t( 'Number of Tags to Show' ), 'optionscontrol_text' );
			$ui->options_number->value = $this->options['number'];
		    $ui->append( 'checkbox', 'options_trans', 'null:null', _t( 'Use Transparent Mode' ), 'optionscontrol_checkbox' );
			$ui->options_trans->value = $this->options['trans'];
		    $ui->append( 'checkbox', 'options_distr', 'null:null', _t( 'Distribute Tags Evenly' ), 'optionscontrol_checkbox' );
			$ui->options_distr->value = $this->options['distr'];
		    $ui->append( 'checkbox', 'options_compat', 'null:null', _t( 'Compatibility Mode' ), 'optionscontrol_checkbox' );
			$ui->options_compat->value = $this->options['compat'];
			$ui->options_compat->helptext = _t( 'Enabling this option switches the plugin to a different way of embedding Flash into the page. Use this if your page has markup errors or if you\'re having trouble getting HB-Cumulus to display correctly.' );
		    $ui->append( 'submit', 'submit', _t( 'Save Options' ) );
		$ui->on_success ( array( $this, 'storeOpts' ) );
		$ui->set_option( 'success_message', _t( 'Options successfully saved.' ) );
		$form_output = $ui->get();
		echo '<div style="width: 300px; float: right; margin: 10px 25px;"><label>'._t( 'Preview' ).'</label>'.$this->get_flashcode( 'config', TRUE ).'</div>';
		echo $form_output;
		break;
            }
	}
    }

    /**
     * Serialize and Store the Options in a single DB entry in the options table
     *
     * @access public
     * @static
     * @param object $ui
     * @return void
     */
     public static function storeOpts ( $ui )
     {
        $newOptions = array();
        foreach( $ui->controls as $option ) {
            if ( $option->name == 'submit' ) continue;
            list( $a, $name ) = explode( '_', $option->name );
            $newOptions[$name] = $option->value;
        }
        Options::set( self::OPTNAME, $newOptions );
     }

    /**
     * Validate height and width to ensure they're set and are positive integers
     *
     * @access public
     * @param string $valid
     * @param string $value
     * @return array
     */
    public function filter_validate_heightWidth( $valid, $value )
    {
	if ( empty( $value ) || !intval( $value ) || intval( $value ) <= 0 ) {
	    return array( _t( "An integer value greater than 0 is required." ) );
	}
	return array();
    }

     /**
      * Validate colour to ensure it's a 6 character value
      * This isn't really necessary, but it ensure predictable results
      * 
      * @access public
      * @param string $valid
      * @param string $value
      * @return array
      */
    public function filter_validate_color( $valid, $value )
    {
        if ( 0 == preg_match( '/([0-9a-f]){6}$/i', $value ) ) {
            return array( _t( "Colour should be in the form dddddd, where 'd' is 0-9 or a-f" ) );
        }
        return array();
    }

     /**
      * Add custom CSS information to "Configure" page
      *
      * @access public
      * @param object $theme
      * @return void
      **/
    public function action_admin_footer( $theme )
    {
        if ( Controller::get_var( 'configure' ) == $this->plugin_id ) {
            $output = '<style type="text/css">';
            if ( Version::HABARI_VERSION == '0.5.2' ) {
                $output .= 'form#hbcumulus .formcontrol { line-height:24px; height:30px; }';
                $output .= 'form#hbcumulus #save input { float:none; }';
                $output .= 'form#hbcumulus p.error {background:none !important; border:none !important; margin-bottom:0 !important; padding:0 !important;}';
            }
	    else {
                $output .= 'form#hbcumulus .formcontrol { line-height:24px; height:18px; }';
            }
        $output .= 'form#hbcumulus span.pct25 select { width:85%; }';
        $output .= 'form#hbcumulus span.pct25 { text-align:right; }';
        $output .= 'form#hbcumulus span.pct5 input { margin-left:25px; }';
        $output .= 'form#hbcumulus p.error { float:left; color:#A00; }';
        $output .= '</style>';
        echo $output;
        }
    }

    /**
     * Generate code needed for cloud
     * 
     * @access private
     * @param string $class (Optional) Provide distinct class names so multiple occurances can occur on the same page
     * @param boolean $config (Optional) Scales image to fit within 300x300 div as used in the config section of the plugin
     * @return string
     */
    private function get_flashcode( $class = '', $config = FALSE )
    {
        $this->options = Options::get( self::OPTNAME );
        $class = ( $class != '' ) ? "_$class" : '';
        $flashtag = '';
        if ( $config ) {
            if ( $this->options['width'] < 300 && $this->options['height'] < 300 ) {
                $max = ( $this->options['width'] >= $this->options['height'] ) ? $this->options['width'] : $this->options['height'];
            }
	    else {
                $max = '300';
                $flashtag .= '<span> ('._t('Scaled to fit').')</span>';
            }

            $ratio = $this->options['height']/$this->options['width'];
            $this->options['width'] = ( $ratio > 1 ) ? $max*( 1/$ratio ) : $max;
            $this->options['height'] = ( $ratio > 1 ) ? $max : $max*$ratio;
        }
        ob_start();
        echo self::build_tag_cloud( $this->options['number'] );
        $tagcloud = urlencode( str_replace( "&nbsp;", " ", ob_get_clean() ) );
        $movie =  $this->get_url() .'/lib/tagcloud.swf';
        $path =  $this->get_url();
	if ( $this->options['compat'] ) {
	    // Non-JS method
	    $flashtag = '<object type="application/x-shockwave-flash" data="'.$movie.'" width="'.$this->options['width'].'" height="'.$this->options['height'].'">';
	    $flashtag .= '<param name="movie" value="'.$movie.'" />';
	    $flashtag .= '<param name="bgcolor" value="#'.$this->options['bgcolor'].'" />';
	    $flashtag .= '<param name="AllowScriptAccess" value="always">';
	    if( $this->options['trans'] == 'true' ){
		$flashtag .= '<param name="wmode" value="transparent">';
	    }
	    $flashtag .= '<param name="flashvars" value="';
	    $flashtag .= 'tcolor=0x' . $this->options['tcolor'];
	    $flashtag .= '&tcolor2=0x' . ($this->options['tcolor2'] == "" ? $this->options['tcolor'] : $this->options['tcolor2']);
	    $flashtag .= '&hicolor=0x' . ($this->options['hicolor'] == "" ? $this->options['tcolor'] : $this->options['hicolor']);
	    $flashtag .= '&tspeed='.$this->options['speed'];
	    $flashtag .= '&distr='.$this->options['distr'];
	    $flashtag .= '&mode='.$this->options['mode'];
	    // put tags in flashvar
	    if( $this->options['mode'] != "cats" ){
		$flashtag .= '&tagcloud='.urlencode('<tags>') . $tagcloud . urlencode('</tags>');
	    }

	    $flashtag .= '" />';
	    $flashtag .= '<div id="hbcumulus'.$class.'"><p style="display:none;">';
	    $flashtag .= urldecode($tagcloud);
	    $flashtag .= '</p><p>HB Cumulus Flash tag cloud by <a href="http://www.colinseymour.co.uk">Colin Seymour</a> requires Flash Player 9 or better.</p></div>';
	    $flashtag .= '</object>';
	} else {
	    // write flash tag
	    $flashtag .= '<!-- SWFObject embed by Geoff Stearns geoff@deconcept.com http://blog.deconcept.com/swfobject/ -->';
	    $flashtag .= '<script type="text/javascript" src="'.$path.'/lib/swfobject-min.js"></script>';
	    $flashtag .= '<div id="hbcumulus'.$class.'"><p style="display:none;">';
	    $flashtag .= urldecode($tagcloud);
	    $flashtag .= '</p><p>HB Cumulus Flash tag cloud by <a href="http://www.colinseymour.co.uk">Colin Seymour</a> requires Flash Player 9 or better.</p></div>';
	    $flashtag .= '<script type="text/javascript">';
	    $flashtag .= 'var rnumber = Math.floor(Math.random()*9999999);'; // force loading of movie to fix IE weirdness
	    $flashtag .= 'var so = new SWFObject("'.$movie.'?r="+rnumber, "tagcloudflash", "'.$this->options['width'].'", "'.$this->options['height'].'", "9", "#'.$this->options['bgcolor'].'");';
	    if( $this->options['trans'] == 'true' ){
		$flashtag .= 'so.addParam("wmode", "transparent");';
	    }
	    $flashtag .= 'so.addParam("allowScriptAccess", "always");';
	    $flashtag .= 'so.addVariable("tcolor", "0x'.$this->options['tcolor'].'");';
	    $flashtag .= 'so.addVariable("tcolor2", "0x' . ($this->options['tcolor2'] == "" ? $this->options['tcolor'] : $this->options['tcolor2']) . '");';
	    $flashtag .= 'so.addVariable("hicolor", "0x' . ($this->options['hicolor'] == "" ? $this->options['tcolor'] : $this->options['hicolor']) . '");';
	    $flashtag .= 'so.addVariable("tspeed", "'.$this->options['speed'].'");';
	    $flashtag .= 'so.addVariable("distr", "'.$this->options['distr'].'");';
	    $flashtag .= 'so.addVariable("mode", "'.$this->options['mode'].'");';
	    // put tags in flashvar
	    if( $this->options['mode'] != "cats" ){
		$flashtag .= 'so.addVariable("tagcloud", "'.urlencode('<tags>') . $tagcloud . urlencode('</tags>').'");';
	    }
	    $flashtag .= 'so.write("hbcumulus'.$class.'");';
	    $flashtag .= '</script>';
	}
        return $flashtag;
    }

    /**
     * Return SQL code needed to exclude tags specified in options
     * 
     * @access private
     * @return string
     */
    private function get_hide_tag_list()
    {
	if ( !empty( $this->options['hide' ] ) ) {
	    $hide_tag_list = '';
            // Convert string into an array
            $tags = preg_split( "/[\s,]+/", $this->options['hide'] );
	    foreach ( $tags as $tag ) {
		$hide_tag_list.= ( $hide_tag_list == '' ? "'{$tag}'" : ", '{$tag}'" );
	    }
	    $hide_tag_list = "AND t.tag_slug NOT IN ( {$hide_tag_list} )";
	    return $hide_tag_list;
	}
	else {
	    return '';
	}
    }

    /**
     * Return font size for weighting
     *
     * @param int $weight
     * @return string
     */
    private function get_font_size_for_weight( $weight )
    {
        $most_size = $this->options['maxfont'];
        $least_size = $this->options['minfont'];
        if ( $most_size > $least_size ) {
            $fontsize = ( ( $weight / 100 ) * ( $most_size - $least_size ) ) + $least_size;
        }
	else {
            $fontsize = ( ( ( 100 - $weight ) / 100 ) * ( $most_size - $least_size ) ) + $most_size;
        }
        return intval( $fontsize ) . "pt";
    }

    /**
     * Return integer for total usage for tag
     *
     * @access private
     * @return int
     */
    private function get_total_tag_usage_count()
    {
	$post_type = Post::type( 'entry' );
	$post_status = Post::status( 'published' );
	$hide_tags = self::get_hide_tag_list();

	$sql = "
		SELECT COUNT(t2p.post_id) AS cnt
		FROM {tag2post} t2p
		INNER JOIN {posts} p
		ON t2p.post_id = p.id
		INNER JOIN {tags} t
		ON t2p.tag_id = t.id
		WHERE p.content_type = {$post_type}
		AND p.status = {$post_status}
		{$hide_tags}";
	$result = DB::get_row( $sql );
	return ( !empty( $result ) ? $result->cnt : 0 );
    }

    /**
     * Get most popular tag count.
     *
     * @access private
     * @return int
     */
    private function get_most_popular_tag_count()
    {
	$post_type = Post::type( 'entry' );
	$post_status = Post::status( 'published' );
	$hide_tags = self::get_hide_tag_list();

	$sql = "
		SELECT COUNT(t2p.post_id) AS cnt
		FROM {posts} p
		INNER JOIN {tag2post} t2p
		ON p.id = t2p.post_id
		INNER JOIN {tags} t
		ON t2p.tag_id = t.id
		WHERE p.content_type = {$post_type}
		AND p.status = {$post_status}
		{$hide_tags}
		GROUP BY t.id
		ORDER BY cnt DESC
		LIMIT 1";
	$result = DB::get_row( $sql );

	return ( !empty( $result ) ? $result->cnt : 0 );
    }

    /**
     * Return the string list of tags used to form the cloud
     *
     * @access private
     * @param int $num_tag
     * @return string
     */
    private function build_tag_cloud( $num_tag = '' )
    {
	$tag_cloud = '';
	$post_type = Post::type( 'entry' );
	$post_status = Post::status( 'published' );

	$limit = ( empty( $num_tag ) ) ? '' : "LIMIT {$num_tag}";

	$hide_tags = self::get_hide_tag_list();
	$total_tag_cnt = self::get_total_tag_usage_count();
	$most_popular_tag_cnt = self::get_most_popular_tag_count();

	$sql = "
		SELECT t.tag_text AS tag_text, t.tag_slug AS tag_slug, t.id AS id,
			COUNT(t2p.post_id) AS cnt,
			COUNT(t2p.post_id) * 100 / {$total_tag_cnt} AS weight,
			COUNT(t2p.post_id) * 100 / {$most_popular_tag_cnt} AS relative_weight
		FROM {posts} p
		INNER JOIN {tag2post} t2p
		ON p.id = t2p.post_id
		INNER JOIN {tags} t
		ON t2p.tag_id = t.id
		WHERE p.content_type = {$post_type}
		AND p.status = {$post_status}
		{$hide_tags}
		GROUP BY t.tag_text, t.tag_slug, t.id
		ORDER BY weight DESC
		{$limit}";
	$results = DB::get_results( $sql );

        $tag_cloud = '';
        if ( $results ) {
	    sort( $results );
	    foreach ( $results as $tag ) {
		$style_str = '';
                $style_str = 'style="font-size: ' . self::get_font_size_for_weight( $tag->relative_weight ) . ';"';
                $tag_cloud.= '<a ' . $style_str . ' href="' . URL::get( 'display_entries_by_tag', array ( 'tag' => $tag->tag_slug ), false ) . '" rel="tag" title="' . $tag->tag_text . " ({$tag->cnt})" . '">'. $tag->tag_text . '</a>';
                $tag_cloud.= "\n";
            }
        }
	return $tag_cloud;
    }

    /**
     * Format post content. Calls HbCumulusFormat::hbcumulus.
     *
     * We use Format here instead of filter_post_content_out to ensure the code isn't actually replace
     * until the page is displayed.  This prevents errors or the display of rubbish in the event the
     * plugin is deactivated.
     *
     * @access public
     * @return void
     */
    public function action_init()
    {
	Format::apply( 'hbcumulus', 'post_content_out' );
    }

    /**
     * Replace any instance of the <!-- hb-cumulus --> tag with the Flash tag cloud.
     *
     * @access public
     * @param string $content
     * @return string
     */
    public function filter_hbcumulus ( $content)
    {
        $content= preg_replace( '/<!--\s*hb-cumulus\s*-->/i', $this->get_flashcode( 'post' ), $content );
        return $content;
    }

    /**
     * Adds functionality for inclusion in Theme files.
     * Implements the $theme->hbcumulus(); functionality
     *
     * @param object $theme
     * @return string
     */
    public function theme_hbcumulus( $theme )
    {
        return $this->get_flashcode( 'theme' );
    }

}

class HbCumulusFormat extends Format
{
    public function hbcumulus( $content )
    {
	return Plugins::filter( 'hbcumulus', $content );
    }
}
?>
