<nav class="navbar navbar-inverse" role="navigation">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand{% if router.getRewriteUri() == '/' %} active{% endif %}" href="{{ url() }}">
                <img src="{{ url('img/nextspot-logo-header.png') }}"/>
            </a>
        </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li class="{% if router.getRewriteUri() == '/' %}active{% endif %}">
                    <a href="{{ url() }}"><span class="glyphicon glyphicon-home"></span> Home</a>
                </li>
                <li class="{% if router.getRewriteUri() == '/search' %}active{% endif %}">
                    <a href="{{ url('search') }}?place=bar" class="search"><span class="glyphicon glyphicon-glass"></span> Search</a>
                </li>
                <li>
                    <a href="#"><span class="glyphicon glyphicon-tag"></span> Deals <span class="label label-danger">New!</span></a>
                    </li>
                <li>
                    <a href="{{ url('session/end') }}"><span class="glyphicon glyphicon-off"></span> Logout</a>
                </li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
<div class="container">

    <div id="alert" class="alert hide"></div>

    {{ content() }}

</div> <!-- /container -->

<div id="subfooter">
    <div class="container">
        <div class="col-sm-12">
            <form action="http://nextspot.us3.list-manage.com/subscribe/post?u=31c6b72e4ea486673cbe5e2c8&amp;id=5ac48a0523" method="post">
                <div class="form-group">
                    
                <h4 class="media-heading">Get the latest</h4>
                <p class="text-muted">Sign up for our mailing list</p>
                    <input type="text" name="EMAIL" class="form-control" placeholder="Email" />
                    <button type="submit" class="btn btn-danger">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="footer">
    <div class="container">
        <div class="col-sm-4">
            <h4>My Links</h4>
            <ul>
                <li><a href="#">Dashboard</a></li>
                <li><a href="#">Search</a></li>
                <li><a href="#">Settings</a></li>
            </ul>
        </div>
        <div class="col-sm-4">
            <h4>Company</h4>
            <ul>
                <li><a href="#">About Us</a></li>
                <li><a href="#">Contact Us</a></li>
                <li><a href="#">Blog</a></li>
            </ul>
        </div>
        <div class="col-sm-4">
            <h4>Support</h4>
            <ul>
                <li><a href="#">FAQ</a></li>
                <li><a href="#">Security</a></li>
                <li><a href="#">Terms &amp; Conditions</a></li>
                <li><a href="#">Privacy Policy</a></li>
            </ul>
        </div>

        <p class="text-muted">NextStop &copy; 2014</p>

    </div>
</div>
