import React from 'react';
import ReactDOM from 'react-dom';

import { GlobalsProvider } from 'GlobalsContext';
import AuthenticationApp from './AuthenticationApp';

// Import webpageBaseData variable from wiki.index.twig
import webpageBaseData from 'webpageBaseData';

const globals = {
	authData: webpageBaseData.authenticationData,
	flash: webpageBaseData.flash
};

ReactDOM.render(
	<GlobalsProvider globals={ globals }>
		<AuthenticationApp
			formProperties={ webpageBaseData.formProperties }
		/>
	</GlobalsProvider>,
	document.getElementById('root')
);
