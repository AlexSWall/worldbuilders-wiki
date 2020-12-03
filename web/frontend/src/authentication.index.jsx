import React from 'react';
import ReactDOM from 'react-dom';

import { GlobalsProvider } from 'GlobalsContext';
import AuthenticationApp from './AuthenticationApp';

// Import globalsData variable from within script in the HTML.
import globalsData from 'globalsData';

ReactDOM.render(
	<GlobalsProvider>
		<AuthenticationApp
			formProperties={ globalsData.formProperties }
		/>
	</GlobalsProvider>,
	document.getElementById('root')
);
