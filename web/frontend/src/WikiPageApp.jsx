import React from 'react';

import NavigationBar from './Components/NavigationBar';
import Footer from './Components/Footer';
import WikiPageLoader from './Components/WikiPageLoader';

export default function WikiPageApp()
{
	return (
		<div id="pageWrapper">
			<NavigationBar />
			<main>
				<div id="contentWrapper">
					<div id="content">
						<div id="mainPanelWrapper">
							<div className="mainPanel">
								<WikiPageLoader />
							</div>
						</div>
					</div>
				</div>
			</main>
			<Footer />
		</div>
	);
}
