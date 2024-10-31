=== One Sport - Route Map ===
Contributors: http://www.onesportevent.com/about-us
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JKKRLSSDU7KY6
Tags: map, google map, bing map, draw routes, mapping, running routes, biking routes, cycling routes, silverlight, club, ajax
Requires at least: 2.7
Tested up to: 3.3.2
Stable tag: 3.0.1

Create, display and share routes guests or admins have drawn in the mapping tool.

== Description ==
Several major sites now use the mapping plugin including Runners World Africa who have kindly paid to enhance the plug-in further.

Fun widget where you can provide your guests with a list of routes they can run, walk or bike in your area.  They will spend even more time on your website creating their own routes too which increases google ranking.

Because we all share the list of routes your site will instantly look interesting and engaging if there are routes in your area - although you can use the plug-in in club mode with just your own routes too.

Thanks to everyone who has sent feedback to sport@onesportevent.com.  Do contact me if you need help with styling or improvement requests; the styles should generally work out of the box but I am always making improvements to handle the varying themes that people use the plug-in with.

Your guests can also draw new routes on top of the map and get elevation profiles, calorie and distance and time measures.

The db export website example below shows how DB export have used the plug-in with their own database as a fun-challenge / competition entry, so if you map a new route on their website you go into the draw to get a dozen export 33 each month.  If you register you'll note how they have a completely registration process.

= Some example sites =

* http://www.fitnessmentor.com/routes
* http://www.dbexportbeer.co.nz/Competitions/Map-Training.aspx
* http://www.onesportevent.com/routes 

= Features =

* Easy and instantly available.  Install the plug-in and routes appear on your website instantly
* Flexible - configure which areas you want to see routes for
* Slick mapping - carefully designed to be easy to use all internet users
* Maximum screen space - a fold always interface and full screen options lets you see maps properly
* Latest technology - Silverlight 4.0 from Microsoft; onetime cached download means fast subsequent map browsing
* Constantly updated - it's a shared database everyone is using
* Valid XHTML - Valid CSS based output works with all web browsers
* High performance - your website renders first and multiple connections allow parallel downloads
* Free internet, online chat and telephone support, see about us page at onesportevent
* NEW! Automatic fallback to a basic HTML mapper with google maps if the advanced Silverlight mapper is not available

If you are a club, maybe you want to provide a list of just your clubs running/biking/walking routes? contact me to get a club id.

Information on the route mapping plug-in is now available from my website - 
http://www.onesportevent.com/get-free-mapping-tool-for-your-website/

Same goes for my events plug-in, which IMHO, is very cool! :)
http://www.onesportevent.com/get-free-event-calendar-widget-on-your-website/

== Installation ==

1. Upload the entire 'onesportroute' directory to `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. From Settings, One Sport Routes, choose 'create new route page' and configure the settings how you want them.
1. Get an API key from http://www.onesportevent.com/get-widget-key
1. Optionally get an API key if you want to use Microsoft BING maps http://www.bingmapsportal.com/

== Frequently Asked Questions ==

= How to make theme work with mine? =
The mapper has been professional styled but there are so many themes out there it may not look perfect with your theme out of the box.  You have full control over the theme by editing the css file in the style section or hosting it on your own website.   You can also email me at sport@onesportevent.com with a link to your page and I can adjust styles to fit with your website.

= I entered stuff into the style or region parameters and it stopped working =
Yep, checkout the bottom of the api documentation page (link below) for how to get valid codes.  I see a lot of people entering, for example - 'australia' into the area, or 'running' into the activities which won't work, those fields are expecting numbers like '2' or '3' - see the docs or email/forum msg me to ask.

If the mapper does not look right then you need to change either your theme, mine or both.  By default you reference the theme on the api website - http://api.onesportevent.com/api/style/v1/css/osestyle.css but there is also a local copy which will be in the path where you installed the plugin, e.g. http://yourwebsite.com/wp-content/plugins/one-sport-route-mapper/css/osestyle.css 

= Not wide enough? =
You can use the settings to turn off the sidebars.  Alternatively, you can probably use a wordpress page template that does **not** have a sidebar to get more space.  Edit the page the theme is on and change the template if you need to.  

To edit your web theme go into 'Appearance -> Editor ->' and shrink/expand either or your website themes css. Be sure to take a backup copy

To edit the mapper theme go into 'Plugins -> Editor -> [select route mapper in combobox on right]' and edit the css file, again, be sure to take a backup copy

= Can I get nicer maps?  These ones look average = 

Sure thing; by default I'm using the free open source maps, which I have to say are excellent given it's an open source project.   If you enter a BING map key you can use the Microsoft Maps which are very nice!

= But doesn't the BING map cost? =

Depends on your usage - at the time of writing they have free map keys which you probably qualify for.. but go read Microsofts terms and conditions for BING, there is a link to BING from inside the plug-in

= Can I use google maps? =

The default HTML fallback option uses google maps.  When I saw the Silverlight BING option I was blown away how much better and smoother it was so thats the default.  It does have a one-time download which is a bit slower, but then your golden!

= What about my clubs routes? = 

Yes you can make the plug in show just see a sub-set of routes for a given area that you want to see.

= What about saving routes? = 

If your visitor saves a route (optional) then it is saved into the database and the routes can be seen by everyone.  All sites using the plug-in benefit because the existing routes make your site look active and used, which is critical for new websites.  You can enable wordpress login integration with a single checkbox so visitors can can see their personal workout statistics and route statistics which they return.  You can also optionally emailed a permalink when users save routes.

= Is there advertising in the plug in? = 

Nope.  But donations are welcomed.. encouraged even!  If you want your own logo on the mapper I can do this for a small cost.  If you turn on the permalink email the email gets sent from onesportevent.com with a back to our sponser fitnessmentor.com, but this can be turned on/off.

= Why do I need an API key? = 

Initially I didn't have one, however I realised I then have no way of stopping a website if they are abusing the system in some way, or leeching more bandwidth than my mortgate can afford!

= Place to get your own API key is here =
http://www.onesportevent.com/get-widget-key

= All API Documentation is now maintained online =
http://www.onesportevent.com/route-mapping-api-documentation/

= Terms and conditions are here =
http://www.onesportevent.com/api-terms-and-conditions/

= Features about the route widget are here =
http://www.onesportevent.com/get-free-mapping-tool-for-your-website/

= Download page for HTML and CMS's is here =
http://www.onesportevent.com/route-mapping-download/


== Changelog ==
= Version 1.3 =
This is the first version!   Please don't rate me down - send me some feedback about how it works for you and I will incorporate feedback into new editions as I make improvements.

= Version 1.4 =
Ok just installed the first version live myself, and I see my default css isn't quite right, it is incorrectly overriding the users theme in some cases, and in others the users theme makes my default theme look crap.  I have made some improvements but will need more over the next few days.  Also realised it will be easier for people to create a copy of my theme if I **also** ship it, so have included it in the package.  See FAQ on this matter.

= Version 1.5 =
Corrected outgoing email if user opts for saving a route; it had left-over sponsors message (who paid for the development) in it; now removed.  Added more generic email and new option to use include your own message.  Improved routing on mapper; sometimes Microsoft's routing server fails and this condition was not handled well by a third party software, which has been upgraded.  Seems to work flawlessly now?  More improvements to API styling so it works better 'out of the box', including a parameter to control how many areas are displays horizontally; although still more improvements are comming.

= Version 1.6 =
As identified by Will Chapman (thanks), didn't work if your installations didn't use the default wp_ prefix for tables.  Fixed up errors in linking on the admin screen, and installation problems.  Added cleanup of settings on uninstall.  Improved shared stylesheet.

= Version 1.7 =
World view now correct shows a link to create new maps.  Started working on the GPX file upload, improved the mapper performance a little and a few flexibility improvements to the API.  (finally!) added a couple of training videos into the mapper, and tooltips to make settings more obvious.

= Version 1.8 =
Fixed crash bug when opening personal stats before a route has been loaded from database.  Added feature to support website specific routes so you can exclude routes you create from appearing on other api websites.

= Version 1.9 =
Can now restrict which activities are available when saving workouts.  Fixed problem where mapper raised errors if could not save user preferences - e.g. the user had purposefully disabled the silverlight local storage.  Improved other aspects of code robustness.

= Version 2.0 =
Major CSS version upgrade to flexible width, or fluid css layout capability.  A lot of the time the list would not show correcly out of the box meaning you had to change the css to fit with your website.  Now, by default the layout will render nicely on a variety of different width themes without requiring any changes.
Areas panel can now be hidden/shown.  Fixed major bug where routes sometimes loaded and displayed incorrectly in some cases.

= Version 2.2 =
Updated mapping tool to improve reliability, display confirmation messages on save and feedback

= Version 2.3 =
Updated mapper, now v4.1, brand new engine, fixed bugs with route length calculation, supports danish language, deliver of files defaults to amazon for faster more local download

= Version 2.4 =
Fixed overlooked layout display bug in configuration, language settings now read from user browser rather than user operating system

= Version 2.5 =
Fixed bug preventing mapper loading on websites under certain configuration options, generally improved stability, added gps functionality for beta users

= Version 2.6 =
New option to avoid double login; so if user is already logged into the blog and wants to save their route to your blog they are not prompted to login again.  Improve mapper control panel height to allow more space, implimented suggestion by trisport to not show welcome screen to new users if loading existing map.

= Version 3.0 =
Major upgrades to both the mapper and routes list.  Loads of new features and options including two new present styles for the routes and the ability to deeply restyle the mapper and the routes list right from within the wordpress plug-in (can also be done via html).  Even the buttons inside the mapper can be easily swapped out for buttons of your choosing.  Mapper now includes automatic fallback option to a basic HTML version using google maps if the end user doesn't have silverlight on their PC.  Now works on apple ipad.

== Screenshots ==

1. Mapper normal view
1. Mapper folds away menus
1. List of routes 
1. High resolution satellite view
1. Some Settings
1. Plug-in admin screen
1. Example with different branding style
1. Mapping screen styling options
1. Branding example
1. Expandable social stats

== Upgrade Notice ==

OneSport - Massive changes to with a new pre-set styling options, HTML google maps version, ipad compadible, fluid layout.