import React from 'react';
import ReactDOM from 'react-dom';

import { GlobalsProvider } from 'GlobalsContext';
import WikiPageApp from './WikiPageApp';

// Import webpageBaseData variable from wiki.index.twig
import webpageBaseData from 'webpageBaseData';

const globals = {
	authData: webpageBaseData.authenticationData,
	flash: webpageBaseData.flash
};

ReactDOM.render(
	<GlobalsProvider globals={ globals }>
		<WikiPageApp />
	</GlobalsProvider>,
	document.getElementById('root')
);
