Plugin: HB-Cumulus 1.5
URL: http://www.lildude.co.uk/projects/hb-cumulus
Plugin Author: Colin Seymour - http://colinseymour.co.uk
Credit goes to: Roy Tanck for the original WP-Cumulus plugin and Geoff Stearns for
swfobject as used by Roy's work.
Licenses:  HB-Cumulus (hb-cumulus.plugin.php) : Apache Software License 2.0
           TagCloud (tagcloud.swf): GNU Public License v3
           swfobject (swfobject.js): MIT License

HB-Cumulus is a Flash-based tag cloud for Habari that displays your tag cloud in a rotating sphere.

HB-Cumulus is a port of the brilliant WP-Cumulus (http://wordpress.org/extend/plugins/wp-cumulus/) by Roy Tanck (http://www.roytanck.com/).

FUNCTIONALITY
-------------

I've implemented all the functionality offered by WP-Cumulus 1.23 and a bit more.

Functionality includes the ability to set...

    * your own width and height
    * your own foreground, background (or transparent) and highlight colours
    * a rotation speed to suit your needs
    * the keywords to exclude
    * the number of keywords to display
    * the minimum and maximum font sizes to use

... all within the Habari plugin configuration options. There is even a preview of
the cloud within the configuration section so you can see your changes taking effect
as you save them.


INSTALLATION
------------

   1. Download either the zip or tar.bz2 to your server
   2. Extract the contents to a temporary location (not strictly necessary, but just being safe)
   3. Move the hb-cumulus directory to /path/to/your/habari/user/plugins/
   4. Refresh your plugins page, activate the plugin and configure it to suit your needs

That's it. You're ready to implement the cloud into your site.


UPGRADE
-------

The upgrade procedure is as per the installation procedure, but please ensure you
de-activate the plugin first.  This will ensure your current settings are merged
with any new options that may be added with later releases and reduce the chances
of encountering errors.


USAGE
-----

There are two ways you can use HB-Cumulus:

   1. In ANY page or post:

      You can show the cloud in any page or post by putting the following code into
      the post/page content:

      <!-- hb-cumulus -->

      This tag is NOT case sensitive, so don't worry too much about the case or 
      spacing. So long as you have all of the above characters in that order, it
      should display.

   2. In ANY theme file:

      You can show the cloud anywhere on your site within your theme files, for
      example in the sidebar using:

      $theme->hbcumulus();

      This IS case sensitive, so you'll need to be sure you get it 100% correct.


ADDITIONAL INFORMATION
----------------------

There are a couple of things worth noting for reference purposes:

    * Deactivating the plugin will delete your saved HB-Cumulus options, so you
      can always return to the default options by reactivating the plugin.
    * The following options are provided by default:
          o Width: 500px *
          o Height: 375px *
          o Tag Colour: #FFFFFF - to ensure reliable behaviour, HB-Cumulus will only accept 6 character HTML colours *
          o Second Colour: #FFFFFF (Optional) *
          o Highlight Colour: #FFFFFF (Optional) *
          o Background Colour: #333333 *
          o Speed: 100 - percentage *
          o Transparent: FALSE *
          o Distribute Tags Evenly: FALSE *
          o List of tags to hide: <not set> - this can be a space or comma separated list
          o Minimum Font Size: 8pt
          o Maximum Font Size: 25pt
          o Number of Tags to show: 30 - set to 0 or nothing to show all tags

      More information about the options marked with * can be found on the WP-Cumulus notes page.
    * Hb-Cumulus will work with Habari 0.6.2 and the latest SVN code.
    * Habari has no concept of categories at the moment, so it'll only show tags.
      If and when Habari gets categories, I'll update the plugin to support categories too.


REVISION HISTORY
----------------

1.5	- Updated tagcloud.swf to that provided with WP-Cumulus 1.23
	- Updated swfobject to version 2.2
	- Improved HTML validity
1.4	- Tidied up code a bit better and minimised swfobject.js
        - Made compatible with Habari SVN code (aka 0.7 when it's released)
1.3	- Updated tagcloud.swf to that provided with WP-Cumulus 1.20, which includes
          the following relevant fixes:
			"Fixed the mouse pointer not changing to a hand when hovering tags."
			"Adds Turkish language support to the Flash movie."
	    - Like the WP-Cumulus plugin, I've also added a "Compatibility" option.  
              When this is enabled, the plugin will not use the SWFObject Javascript
              to embed the Flash.
1.2     - Updated tagcloud.swf to that provided with WP-Cumulus 1.18, which includes
          the following fix:
            "Improves mouse detection in transparent mode"
        - Also sorted out the documenting of the licensing of the various components
        - Updated directory structure to better separate the 3rd party components
          and the HB-Cumulus code
		- Tidied up code to adhere closer to the Habari coding standards
1.1     - Fixed bug in plugin de-activation that caused HB-Cumulus settings to be
          deleted when other plugins were de-activated.
1.0     - Initial release

That's it folks. If you encounter any problems, please feel free to leave a comment
on the post that relates to the release.
