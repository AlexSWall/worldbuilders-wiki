import React from 'react';
import ReactDOM from 'react-dom';

import { GlobalsProvider } from 'GlobalsContext';
import WikiPageApp from './WikiPageApp';

// Import webpageBaseData variable from wiki.index.twig
import webpageBaseData from 'webpageBaseData';

const globalData = {
	authData: webpageBaseData.authenticationData,
	flash: webpageBaseData.flash
};

ReactDOM.render(
	<GlobalsProvider data={ globalData }>
		<WikiPageApp />
	</GlobalsProvider>,
	document.getElementById('root')
);
