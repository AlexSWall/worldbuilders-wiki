import React from 'react';

import NavigationBar from './Components/NavigationBar';
import Flash from './Components/Flash';
import Footer from './Components/Footer';
import Sidebar from './Components/Sidebar';
import WikiPageLoader from './Components/WikiPageLoader';

export default function WikiPageApp()
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
								<WikiPageLoader urlBase='/w/' />
							</div>
						</div>
					</div>
				</div>
			</main>
			<Footer />
			<Sidebar />
		</div>
	);
}
