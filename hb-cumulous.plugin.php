<?php
/**
 * This is a direct port of WP-Cumulus available from http://www.roytanck.com/2008/03/15/wp-cumulus-released
 *
 * TODO:
 *  - Create my own FormControls - cos the defaults are quite pants.
 */
class HbCumulus extends Plugin {
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
                'width' => '550',
                'height' => '375',
                'tcolor' => 'ffffff',
                'tcolor2' => 'ffffff',
                'hicolor' => 'ffffff',
                'bgcolor' => '333333',
                'speed' => '100',
                'trans' => 'false',
                'distr' => 'false',
                'args' => '',
                'mode' => 'tags'
            );
			Options::set('hb-cumulus__options_' . User::identify()->id, serialize($defOptions));
		}
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
                    $options = unserialize(Options::get('hb-cumulus__options_' . User::identify()->id));
					$ui= new FormUI( strtolower( get_class( $this ) ) );
                    $ui->append('fieldset', 'appearance', _t('Appearance'));
                        $ui->appearance->append( 'hidden', 'option_mode', 'null:null');
                            $ui->appearance->option_mode->value = $options['mode'];
                        $ui->appearance->append( 'text', 'options_width', 'null:null', _t('Width of Flash Tag Cloud (px)'), 'optionscontrol_text');
                            $ui->appearance->options_width->value = $options['width'];
                        $ui->appearance->append( 'text', 'options_height', 'null:null', _t('Height of Flash Tag Cloud (px)'), 'optionscontrol_text');
                            $ui->appearance->options_height->value = $options['height'];
                        $ui->appearance->append( 'text', 'options_tcolor', 'null:null', _t('Color of the Tags'), 'optionscontrol_text');
                            $ui->appearance->options_tcolor->value = $options['tcolor'];
                        $ui->appearance->append( 'text', 'options_tcolor2', 'null:null', _t('Second Color for Gradient (opt)'), 'optionscontrol_text');
                            $ui->appearance->options_tcolor2->value = $options['tcolor2'];
                        $ui->appearance->append( 'text', 'options_hicolor', 'null:null', _t('Highlight Color (opt)'), 'optionscontrol_text');
                            $ui->appearance->options_hicolor->value = $options['hicolor'];
                        $ui->appearance->append( 'text', 'options_bgcolor', 'null:null', _t('Background Color'), 'optionscontrol_text');
                            $ui->appearance->options_bgcolor->value = $options['bgcolor'];
                        $ui->appearance->append( 'text', 'options_speed', 'null:null', _t('Rotation Speed'), 'optionscontrol_text');
                            $ui->appearance->options_speed->value = $options['speed'];
                        //$ui->appearance->append( 'text', 'options_trans', 'null:null', _t('Use Transparent Mode'), 'optionscontrol_text');
                        //    $ui->appearance->options_trans->value = $options['trans'];
                        $ui->appearance->append( 'select', 'options_trans', 'null:null', _t( 'Use Transparent Mode' ));
                            $ui->appearance->options_trans->template = 'optionscontrol_select';
                            $ui->appearance->options_trans->options = array('true' => 'True', 'false' => 'False');
                        //$ui->appearance->append( 'text', 'options_distr', 'null:null', _t('Distribute Tags Evenly on Sphere'), 'optionscontrol_text');
                        //    $ui->appearance->options_distr->value = $options['distr'];
                        $ui->appearance->append( 'select', 'options_distr', 'null:null', _t( 'Distribute Tags Evenly on Sphere' ));
                            $ui->appearance->options_distr->template = 'optionscontrol_select';
                            $ui->appearance->options_distr->options = array('true' => 'True', 'false' => 'False');
                        //$ui->appearance->append( 'text', 'options_args', 'null:null', _t('Parameter String for $tags'), 'optionscontrol_text');
                        //    $ui->appearance->options_args->value = $options['args'];
                          
                    $ui->append('fieldset', 'advanced', _t('Advanced Options'));
                        $ui->advanced->append( 'label', '', _t('Please leave this setting empty unless you know what you\'re doing.'));
                        $ui->advanced->append( 'text', 'options_args', 'null:null', _t('Parameter String for $tags'), 'optionscontrol_text');
                            $ui->advanced->options_width->value = $options['args'];

					$ui->append( 'submit', 'save', _t('Save Options') );
                    $ui->on_success( array($this, 'serializeNStoreOpts') );
					$ui->out();
				break;
            }
		}
	}

    /**
     * Action handler: This takes all the options, serializes them and then stores in the options table.
     */

     static function serializeNStoreOpts ($ui) {
        $newOptions = array();
        foreach($ui->appearance->controls as $option) {
            list($a, $name) = explode('_', $option->name);
            $newOptions[$name] = $option->value;
        }
        Options::set('hb-cumulus__options_' . User::identify()->id, serialize($newOptions));
     }

     /**
	  * Add custom styling and Javascript controls to the footer of the admin interface
      *
      * This won't be necessary if I implement my own FormControls
	  **/
	public function action_admin_footer( $theme ) {
        if (Controller::get_var('configure') == $this->plugin_id) {
			echo <<< HB_CUMULUS_CSS
                <style type="text/css">
                   form#hbcumulus .formcontrol { line-height: 24px; height: 18px; }
                </style>
HB_CUMULUS_CSS;
		}
    }
}

?>
