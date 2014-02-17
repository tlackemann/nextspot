<h3>Try out this {{ term }}!</h3>
<div class="panel panel-default">
  <div class="panel-body">
  	<h2 class="media-heading">{{ place.name }}</h2>

  	<div class="media">
	  <!-- <a class="pull-left" href="#">
	    <img class="media-object" src="..." alt="...">
	  </a> -->
	  <div class="media-body">
	    <h4 class="media-heading">
	    	{% if place.location.address is defined %}{{ place.location.address }}{% endif %}
	    	{% if place.location.city is defined %}{{ place.location.city }},{% endif %}
	    	{% if place.location.state is defined %}{{ place.location.state }}{% endif %}
	    	{% if place.location.postalCode is defined %}{{ place.location.postalCode }}{% endif %}</h4>
	    <p>
	    {% if place.url is defined %}
	    	<a href="{{ place.url }}" target="_blank">{{ place.url }}</a><br/>
    	{% endif %}
    	{% if place.contact.formattedPhone is defined %}
	    	Phone: {{ place.contact.formattedPhone }}<br/>
	    {% endif %}
	    </p>
	  </div>
	</div>

    <hr/>
    <p><a href="#" class="btn btn-primary" id="like" role="button" data-id="{{ place.id }}">On my way!</a> <a href="#" class="btn btn-default" role="button" id="dislike" data-id="{{ place.id }}">No, try again</a></p>
  </div>
</div>
<h4>Not what you expected? Find a nearby</h4>
<div class="btn-group btn-group-justified">
	{% if term != 'bar' %}
    <div class="btn-group">
        <a data-latlng="" class="search btn btn-default" href="{{ url('search') }}?place=bar">Bar</a>
    </div>
    {% endif %}
    {% if term != 'pub' %}
    <div class="btn-group">
        <a data-latlng="" class="search btn btn-default" href="{{ url('search') }}?place=pub">Pub</a>
    </div>
    {% endif %}
    {% if term != 'club' %}
    <div class="btn-group">
        <a data-latlng="" class="search btn btn-default" href="{{ url('search') }}?place=club">Club</a>
    </div>
    {% endif %}
    {% if term != 'restaurant' %}
    <div class="btn-group">
        <a data-latlng="" class="search btn btn-default" href="{{ url('search') }}?place=restaurant">Restaurant</a>
    </div>
    {% endif %}
    {% if term != 'random' %}
    <div class="btn-group">
        <a data-latlng="" class="search btn btn-default" href="{{ url('search') }}?place=random">Random</a>
    </div>
    {% endif %}
</div>

<hr/>