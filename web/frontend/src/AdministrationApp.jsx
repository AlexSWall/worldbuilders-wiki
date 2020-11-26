import React from 'react';

import NavigationBar from './Components/NavigationBar';
import Flash from './Components/Flash';
import AdministrationPanel from './Components/AdministrationPanel';

export default function AdministrationApp()
{
	return (
		<div id="pageWrapper">
			<NavigationBar />
			<Flash />
			<main>
				<div id="contentWrapper">
					<div id="content">
						<div id="mainPanelWrapper">
							<div id="mainPanel">
								<AdministrationPanel />
							</div>
						</div>
					</div>
				</div>
			</main>
		</div>
	);
}
