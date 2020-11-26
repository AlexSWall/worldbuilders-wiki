import React from 'react';
import ReactDOM from 'react-dom';

import { GlobalsProvider } from 'GlobalsContext';
import WikiPageApp from './WikiPageApp';

ReactDOM.render(
	<GlobalsProvider>
		<WikiPageApp />
	</GlobalsProvider>,
	document.getElementById('root')
);
