import React from 'react';
import ReactDOM from 'react-dom';
import AuthenticationApp from './AuthenticationApp';

import myApp from 'myApp'; /* Imports myApp variable from index.php. */


ReactDOM.render(
	<AuthenticationApp
		auth={myApp.auth}
		formProperties={myApp.formProperties}
		csrfField={myApp.csrfField}
	/>, document.getElementById('root'));
