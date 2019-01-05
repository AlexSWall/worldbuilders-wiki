{% if user %}
	<p>Hello {{ user.name }},</p>
{% else %}
	<p>Hello there,</p>
{% endif %}

{% block content %}{% endblock %}