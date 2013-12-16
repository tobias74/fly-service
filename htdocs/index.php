<!DOCTYPE html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  	<head>
	    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
		
		<meta charset="utf-8">		
		
		<link href="bb_zf_app/stylesheets/screen.css?<?php echo mt_rand(1,9999); ?>" rel="stylesheet" type="text/css"/>
		<!--[if IE]>
		<link href="bb_zf_app/stylesheets/ie.css" media="screen, projection" rel="stylesheet" type="text/css"/>
		<![endif]-->

			
	    <link rel="stylesheet" href="bb_zf_app/lib/css/jquery-ui-base.css" />
		
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
		<script src="bb_zf_app/lib/json2.js"></script>
		<script src="bb_zf_app/lib/mustache.js"></script>
		<script src="bb_zf_app/lib/jquery.js"></script>
		<script src="bb_zf_app/lib/jquery-ui.js"></script>
		<script src="bb_zf_app/lib/jquery.tmpl.js"></script>
		<script src="bb_zf_app/lib/underscore.js"></script>
		<script src="bb_zf_app/lib/backbone.js"></script>
		<script src="bb_zf_app/lib/backbone.localStorage.js"></script>
		<script src="bb_zf_app/lib/backbone.paginator.js"></script>
		<script src="bb_zf_app/lib/IDBStore.js"></script>
		
		<script>

			Backbone.View.prototype.destroy = function() {
				this.undelegateEvents();
				this.model && this.model.off(null, null, this);
				this.collection && this.collection.off(null, null, this);
				this.outitialize &&	this.outitialize();
				this.destroyChildViews();
				this.remove();
			}
			
			Backbone.View.prototype.destroyChildViews = function() {
				_.each(this._childViews, function(view){
					view.destroy(); 
				});
			}

			Backbone.View.prototype.attachChildViews = function(childViews, options) {
				options || (options = {});
				if (options.reset)
				{
				    console.debug('destroying child-views');
				    this.destroyChildViews();
				}
				else
				{
				    console.debug('#####################not destroying child views? why???');
				}
				childViews = _.isArray(childViews) ? childViews.slice() : [childViews];
				this._childViews || (this._childViews = new Array());

				_.each(childViews, _.bind(function(view){
					if (_.indexOf(this._childViews, view) === -1) this._childViews.push(view);
					if (options.render) view.render();
				}, this));
			}

			Backbone.View.prototype.detachChildViews = function(childViews, options) {
				options || (options = {});
				childViews = _.isArray(childViews) ? childViews.slice() : [childViews];
	
				_.each(childViews, _.bind(function(view){
					this._childViews = _.without(this._childViews, view)
					if (options.remove) view.remove();
					if (options.destroy) view.destroy();
				}, this));

			}
			
            
            Backbone.Model.prototype.parse = function(resp, xhr) {
                if (this.myDelegate && this.myDelegate.willParse)
                {
                    this.myDelegate.willParse(resp, xhr);
                }
                return resp;
            }
			
            Backbone.Model.prototype.setDelegate = function(delegate) {
                this.myDelegate = delegate;
            }


            Backbone.Collection.prototype.parse = function(resp, xhr) {
                if (this.myDelegate && this.myDelegate.willParse)
                {
                    this.myDelegate.willParse(resp, xhr);
                }
                return resp;
            }
            
            Backbone.Collection.prototype.setDelegate = function(delegate) {
                this.myDelegate = delegate;
            }
			    



		</script>

		<script src="//connect.facebook.net/en_US/all.js" type="text/javascript"></script>
		
		<script src="bb_zf_app/bin/app.js?some=<?php echo rand(0,9999);?>" type="text/javascript"></script>
		
		
	</head>

	<body>
		<div id="fb-root"></div>
		<script>
		  // Load the SDK Asynchronously
		  /*
		  (function(d){
		     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
		     if (d.getElementById(id)) {return;}
		     js = d.createElement('script'); js.id = id; js.async = true;
		     js.src = "//connect.facebook.net/en_US/all.js";
		     ref.parentNode.insertBefore(js, ref);
		   }(document));
		   */
		</script>
		
		
		
		<?php
			date_default_timezone_set('Europe/Berlin');
			system('callcake.bat');
			system('callcompass.bat');
			require_once(dirname(__FILE__).'/bb_zf_app/bin/status.log');
			require_once(dirname(__FILE__).'/bb_zf_app/bin/app.html');
		?>
	</body>
</html>