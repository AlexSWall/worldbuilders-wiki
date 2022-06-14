import React from 'react';
import ReactDOM from 'react-dom';

import { GlobalStateWrapper } from 'GlobalState';

import { AdministrationApp } from './AdministrationApp';

ReactDOM.render(
	<GlobalStateWrapper>
		<AdministrationApp />
	</GlobalStateWrapper>,
	document.getElementById('root')
);
