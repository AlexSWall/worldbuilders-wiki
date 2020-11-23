import React from 'react';
import ReactDOM from 'react-dom';
import WikiPageApp from './WikiPageApp';

import webpageBaseData from 'webpageBaseData'; /* Imports webpageBaseData variable from wiki.intex.twig */

ReactDOM.render(<WikiPageApp 
	authenticationData={webpageBaseData.authenticationData}
	flash={webpageBaseData.flash}
/>, document.getElementById('root'));
