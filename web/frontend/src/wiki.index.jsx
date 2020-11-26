import React from 'react';
import ReactDOM from 'react-dom';

import WikiPageApp from './WikiPageApp';
import { GlobalProvider } from './GlobalStore';

// Import webpageBaseData variable from wiki.index.twig
import webpageBaseData from 'webpageBaseData';

const globalData = {
	authData: webpageBaseData.authenticationData,
	flash: webpageBaseData.flash
};

ReactDOM.render(
	<GlobalProvider data={ globalData }>
		<WikiPageApp />
	</GlobalProvider>,
	document.getElementById('root')
);
