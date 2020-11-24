import React from 'react';

import NavigationBar from './Components/NavigationBar';
import Flash from './Components/Flash';
import AuthenticationPanel from './Components/AuthenticationPanel';
import AuthForm from './Components/Authentication/AuthForm';

export default function AuthenticationApp({ authenticationData, flash, formProperties })
{
	return (
		<div id="pageWrapper">
			<NavigationBar authenticationData={ authenticationData } />
			<Flash flash={ flash } />
			<main>
				<div id="contentWrapper">
					<div id="content">
						<div id="mainPanelWrapper">
							<div id="mainPanel">
								<AuthenticationPanel>
									<AuthForm formProperties={ formProperties } />
								</AuthenticationPanel>
							</div>
						</div>
					</div>
				</div>
			</main>
		</div>
	);
}
