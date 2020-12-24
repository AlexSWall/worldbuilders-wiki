import React from 'react';
import ReactDOM from 'react-dom';

import { GlobalsProvider } from 'GlobalsContext';

import NavigationBar from './Components/NavigationBar';
import ResetPasswordForm from './Components/Authentication/ResetPasswordForm';

ReactDOM.render(
	<GlobalsProvider>
		<div id="pageWrapper">
			<NavigationBar />
			<main>
				<div id="formWrapper">
					<ResetPasswordForm />
				</div>
			</main>
		</div>
	</GlobalsProvider>,
	document.getElementById('root')
);
