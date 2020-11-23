import React from 'react';
import ReactDOM from 'react-dom';
import AdministrationApp from './AdministrationApp';

import webpageBaseData from 'webpageBaseData'; /* Imports webpageBaseData variable from index.php. */

ReactDOM.render(<AdministrationApp
	authenticationData={webpageBaseData.authenticationData}
	flash={webpageBaseData.flash}
/>, document.getElementById('root'));
