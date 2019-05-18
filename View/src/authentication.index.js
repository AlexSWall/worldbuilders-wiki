import React from 'react';
import ReactDOM from 'react-dom';
import AuthenticationApp from './AuthenticationApp';

import webpageBaseData from 'webpageBaseData'; /* Imports webpageBaseData variable from index.php. */

ReactDOM.render(<AuthenticationApp
	authenticationData={webpageBaseData.authenticationData}
	formProperties={webpageBaseData.formProperties}
	flash={webpageBaseData.flash}
/>, document.getElementById('root'));
