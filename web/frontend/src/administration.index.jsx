import React from 'react';
import ReactDOM from 'react-dom';

import { GlobalsProvider } from 'GlobalsContext';
import AdministrationApp from './AdministrationApp';

ReactDOM.render(
	<GlobalsProvider>
		<AdministrationApp />
	</GlobalsProvider>,
	document.getElementById('root')
);
