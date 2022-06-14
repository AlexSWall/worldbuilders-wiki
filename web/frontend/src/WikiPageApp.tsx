import React, { useContext } from 'react';

import { GlobalStateContext } from 'GlobalState';

import Footer from './Components/Footer';
import NavigationBar from './Components/NavigationBar';
import Sidebar from './Components/Sidebars/Sidebar';
import WikiPageLoader from './Components/WikiPageLoader';

export default function WikiPageApp()
{
	const globalState = useContext( GlobalStateContext );

	return (
		<div id="pageWrapper">
			<NavigationBar />
			<main>
				<div id="contentWrapper">
					<div id="content">
						{ globalState.QuickNavigationOpen && <Sidebar /> }
						<div id="mainPanelWrapper">
							<div className="mainPanel">
								<WikiPageLoader />
							</div>
						</div>
						{/* { globalState.QuickNavigationOpen && <Sidebar /> } */}
					</div>
				</div>
			</main>
			<Footer />
		</div>
	);
}
