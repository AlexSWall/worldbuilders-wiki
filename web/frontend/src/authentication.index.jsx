import React from 'react';
import ReactDOM from 'react-dom';

import { GlobalsProvider } from 'GlobalsContext';
import AuthenticationApp from './AuthenticationApp';

// Import webpageBaseData variable from wiki.index.twig
import webpageBaseData from 'webpageBaseData';

ReactDOM.render(
	<GlobalsProvider>
		<AuthenticationApp
			formProperties={ webpageBaseData.formProperties }
		/>
	</GlobalsProvider>,
	document.getElementById('root')
);
