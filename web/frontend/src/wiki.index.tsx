import React from 'react';
import ReactDOM from 'react-dom';

import { GlobalStateWrapper } from 'GlobalState';
import { WikiPageApp } from './WikiPageApp';

ReactDOM.render(
	<GlobalStateWrapper>
		<WikiPageApp />
	</GlobalStateWrapper>,
	document.getElementById('root')
);
