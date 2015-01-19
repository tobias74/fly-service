<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Resized Image Cache (beta)</title
      
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    
    <!-- Latest compiled and minified JavaScript -->
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
    
    <script src="//cdnjs.cloudflare.com/ajax/libs/knockout/3.1.0/knockout-min.js"></script>
    

    <style>
      /* Move down content because we have a fixed navbar that is 50px tall */
      body {
        padding-top: 50px;
        padding-bottom: 20px;
      }      
    </style>



</head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Resized Image Cache (beta)</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <form style="display:none;" class="navbar-form navbar-right">
            <div class="form-group">
              <input type="text" placeholder="Email" class="form-control">
            </div>
            <div class="form-group">
              <input type="password" placeholder="Password" class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Sign in</button>
          </form>
        </div><!--/.navbar-collapse -->
      </div>
    </nav>

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron">
      <div class="container">
        <h1>Resized Images as a Service!</h1>
        <p>This Service offers resizing and caching of images identified by a URL. All your Resizing- and Caching-Needs can be fullfilled by one simple GET-Request. You'll see. Scroll on. </p>
        <p>Please be advised that this service is still in beta-phase. Do not use it in production!</p>
        <p style="display:none;"><a class="btn btn-primary btn-lg" href="#" role="button">Learn more &raquo;</a></p>
      </div>
    </div>

    <div class="container">
      <!-- Example row of columns -->
      <div class="row">
        <div class="col-md-12">
          <h2>Resizing</h2>
          <p>Let's say your users upload images to your website which then can be accessed in the browser by 
            <i style="color:blue;">http://www.yourwebsite.com/uploads/some-very-big-image.jpg</i>. 
            Serving uploaded files to your users without scaling them down a bit is not feasable. You can now easily serve a small version of the image simply by referencing 
            <i style="color:blue;">http://www.resizedimagecache.com/image/getCachedImage/imageSize/small?imageUrl=http://www.yourwebsite.com/uploads/some-very-big-image.jpg</i></p>
          <p style="display:none;"><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
        </div>
      </div>

      <hr>

      <div class="row">
        <div class="col-md-12">
          <h2>Examples</h2>
          
          <form >
            <div class="form-group">
              <label for="imageUrlInput">Your Image URL</label>
              <input type="text" class="form-control" id="imageUrlInput" placeholder="Enter Image URL" data-bind="value: originalImagePath">
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
          </form>          
          
          <hr>
          
          <ul class="media-list">
            <li class="media">
              <h4>Serving a small version of your image</h4>
              <code class="media-left" data-bind="text: smallImagePath"></code>
              <img class="media-body" data-bind="attr:{src: smallImagePath}" />
              <hr>
            </li>

            <li class="media">
              <h4>Serving a medium version of your image</h4>
              <code class="media-left" data-bind="text: mediumImagePath"></code>
              <img class="media-body" data-bind="attr:{src: mediumImagePath}" />
              <hr>
            </li>

            <li class="media">
              <h4>Serving a big version of your image</h4>
              <code class="media-left" data-bind="text: bigImagePath"></code>
              <img class="media-body" data-bind="attr:{src: bigImagePath}" />
              <hr>
            </li>

            <li class="media">
              <h4>Serving a custom version of your image given maximum width and height, using whatever is reached first</h4>
              <code class="media-left" data-bind="text: custom_1_ImagePath"></code>
              <img class="media-body" data-bind="attr:{src: custom_1_ImagePath}" />
              <hr>
            </li>

            <li class="media">
              <h4>Serving a custom version of your image given maximum width and height, using whatever is reached first</h4>
              <code class="media-left" data-bind="text: custom_2_ImagePath"></code>
              <img class="media-body" data-bind="attr:{src: custom_2_ImagePath}" />
              <hr>
            </li>

            <li class="media">
              <h4>Serving a big version of your image in 16 x 9 format</h4>
              <code class="media-left" data-bind="text: custom_3_ImagePath"></code>
              <img class="media-body" data-bind="attr:{src: custom_3_ImagePath}" />
              <hr>
            </li>

            <li class="media">
              <h4>Serving a medium version of your image in 4 x 3 format</h4>
              <code class="media-left" data-bind="text: custom_4_ImagePath"></code>
              <img class="media-body" data-bind="attr:{src: custom_4_ImagePath}" />
              <hr>
            </li>


            
          </ul>
        </div>
      </div>

      <hr>

      <footer style="display:none;">
        <p>&copy; </p>
      </footer>
    </div> <!-- /container -->



    <script type="text/javascript">
        var viewModel = {
            url: ko.observable("year-end.html"),
            details: ko.observable("Report including final year-end statistics"),
            originalImagePath: ko.observable("http://upload.wikimedia.org/wikipedia/commons/0/05/20100726_Kalamitsi_Beach_Ionian_Sea_Lefkada_island_Greece.jpg")
        };
        
        viewModel.smallImagePath = ko.dependentObservable(function(){
          return "http://www.resizedimagecache.com/image/getCachedImage/imageSize/small?imageUrl=" + viewModel.originalImagePath();
        });

        viewModel.mediumImagePath = ko.dependentObservable(function(){
          return "http://www.resizedimagecache.com/image/getCachedImage/imageSize/medium?imageUrl=" + viewModel.originalImagePath();
        });

        viewModel.bigImagePath = ko.dependentObservable(function(){
          return "http://www.resizedimagecache.com/image/getCachedImage/imageSize/big?imageUrl=" + viewModel.originalImagePath();
        });

        viewModel.custom_1_ImagePath = ko.dependentObservable(function(){
          return "http://www.resizedimagecache.com/image/getCachedImage/imageSize/custom/maxWidth/600/maxHeight/400?imageUrl=" + viewModel.originalImagePath();
        });

        viewModel.custom_2_ImagePath = ko.dependentObservable(function(){
          return "http://www.resizedimagecache.com/image/getCachedImage/imageSize/custom/maxWidth/400/maxHeight/400/format/square?imageUrl=" + viewModel.originalImagePath();
        });

        viewModel.custom_3_ImagePath = ko.dependentObservable(function(){
          return "http://www.resizedimagecache.com/image/getCachedImage/imageSize/big/format/9by6?imageUrl=" + viewModel.originalImagePath();
        });

        viewModel.custom_4_ImagePath = ko.dependentObservable(function(){
          return "http://www.resizedimagecache.com/image/getCachedImage/imageSize/medium/format/4by3?imageUrl=" + viewModel.originalImagePath();
        });


        ko.applyBindings(viewModel);
    </script>


  </body>
</html>
