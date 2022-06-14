import React from 'react';
import ReactDOM from 'react-dom';

import { GlobalStateWrapper } from 'GlobalState';

import { NavigationBar } from './Components/NavigationBar';
import { ResetPasswordForm } from './Components/Authentication/ResetPasswordForm';

ReactDOM.render(
	<GlobalStateWrapper>
		<div id="pageWrapper">
			<NavigationBar />
			<main>
				<div id="formWrapper">
					<ResetPasswordForm />
				</div>
			</main>
		</div>
	</GlobalStateWrapper>,
	document.getElementById('root')
);
