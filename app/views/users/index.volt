<div class="jumbotron">
        <h1>Where to next?</h1>
        <p>Current location: <span id="location"></span></p>
        <!-- <div class="btn-group btn-group-justified">
            <div class="btn-group">
                <a data-latlng="" class="search btn btn-default" href="{{ url('search') }}?place=bar">Bar</a>
            </div>
            <div class="btn-group">
                <a data-latlng="" class="search btn btn-default" href="{{ url('search') }}?place=pub">Pub</a>
            </div>
            <div class="btn-group">
                <a data-latlng="" class="search btn btn-default" href="{{ url('search') }}?place=club">Club</a>
            </div>
            <div class="btn-group">
                <a data-latlng="" class="search btn btn-default" href="{{ url('search') }}?place=restaurant">Restaurant</a>
            </div>
            <div class="btn-group">
                <a data-latlng="" class="search btn btn-default" href="{{ url('search') }}?place=random">Random</a>
            </div>
        </div> -->
        <ul class="nav nav-pills nav-justified">
            <li>
                <a data-latlng="" class="search" href="{{ url('search') }}?place=bar">Bar</a>
            </li>
            <li>
                <a data-latlng="" class="search" href="{{ url('search') }}?place=pub">Pub</a>
            </li>
            <li>
                <a data-latlng="" class="search" href="{{ url('search') }}?place=club">Club</a>
            </li>
            <li>
                <a data-latlng="" class="search" href="{{ url('search') }}?place=restaurant">Restaurant</a>
            </li>
            <li>
                <a data-latlng="" class="search" href="{{ url('search') }}?place=random">Random</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-8">
            <h2>Recently Visited</h2>
            {% if places is defined %}
                {% for place in places %}
                <div class="caption">
                    {% for like in likes %}
                        {% if like.place_id is place.id %}
                        <small class="text-muted">{{ date("F d, Y h:i a", strtotime(like.created_at)) }}</small>
                        {% endif %}
                    {% endfor %}
                    <h3 class="media-heading">{{ place.name }}</h3>
                </div>
                {% endfor %}
            {% else %}
                <p class="text-muted">
                    Nothing yet :(
                </p>
            {% endif %}
        </div>
        <hr class="sm-only"/>
        <div class="col-md-4" id="ad">
            <small>advertisement</small>
            <a href="#">
                <img class="thumbnail" src="http://placekitten.com/g/300/300"/>
            </a>
        </div>
    </div>

    <hr/>

    <div class="row">
        <div class="col-lg-6">
            <h4>Connect with</h4>
            {% if !user.fs_uid %}
            <a class="btn btn-block btn-social btn-foursquare" href="{{ url('foursquare') }}">
                <i class="fa fa-foursquare"></i> Connect with Foursquare
            </a>
            {% endif %}
            {% if !user.fb_uid %}
            <a class="btn btn-block btn-social btn-facebook" href="{{ url('facebook') }}">
                <i class="fa fa-facebook"></i> Connect with Facebook
            </a>
            {% endif %}
            {% if !user.ig_uid %}
            <a class="btn btn-block btn-social btn-instagram" href="{{ url('instagram') }}">
                <i class="fa fa-instagram"></i> Connect with Instagram
            </a>
            {% endif %}
            {% if !user.tw_uid %}
            <a class="btn btn-block btn-social btn-twitter" href="{{ url('twitter') }}">
                <i class="fa fa-twitter"></i> Connect with Twitter
            </a>
            {% endif %}
            {% if !user.gp_uid %}
            <a class="btn btn-block btn-social btn-google-plus" href="{{ url('gplus') }}">
                <i class="fa fa-google-plus"></i> Connect with Google+
            </a>
            {% endif %}
        </div>

        <hr class="md-only"/>

        <div class="col-lg-6">
            <h4>Link your accounts</h4>
            <p>Link your other accounts to discover more places and connect with your friends while you're out</p>

            <h4>Linked accounts</h4>
            {% if user.fs_uid %}
            <p>
                <a class="btn btn-social-icon btn-foursquare"><i class="fa fa-foursquare"></i></a>
            {% endif %}
            {% if user.fb_uid %}
                <a class="btn btn-social-icon btn-facebook"><i class="fa fa-facebook"></i></a>
            {% endif %}
            {% if user.ig_uid %}
                <a class="btn btn-social-icon btn-instagram"><i class="fa fa-instagram"></i></a>
            {% endif %}
            {% if user.tw_uid %}
                <a class="btn btn-social-icon btn-twitter"><i class="fa fa-twitter"></i></a>
            {% endif %}
            {% if user.gp_uid %}
                <a class="btn btn-social-icon btn-gplus"><i class="fa fa-gplus"></i></a>
            </p>
            {% endif %}
        </div>
    </div>

    <hr/>