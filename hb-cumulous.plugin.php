<?php
/**
 * This is a port of WP-Cumulus by Roy Tanck (http://www.roytanck.com/2008/03/15/wp-cumulus-released)
 * TODO: but I've updated the SFWObject code to 2.1 so multiple instances can be supported.
 */

class HbCumulus extends Plugin {

    private $options = array();
    public function info() {
        return array (
            'name' => 'Hb-Cumulus',
            'url' => 'http://www.lildude.co.uk/projects/hb-cumulus',
            'author' => 'Colin Seymour',
            'authorurl' => 'http://www.colinseymour.co.uk/',
            'version' => '0.1',
            'description' => 'Flash based Tag Cloud for Habari.',
            'license' => 'GPL 3.0',     // Got to workout this license malarky
        );
    }

    /**
	 * Add update beacon support
     * @todo: add beacon support
	 **/
	public function action_update_check()
	{
	 	//Update::add( 'Hb-Cumulus', 'Todo', $this->info->version );
	}

    /**
	 * When Plugin is activated insert default options
	 */
	public function action_plugin_activation( $file )
	{
		if(Plugins::id_from_file($file) == Plugins::id_from_file(__FILE__)) {
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
                'number' => '30'
            );
			Options::set('hb-cumulus__options', serialize($defOptions));
		}
	}

    /**
     * Delete all settings when deactivating the plugin
     */
    public function action_plugin_deactivation( $file ) {
        Options::delete('hb-cumulus__options');
    }

    /**
	 * Show configure option
	 */
	public function filter_plugin_config( $actions, $plugin_id )
	{
		if ( $plugin_id == $this->plugin_id() ) {
			$actions[]= _t('Configure');
		}
		return $actions;
	}


	/**
	 * Handle special plugin requests
	 */
	public function action_plugin_ui( $plugin_id, $action )
	{
		if ( $plugin_id == $this->plugin_id() ) {
			switch ( $action ) {
				case _t('Configure'):
                    $this->options = unserialize(Options::get('hb-cumulus__options'));
					$ui= new FormUI( strtolower( get_class( $this ) ) );
                        $ui->append( 'hidden', 'option_mode', 'null:null');
                            $ui->option_mode->value = $this->options['mode'];
                        $ui->append( 'text', 'options_width', 'null:null', _t('Width of Flash Tag Cloud (px)'), 'optionscontrol_text');
                            $ui->options_width->value = $this->options['width'];
                            $ui->options_width->add_validator( 'validate_heightWidth' );
                        $ui->append( 'text', 'options_height', 'null:null', _t('Height of Flash Tag Cloud (px)'), 'optionscontrol_text');
                            $ui->options_height->value = $this->options['height'];
                            $ui->options_height->add_validator( 'validate_heightWidth' );
                        $ui->append( 'text', 'options_tcolor', 'null:null', _t('Color of the Tags'), 'optionscontrol_text');
                            $ui->options_tcolor->value = $this->options['tcolor'];
                            $ui->options_tcolor->add_validator( 'validate_color' );
                        $ui->append( 'text', 'options_tcolor2', 'null:null', _t('Second Color for Gradient (opt)'), 'optionscontrol_text');
                            $ui->options_tcolor2->value = $this->options['tcolor2'];
                        $ui->append( 'text', 'options_hicolor', 'null:null', _t('Highlight Color (opt)'), 'optionscontrol_text');
                            $ui->options_hicolor->value = $this->options['hicolor'];
                        $ui->append( 'text', 'options_bgcolor', 'null:null', _t('Background Color'), 'optionscontrol_text');
                            $ui->options_bgcolor->value = $this->options['bgcolor'];
                            $ui->options_bgcolor->add_validator( 'validate_color' );
                        $ui->append( 'text', 'options_speed', 'null:null', _t('Rotation Speed'), 'optionscontrol_text');
                            $ui->options_speed->value = $this->options['speed'];
                        $ui->append( 'text', 'options_hide', 'null:null', _t('Tag(s) to be Hidden'), 'optionscontrol_text');
                            $ui->options_hide->value = $this->options['hide'];
                        $ui->append( 'text', 'options_minfont', 'null:null', _t('Minimum Font Size (pt)'), 'optionscontrol_text');
                            $ui->options_minfont->value = $this->options['minfont'];
                        $ui->append( 'text', 'options_maxfont', 'null:null', _t('Maximum Font Size (pt)'), 'optionscontrol_text');
                            $ui->options_maxfont->value = $this->options['maxfont'];
                        $ui->append( 'text', 'options_number', 'null:null', _t('Number of Tags to Show'), 'optionscontrol_text');
                            $ui->options_number->value = $this->options['number'];
                        $ui->append( 'checkbox', 'options_trans', 'null:null', _t('Use Transparent Mode'), 'optionscontrol_checkbox');
                            $ui->options_trans->value = $this->options['trans'];
                        $ui->append( 'checkbox', 'options_distr', 'null:null', _t('Distribute Tags Evenly'), 'optionscontrol_checkbox');
                            $ui->options_distr->value = $this->options['distr'];
					$ui->append( 'submit', 'save', _t('Save Options') );
                    $ui->on_success( array($this, 'serializeNStoreOpts') );
                    $ui->set_option('success_message', _t('Options successfully saved.'));
                    $form_output = $ui->get();
                    echo '<div style="width: 300px; float: right; margin: 10px 25px;"><label>'._t('Preview').'</label>'.$this->get_flashcode(TRUE).'</div>';
                    echo $form_output;
				break;
            }
		}
	}

    /**
     * Action handler: This takes all the options, serializes them and then stores in the options table.
     */

     static function serializeNStoreOpts ($ui) {
        $newOptions = array();
        foreach($ui->controls as $option) {
            if ($option->name == 'save') continue;
            list($a, $name) = explode('_', $option->name);
            $newOptions[$name] = $option->value;
        }
        Options::set('hb-cumulus__options', serialize($newOptions));

     }

     /**
      * Validate height and width
      */
    public function filter_validate_heightWidth( $valid, $value )
	{
			if ( empty( $value ) || !intval($value) || intval($value) <= 0 ) {
				return array( _t( "An integer value greater than 0 is required." ) );
			}
		return array();
	}

     /**
      * Validate colour
      */
    public function filter_validate_color( $valid, $value )
    {
        /*if ( !empty( $this->config['tag_by_color'] ) && 'Y' == $this->config['tag_by_color'] ) {
            if ( empty( $value ) ) {
                return array( _t( "A value for this field is required when using 'Popularity by Color'." ) );
            }*/
            if ( 0 == preg_match( '/([0-9a-f]){6}$/i', $value ) ) {
                return array( _t( "Color format must be #dddddd, where 'd' is 0-9 or a-f" ) );
            }
        //}
        return array();
    }

     /**
	  * Add custom styling and Javascript controls to the footer of the admin interface
      *
      * This won't be necessary if I implement my own FormControls
	  **/
	public function action_admin_footer( $theme ) {
        if (Controller::get_var('configure') == $this->plugin_id) {
            $output = '<style type="text/css">';
            if (Version::HABARI_VERSION == '0.5.2') {
                $output .= 'form#hbcumulus .formcontrol { line-height: 24px; height: 30px; }';
                $output .= 'form#hbcumulus #save input { float:none; }';

            } else {
                $output .= 'form#hbcumulus .formcontrol { line-height: 24px; height: 18px; }';
            }
        $output .= 'form#hbcumulus span.pct25 select { width: 85%; }';
        $output .= 'form#hbcumulus span.pct25 { text-align: right; }';
        $output .= 'form#hbcumulus span.pct5 input { margin-left: 25px; }';
        $output .= 'form#hbcumulus p.error { float: left; color: #A00; }';
        $output .= '</style>';
        echo $output;
        }
    }

    /**
     * The function that generates the code for the cloud
     */
    private function get_flashcode($config = FALSE) {
        $this->options = unserialize(Options::get('hb-cumulus__options'));
        $flashtag = '';
        if ($config) {
            if ( $this->options['width'] < 300 && $this->options['height'] < 300) {
                $max = ($this->options['width'] >= $this->options['height']) ? $this->options['width'] : $this->options['height'];
            } else {
                $max = '300';
                $flashtag .= '<span> ('._t('Scaled to fit').')</span>';
            }

            $ratio = $this->options['height']/$this->options['width'];
            $this->options['width'] = ($ratio > 1) ? $max*(1/$ratio) : $max;
            $this->options['height'] = ($ratio > 1) ? $max : $max*$ratio;
        }
        ob_start();
        echo self::build_tag_cloud();
        $tagcloud = urlencode( str_replace( "&nbsp;", " ", ob_get_clean() ) );
        $movie =  $this->get_url() .'/tagcloud.swf';
        $path =  $this->get_url();
        // write flash tag
        $flashtag .= '<!-- SWFObject embed by Geoff Stearns geoff@deconcept.com http://blog.deconcept.com/swfobject/ -->';
        $flashtag .= '<script type="text/javascript" src="'.$path.'/swfobject.js"></script>';
        $flashtag .= '<div id="hbcumulus"><p style="display:none;">';
        $flashtag .= urldecode($tagcloud);
        $flashtag .= '</p><p>HB Cumulus Flash tag cloud by <a href="http://www.colinseymour.co.uk">Colin Seymour</a> requires Flash Player 9 or better and can only be displayed once per page.</p></div>';
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
        $flashtag .= 'so.write("hbcumulus");';
        $flashtag .= '</script>';
        return $flashtag;
    }

    /**
     * Construst SQL from list of tags
     */

    private function get_hide_tag_list()
	{
		if ( !empty( $this->options['hide' ] ) ) {
			$hide_tag_list = '';
            // Convert string into an array
            $tags = preg_split("/[\s,]+/", $this->options['hide']);
			foreach ( $tags as $tag ) {
				$hide_tag_list.= ( $hide_tag_list == '' ? "'{$tag}'" : ", '{$tag}'" );
			}
			$hide_tag_list = "AND t.tag_slug NOT IN ({$hide_tag_list})";
			return $hide_tag_list;
		} else {
			return '';
		}
	}


    private function get_font_size_for_weight( $weight ) {
        $most_size = $this->options['maxfont'];
        $least_size = $this->options['minfont'];
        if ( $most_size > $least_size ) {
            $fontsize = ( ( $weight / 100 ) * ( $most_size - $least_size ) ) + $least_size;
        } else {
            $fontsize = ( ( ( 100 - $weight ) / 100 ) * ( $most_size - $least_size ) ) + $most_size;
        }
        return intval( $fontsize ) . "pt";
    }

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

	private function build_tag_cloud( $num_tag = '' )
	{
		$tag_cloud = '';
		$post_type = Post::type( 'entry' );
		$post_status = Post::status( 'published' );

        /* No limit
		if ( empty( $num_tag ) ) {
			$limit = ( empty( $this->config['num_tag'] ) ? '' : "LIMIT {$this->config['num_tag']}" );
		}
		else {
			$limit = "LIMIT {$num_tag}";
		}
         */
        $limit = '';
		$hide_tags = self::get_hide_tag_list();
		$total_tag_cnt = self::get_total_tag_usage_count();
		$most_popular_tag_cnt = self::get_most_popular_tag_count();

		// Get tag and usage count descending
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

		sort( $results );
        $tag_cloud = '';
        if ( $results ) {
			foreach ( $results as $tag ) {
				$style_str = '';
                $style_str = 'style="font-size: ' . self::get_font_size_for_weight( $tag->relative_weight ) . ';"';
                $tag_cloud.= '<a ' . $style_str . ' href="' . URL::get( 'display_entries_by_tag', array ( 'tag' => $tag->tag_slug ), false ) . '" rel="tag" title="' . $tag->tag_text . " ({$tag->cnt})" . '">'. $tag->tag_text . '</a>';
                $tag_cloud.= "\n";
            }
        }
		return $tag_cloud;
	}

    /* We use formatting here as we don't want the tag added to a post replaced by all the source code.
     * This is so the posts don't display rubbish or errors if the plugin is deactivated.
     */
    public function action_init()
    {
            Format::apply( 'hbcumulus', 'post_content_out' );
    }

    public function filter_hbcumulus ( $content) {
        $content= preg_replace( '/<!--\s*hb-cumulus\s*-->/', $this->get_flashcode(), $content );
        return $content;
    }

    public function theme_hbcumulus($theme) {
        return $this->get_flashcode();
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
